<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\Rental;
use App\Models\Service;
use App\Models\Transaction;

class ServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rooms = Room::where('deleted_at', NULL)->get();
        return view('services.index', compact('rooms'));
    }

    public function show($id)
    {
        // Busca la sala por su ID. Si no la encuentra, lanzará un error 404.
        $room = Room::findOrFail($id);
        return view('services.register', [
            'room' => $room
        ]);
    }


    public function startRental(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
        ], [
            'start_time.date_format' => 'El campo hora debe tener el formato HH:MM (por ejemplo: 14:30)',
            'start_time.required' => 'La hora es obligatoria',
        ]);

        return $request;

        DB::beginTransaction();

        try {

            $service = Service::create([
                'room_id' => $request->room_id,
                'person_id'=>$request->person_id,
                'start_time' => $request->start_time,
                'amount_room'=> $request->amountSala,
                'amount_products'=> 1,
                'total_amount'=> 1,

                'observation' => 'Inicio de alquiler',
            ]);

            $transaction = Transaction::create([
                    'status' => 'Completado',
            ]);





            $room = Room::findOrFail($request->room_id);


            if ($room->status != 'Disponible') {
                return redirect()->route('voyager.services.show', $room->id)->with([
                    'message'    => 'Error: La sala ya se encuentra ocupada.',
                    'alert-type' => 'error',
                ]);
            }

            // Cambiar el estado de la sala
            $room->status = 'Ocupada';
            $room->save();

            // Crear el nuevo registro de alquiler
            Rental::create([
                'room_id' => $room->id,
                'customer_name' => $request->customer_name,
                'start_time' => now(),
                'status' => 'active'
            ]);

            DB::commit();

            return redirect()->route('services.show', $room->id)->with([
                'message'    => 'Alquiler iniciado exitosamente para la sala: ' . $room->name,
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('services.index')->with([
                'message'    => 'Ocurrió un error al iniciar el alquiler: ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}
