<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceSchedule;
use App\Models\Booking;
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
}
