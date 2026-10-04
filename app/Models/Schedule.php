<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
    'driver_id', 'truck_id', 'origin_address', 'destination_address',
    'departure_at', 'expected_arrival_at', 'status', 'assigned_at',
    ];
    
    protected $casts = [
    'departure_at'        => 'datetime',
    'expected_arrival_at' => 'datetime',
    'assigned_at'         => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
    
    public const ACTIVE = ['pending', 'accepted', 'en_route', 'needs_reassignment'];
    
    public const NEXT = [
    'pending'            => ['accepted', 'cancelled'],
    'accepted'           => ['en_route', 'cancelled'],
    'en_route'           => ['delivered', 'cancelled'],
    'needs_reassignment' => ['cancelled'],
    'delivered'          => [],
    'cancelled'          => [],
    
    ];
    
    public const DECLINE_REASONS = [
    'sick'            => 'Sick',
    'emergency'       => 'Emergency',
    'vehicle_problem' => 'Vehicle problem',
    'other'           => 'Other',
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

    public function getReferenceAttribute(): string
    {
        return 'FP-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }


    // 'urgent' or 'overdue' for trips that still need a driver to confirm.
    public function getUrgencyAttribute(): ?string
    {
        if (! in_array($this->status, ['pending', 'needs_reassignment'], true)) {
            return null;
        }
        
        if ($this->departure_at->isPast()) {
            return 'overdue';
        }
        
        $window = (int) config('freightpath.late_window_hours', 3);
        
        return $this->departure_at->lte(now()->addHours($window)) ? 'urgent' : null;
        }
        
        // When the current driver was offered the trip (pending trips only).
        public function getAwaitingSinceAttribute(): ?\Illuminate\Support\Carbon
        {
            return $this->status === 'pending' ? ($this->assigned_at ?? $this->created_at) : null;
        }
    }
