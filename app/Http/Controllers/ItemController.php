<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Branch;
use App\Models\IncomeDetail;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    protected $storageController;
    public function __construct()
    {
        $this->middleware('auth');
        $this->storageController = new StorageController();
    }

    public function index()
    {
        $this->custom_authorize('browse_items');
        $categories = Item::with(['itemCategory'])
            ->whereHas('itemCategory', function($q){
                $q->where('deleted_at', null);
            })
            ->where('deleted_at', null)
            ->select('itemCategory_id')
            ->groupBy('itemCategory_id')
            ->get();

        return view('parameters.items.browse', compact('categories'));
    }

    public function list(){
        $search = request('search') ?? null;
        $paginate = request('paginate') ?? 10;
        $category_id = request('category') ?? null;
        $user = Auth::user();

        $data = Item::with(['itemCategory', 'itemStocks'=>function($q)use($user){
                            $q->where('deleted_at', null);
                        }])
                        ->where(function($query) use ($search){
                            $query->OrwhereHas('itemCategory', function($query) use($search){
                                $query->whereRaw($search ? "name like '%$search%'" : 1);
                            })
                            // ->OrwhereHas('presentation', function($query) use($search){
                            //     $query->whereRaw($search ? "name like '%$search%'" : 1);
                            // })
                            ->OrWhereRaw($search ? "id = '$search'" : 1)
                            ->OrWhereRaw($search ? "observation like '%$search%'" : 1)
                            ->OrWhereRaw($search ? "name like '%$search%'" : 1);
                        })
                        ->where('deleted_at', NULL)
                        ->whereRaw($category_id? "itemCategory_id = '$category_id'" : 1)
                        ->orderBy('id', 'DESC')
                        ->paginate($paginate);

        return view('parameters.items.list', compact('data'));
    }

    public function store(Request $request)
    {
        $this->custom_authorize('add_items');
        $request->validate([
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
            Item::create([
                'brand_id' => $request->brand_id,
                'itemCategory_id' => $request->itemCategory_id,
                'name' => $request->name,
                'presentation_id' => $request->presentation_id,
                'observation' => $request->observation,
                'image' => $this->storageController->store_image($request->image, 'items')
            ]);

            DB::commit();
            return redirect()->route('voyager.items.index')->with(['message' => 'Registrado exitosamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('voyager.items.index')->with(['message' => $th->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function update(Request $request, $id){
        $this->custom_authorize('edit_items');
        $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,bmp,webp'
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'image.image' => 'El archivo debe ser una imagen.',
                'image.mimes' => 'La imagen debe tener uno de los siguientes formatos: jpeg, jpg, png, bmp, webp.'
            ]
        );

        DB::beginTransaction();
        try {
            
            $item = Item::find($id);
            // $item->brand_id = $request->brand_id;
            $item->itemCategory_id = $request->itemCategory_id;
            $item->name = $request->name;
            // $item->presentation_id = $request->presentation_id;
            $item->observation = $request->observation;
            $item->status = $request->status=='on' ? 1 : 0;

            if ($request->image) {
                $item->image = $this->storageController->store_image($request->image, 'items');
            }
            $item->update();

            DB::commit();
            return redirect()->route('voyager.items.index')->with(['message' => 'Actualizada exitosamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('voyager.items.index')->with(['message' => $th->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function show($id)
    {
        $this->custom_authorize('read_items');

        $user = Auth::user();
        $ok = 1;
        if($user->role->id != 1 && $user->role->id != 2)
        {
            $ok = "id = $user->branch_id";
        }

        $item = Item::with(['itemCategory'])
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();

        return view('parameters.items.read', compact('item'));
    }

    public function listStock($id)
    {
        $paginate = request('paginate') ?? 10;
        $status = request('status') ?? null;
        $branch = request('branch') ?? null;
        $user = Auth::user();
        $data = ItemStock::where('item_id', $id)
            // ->whereRaw($branch? "branch_id = '$branch'" : 1)
            ->where('deleted_at', null)
            ->orderBy('id', 'DESC')
            ->paginate($paginate);
        return view('parameters.items.listStock', compact('data'));
    }


    public function storeStock(Request $request, $id)
    {
        $this->custom_authorize('add_items');    
        DB::beginTransaction();
        try {
            ItemStock::create([
                // 'branch_id'=>$request->branch_id,
                'item_id' => $id,
                'lote'=>$request->lote,
                'quantity' =>  $request->quantity,
                'stock' => $request->quantity,
                'pricePurchase' => $request->pricePurchase,
                'priceSale' => $request->priceSale,

                'type' => 'Ingreso',
                'observation' => $request->observation,
            ]);
            DB::commit();
            return redirect()->route('voyager.items.show', ['id'=>$id])->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success']);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('voyager.items.show',  ['id'=>$id])->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        } 
    }

    // public function destroyStock($id, $stock)
    // {
    //     $item = ItemStock::where('id', $stock)
    //             ->where('deleted_at', null)
    //             ->first();
    //     if($item->stock != $item->quantity)
    //     {
    //         return redirect()->route('voyager.items.show', ['id'=>$id])->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
    //     }

    //     DB::beginTransaction();
    //     try {            
    //         if($item->incomeDetail_id != null)
    //         {
    //             $incomeDetail = IncomeDetail::where('deleted_at', null)->where('id', $item->incomeDetail_id)->first();
    //             $incomeDetail->increment('stock', $item->quantity);
    //         }
    //         $item->delete();

    //         DB::commit();
    //         return redirect()->route('voyager.items.show', ['id'=>$id])->with(['message' => 'Eliminado exitosamente.', 'alert-type' => 'success']);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return redirect()->route('voyager.items.show', ['id'=>$id])->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
    //     }
    // }

    // public function listSales($id)
    // {
    //     $paginate = request('paginate') ?? 10;
    //     $sales = \App\Models\SaleDetail::with(['sale.person'])
    //         ->whereHas('itemStock', function($q) use ($id){
    //             $q->where('item_id', $id);
    //         })
    //         ->where('deleted_at', null)
    //         ->orderBy('id', 'DESC')
    //         ->paginate($paginate);
    //     return view('parameters.items.partials.list-sales', compact('sales'));
    // }


    public function itemStockList(){
        $search = request('q');
        
        $data = ItemStock::with(['item', 'item.itemCategory'])
            ->Where(function($query) use ($search){
                if($search){
                    $query->whereHas('item', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrwhereHas('item.itemCategory', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrWhereRaw($search ? "id like '%$search%'" : 1);
                }
            })
            ->where('deleted_at', null)
            ->where('stock', '>', 0)
            ->get();
        return response()->json($data);
    }
}
