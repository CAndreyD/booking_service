<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ServiceSchedule;
use Inertia\Inertia;
use App\Models\Service;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return Inertia::render('ServicesIndex', [
            'services' => $services,
        ]);
    }

    public function show(Service $service)
    {
        return Inertia::render('ServiceShow', [
            'service' => $service,
        ]);
    }

    public function weekSlots(Service $service)
    {
        $tz = 'Europe/Moscow';

        // Получаем брони за неделю
        $bookings = Booking::where('service_id', $service->id)
            ->where('status', 'active')
            ->get();

        // Рабочие часы (например, 10:00–20:00)
        $workStart = 10; // 10:00
        $workEnd   = 20; // 20:00
        $slotMinutes = 30; // длительность слота

        // Формируем дни недели (Пн-Сб)
        $weekDays = collect();
        $startDate = now()->startOfWeek(); // Пн текущей недели
        for ($i = 0; $i < 6; $i++) {
            $day = $startDate->copy()->addDays($i);
            $slots = [];
            for ($h = $workStart; $h < $workEnd; $h++) {
                foreach ([0, $slotMinutes] as $m) {
                    $slotStart = $day->copy()->setTime($h, $m, 0);
                    $slotEnd   = $slotStart->copy()->addMinutes($slotMinutes);

                    // Проверка на занятость
                    $isBusy = $bookings->contains(function ($b) use ($slotStart, $slotEnd, $tz) {
                        $bookingStart = Carbon::parse($b->start_at)->timezone($tz);
                        $bookingEnd   = Carbon::parse($b->end_at)->timezone($tz);
                        return $slotStart < $bookingEnd && $slotEnd > $bookingStart;
                    });

                    $slots[] = [
                        'time' => $slotStart->format('H:i'),
                        'start_at' => $slotStart->format('Y-m-d H:i'),
                        'end_at'   => $slotEnd->format('Y-m-d H:i'),
                        'busy' => $isBusy,
                    ];
                }
            }
            $weekDays[$day->format('Y-m-d')] = $slots;
        }

        return response()->json(['slots' => $weekDays]);
    }
}
