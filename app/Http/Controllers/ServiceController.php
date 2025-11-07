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

        if($room->status == 'Disponible') {
            // Si la sala está disponible, muestra el formulario para iniciar un nuevo alquiler
            return view('services.register', [
                'room' => $room
            ]);
        } else {
            // Si la sala está ocupada, muestra los detalles del servicio actual
            $service = Service::where('room_id', $room->id)
                                ->where('status', 'vigente')
                                ->with(['person', 'serviceTimes', 'serviceItems.itemStock.item'])
                                ->firstOrFail();

            return view('services.read', [
                'room' => $room,
                'service' => $service
            ]);
        }


        
    }


    public function startRental(Request $request)
    {      
        return $request;
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
                        'service_id' => $service->id,
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

    public function addItem(Request $request, Service $service)
    {
        $request->validate([
            'item_stock_id' => 'required|exists:item_stocks,id',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0.01',
        ]);

        $itemStock = ItemStock::findOrFail($request->item_stock_id);

        if ($itemStock->stock < $request->quantity) {
            return back()->with(['message' => 'Stock insuficiente.', 'alert-type' => 'error']);
        }

        DB::beginTransaction();
        try {
            ServiceItem::create([
                'service_id' => $service->id,
                'itemStock_id' => $itemStock->id,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'amount' => $request->price * $request->quantity,
            ]);

            $itemStock->decrement('stock', $request->quantity);

            $service->total_amount += $request->price * $request->quantity;
            $service->amount_products += $request->price * $request->quantity;
            $service->save();

            DB::commit();

            return redirect()->route('services.show', $service->room_id)->with([
                'message'    => 'Producto agregado exitosamente.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'Ocurrió un error al agregar el producto: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function finishService(Request $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $cashier = $this->cashier(null,'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"');
            if (!$cashier) {
                return redirect()->route('services.index')->with(['message' => 'No tienes una caja abierta.', 'alert-type' => 'warning']);
            }

            $totalPagado = $service->serviceTransactions->sum('amount');
            $deuda = $service->total_amount - $totalPagado;

            if ($deuda > 0) {
                $request->validate([
                    'payment_method' => 'required'
                ]);

                $transaction = Transaction::create([
                    'status' => 'Completado',
                ]);

                if ($request->payment_method == 'efectivo') {
                    $request->validate([
                        'amount_received' => 'required|numeric|min:'.$deuda
                    ]);
                    ServiceTransaction::create([
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'cashier_id' => $cashier->id,
                        'amount' => $deuda,
                        'paymentType' => 'Efectivo',
                    ]);
                } elseif ($request->payment_method == 'qr') {
                    ServiceTransaction::create([
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'cashier_id' => $cashier->id,
                        'amount' => $deuda,
                        'paymentType' => 'Qr',
                    ]);
                } elseif ($request->payment_method == 'ambos') {
                    $request->validate([
                        'amount_efectivo' => 'required|numeric|min:0.01',
                        'amount_qr' => 'required|numeric|min:0.01',
                    ]);

                    if( ($request->amount_efectivo + $request->amount_qr) != $deuda) {
                        return back()->with(['message' => 'La suma de los montos debe ser igual a la deuda.', 'alert-type' => 'error']);
                    }

                    ServiceTransaction::create([
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'cashier_id' => $cashier->id,
                        'amount' => $request->amount_efectivo,
                        'paymentType' => 'Efectivo',
                    ]);

                    ServiceTransaction::create([
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'cashier_id' => $cashier->id,
                        'amount' => $request->amount_qr,
                        'paymentType' => 'Qr',
                    ]);
                }
            }

            $service->status = 'Finalizado';
            $service->save();

            $room = Room::find($service->room_id);
            $room->status = 'Disponible';
            $room->save();

            DB::commit();

            return redirect()->route('services.index')->with([
                'message'    => 'Servicio finalizado exitosamente.',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'Ocurrió un error al finalizar el servicio: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }
}
