<?php

namespace App\Http\Controllers;

use App\Http\Services\SlotService;
use App\Models\Service;
use App\Models\Booking;
use Inertia\Inertia;
use Carbon\Carbon;

/**
 * Контроллер для отображения списка услуг и расписания для бронирования.
 */
class ServiceController extends Controller
{
    /**
     * Отображает список всех услуг.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $services = Service::all();
        return Inertia::render('ServicesIndex', compact('services'));
    }
    /**
     * Отображает страницу конкретной услуги с доступными слотами на неделю.
     *
     * @param Service $service Экземпляр услуги, полученный через Route Model Binding.
     * @return \Inertia\Response
     */
    public function show(Service $service)
    {
        $slots = app(SlotService::class)->generateWeekSlots($service);
        return Inertia::render('ServiceShow', [
            'service' => $service,
            'slots' => $slots,
            'bookingUrl' => route('bookings.store', $service->id)
        ]);
    }

}
