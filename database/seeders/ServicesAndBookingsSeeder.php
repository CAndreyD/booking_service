<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ServicesAndBookingsSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Booking::truncate();
        ServiceSchedule::truncate();
        Service::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tz = 'Europe/Moscow';

        // Услуги
        $kv30 = Service::create(['name' => 'Поездка на квадроцикле (30)', 'duration_minutes' => 30]);
        $kv60 = Service::create(['name' => 'Поездка на квадроцикле (60)', 'duration_minutes' => 60]);
        $en60 = Service::create(['name' => 'Тур на эндуро (60)', 'duration_minutes' => 60]);
        $en120 = Service::create(['name' => 'Тур на эндуро (120)', 'duration_minutes' => 120]);

        foreach ([$kv30, $kv60, $en60, $en120] as $s) {
            for ($wd = 1; $wd <= 6; $wd++) {
                ServiceSchedule::create([
                    'service_id' => $s->id,
                    'weekday' => $wd,
                    'start_time' => '10:00:00',
                    'end_time' => '20:00:00',
                ]);
            }
        }

        // 16.10.2025 и 17.10.2025
        $bookings = [
            // Поездка квадро 30: 16.10 13:00, 16:00
            [$kv30, '2025-10-16 13:00'],
            [$kv30, '2025-10-16 16:00'],

            // Поездка квадро 30: 17.10 10:00,11:00,13:00,18:00
            [$kv30, '2025-10-17 10:00'],
            [$kv30, '2025-10-17 11:00'],
            [$kv30, '2025-10-17 13:00'],
            [$kv30, '2025-10-17 18:00'],

            // Поездка квадро 60: 16.10 10:00
            [$kv60, '2025-10-16 10:00'],

            // Тур эндуро 60: 16.10 10:00,11:30,18:30
            [$en60, '2025-10-16 10:00'],
            [$en60, '2025-10-16 11:30'],
            [$en60, '2025-10-16 18:30'],

            // Тур эндуро 120: 17.10 14:00
            [$en120, '2025-10-17 14:00'],
        ];

        foreach ($bookings as [$service, $mskDatetime]) {
            $start = Carbon::createFromFormat('Y-m-d H:i', $mskDatetime, $tz);
            $totalMinutes = $service->duration_minutes + 30;
            $end = (clone $start)->addMinutes($totalMinutes);

            Booking::create([
                'service_id' => $service->id,
                'start_at' => $start,
                'end_at' => $end,
                'client_name' => 'Client',
                'client_phone' => '+7 000 000 00 00',
                'status' => 'active',
            ]);
        }
    }
}
