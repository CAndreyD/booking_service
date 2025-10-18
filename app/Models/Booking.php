<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'service_id','start_at','end_at','client_name','client_phone','status'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeOverlapping($q, $serviceId, $start, $end)
    {
        return $q->where('service_id', $serviceId)
                 ->where('status', 'active')
                 ->where('start_at', '<', $end)
                 ->where('end_at', '>', $start);
    }
}
