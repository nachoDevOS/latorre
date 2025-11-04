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

    /**
     * Muestra la vista de gestión para una sala específica.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        // Busca la sala por su ID. Si no la encuentra, lanzará un error 404.
        $room = Room::findOrFail($id);

        // Si la sala está ocupada, busca el alquiler activo.
        $activeRental = null;
        if ($room->status == 'Ocupada') {
            $activeRental = Rental::where('room_id', $id)
                                  ->where('status', 'active')
                                  ->latest('start_time')
                                  ->first();
        }

        // Devuelve la vista 'services.show' y le pasa la información de la sala.
        return view('services.register', [
            'room' => $room,
            'activeRental' => $activeRental
        ]);
    }

    /**
     * Inicia un nuevo alquiler para una sala.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startRental(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

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
