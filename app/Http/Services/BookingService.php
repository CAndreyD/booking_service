<?php

namespace App\Http\Services;

use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(Service $service, array $data): void
    {
        $tz = 'Europe/Moscow';
        $start = Carbon::createFromFormat('Y-m-d H:i', "{$data['date']} {$data['time']}", $tz);
        $end = $start->copy()->addMinutes($service->duration_minutes + 30);

        if ((int)$start->format('N') === 7) {
            throw new \Exception('Бронирование в воскресенье запрещено');
        }

        if ($start->lt($start->copy()->setTime(10, 0)) || $end->gt($start->copy()->setTime(20, 0))) {
            throw new \Exception('Выбранное время вне рабочих часов');
        }

        DB::transaction(function () use ($service, $start, $end, $data) {
            $conflict = Booking::where('service_id', $service->id)
                ->where('status', 'active')
                ->where('start_at', '<', $end)
                ->where('end_at', '>', $start)
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new \Exception('Выбранное время пересекается с уже существующим бронированием или не хватает времени до следующего слота');
            }

            Booking::create([
                'service_id' => $service->id,
                'start_at' => $start,
                'end_at' => $end,
                'client_name' => $data['client_name'],
                'client_phone' => $data['client_phone'],
                'status' => 'active',
            ]);
        });
    }
}
