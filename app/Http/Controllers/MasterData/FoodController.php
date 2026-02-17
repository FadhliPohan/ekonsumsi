<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\masterData\Food;
use App\Models\masterData\FoodLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $query = Food::orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $foods = $query->paginate(9);

        if ($request->ajax()) {
            return view('food.grid', compact('foods'))->render();
        }

        return view('food.index', compact('foods'));
    }

    public function show($uuid)
    {
        $food = Food::where('uuid', $uuid)->firstOrFail();
        $logs = $food->logs()->limit(20)->get();

        return response()->json([
            'food' => $food,
            'logs' => $logs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'qty_available' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('food', 'public');
            }

            $food = Food::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'qty_available' => $validated['qty_available'],
                'is_active' => $request->has('is_active') ? 1 : 0,
                'image' => $imagePath,
                'updated_by' => Auth::id(),
                'updated_name' => Auth::user()->name,
            ]);

            // Log pembuatan makanan baru
            FoodLog::create([
                'id_food' => $food->id,
                'type' => 'stock_in',
                'qty' => $validated['qty_available'],
                'price_before' => 0,
                'price_after' => $validated['price'],
                'qty_before' => 0,
                'qty_after' => $validated['qty_available'],
                'description' => 'Makanan baru ditambahkan: ' . $validated['name'] . ' (Stok awal: ' . $validated['qty_available'] . ', Harga: Rp ' . number_format($validated['price'], 0, ',', '.') . ')',
                'created_by' => Auth::id(),
                'created_name' => Auth::user()->name,
            ]);
        });

        return response()->json(['success' => 'Makanan berhasil ditambahkan.']);
    }

    public function update(Request $request, $uuid)
    {
        $food = Food::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'qty_available' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($food, $validated, $request) {
            $oldPrice = $food->price;
            $oldQty = $food->qty_available;
            $newPrice = $validated['price'];
            $newQty = $validated['qty_available'];
            $logs = [];

            // Log perubahan harga
            if ($oldPrice != $newPrice) {
                $logs[] = [
                    'id_food' => $food->id,
                    'type' => 'price_change',
                    'qty' => 0,
                    'price_before' => $oldPrice,
                    'price_after' => $newPrice,
                    'qty_before' => $oldQty,
                    'qty_after' => $newQty,
                    'description' => 'Perubahan harga ' . $food->name . ': Rp ' . number_format($oldPrice, 0, ',', '.') . ' → Rp ' . number_format($newPrice, 0, ',', '.'),
                    'created_by' => Auth::id(),
                    'created_name' => Auth::user()->name,
                ];
            }

            // Log perubahan stok
            if ($oldQty != $newQty) {
                $diff = $newQty - $oldQty;
                $type = $diff > 0 ? 'stock_in' : 'stock_out';
                $logs[] = [
                    'id_food' => $food->id,
                    'type' => $type,
                    'qty' => abs($diff),
                    'price_before' => $oldPrice,
                    'price_after' => $newPrice,
                    'qty_before' => $oldQty,
                    'qty_after' => $newQty,
                    'description' => ($diff > 0 ? 'Stok masuk' : 'Stok keluar') . ' ' . $food->name . ': ' . abs($diff) . ' item (Stok: ' . $oldQty . ' → ' . $newQty . ')',
                    'created_by' => Auth::id(),
                    'created_name' => Auth::user()->name,
                ];
            }

            // Upload image baru
            if ($request->hasFile('image')) {
                // Hapus gambar lama
                if ($food->image && Storage::disk('public')->exists($food->image)) {
                    Storage::disk('public')->delete($food->image);
                }
                $food->image = $request->file('image')->store('food', 'public');
            }

            $food->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $newPrice,
                'qty_available' => $newQty,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'image' => $food->image,
                'updated_by' => Auth::id(),
                'updated_name' => Auth::user()->name,
            ]);

            foreach ($logs as $log) {
                FoodLog::create($log);
            }
        });

        return response()->json(['success' => 'Makanan berhasil diperbarui.']);
    }

    public function destroy($uuid)
    {
        $food = Food::where('uuid', $uuid)->firstOrFail();

        DB::transaction(function () use ($food) {
            FoodLog::create([
                'id_food' => $food->id,
                'type' => 'stock_out',
                'qty' => $food->qty_available,
                'price_before' => $food->price,
                'price_after' => 0,
                'qty_before' => $food->qty_available,
                'qty_after' => 0,
                'description' => 'Makanan dihapus: ' . $food->name,
                'created_by' => Auth::id(),
                'created_name' => Auth::user()->name,
            ]);

            $food->delete();
        });

        return response()->json(['success' => 'Makanan berhasil dihapus.']);
    }

    public function addStock(Request $request, $uuid)
    {
        $food = Food::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($food, $validated) {
            $oldQty = $food->qty_available;
            $newQty = $oldQty + $validated['qty'];

            $food->update([
                'qty_available' => $newQty,
                'updated_by' => Auth::id(),
                'updated_name' => Auth::user()->name,
            ]);

            FoodLog::create([
                'id_food' => $food->id,
                'type' => 'stock_in',
                'qty' => $validated['qty'],
                'price_before' => $food->price,
                'price_after' => $food->price,
                'qty_before' => $oldQty,
                'qty_after' => $newQty,
                'description' => $validated['description'] ?? ('Penambahan stok ' . $food->name . ': +' . $validated['qty'] . ' item (Stok: ' . $oldQty . ' → ' . $newQty . ')'),
                'created_by' => Auth::id(),
                'created_name' => Auth::user()->name,
            ]);
        });

        return response()->json(['success' => 'Stok berhasil ditambahkan.']);
    }
}
