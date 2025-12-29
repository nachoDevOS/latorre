<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Cashier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $cashier = $this->cashierMoney(null, 'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"')->original;

        if (!$cashier) {
            return redirect()
                ->back()
                ->with(['message' => 'Usted no cuenta con caja abierta.', 'alert-type' => 'warning']);
        }

        if($cashier['amountCashier'] < $request->amount)
        {
            return redirect()
                ->back()
                ->with(['message' => 'No cuenta con monto en efectivo disponible para realizar un gasto.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();
        try {
            $sale = Expense::create([
                'observation' => $request->observation,
                'amount' => $request->amount,
                'cashier_id' => $cashier['cashier']->id,
            ]);

            DB::commit();
            return redirect()
                ->back()
                ->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success', 'sale' => $sale]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->logError($th, $request);
            return redirect()
                ->back()
                ->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    public function destroy($id)
    {
        $expense = Expense::where('deleted_at', null)
            ->where('id', $id)
            ->first();

        $cashier = $this->cashier(null,'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"');
        if (!$cashier) {
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Usted no cuenta con caja abierta.', 'alert-type' => 'warning']);
        }
        if($cashier->id != $expense->cashier_id){
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'No puede modificar ventas de otra caja.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();
        try {
            $expense->delete();
            DB::commit();
            return redirect()->back()->with(['message' => 'Eliminado exitosamente.', 'alert-type' => 'success'])->withInput();
            
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error'])->withInput();

        }
    }
}
