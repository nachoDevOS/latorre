<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class Item extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'itemCategory_id',
        'image',
        'name',
        'observation',
        'status',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function itemCategory()
    {
        return $this->belongsTo(itemCategory::class, 'itemCategory_id')->withTrashed();
    }

    // public function itemSalestocks()
    // {
    //     return $this->hasMany(ItemSaleStock::class, 'itemSale_id');
    // }

    // /**
    //  * Un producto puede estar en muchos detalles de venta.
    //  */
    // public function saleDetails()
    // {
    //     // La clave foránea en la tabla 'sale_details' es 'item_id'
    //     return $this->hasMany(SaleDetail::class, 'item_id');
    // }
}
