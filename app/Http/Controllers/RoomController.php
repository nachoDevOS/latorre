<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    protected $storageController;
    public function __construct()
    {
        $this->middleware('auth');
        $this->storageController = new StorageController();
    }

    public function index()
    {
        $this->custom_authorize('browse_rooms');

        return view('parameters.rooms.browse');
    }

    public function list(){
        $search = request('search') ?? null;
        $paginate = request('paginate') ?? 10;

        $data = Room::where(function($query) use ($search){
                            $query->WhereRaw($search ? "id = '$search'" : 1)
                            ->OrWhereRaw($search ? "type like '%$search%'" : 1)
                            ->OrWhereRaw($search ? "observation like '%$search%'" : 1)
                            ->OrWhereRaw($search ? "name like '%$search%'" : 1);
                        })
                        ->where('deleted_at', NULL)
                        ->orderBy('id', 'DESC')
                        ->paginate($paginate);

        return view('parameters.rooms.list', compact('data'));
    }

    public function store(Request $request)
    {
        $this->custom_authorize('add_rooms');
        $request->validate([
                'type' => 'required|string|in:Normal,Vip',
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,bmp,webp|max:2048' // 🎉 CAMBIO AQUÍ: Se añade max:3072
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'image.image' => 'El archivo debe ser una imagen.',
                'image.mimes' => 'La imagen debe tener uno de los siguientes formatos: jpeg, jpg, png, bmp, webp.',
                'image.max' => 'La imagen no puede pesar más de 2 megabytes (MB).' // ✍️ CAMBIO AQUÍ: Mensaje personalizado para el tamaño
            ]
        );
        try {
            Room::create([
                'type' => $request->type,
                'name' => $request->name,
                'observation' => $request->observation,
                'image' => $this->storageController->store_image($request->image, 'rooms')
            ]);

            DB::commit();
            return redirect()->route('voyager.rooms.index')->with(['message' => 'Registrado exitosamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('voyager.rooms.index')->with(['message' => $th->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function update(Request $request, $id){
        $this->custom_authorize('edit_rooms');

        $request->validate([            
            'type' => 'required|string|in:Normal,Vip',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,bmp,webp|max:2048' // 🎉 CAMBIO AQUÍ: Se añade max:3072
        ],

        [
            'name.required' => 'El nombre es obligatorio.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe tener uno de los siguientes formatos: jpeg, jpg, png, bmp, webp.',
            'image.max' => 'La imagen no puede pesar más de 2 megabytes (MB).' // ✍️ CAMBIO AQUÍ: Mensaje personalizado para el tamaño
        ]);

        DB::beginTransaction();
        try {
            
            $room = Room::find($id);
            $room->name = $request->name;
            $room->type = $request->type;
            $room->observation = $request->observation;
            $room->status = $request->status=='on' ? 1 : 0;

            if ($request->image) {
                $room->image = $this->storageController->store_image($request->image, 'rooms');
            }          
            $room->update();
            DB::commit();
            return redirect()->route('voyager.rooms.index')->with(['message' => 'Actualizada exitosamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('voyager.rooms.index')->with(['message' => $th->getMessage(), 'alert-type' => 'error']);
        }
    }


    public function show($id)
    {
        $this->custom_authorize('read_rooms');

        $room = Room::where('id', $id)
            ->where('deleted_at', null)
            ->first();

        return view('parameters.rooms.read', compact('room'));
    }

    public function storeDetail(Request $request, $id)
    {
        $this->custom_authorize('add_rooms');
        $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,bmp,webp|max:2048'
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'image.image' => 'El archivo debe ser una imagen.',
                'image.mimes' => 'La imagen debe tener uno de los siguientes formatos: jpeg, jpg, png, bmp, webp.',
                'image.max' => 'La imagen no puede pesar más de 2 megabytes (MB).'
            ]
        );
        DB::beginTransaction();
        try {
            RoomDetail::create([
                'room_id' => $id,
                'name' => $request->name,
                'observation' => $request->observation,
                'image' => $this->storageController->store_image($request->image, 'rooms/details')
            ]);

            DB::commit();
            return redirect()->route('voyager.rooms.show', ['id'=>$id])->with(['message' => 'Registrado exitosamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('voyager.rooms.show', ['id'=>$id])->with(['message' => $th->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function listDetails($id)
    {
        $paginate = request('paginate') ?? 10;
        $data = RoomDetail::where('room_id', $id)
                        ->where('deleted_at', NULL)
                        ->orderBy('id', 'DESC')
                        ->paginate($paginate);
        return view('parameters.rooms.partials.list-details', compact('data'));
    }
}
