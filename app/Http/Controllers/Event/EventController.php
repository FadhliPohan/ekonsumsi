<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\event\Event;
use App\Models\event\Consumtion;
use App\Models\event\Peserta;
use App\Models\event\StatusLog;
use App\Models\masterData\Food;
use App\Models\masterData\FoodLog;
use App\Models\masterData\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('name_departemen', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%')
                    ->orWhere('name_user_created', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->paginate(10);

        if ($request->ajax()) {
            return view('event.table', compact('events'))->render();
        }

        return view('event.index', compact('events'));
    }

    public function create()
    {
        $departemens = Departemen::where('is_active', 1)->orderBy('name')->get();
        $foods = Food::where('is_active', 1)->orderBy('name')->get();
        $users = User::with('detail.departemen')->orderBy('name')->get();

        return view('event.form', compact('departemens', 'foods', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_departemen' => 'required|exists:departemens,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // Consumtions
            'foods' => 'required|array|min:1',
            'foods.*.id_food' => 'required|exists:foods,id',
            'foods.*.qty' => 'required|integer|min:1',
            // Pesertas
            'pesertas' => 'required|array|min:1',
            'pesertas.*.user_id' => 'required|exists:users,id',
        ]);

        // Validate no duplicate foods
        $foodIds = array_column($validated['foods'], 'id_food');
        if (count($foodIds) !== count(array_unique($foodIds))) {
            return response()->json(['error' => 'Terdapat makanan yang sama dalam daftar. Mohon hapus duplikat.'], 422);
        }

        // Validate no duplicate pesertas
        $pesertaUserIds = array_column($validated['pesertas'], 'user_id');
        if (count($pesertaUserIds) !== count(array_unique($pesertaUserIds))) {
            return response()->json(['error' => 'Terdapat peserta yang sama dalam daftar. Mohon hapus duplikat.'], 422);
        }

        DB::transaction(function () use ($validated, $request) {
            $departemen = Departemen::find($validated['id_departemen']);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('event', 'public');
            }

            $event = Event::create([
                'name' => $validated['name'],
                'id_departemen' => $validated['id_departemen'],
                'name_departemen' => $departemen->name,
                'status' => Event::STATUS_OPEN,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'location' => $validated['location'] ?? null,
                'image' => $imagePath,
                'id_user_created' => Auth::id(),
                'name_user_created' => Auth::user()->name,
                'description' => $validated['description'] ?? null,
            ]);

            // Save consumtions
            foreach ($validated['foods'] as $foodData) {
                $food = Food::find($foodData['id_food']);
                Consumtion::create([
                    'id_event' => $event->id,
                    'id_food' => $food->id,
                    'food_name' => $food->name,
                    'id_departemen' => $event->id_departemen,
                    'id_user' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'qty' => $foodData['qty'],
                    'price' => $food->price,
                    'total' => $foodData['qty'] * $food->price,
                ]);
            }

            // Save pesertas
            foreach ($validated['pesertas'] as $pesertaData) {
                $user = User::find($pesertaData['user_id']);
                Peserta::create([
                    'id_event' => $event->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'status' => 1, // default hadir
                ]);
            }

            // Log status
            StatusLog::create([
                'id_event' => $event->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'status_from' => null,
                'status_to' => Event::STATUS_OPEN,
                'description' => 'Event dibuat oleh ' . Auth::user()->name,
            ]);
        });

        return response()->json(['success' => 'Event berhasil dibuat.']);
    }

    public function show($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $consumtions = $event->consumtions;
        $pesertas = $event->pesertas;
        $statusLogs = $event->statusLogs;

        return response()->json([
            'event' => $event,
            'consumtions' => $consumtions,
            'pesertas' => $pesertas,
            'statusLogs' => $statusLogs,
        ]);
    }

    public function edit($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        if (!$event->isEditable()) {
            return redirect()->route('event.index')->with('error', 'Event tidak dapat diedit. Status sudah diproses.');
        }

        $departemens = Departemen::where('is_active', 1)->orderBy('name')->get();
        $foods = Food::where('is_active', 1)->orderBy('name')->get();
        $users = User::with('detail.departemen')->orderBy('name')->get();
        $consumtions = $event->consumtions()->with('food')->get();
        $pesertas = $event->pesertas;

        return view('event.form', compact('event', 'departemens', 'foods', 'users', 'consumtions', 'pesertas'));
    }

    public function update(Request $request, $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        // Hanya bisa edit jika status Open atau Reject
        if (!$event->isEditable()) {
            return response()->json(['error' => 'Event tidak dapat diedit. Status sudah diproses.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_departemen' => 'required|exists:departemens,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'foods' => 'required|array|min:1',
            'foods.*.id_food' => 'required|exists:foods,id',
            'foods.*.qty' => 'required|integer|min:1',
            'pesertas' => 'required|array|min:1',
            'pesertas.*.user_id' => 'required|exists:users,id',
        ]);

        // Validate no duplicate foods
        $foodIds = array_column($validated['foods'], 'id_food');
        if (count($foodIds) !== count(array_unique($foodIds))) {
            return response()->json(['error' => 'Terdapat makanan yang sama dalam daftar. Mohon hapus duplikat.'], 422);
        }

        // Validate no duplicate pesertas
        $pesertaUserIds = array_column($validated['pesertas'], 'user_id');
        if (count($pesertaUserIds) !== count(array_unique($pesertaUserIds))) {
            return response()->json(['error' => 'Terdapat peserta yang sama dalam daftar. Mohon hapus duplikat.'], 422);
        }

        DB::transaction(function () use ($event, $validated, $request) {
            $departemen = Departemen::find($validated['id_departemen']);

            if ($request->hasFile('image')) {
                if ($event->image && Storage::disk('public')->exists($event->image)) {
                    Storage::disk('public')->delete($event->image);
                }
                $event->image = $request->file('image')->store('event', 'public');
            }

            // Jika sebelumnya reject, kembalikan ke open
            $oldStatus = $event->status;
            $newStatus = ($oldStatus == Event::STATUS_REJECT) ? Event::STATUS_OPEN : $oldStatus;

            $event->update([
                'name' => $validated['name'],
                'id_departemen' => $validated['id_departemen'],
                'name_departemen' => $departemen->name,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'location' => $validated['location'] ?? null,
                'image' => $event->image,
                'description' => $validated['description'] ?? null,
                'status' => $newStatus,
                'reject_reason' => null,
            ]);

            // Re-create consumtions
            $event->consumtions()->forceDelete();
            foreach ($validated['foods'] as $foodData) {
                $food = Food::find($foodData['id_food']);
                Consumtion::create([
                    'id_event' => $event->id,
                    'id_food' => $food->id,
                    'food_name' => $food->name,
                    'id_departemen' => $event->id_departemen,
                    'id_user' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'qty' => $foodData['qty'],
                    'price' => $food->price,
                    'total' => $foodData['qty'] * $food->price,
                ]);
            }

            // Re-create pesertas
            $event->pesertas()->forceDelete();
            foreach ($validated['pesertas'] as $pesertaData) {
                $user = User::find($pesertaData['user_id']);
                Peserta::create([
                    'id_event' => $event->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'status' => 1,
                ]);
            }

            // Log jika status berubah dari reject ke open
            if ($oldStatus == Event::STATUS_REJECT && $newStatus == Event::STATUS_OPEN) {
                StatusLog::create([
                    'id_event' => $event->id,
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'status_from' => Event::STATUS_REJECT,
                    'status_to' => Event::STATUS_OPEN,
                    'description' => 'Event diajukan ulang oleh ' . Auth::user()->name,
                ]);
            }
        });

        return response()->json(['success' => 'Event berhasil diperbarui.']);
    }

    public function destroy($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        if (!$event->isDeletable()) {
            return response()->json(['error' => 'Event tidak dapat dihapus. Status sudah diproses.'], 403);
        }

        DB::transaction(function () use ($event) {
            $event->consumtions()->delete();
            $event->pesertas()->delete();
            $event->statusLogs()->delete();
            $event->delete();
        });

        return response()->json(['success' => 'Event berhasil dihapus.']);
    }

    /**
     * Update status event (approve/reject/close)
     */
    public function updateStatus(Request $request, $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'action' => 'required|in:approve,reject,close',
            'description' => 'nullable|string',
        ]);

        $action = $validated['action'];
        $description = $validated['description'] ?? null;
        $oldStatus = $event->status;
        $newStatus = $oldStatus;

        switch ($oldStatus) {
            case Event::STATUS_OPEN:
                if ($action === 'approve') {
                    $newStatus = Event::STATUS_APPROVED_VP;
                    $description = $description ?: 'Disetujui oleh Manager Departemen';
                } elseif ($action === 'reject') {
                    $newStatus = Event::STATUS_REJECT;
                    $description = $description ?: 'Ditolak oleh Manager Departemen';
                }
                break;

            case Event::STATUS_APPROVED_VP:
                if ($action === 'approve') {
                    $newStatus = Event::STATUS_ON_PROCESS;
                    $description = $description ?: 'Diterima oleh Staf Umum, sedang diproses';
                }
                break;

            case Event::STATUS_ON_PROCESS:
                if ($action === 'approve') {
                    $newStatus = Event::STATUS_APPROVED_VP_UMUM;
                    $description = $description ?: 'Disetujui oleh Manager Dept. Umum';
                } elseif ($action === 'reject') {
                    $newStatus = Event::STATUS_REJECT;
                    $description = $description ?: 'Ditolak oleh Manager Dept. Umum';
                }
                break;

            case Event::STATUS_APPROVED_VP_UMUM:
                if ($action === 'close') {
                    $newStatus = Event::STATUS_CLOSE_BY_UMUM;
                    $description = $description ?: 'Makanan sudah dikirim oleh Staf Umum';
                }
                break;

            case Event::STATUS_CLOSE_BY_UMUM:
                if ($action === 'close') {
                    $newStatus = Event::STATUS_CLOSE_BY_USER;
                    $description = $description ?: 'Pesanan diterima, event selesai';
                }
                break;
        }

        if ($newStatus === $oldStatus) {
            return response()->json(['error' => 'Aksi tidak valid untuk status saat ini.'], 422);
        }

        DB::transaction(function () use ($event, $oldStatus, $newStatus, $description, $validated) {
            $event->update([
                'status' => $newStatus,
                'reject_reason' => ($newStatus == Event::STATUS_REJECT) ? $validated['description'] : $event->reject_reason,
            ]);

            StatusLog::create([
                'id_event' => $event->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'status_from' => $oldStatus,
                'status_to' => $newStatus,
                'description' => $description,
            ]);

            // ── Deduct food stock & create FoodLog when closed by Umum ──
            if ($newStatus == Event::STATUS_CLOSE_BY_UMUM) {
                $consumtions = $event->consumtions()->with('food')->get();

                foreach ($consumtions as $item) {
                    $food = $item->food;
                    if (!$food) continue;

                    $qtyBefore = (int) $food->qty_available;
                    $qtyAfter  = max(0, $qtyBefore - (int) $item->qty);

                    // Update food stock
                    $food->update([
                        'qty_available' => $qtyAfter,
                        'updated_by'   => Auth::id(),
                        'updated_name' => Auth::user()->name,
                    ]);

                    // Create FoodLog for monitoring
                    FoodLog::create([
                        'id_food'      => $food->id,
                        'type'         => 'out',
                        'qty'          => $item->qty,
                        'price_before' => $food->price,
                        'price_after'  => $food->price,
                        'qty_before'   => $qtyBefore,
                        'qty_after'    => $qtyAfter,
                        'description'  => 'Konsumsi event: ' . $event->name,
                        'created_by'   => Auth::id(),
                        'created_name' => Auth::user()->name,
                    ]);
                }
            }
        });

        $statusLabel = Event::STATUS_LABELS[$newStatus] ?? $newStatus;
        return response()->json(['success' => "Status berhasil diubah menjadi: {$statusLabel}"]);
    }
}
