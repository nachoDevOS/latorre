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
            $q->where('status', 'vigente')->with(['serviceTimes' => function ($query) {
                $query->orderBy('id', 'asc');
            }]);
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
        $request->validate([
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'nullable|date|required_with:end_time',
            'end_time' => 'nullable|date_format:H:i',
        ], [
            'start_date.required' => 'La fecha es obligatoria.',
            'start_time.date_format' => 'El campo hora debe tener el formato HH:MM (por ejemplo: 14:30)',
            'start_time.required' => 'La hora es obligatoria.',
            'end_date.required_with' => 'La fecha de fin es obligatoria si se especifica una hora de fin.',
            'end_time.date_format' => 'El campo hora de fin debe tener el formato HH:MM.',
        ]);

        $startDateTime = \Carbon\Carbon::parse($request->start_date . ' ' . $request->start_time);

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

        if ($request->end_time) {
            $endDateTime = \Carbon\Carbon::parse($request->end_date . ' ' . $request->end_time);
            if ($endDateTime->lessThan($startDateTime)) {
                return redirect()->back()->withInput()->withErrors(['message' => 'La fecha y hora de fin no puede ser anterior a la fecha y hora de inicio.']);
            }
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
                'start_time' => $startDateTime->toDateTimeString(),
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

            $endDateTimeString = $request->end_time ? \Carbon\Carbon::parse($request->end_date . ' ' . $request->end_time)->toDateTimeString() : null;

            ServiceTime::create([
                'service_id' => $service->id,
                'time_type' => $request->end_time? 'Tiempo fijo': 'Tiempo sin límite',
                'start_time' => $startDateTime->toDateTimeString(),
                'end_time' => $endDateTimeString,
                'total_time' => $request->end_time? null : null,
                'amount' => $request->end_time?$request->amountSala: 0,
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
                        'type' => 'Ingreso',
                        'cashier_id' => $cashier->id,
                        'amount' => $amount_efectivo,
                        'paymentType' => 'Efectivo',
                    ]);
            }
            return $amount_Qr;

            if ($request->payment_method == 'qr' || $request->payment_method == 'ambos' && ($amount_efectivo + $amount_Qr) > 0) {
                    ServiceTransaction::create([
                        'service_id' => $service->id,
                        'transaction_id' => $transaction->id,
                        'type' => 'Ingreso',
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

        // Lógica para finalizar un tiempo abierto
        $lastTime = $service->serviceTimes->last();
        // Este bloque se ejecuta solo si hay un tiempo abierto que se está cerrando desde el formulario de cobro.
        // El modal para finalizar tiempo usa el método updateTime, no este.
        // if ($lastTime && !$lastTime->end_time) {
        //     $request->validate([
        //         'final_end_time' => 'required|date_format:H:i|after_or_equal:'.$lastTime->start_time,
        //         'final_amount' => 'required|numeric|min:0',
        //     ], [
        //         'final_end_time.required' => 'La hora de finalización es obligatoria.',
        //         'final_end_time.after_or_equal' => 'La hora de fin no puede ser anterior a la de inicio.',
        //         'final_amount.required' => 'El monto por el último período es obligatorio.',
        //         'final_amount.min' => 'El monto no puede ser negativo.',
        //     ]);

        //     // Actualizar el último registro de tiempo
        //     $lastTime->end_time = $request->final_end_time;
        //     $lastTime->amount = $request->final_amount;
        //     $lastTime->time_type = 'Tiempo fijo'; // Se cierra el tiempo
        //     $lastTime->save();

        //     // Actualizar los montos totales del servicio
        //     $service->amount_room = $service->serviceTimes()->sum('amount');
        //     $service->total_amount = $service->amount_room + $service->amount_products;
        //     $service->save();
        // }
        
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

    public function addTime(Request $request, Service $service)
    {
        
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'amountSala' => 'nullable|numeric|min:0',
        ], [
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'start_time.date_format' => 'El formato de la hora de inicio no es válido.',
            'end_time.date_format' => 'El formato de la hora de fin no es válido.',
            'end_time.after_or_equal' => 'La hora de fin no puede ser anterior a la hora de inicio.',
            'amountSala.required' => 'El monto es obligatorio.',
            'amountSala.numeric' => 'El monto debe ser un número.',
            'amountSala.min' => 'El monto no puede ser negativo.',
        ]);

        DB::beginTransaction();
        try {
            // Crear el nuevo registro de tiempo
            ServiceTime::create([
                'service_id' => $service->id,
                'time_type' => $request->end_time ? 'Tiempo fijo' : 'Tiempo sin límite',
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'amount' => $request->amountSala ?? 0,
            ]);

            // Actualizar los montos del servicio
            $amountToAdd = $request->amountSala ?? 0;
            $service->amount_room += $amountToAdd;
            $service->total_amount += $amountToAdd;
            $service->save();

            DB::commit();

            return redirect()->route('services.show', $service->room_id)->with([
                'message'    => 'Se ha agregado tiempo adicional al servicio exitosamente.',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('services.show', $service->room_id)->with([
                'message'    => 'Ocurrió un error al agregar tiempo adicional: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function addPayment(Request $request, Service $service)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string'
        ]);

        $cashier = $this->cashier(null,'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"');
        if (!$cashier) {
            return back()->with(['message' => 'No tienes una caja abierta.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create(['status' => 'Completado']);

            if ($request->payment_method == 'efectivo') {
                $request->validate(['amount_received' => 'required|numeric|min:'.$request->amount]);
                ServiceTransaction::create([
                    'service_id' => $service->id,
                    'transaction_id' => $transaction->id,
                    'cashier_id' => $cashier->id,
                    'amount' => $request->amount,
                    'paymentType' => 'Efectivo',
                    'observation' => $request->observation ?? '',
                ]);
            } elseif ($request->payment_method == 'qr') {
                ServiceTransaction::create([
                    'service_id' => $service->id,
                    'transaction_id' => $transaction->id,
                    'cashier_id' => $cashier->id,
                    'amount' => $request->amount,
                    'paymentType' => 'Qr',
                    'observation' => $request->observation ?? '',
                ]);
            } elseif ($request->payment_method == 'ambos') {
                $request->validate([
                    'amount_efectivo' => 'required|numeric|min:0.01',
                    'amount_qr' => 'required|numeric|min:0.01',
                ]);

                if( ($request->amount_efectivo + $request->amount_qr) != $request->amount) {
                    return back()->with(['message' => 'La suma de los montos debe ser igual al adelanto total.', 'alert-type' => 'error'])->withInput();
                }

                ServiceTransaction::create(['service_id' => $service->id, 'transaction_id' => $transaction->id, 'cashier_id' => $cashier->id, 'amount' => $request->amount_efectivo, 'paymentType' => 'Efectivo', 'observation' => $request->observation ?? '']);
                ServiceTransaction::create(['service_id' => $service->id, 'transaction_id' => $transaction->id, 'cashier_id' => $cashier->id, 'amount' => $request->amount_qr, 'paymentType' => 'Qr', 'observation' => $request->observation ?? '']);
            }

            DB::commit();

            return redirect()->route('services.show', $service->room_id)->with([
                'message'    => 'Adelanto registrado exitosamente.',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'Ocurrió un error al registrar el adelanto: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }



    public function updateTime(Request $request, ServiceTime $serviceTime)
    {
        if ($request->end_time < $serviceTime->start_time) {
            // No se hace nada, se asume que es del día siguiente
        } else {
            // Si no es del día siguiente, se valida que no sea anterior
            if (strtotime($request->end_time) < strtotime($serviceTime->start_time)) {
                return back()->withErrors(['end_time' => 'La hora de fin no puede ser anterior a la de inicio.'])->withInput();
            }
        }

        $request->validate([
            'end_time' => 'required|date_format:H:i',
            'amount' => 'required|numeric|min:0',
        ], [
            'end_time.required' => 'La hora de finalización es obligatoria.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.min' => 'El monto no puede ser negativo.',
        ]);

        DB::beginTransaction();
        try {
            // Obtener la instancia del servicio ANTES de modificar serviceTime
            $service = $serviceTime->service;

            // Actualizar el registro de tiempo
            $serviceTime->end_time = $request->end_time;
            $serviceTime->amount = $request->amount;
            $serviceTime->time_type = 'Tiempo fijo'; // Se convierte en tiempo fijo
            $serviceTime->save();

            // Recalcular y actualizar los montos totales del servicio con la instancia guardada
            $service->amount_room = $service->serviceTimes()->sum('amount');
            $service->total_amount = $service->amount_room + $service->amount_products;
            $service->save();

            DB::commit();

            return redirect()->route('services.show', $service->room_id)->with([
                'message'    => 'El período de tiempo ha sido finalizado exitosamente.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'Ocurrió un error al actualizar el tiempo: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }
}
