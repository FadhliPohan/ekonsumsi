<?php

namespace App\Http\Controllers;

use App\Models\event\Event;
use App\Models\event\Consumtion;
use App\Models\event\Peserta;
use App\Models\event\StatusLog;
use App\Models\masterData\Departemen;
use App\Models\masterData\Food;
use App\Models\User;
use App\Models\saldo\Saldo;
use App\Models\saldo\logSaldo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view dashboard');
    }

    public function index()
    {
        // ── KPI Cards ──
        $kpi = [
            'total_events'     => Event::count(),
            'total_pengeluaran' => (float) Consumtion::sum(DB::raw('qty * price')),
            'total_peserta'    => Peserta::count(),
            'total_foods'      => Food::where('is_active', 1)->count(),
            'pending_events'   => Event::whereIn('status', [
                Event::STATUS_OPEN,
                Event::STATUS_APPROVED_VP,
                Event::STATUS_ON_PROCESS,
                Event::STATUS_APPROVED_VP_UMUM,
            ])->count(),
        ];

        // ── EVENT TAB ──

        // 1. Event per status (donut)
        $eventByStatus = Event::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        // 2. Tren event per bulan (line) — last 12 months
        $eventTrend = Event::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')->orderBy('bulan')->get()
            ->pluck('total', 'bulan')->toArray();

        // 3. Event per departemen (bar)
        $eventByDept = Event::join('departemens', 'events.id_departemen', '=', 'departemens.id')
            ->select('departemens.name', DB::raw('COUNT(*) as total'))
            ->groupBy('departemens.name')
            ->orderByDesc('total')->limit(10)->get();

        // 4. Rata-rata waktu approval (Open → Approved VP) — simple approach
        $approvals = StatusLog::where('status_to', Event::STATUS_APPROVED_VP)
            ->join('events', 'event_status_logs.id_event', '=', 'events.id')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, events.created_at, event_status_logs.created_at)) as avg_hours'))
            ->whereNull('event_status_logs.deleted_at')
            ->value('avg_hours');
        $avgApprovalHours = round((float) $approvals, 1);

        // 5. Event terbaru
        $latestEvents = Event::orderByDesc('created_at')->limit(8)->get();

        // ── KONSUMSI TAB ──

        // 6. Top 10 makanan
        $topFoods = Consumtion::join('foods', 'consumtions.id_food', '=', 'foods.id')
            ->select('foods.name', DB::raw('SUM(consumtions.qty) as total_qty'), DB::raw('SUM(consumtions.qty * consumtions.price) as total_value'))
            ->groupBy('foods.name')
            ->orderByDesc('total_qty')->limit(10)->get();

        // 7. Pengeluaran konsumsi per bulan (area)
        $monthlySpending = Event::join('consumtions', 'events.id', '=', 'consumtions.id_event')
            ->select(
                DB::raw("DATE_FORMAT(events.created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(consumtions.qty * consumtions.price) as total')
            )
            ->where('events.created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')->orderBy('bulan')->get()
            ->pluck('total', 'bulan')->toArray();

        // 8. Distribusi harga makanan
        $priceRanges = [
            '0-10K'   => Food::where('is_active', 1)->where('price', '<', 10000)->count(),
            '10-25K'  => Food::where('is_active', 1)->whereBetween('price', [10000, 25000])->count(),
            '25-50K'  => Food::where('is_active', 1)->whereBetween('price', [25001, 50000])->count(),
            '50-100K' => Food::where('is_active', 1)->whereBetween('price', [50001, 100000])->count(),
            '100K+'   => Food::where('is_active', 1)->where('price', '>', 100000)->count(),
        ];

        // 9. Stok makanan rendah (< 10)
        $lowStockFoods = Food::where('is_active', 1)
            ->where('qty_available', '<', 10)
            ->orderBy('qty_available')
            ->limit(10)->get();

        // 10. Perubahan harga (latest 10 changes)
        $priceChanges = DB::table('food_logs')
            ->join('foods', 'food_logs.id_food', '=', 'foods.id')
            ->where('food_logs.type', 'price')
            ->select('foods.name', 'food_logs.price_before', 'food_logs.price_after', 'food_logs.created_at')
            ->orderByDesc('food_logs.created_at')
            ->limit(10)->get();

        // ── SALDO TAB ──

        // 11. Saldo per departemen
        $saldoByDept = Saldo::join('departemens', 'saldos.id_departemen', '=', 'departemens.id')
            ->select('departemens.name', 'saldos.saldo')
            ->orderByDesc('saldos.saldo')->get();

        // 12. Riwayat mutasi saldo per bulan
        $saldoMutasi = logSaldo::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
            DB::raw('SUM(saldo) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')->orderBy('bulan')->get()
            ->pluck('total', 'bulan')->toArray();

        // 13. Anggaran vs Pengeluaran per dept
        $deptBudget = Departemen::with('saldo')->where('is_active', 1)->get()->map(function ($d) {
            $spending = Event::where('events.id_departemen', $d->id)
                ->join('consumtions', 'events.id', '=', 'consumtions.id_event')
                ->sum(DB::raw('consumtions.qty * consumtions.price'));
            return [
                'name'    => $d->name,
                'saldo'   => $d->saldo ? (float)$d->saldo->saldo : 0,
                'spending' => (float)$spending,
            ];
        });

        // 14. Dept dengan pengeluaran tertinggi
        $topSpendingDept = Event::join('consumtions', 'events.id', '=', 'consumtions.id_event')
            ->join('departemens', 'events.id_departemen', '=', 'departemens.id')
            ->select('departemens.name', DB::raw('SUM(consumtions.qty * consumtions.price) as total'))
            ->groupBy('departemens.name')
            ->orderByDesc('total')->limit(5)->get();

        // ── USER TAB ──

        // 15. User per departemen
        $userByDept = DB::table('user_details')
            ->join('departemens', 'user_details.id_departemen', '=', 'departemens.id')
            ->whereNull('user_details.deleted_at')
            ->select('departemens.name', DB::raw('COUNT(*) as total'))
            ->groupBy('departemens.name')
            ->orderByDesc('total')->get();

        // 16. Top 10 peserta aktif
        $topPeserta = Peserta::join('users', 'pesertas.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as total'))
            ->groupBy('users.name')
            ->orderByDesc('total')->limit(10)->get();

        // 17. Rata-rata peserta per event
        $avgPeserta = Event::count() > 0
            ? round(Peserta::count() / Event::count(), 1)
            : 0;

        // 18. Tingkat kehadiran
        $totalPeserta = Peserta::count();
        $hadirPeserta = Peserta::where('status', 1)->count();
        $kehadiranPct = $totalPeserta > 0 ? round(($hadirPeserta / $totalPeserta) * 100, 1) : 0;

        return view('dashboard', compact(
            'kpi',
            'eventByStatus',
            'eventTrend',
            'eventByDept',
            'avgApprovalHours',
            'latestEvents',
            'topFoods',
            'monthlySpending',
            'priceRanges',
            'lowStockFoods',
            'priceChanges',
            'saldoByDept',
            'saldoMutasi',
            'deptBudget',
            'topSpendingDept',
            'userByDept',
            'topPeserta',
            'avgPeserta',
            'kehadiranPct',
            'hadirPeserta',
            'totalPeserta'
        ));
    }
}
