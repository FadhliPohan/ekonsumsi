<?php

namespace App\Http\Controllers\Saldo;

use App\Http\Controllers\Controller;
use App\Models\masterData\Departemen;
use App\Models\saldo\logSaldo;
use App\Models\saldo\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaldoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage saldo');
    }

    /**
     * Display a listing of all departemen with their saldo.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departements = Departemen::where('is_active', true)
                ->with('saldo')
                ->orderBy('name')
                ->paginate(10);

            return view('saldo.table', compact('departements'))->render();
        }

        $departements = Departemen::where('is_active', true)
            ->with('saldo')
            ->orderBy('name')
            ->paginate(10);

        return view('saldo.index', compact('departements'));
    }

    /**
     * Get log saldo for a specific departemen (JSON for modal).
     */
    public function show($uuid)
    {
        $departemen = Departemen::where('uuid', $uuid)->firstOrFail();
        $saldo = Saldo::where('id_departemen', $departemen->id)->first();

        $logs = [];
        $currentSaldo = 0;

        if ($saldo) {
            $currentSaldo = $saldo->saldo;
            $logs = logSaldo::where('id_saldo', $saldo->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'departemen' => $departemen,
            'saldo' => $currentSaldo,
            'logs' => $logs,
        ]);
    }

    /**
     * Store a new saldo transaction (auto-create saldo if not exists).
     */
    public function store(Request $request)
    {
        $request->validate([
            'departemen_uuid' => 'required|exists:departemens,uuid',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|in:masuk,keluar',
            'description' => 'nullable|string|max:500',
        ]);

        $departemen = Departemen::where('uuid', $request->departemen_uuid)->firstOrFail();
        $user = Auth::user();

        // Find or create saldo for this departemen
        $saldo = Saldo::firstOrCreate(
            ['id_departemen' => $departemen->id],
            ['saldo' => 0]
        );

        // Validasi saldo keluar tidak boleh melebihi saldo saat ini
        if ($request->status === 'keluar' && $request->jumlah > $saldo->saldo) {
            return response()->json([
                'errors' => ['jumlah' => ['Jumlah keluar melebihi saldo saat ini (Rp ' . number_format($saldo->saldo, 0, ',', '.') . ')']]
            ], 422);
        }

        DB::transaction(function () use ($saldo, $request, $user) {
            // Create log
            logSaldo::create([
                'id_saldo' => $saldo->id,
                'saldo' => $request->jumlah,
                'description' => $request->description,
                'status' => $request->status,
                'created_by' => $user->id,
                'created_name' => $user->name,
                'updated_by' => $user->id,
                'updated_name' => $user->name,
            ]);

            // Update saldo
            if ($request->status === 'masuk') {
                $saldo->saldo += $request->jumlah;
            } else {
                $saldo->saldo -= $request->jumlah;
            }
            $saldo->updated_by = $user->id;
            $saldo->updated_name = $user->name;
            $saldo->save();
        });

        return response()->json(['success' => 'Transaksi saldo berhasil disimpan.']);
    }
}
