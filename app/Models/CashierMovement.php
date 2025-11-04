<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class CashierMovement extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'cashier_id', 'amount', 'observation', 'type', 'deleted_at', 'status',
        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function cashier(){
        return $this->belongsTo(Cashier::class, 'cashier_id');
    }

    public function details(){
        return $this->hasMany(CashierDetail::class, 'cashierMovement_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'registerUser_id');
    }
    
}
