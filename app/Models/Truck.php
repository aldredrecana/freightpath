<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = ['plate_no', 'model', 'capacity_kg', 'status'];
    
}
