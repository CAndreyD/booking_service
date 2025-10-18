<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request, Service $service, BookingService $bookingService)
    {
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'client_name' => 'required|string',
            'client_phone' => 'required|string',
        ]);

        try {
            $bookingService->createBooking($service, $data);

            return back()
                ->with('success', 'Бронирование успешно создано!');
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'error' => $e->getMessage()
            ]);
        }
    }
}
