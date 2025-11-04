<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\Room; // Asegúrate de que el modelo y namespace son correctos

class ServicesController extends VoyagerBaseController
{
    /**
     * Muestra la vista de gestión para una sala específica.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $id)
    {
        // Busca la sala por su ID. Si no la encuentra, lanzará un error 404.
        $room = Room::findOrFail($id);

        // Devuelve la vista 'services.show' y le pasa la información de la sala.
        // Esta es la vista que crearemos en el siguiente paso para mostrar los detalles.
        return view('services.show', [
            'room' => $room
        ]);
    }
}