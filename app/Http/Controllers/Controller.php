<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\Cashier;
use App\Models\CashierMovement;
use App\Models\CashierDetail;
use App\Models\CashierDetailCash;
use DateTime;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function custom_authorize($permission){
        if(!Auth::user()->hasPermission($permission)){
            abort(403, 'THIS ACTIO UNAUTHORIZED.');
        }
    }



    //Para obtener el detalle de cualquier caja y en cualquier estado que no se encuentre eliminada (Tipo de ID, cashier_id o user_id , status)
    public function cashier($id, $user, $status)
    {
        $cashier = Cashier::with(['movements' => function($q){
                            $q->where('deleted_at', NULL)
                            ->with(['details.detailCashes']);
                        },
                        'details' => function($q){
                            $q->where('deleted_at', NULL)
                            ->with(['detailCashes']);
                        },
                        // 'sales' => function($q) {                
                        //     $q->whereHas('saleTransactions', function($q) {
                        //         $q->whereIn('paymentType', ['Efectivo', 'Qr']);
                        //     })
                        //     ->with(['person', 'register', 'saleDetails', 'saleTransactions' => function($q) {
                        //         $q->where('deleted_at', NULL);
                        //     }]);
                        // },
                        'expenses.categoryExpense'

                    ])
                    ->whereRaw($id?$id:1) // id de cashier
                    ->whereRaw($user?$user:1) //user_id del usario de cashier
                    ->where('deleted_at', null)
                    ->whereRaw($status?$status:1)
                    ->first();   
        
        return $cashier;
    }

    
}
