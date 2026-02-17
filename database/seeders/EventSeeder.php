<?php

namespace Database\Seeders;

use App\Models\event\Event;
use App\Models\event\Consumtion;
use App\Models\event\Peserta;
use App\Models\event\StatusLog;
use App\Models\masterData\Departemen;
use App\Models\masterData\Food;
use App\Models\masterData\FoodLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $departemens = Departemen::where('is_active', 1)->get();
        $foods       = Food::where('is_active', 1)->get();
        $users       = User::all();

        if ($departemens->isEmpty() || $foods->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠ Pastikan Departemen, Food, dan User sudah di-seed terlebih dahulu.');
            return;
        }

        $eventNames = [
            'Rapat Koordinasi',
            'Meeting Bulanan',
            'Workshop',
            'Sosialisasi',
            'Training',
            'Seminar',
            'Gathering',
            'Rapat Direksi',
            'FGD',
            'Kick-off Project',
            'Review Kinerja',
            'Rapat Evaluasi',
            'Town Hall',
            'Briefing Pagi',
            'Rapat Anggaran',
            'Pelatihan K3',
            'Diskusi Panel',
            'Launching Produk',
            'Acara HUT',
            'Farewell Party',
            'Rapat Darurat',
            'Rapat Strategis',
            'Audit Meeting',
            'IT Review',
            'Safety Meeting',
            'Rapat Sales',
            'Customer Visit',
            'Vendor Meeting',
        ];

        $locations = [
            'Ruang Rapat Lt. 1',
            'Aula Utama',
            'Ruang Meeting VIP',
            'Ruang Rapat Lt. 3',
            'Ballroom Hotel',
            'Gedung Serbaguna',
            'Ruang Rapat Direksi',
            'Cafetaria',
            'Ruang Training',
            'Outdoor Area',
            'Ruang Rapat Lt. 2',
            'Convention Hall',
        ];

        // Distribute statuses for variety
        $statusPool = [
            Event::STATUS_OPEN,            // ~8
            Event::STATUS_OPEN,
            Event::STATUS_OPEN,
            Event::STATUS_OPEN,
            Event::STATUS_OPEN,
            Event::STATUS_OPEN,
            Event::STATUS_OPEN,
            Event::STATUS_OPEN,
            Event::STATUS_APPROVED_VP,      // ~7
            Event::STATUS_APPROVED_VP,
            Event::STATUS_APPROVED_VP,
            Event::STATUS_APPROVED_VP,
            Event::STATUS_APPROVED_VP,
            Event::STATUS_APPROVED_VP,
            Event::STATUS_APPROVED_VP,
            Event::STATUS_ON_PROCESS,       // ~7
            Event::STATUS_ON_PROCESS,
            Event::STATUS_ON_PROCESS,
            Event::STATUS_ON_PROCESS,
            Event::STATUS_ON_PROCESS,
            Event::STATUS_ON_PROCESS,
            Event::STATUS_ON_PROCESS,
            Event::STATUS_APPROVED_VP_UMUM, // ~6
            Event::STATUS_APPROVED_VP_UMUM,
            Event::STATUS_APPROVED_VP_UMUM,
            Event::STATUS_APPROVED_VP_UMUM,
            Event::STATUS_APPROVED_VP_UMUM,
            Event::STATUS_APPROVED_VP_UMUM,
            Event::STATUS_CLOSE_BY_UMUM,    // ~8
            Event::STATUS_CLOSE_BY_UMUM,
            Event::STATUS_CLOSE_BY_UMUM,
            Event::STATUS_CLOSE_BY_UMUM,
            Event::STATUS_CLOSE_BY_UMUM,
            Event::STATUS_CLOSE_BY_UMUM,
            Event::STATUS_CLOSE_BY_UMUM,
            Event::STATUS_CLOSE_BY_UMUM,
            Event::STATUS_CLOSE_BY_USER,    // ~8
            Event::STATUS_CLOSE_BY_USER,
            Event::STATUS_CLOSE_BY_USER,
            Event::STATUS_CLOSE_BY_USER,
            Event::STATUS_CLOSE_BY_USER,
            Event::STATUS_CLOSE_BY_USER,
            Event::STATUS_CLOSE_BY_USER,
            Event::STATUS_CLOSE_BY_USER,
            Event::STATUS_REJECT,           // ~6
            Event::STATUS_REJECT,
            Event::STATUS_REJECT,
            Event::STATUS_REJECT,
            Event::STATUS_REJECT,
            Event::STATUS_REJECT,
        ];
        shuffle($statusPool);

        for ($i = 0; $i < 50; $i++) {
            $dept       = $departemens->random();
            $creator    = $users->random();
            $status     = $statusPool[$i];
            $startDate  = Carbon::now()->subDays(rand(1, 180));
            $endDate    = (clone $startDate)->addHours(rand(2, 8));

            DB::transaction(function () use (
                $faker,
                $eventNames,
                $locations,
                $foods,
                $users,
                $dept,
                $creator,
                $status,
                $startDate,
                $endDate,
                $i
            ) {
                // ── Create Event ──
                $event = Event::create([
                    'name'             => $eventNames[array_rand($eventNames)] . ' ' . ($i + 1),
                    'id_departemen'    => $dept->id,
                    'name_departemen'  => $dept->name,
                    'status'           => $status,
                    'start_date'       => $startDate,
                    'end_date'         => $endDate,
                    'location'         => $locations[array_rand($locations)],
                    'description'      => $faker->sentence(10),
                    'id_user_created'  => $creator->id,
                    'name_user_created' => $creator->name,
                ]);

                // ── Add Consumptions (2-6 food items per event) ──
                $numFoods    = rand(2, min(6, $foods->count()));
                $selectedFoods = $foods->random($numFoods);

                foreach ($selectedFoods as $food) {
                    $qty = rand(5, 50);
                    Consumtion::create([
                        'id_event'      => $event->id,
                        'id_food'       => $food->id,
                        'food_name'     => $food->name,
                        'id_departemen' => $dept->id,
                        'id_user'       => $creator->id,
                        'user_name'     => $creator->name,
                        'qty'           => $qty,
                        'price'         => $food->price,
                        'total'         => $qty * $food->price,
                        'status'        => 1,
                        'description'   => null,
                    ]);

                    // If event is closed, deduct stock and log
                    if (in_array($status, [Event::STATUS_CLOSE_BY_UMUM, Event::STATUS_CLOSE_BY_USER])) {
                        $qtyBefore = (int) $food->qty_available;
                        $qtyAfter  = max(0, $qtyBefore - $qty);

                        $food->update(['qty_available' => $qtyAfter]);

                        FoodLog::create([
                            'id_food'      => $food->id,
                            'type'         => 'out',
                            'qty'          => $qty,
                            'price_before' => $food->price,
                            'price_after'  => $food->price,
                            'qty_before'   => $qtyBefore,
                            'qty_after'    => $qtyAfter,
                            'description'  => 'Konsumsi event: ' . $event->name,
                            'created_by'   => $creator->id,
                            'created_name' => $creator->name,
                        ]);
                    }
                }

                // ── Add Participants (3-15 users per event) ──
                $numPeserta      = rand(3, min(15, $users->count()));
                $selectedUsers   = $users->random($numPeserta);

                foreach ($selectedUsers as $user) {
                    // Closed events: 80% hadir, open events: 0 (belum hadir)
                    $pesertaStatus = in_array($status, [Event::STATUS_CLOSE_BY_UMUM, Event::STATUS_CLOSE_BY_USER])
                        ? ($faker->boolean(80) ? 1 : 0)
                        : 0;

                    Peserta::create([
                        'id_event'    => $event->id,
                        'user_id'     => $user->id,
                        'user_name'   => $user->name,
                        'status'      => $pesertaStatus,
                        'description' => null,
                    ]);
                }

                // ── Create Status Logs (simulate workflow) ──
                $workflow = $this->getWorkflowForStatus($status);
                $logTime  = (clone $event->created_at);

                foreach ($workflow as $step) {
                    $logTime = $logTime->addHours(rand(1, 24));
                    StatusLog::create([
                        'id_event'    => $event->id,
                        'user_id'     => $creator->id,
                        'user_name'   => $creator->name,
                        'status_from' => $step['from'],
                        'status_to'   => $step['to'],
                        'description' => $step['desc'],
                        'created_at'  => $logTime,
                        'updated_at'  => $logTime,
                    ]);
                }
            });
        }

        $this->command->info('✅ 50 events seeded with consumptions, participants, and status logs.');
    }

    /**
     * Return the workflow steps needed to reach a given status.
     */
    private function getWorkflowForStatus(int $status): array
    {
        $steps = [];

        if ($status >= Event::STATUS_APPROVED_VP) {
            $steps[] = ['from' => Event::STATUS_OPEN, 'to' => Event::STATUS_APPROVED_VP, 'desc' => 'Disetujui oleh VP Departemen'];
        }
        if ($status >= Event::STATUS_ON_PROCESS) {
            $steps[] = ['from' => Event::STATUS_APPROVED_VP, 'to' => Event::STATUS_ON_PROCESS, 'desc' => 'Diterima oleh Staf Umum'];
        }
        if ($status >= Event::STATUS_APPROVED_VP_UMUM && $status != Event::STATUS_REJECT) {
            $steps[] = ['from' => Event::STATUS_ON_PROCESS, 'to' => Event::STATUS_APPROVED_VP_UMUM, 'desc' => 'Disetujui oleh VP Umum'];
        }
        if ($status == Event::STATUS_CLOSE_BY_UMUM || $status == Event::STATUS_CLOSE_BY_USER) {
            $steps[] = ['from' => Event::STATUS_APPROVED_VP_UMUM, 'to' => Event::STATUS_CLOSE_BY_UMUM, 'desc' => 'Makanan sudah dikirim'];
        }
        if ($status == Event::STATUS_CLOSE_BY_USER) {
            $steps[] = ['from' => Event::STATUS_CLOSE_BY_UMUM, 'to' => Event::STATUS_CLOSE_BY_USER, 'desc' => 'Pesanan diterima, event selesai'];
        }
        if ($status == Event::STATUS_REJECT) {
            // Reject can come from Open or On Process
            $steps[] = ['from' => Event::STATUS_OPEN, 'to' => Event::STATUS_REJECT, 'desc' => 'Ditolak oleh Manager'];
        }

        return $steps;
    }
}
