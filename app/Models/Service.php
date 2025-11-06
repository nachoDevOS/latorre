<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class Service extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'room_id',
        'person_id',
        'start_time',
        'amount_room',
        'amount_products',
        'total_amount',
        'observation',
        'status',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');   
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');   
    }
    public function serviceTimes()
    {
        return $this->hasMany(ServiceTime::class, 'service_id');   
    }
    public function serviceItems()
    {
        return $this->hasMany(ServiceItem::class, 'service_id');   
    }
    public function serviceTransactions()
    {
        return $this->hasMany(ServiceTransaction::class, 'service_id');   
    }


    
}
