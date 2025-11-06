<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashier;
use App\Models\Service;
use App\Models\Person;
use Carbon\Carbon;
use DateTime;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function generarMesesRetroactivos($mes, $año) {
  
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        
        // Convertir el nombre del mes a minúsculas para comparación
        // $mes = strtolower($mes);
        
        // Obtener el número del mes
        $numero_mes = array_search($mes, $meses);
        
        if ($numero_mes === false) {
            return "Error: El mes '$mes' no es válido.";
        }
        
        $resultado = [];
        
        // Empezamos desde el mes siguiente al indicado del año anterior
        $mes_actual = $numero_mes + 1;
        $año_actual = $año - 1;
        
        // Generar los 12 meses
        for ($i = 0; $i < 12; $i++) {
            // Si el mes actual es mayor a 12, ajustar a enero del año siguiente
            if ($mes_actual > 12) {
                $mes_actual = 1;
                $año_actual++;
            }
            
            // Agregar el mes al resultado con número y año
            $resultado[] = [
                'month_number' => $mes_actual,
                'year' => $año_actual,
                'month' => $meses[$mes_actual], // Opcional: por si necesitas el nombre también
                'amount' => 0 // Inicializar el monto en 0
            ];
            
            // Avanzar al siguiente mes
            $mes_actual++;
        }
        
        return $resultado;
    }

    function generarDiasSemana($fecha, $sales) {
        // Días de la semana en español
        $daysWeek = [
            'Domingo', 'Lunes', 'Martes', 'Miércoles', 
            'Jueves', 'Viernes', 'Sábado'
        ];
        
        // Convertir la fecha a objeto DateTime
        $fecha_obj = new DateTime($fecha);
        
        // Retroceder 6 días para empezar desde 7 días antes de la fecha dada
        $fecha_inicio = clone $fecha_obj;
        $fecha_inicio->modify("-6 days");
        
        $resultado = [];
        
        // Generar los 7 días de la semana empezando desde 6 días antes
        for ($i = 0; $i < 7; $i++) {
            // Agregar el día al resultado
            $numero_dia_semana = (int)$fecha_inicio->format('w'); // 0=domingo, 6=sábado
            $nombre_dia = $daysWeek[$numero_dia_semana];
            
            $resultado[] = [
                'date' => $fecha_inicio->format('Y-m-d'),
                'dateInverso' => $fecha_inicio->format('d-m-Y'),
                'day' => $numero_dia_semana,
                'name' => $nombre_dia,
                'amount' => 0 // Inicializar el monto en 0
            ];
            
            // Avanzar al siguiente día
            $fecha_inicio->modify('+1 day');
        }

        // calculamos el total de las ventas a día
        foreach ($resultado as $index => $dayData) {
            $amount = $sales->where('deleted_at', null)->filter(function ($sale) use ($dayData) {
                return $sale->created_at->format('Y-m-d') === $dayData['date'];
            })->sum('amount');
            
            $resultado[$index]['amount'] = $amount;           
        }
        
        return $resultado;
    }


    public function productTop5Day($date ,$sales)
    {

        $sales = $sales->where('deleted_at', null)->filter(function ($sale) use ($date) {
                    return $sale->created_at->format('Y-m-d') === $date;
                });
        // 1. Recolectar todos los detalles de venta "saleDetails"
        $allDetails = $sales->flatMap->serviceItems;

        // 2. Agrupar por producto y sumar cantidades
        $productSales = [];

        foreach ($allDetails as $detail) {
            $itemId = $detail->itemSale->id;
            $itemName = $detail->itemSale->name; // Ajusta según tu campo
            $quantity = $detail->quantity; // Ajusta según tu campo de cantidad
            
            if (!isset($productSales[$itemId])) {
                $productSales[$itemId] = [
                    'name' => $itemName,
                    'total_quantity' => 0,
                    'item_id' => $itemId
                ];
            }
            
            $productSales[$itemId]['total_quantity'] += $quantity;
        }

        // 3. Ordenar por cantidad descendente
        usort($productSales, function($a, $b) {
            return $b['total_quantity'] - $a['total_quantity'];
        });

        // 4. Obtener los top 5
        $top5Products = array_slice($productSales, 0, 5);
        // Resultado
        // return response()->json($top5Products);
        return $top5Products;;
    }
    public function IndexSystem($typeCashier)
    {
        $month = date('m');
        $year = date('Y');
        $day = date('d');

        $meses = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');       

        $monthInteractive = $this->generarMesesRetroactivos($meses[intval($month)], $year);
 


        $startDate = $monthInteractive[0]['year'] . '-' . str_pad($monthInteractive[0]['month_number'], 2, '0', STR_PAD_LEFT) . '-01';
        $endDate = date('Y-m-t', strtotime($monthInteractive[11]['year'] . '-' . str_pad($monthInteractive[11]['month_number'], 2, '0', STR_PAD_LEFT) . '-01'));


        // Si el tipo es "Todo", no aplicamos filtro. De lo contrario, filtramos por el tipo de venta.
        if ($typeCashier && $typeCashier !== 'Todo') {
            $typeCashier = "sale = '$typeCashier'";
        } else {
            $typeCashier = 1; // Condición que siempre es verdadera para no filtrar
        }

        // Primero obtienes los cashiers filtrados
        // $cashiers = Cashier::where('deleted_at', null)
        //     ->whereRaw($typeCashier)
        //     ->get();

        // // Extraes los IDs de los cashiers
        // $cashierIds = $cashiers->pluck('id');

        $sales = Service::with('serviceTransactions', 'serviceItems.itemStock.item')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->whereIn('cashier_id', $cashierIds) // Filtra por los IDs de los cashiers
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();

        

        $amountDaytotal = $sales->where('deleted_at', null)
                ->filter(function ($sale) {
                    return $sale->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->sum('amount');

        $saleDaytotal = $sales->where('deleted_at', null)
                ->filter(function ($sale) {
                    return $sale->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->count();

        // calculamos el total de las ventas a mes
        foreach ($monthInteractive as $index => $monthData) {
            $amount = $sales->where('deleted_at', null)->filter(function ($sale) use ($monthData) {
                $saleDate = Carbon::parse($sale->created_at);
                return $saleDate->year == $monthData['year'] && 
                    $saleDate->month == $monthData['month_number'];
            })->sum('amount');
            
            $monthInteractive[$index]['amount'] = $amount;           
        }

        // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        // Para obtener el top 5 de productos del día
        $productTop5Day = $this->productTop5Day(date('Y-m-d'), $sales);


        // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        // Para obtener las ventas del día de la semana 
        $weekDays = $this->generarDiasSemana(date('Y-m-d'), $sales);

        $people = Person::where('deleted_at', null)->get()->count();//Para obtener la cantidad de clientes 
    
        return response()->json([
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'monthInteractive' => $monthInteractive,
            'sales'=> $sales,
            'amountDaytotal'=> $amountDaytotal,
            'saleDaytotal'=>$saleDaytotal,
            'customer' => $people,
            'productTop5Day' => $productTop5Day,
            'weekDays' => $weekDays,


            // Para mostrar el monto de las ventas
            // 'amountQrDay' => $amountQrDay, // total ventas del día por tipo de pago Qr
            // 'amountEfectivoDay' => $amountEfectivoDay // total ventas del día por tipo de pago Efectivo
        ]);
    }
}
