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

                        'serviceTransactions' => function($q) {                
                            $q->where('deleted_at', NULL)
                            ->with(['service', 'service.person', 'service.serviceItems']);
                        },


                        'expenses.categoryExpense'

                    ])
                    ->whereRaw($id?$id:1) // id de cashier
                    ->whereRaw($user?$user:1) //user_id del usario de cashier
                    ->where('deleted_at', null)
                    ->whereRaw($status?$status:1)
                    ->first();   
        
        return $cashier;
    }


    public function cashierMoney($id, $user, $status)
    {
        $cashier = $this->cashier($id, $user, $status);

        if($cashier){
            $cashierIn = $cashier->movements->where('type', 'Ingreso')->where('deleted_at', NULL)->where('status', 'Aceptado')->sum('amount');

            $paymentEfectivo = $cashier->serviceTransactions->where('paymentType', 'Efectivo')->sum('amount');

            $paymentQr = $cashier->serviceTransactions->where('paymentType', 'Qr')->sum('amount');

            $cashierOut = $cashier->expenses->where('deleted_at', null)->sum('amount');

            $amountCashier = ($cashierIn + $paymentEfectivo) - $cashierOut;
        }

        return response()->json([
            'return' => $cashier?true:false,
            'cashier' => $cashier?$cashier:null,
            // // datos en valores
            'paymentEfectivo' => $cashier?$paymentEfectivo:null,//Para obtener el total de dinero en efectivo recaudado en general
            'paymentQr' => $cashier?$paymentQr:null, //Para obtener el total de dinero en QR recaudado en general
            'amountCashier'=>$cashier?$amountCashier:null, //dinero disponible en caja para su uso 'solo dinero que hay en la caja disponible y cobro solo en efectivos'

            // 'amountEgres' =>$cashier?$amountEgres:null, // dinero prestado de prenda y diario

            'cashierOut'=>$cashier?$cashierOut:null, //Gastos Adicionales

            'cashierIn'=>$cashier?$cashierIn:null// Dinero total abonado a las cajas
        ]);
    }

    
}
