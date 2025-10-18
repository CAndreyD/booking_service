<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Booking;
use Inertia\Inertia;
use Carbon\Carbon;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return Inertia::render('ServicesIndex', compact('services'));
    }

    public function show(Service $service)
    {
        return Inertia::render('ServiceShow', [
            'service' => $service,
            'slots' => $this->generateWeekSlots($service),
            'bookingUrl' => route('bookings.store', $service->id)
        ]);
    }

    private function generateWeekSlots(Service $service): array
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
