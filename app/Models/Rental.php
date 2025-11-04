<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'customer_name',
        'start_time',
        'status',
    ];

    public function room() {
        return $this->belongsTo(Room::class);
    }
}