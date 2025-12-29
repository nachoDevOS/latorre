<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\ServiceTransaction;
use App\Models\Transaction;
use App\Models\ItemStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class ServiceSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('services.browse');
    }

    public function create()
    {
        return view('services.edit-add');
    }

    public function store(Request $request)
    {

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:item_stocks,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'amount_product' => 'required|numeric|min:0.01', // Cambiado de total_amount a amount_product
            'payment_method' => 'required|string|in:efectivo,qr,ambos',
        ]);

        $cashier = $this->cashier(null, 'user_id = "' . Auth::user()->id . '"', 'status = "Abierta"');
        if (!$cashier) {
            return back()->with(['message' => 'No tienes una caja abierta.', 'alert-type' => 'warning'])->withInput();
        }

        $total_a_pagar = $request->amount_product; // Usar el valor correcto del formulario

        if ($request->payment_method == 'efectivo') {
            $request->validate(['amount_received' => 'required|numeric|min:'.$total_a_pagar]);
        } elseif ($request->payment_method == 'ambos') {
            $request->validate([
                'amount_efectivo' => 'required|numeric|min:0.01',
                'amount_qr' => 'required|numeric|min:0.01',
            ]);
            if (bccomp($request->amount_efectivo + $request->amount_qr, $total_a_pagar, 2) < 0) {
                return redirect()->back()->withInput()->withErrors(['message' => 'La suma del monto en efectivo y el monto por QR no puede ser menor al monto total.']);
            }
        }

        DB::beginTransaction();
        try {
            $service = Service::create([
                'person_id' => $request->person_id, // Usar el cliente del selector, o 1 por defecto
                // 'start_time' => Carbon::now(),
                'amount_room' => 0,
                'amount_products' => $total_a_pagar,
                'total_amount' => $total_a_pagar,
                'observation' => 'Venta solo de productos. ' . ($request->observation ?? ''),
                'status' => 'Finalizado', // Se finaliza al momento
            ]);

            $transaction = Transaction::create(['status' => 'Completado']);

            foreach ($request->products as $product) {
                $itemStock = ItemStock::findOrFail($product['id']);
                if ($itemStock->stock < $product['quantity']) {
                    DB::rollBack();
                    return back()->with(['message' => 'Stock insuficiente para ' . $itemStock->item->name, 'alert-type' => 'error']);
                }

                ServiceItem::create([
                    'service_id' => $service->id, 'transaction_id' => $transaction->id, 'itemStock_id' => $itemStock->id,
                    'pricePurchase' => $itemStock->pricePurchase, 'price' => $product['price'], 'quantity' => $product['quantity'],
                    'amount' => $product['price'] * $product['quantity'],
                ]);
                $itemStock->decrement('stock', $product['quantity']);
            }

            if ($request->payment_method == 'efectivo' || $request->payment_method == 'qr') {
                ServiceTransaction::create(['service_id' => $service->id, 'transaction_id' => $transaction->id, 'cashier_id' => 1, 'amount' => $total_a_pagar, 'paymentType' => ucfirst($request->payment_method), 'type' => 'Ingreso']);
            } elseif ($request->payment_method == 'ambos') {
                if ($request->amount_efectivo > 0) {
                    ServiceTransaction::create(['service_id' => $service->id, 'transaction_id' => $transaction->id, 'cashier_id' => 1, 'amount' => $request->amount_efectivo, 'paymentType' => 'Efectivo', 'type' => 'Ingreso']);
                }
                if ($request->amount_qr > 0) {
                    ServiceTransaction::create(['service_id' => $service->id, 'transaction_id' => $transaction->id, 'cashier_id' => 1, 'amount' => $request->amount_qr, 'paymentType' => 'Qr', 'type' => 'Ingreso']);
                }
            }

            DB::commit();
            return redirect()->route('services-sales.index')->with(['message' => 'Venta de productos registrada exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'Ocurrió un error al registrar la venta.', 'alert-type' => 'error'])->withInput();
        }
    }

    public function list(Request $request)
    {
        $search = $request->search ?? null;
        $paginate = $request->paginate ?? 10;

        $data = Service::with(['person', 'room'])
            ->where(function ($query) use ($search) {
                if ($search) {
                    $query->where('id', 'like', "%$search%")
                        ->orWhere('observation', 'like', "%$search%")
                        ->orWhereHas('person', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%$search%")
                              ->orWhere('paternal_surname', 'like', "%$search%");
                        })
                        ->orWhereHas('room', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });
                }
            })
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->paginate($paginate);
        return view('services.list', compact('data'));
    }

    public function show($id)
    {
        $service = Service::with([
            'person',
            'room',
            'serviceItems.itemStock.item', // Cargar ítems vendidos y sus detalles
            'serviceTimes',               // Cargar tiempos de alquiler
            'serviceTransactions'         // Cargar transacciones de pago
        ])->findOrFail($id);

        // Puedes añadir lógica de permisos aquí si es necesario
        // if (!Auth::user()->hasPermission('read_services-sales')) {
        //     return redirect()->back()->with(['message' => 'No tienes permiso para ver este servicio.', 'alert-type' => 'error']);
        // }
        return view('services.show', compact('service'));
    }
}
