<?php

namespace App\Http\Controllers;

use App\Models\ItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\Rental;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\ServiceTime;
use App\Models\ServiceTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rooms = Room::with(['service' => function($q) {
            $q->where('status', 'vigente')->with('serviceTimes');
        }])->where('deleted_at', NULL)->get();
        return view('services.index', compact('rooms'));
    }

    public function show($id)
    {
        // Busca la sala por su ID. Si no la encuentra, lanzará un error 404.
        $room = Room::findOrFail($id);

        if($room->status != 'Disponible') {
            return view('services.register', [
                'room' => $room
            ]);
        }
        else{
            return view('services.read', [
                'room' => $room
            ]);
        }


        
    }


    public function startRental(Request $request)
    {      
        $request->validate([
            'start_time' => 'required|date_format:H:i',
        ], [
            'start_time.date_format' => 'El campo hora debe tener el formato HH:MM (por ejemplo: 14:30)',
            'start_time.required' => 'La hora es obligatoria',
        ]);

        $amount_Qr = $request->amount_qr ?? 0;
        $amount_efectivo = $request->payment_method == 'efectivo' ? $request->amount_product + $request->amountSala : ($request->amount_efectivo ?? 0);

        if($request->payment_method == 'efectivo' && $amount_efectivo > $request->amount_received)
        {
            return redirect()->back()->withInput()->withErrors(['message' => 'El monto en efectivo no puede ser mayor al monto recibido.']);
        }

        if($request->payment_method == 'ambos' && ($request->amount_product + $request->amountSala) > ($amount_Qr+$amount_efectivo))
        {
            return redirect()->back()->withInput()->withErrors(['message' => 'La suma del monto en efectivo y el monto por Qr debe ser igual al monto total.']);
        }

        $room = Room::findOrFail($request->room_id);

        if ($room->status != 'Disponible') {
            return redirect()->route('voyager.services.show', $room->id)->with(['message'    => 'Error: La sala ya se encuentra ocupada.', 'alert-type' => 'error']);
        }

        $cashier = $this->cashier(null,'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"');
        if (!$cashier) {
            return redirect()
                ->route('services.index')
                ->with(['message' => 'Usted no cuenta con caja abierta.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();

        try {

            $service = Service::create([
                'room_id' => $request->room_id,
                'person_id'=>$request->person_id,
                'start_time' => $request->start_time,
                'amount_room'=> $request->amountSala,
                'amount_products'=> $request->amount_product,
                'total_amount'=> $request->amountSala + $request->amount_product,

                'observation' => 'Inicio de alquiler',
            ]);

            if(($amount_efectivo + $amount_Qr) > 0)
            {
                $transaction = Transaction::create([
                    'status' => 'Completado',
                ]);
            }

            ServiceTime::create([
                'service_id' => $service->id,
                'time_type' => $request->end_time? 'Tiempo fijo': 'Tiempo sin límite',
                'start_time' => $request->start_time,
                'end_time' => $request->end_time? $request->end_time : null,
                'total_time' => $request->end_time? null : null,
                'amount' => $request->amountSala,
            ]);

            if ($request->products) {
                foreach ($request->products as $key => $value) {
                    $itemStock = ItemStock::where('id', $value['id'])->first();
                    ServiceItem::create([
                        'sale_id' => $service->id,
                        'itemStock_id' => $itemStock->id,
                        'price' => $value['price'],
                        'quantity' => $value['quantity'],
                        'amount' => $value['price'] * $value['quantity'],
                    ]);
                    $itemStock->decrement('stock', $value['quantity']);
                } 
            }  
            
            if ($request->payment_method == 'efectivo' || $request->payment_method == 'ambos' && ($amount_efectivo + $amount_Qr) > 0 ) {
                    ServiceTransaction::create([
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'cashier_id' => $cashier->id,
                        'amount' => $amount_efectivo,
                        'paymentType' => 'Efectivo',
                    ]);
            }
            if ($request->payment_method == 'qr' || $request->payment_method == 'ambos' && ($amount_efectivo + $amount_Qr) > 0) {
                    ServiceTransaction::create([
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'cashier_id' => $cashier->id,
                        'amount' =>  $amount_Qr,
                        'paymentType' => 'Qr',
                    ]);
            }
            
            

            // Cambiar el estado de la sala
            $room->status = 'Ocupada';
            $room->save();

            DB::commit();

            return redirect()->route('services.index')->with([
                'message'    => 'Alquiler iniciado exitosamente para la sala: ' . $room->name,
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return 0;
            return redirect()->route('services.index')->with([
                'message'    => 'Ocurrió un error al iniciar el alquiler: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }
}
