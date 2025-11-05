<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\Rental;

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
        return $request;
        $total = 0;
        if ($request->has('products')) {
            foreach ($request->products as $product) {
                $total += $product['price'] * $product['quantity'];
            }
        }
        if ($request->has('amountSala')) {
            $total += $request->amountSala;
        }

        $rules = [
            'room_id' => 'required|exists:rooms,id',
        ];

        if ($request->payment_method == 'efectivo') {
            $rules['amount_received'] = 'required|numeric|min:' . $total;
        }

        if ($request->payment_method == 'ambos') {
            $rules['amount_efectivo'] = 'required|numeric|min:1';
            $rules['amount_qr'] = 'required|numeric|min:1';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
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
