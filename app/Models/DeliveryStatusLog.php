<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryStatusLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['schedule_id', 'logged_by', 'status', 'remarks', 'logged_at'];
    protected $casts = ['logged_at' => 'datetime'];
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
