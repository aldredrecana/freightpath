<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
    'driver_id', 'truck_id', 'origin_address', 'destination_address',
    'departure_at', 'expected_arrival_at', 'status',
    ];
    protected $casts = [
    'departure_at'        => 'datetime',
    'expected_arrival_at' => 'datetime',
    ];
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
    
    public const NEXT = [
    'pending'   => ['accepted', 'cancelled'],
    'accepted'  => ['en_route', 'cancelled'],
    'en_route'  => ['delivered', 'cancelled'],
    'delivered' => [],
    'cancelled' => [],
    
    ];

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DeliveryStatusLog::class)
        ->orderByDesc('logged_at')
        ->orderByDesc('id');
    }

    public function allowedNext(User $user): array
    {
        $next = self::NEXT[$this->status] ?? [];
        
        return array_values(array_filter($next, function ($status) use ($user) {
            return $status === 'cancelled'
            ? ($user->hasRole('admin') || $user->hasRole('dispatcher'))
            : $user->hasRole('driver');
        }));
    }
}
