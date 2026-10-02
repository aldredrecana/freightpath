<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Driver extends Model
{
    protected $fillable = ['user_id', 'license_no', 'license_expiry', 'contact_no', 'address', 'status'];

    protected $casts = ['license_expiry' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
