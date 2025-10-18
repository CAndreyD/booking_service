<?php

namespace App\Http\Services;

use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;

class SlotService
{
    /**
     * Генерирует список 30-минутных временных слотов на текущую неделю (Пн–Сб)
     * для указанной услуги, помечая занятые.
     *
     * Рабочее время: с 10:00 до 20:00.
     * Воскресенье пропускается.
     *
     * @param Service $service Услуга, для которой формируются слоты.
     * @return array Ассоциативный массив: ['YYYY-MM-DD' => [['time' => 'HH:MM', 'busy' => bool], ...]]
     */
    public function generateWeekSlots(Service $service): array
    {
        $tz = 'Europe/Moscow';
        $bookings = Booking::where('service_id', $service->id)
            ->where('status', 'active')
            ->get();

        $week = [];
        $startDate = now()->startOfWeek();

        for ($i = 0; $i < 6; $i++) {
            $day = $startDate->copy()->addDays($i);
            $slots = [];

            for ($h = 10; $h < 20; $h++) {
                foreach ([0, 30] as $m) {
                    $slotStart = $day->copy()->setTime($h, $m);
                    $slotEnd = $slotStart->copy()->addMinutes(30);

                    $isBusy = $bookings->contains(
                        fn($b) =>
                        $slotStart < Carbon::parse($b->end_at, $tz) &&
                            $slotEnd > Carbon::parse($b->start_at, $tz)
                    );

                    $slots[] = [
                        'time' => $slotStart->format('H:i'),
                        'busy' => $isBusy,
                    ];
                }
            }

            $week[$day->format('Y-m-d')] = $slots;
        }

        return $week;
    }
}
