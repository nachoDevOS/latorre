<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class ServiceItem extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'service_id',
        'itemStock_id',
        'pricePurchase',
        'price',
        'quantity',
        'amount',
        
        'status',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');   
    }

    public function itemStock()
    {
        return $this->belongsTo(ItemStock::class, 'itemStock_id'); 
    }
    
}
