<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'duration_minutes'];

    public function schedules()
    {
        return $this->hasMany(ServiceSchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    function getAvailableSlots(Service $service, string $dateYmd): array
    {
        $tz = new \DateTimeZone('Europe/Moscow');
        $day = Carbon::createFromFormat('Y-m-d', $dateYmd, $tz);
        $weekday = (int)$day->format('N'); 
        if ($weekday === 7) return []; 

        
        $schedules = $service->schedules()->where('weekday', $weekday)->get();
        if ($schedules->isEmpty()) return [];

        $step = 30; 
        $totalDuration = $service->duration_minutes + 30;

        $available = [];
        foreach ($schedules as $sch) {
            
            $startMsK = Carbon::createFromFormat('Y-m-d H:i', $dateYmd . ' ' . substr($sch->start_time, 0, 5), $tz);
            $endMsK = Carbon::createFromFormat('Y-m-d H:i', $dateYmd . ' ' . substr($sch->end_time, 0, 5), $tz);

            
            $globalStart = Carbon::createFromFormat('Y-m-d H:i', $dateYmd . ' 10:00', $tz);
            $globalEnd   = Carbon::createFromFormat('Y-m-d H:i', $dateYmd . ' 20:00', $tz);

            $start = $startMsK->greaterThan($globalStart) ? $startMsK : $globalStart;
            $end = $endMsK->lessThan($globalEnd) ? $endMsK : $globalEnd;

            
            $lastStart = (clone $end)->subMinutes($totalDuration);

            for ($t = (clone $start); $t->lessThanOrEqualTo($lastStart); $t->addMinutes($step)) {
                
                $slotStartUtc = $t->copy()->setTimezone('UTC');
                $slotEndUtc = $slotStartUtc->copy()->addMinutes($totalDuration);

                
                $conflict = \App\Models\Booking::overlapping(
                    $service->id,
                    $slotStartUtc,
                    $slotEndUtc
                )->exists();

                if (!$conflict) {
                    $available[] = $t->format('H:i');
                }
            }
        }

        return array_values(array_unique($available));
    }
}
