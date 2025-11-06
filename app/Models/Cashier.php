<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class Cashier extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id', 'sale', 'title', 'observation', 'status', 'amountExpectedClosing',
        'amountOpening', 'amountClosed', 'amountMissing', 'amountLeftover',
        'closed_at', 'deleted_at', 'closeUser_id', 'view',
        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function movements(){
        return $this->hasMany(CashierMovement::class, 'cashier_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');//Para el cajero 
    }

    public function serviceTransactions(){
        return $this->hasMany(ServiceTransaction::class, 'cashier_id')->withTrashed();
    }




    // public function sales(){
    //     return $this->hasMany(Sale::class, 'cashier_id')->withTrashed();
    // }

    public function expenses(){
        return $this->hasMany(Expense::class, 'cashier_id');
    }

    public function details(){
        return $this->hasMany(CashierDetail::class);
    }

    public function userclose(){
        return $this->belongsTo(User::class, 'closeUser_id');
    }
}
