<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class BookingController extends Controller
{

    public function store(Request $request, Service $service)
    {
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'client_name' => 'required|string',
            'client_phone' => 'required|string',
        ]);

        $tz = 'Europe/Moscow';
        $startMsK = Carbon::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['time'], $tz);
        $startUtc = $startMsK->copy()->setTimezone('UTC');
        $totalMinutes = $service->duration_minutes + 30;
        $endUtc = $startUtc->copy()->addMinutes($totalMinutes);

        // запрещаем воскресенье
        if ((int)$startMsK->format('N') === 7) {
            return response()->json(['error' => 'Бронирование в воскресенье запрещено'], 422);
        }

        // рабочие часы
        if (
            $startMsK->lt($startMsK->copy()->setTime(10, 0)) ||
            $endUtc->setTimezone($tz)->gt($startMsK->copy()->setTime(20, 0))
        ) {
            return response()->json(['error' => 'Выбранное время вне рабочий часов'], 422);
        }

        try {
            DB::transaction(function () use ($service, $startUtc, $endUtc, $data) {
                $conflict = Booking::where('service_id', $service->id)
                    ->where('status', 'active')
                    ->where('start_at', '<', $endUtc)
                    ->where('end_at', '>', $startUtc)
                    ->lockForUpdate()
                    ->exists();

                if ($conflict) {
                    throw new \Exception('Слот уже забронирован');
                }

                Booking::create([
                    'service_id' => $service->id,
                    'start_at' => $startUtc,
                    'end_at' => $endUtc,
                    'client_name' => $data['client_name'],
                    'client_phone' => $data['client_phone'],
                    'status' => 'active',
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Бронирование успешно!']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
