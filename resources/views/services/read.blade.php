@extends('voyager::master')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .details-panel .panel-title,
        .products-panel .panel-title {
            font-weight: 500;
        }

        .panel {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .detail-card {
            background-color: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .detail-card strong {
            color: #333;
        }

        .detail-card .icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #6a11cb;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 500;
            color: #4A4A4A;
            border-bottom: 2px solid #dee2e6;
        }

        .summary-panel {
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            font-size: 1.1rem;
            padding: 0.5rem 0;
        }

        .summary-item span {
            color: #6c757d;
        }

        .summary-item strong {
            color: #333;
        }

        .summary-total {
            border-top: 2px solid #dee2e6;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .summary-total .summary-item strong {
            font-size: 1.4rem;
            color: #2575fc;
        }

        .btn-finish {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 500;
            padding: 12px 25px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-finish:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .btn-finish:disabled {
            background: #a5d6a7;
            cursor: not-allowed;
        }

        .btn-end-time {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border: none;
            color: white !important;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-end-time:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            font-weight: bold;
            color: #28a745;
        }

        .live-indicator .dot {
            width: 8px;
            height: 8px;
            background-color: #28a745;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse-animation 1.5s infinite;
        }

        @keyframes pulse-animation {
            0%, 100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            50% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(40, 167, 69, 0); }
        }


    </style>
@endsection

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-8">
                {{-- Panel de Detalles del Servicio --}}
                <div class="panel details-panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-info-circled"></i> Detalles del Servicio</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <i class="voyager-milestone icon"></i>
                                    <span>Sala: <strong>{{ $room->name }} ({{ $room->type }})</strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <i class="voyager-person icon"></i>
                                    <span>Cliente:
                                        <strong>{{ $service->person ? $service->person->name : 'No especificado' }}</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-info">
                            @php
                                $lastTime = $service->serviceTimes->last();
                                // Define si se puede agregar tiempo (si el último período está cerrado)
                                $canAddTime = $lastTime && $lastTime->end_time;
                                // Define si hay un tiempo en curso que impide finalizar (si el último período está abierto)
                                // Esta variable se usará más abajo en el botón de "Finalizar y Cobrar"
                                $canFinishService = $lastTime && !$lastTime->end_time;
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <h3 class="panel-title"><i class="voyager-watch"></i> Historial de Tiempo</h3>
                                @if ($canAddTime)
                                <!-- Botón para abrir el modal -->
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addTimeModal">
                                        <i class="voyager-plus"></i> Agregar Tiempo
                                    </button>
                                @endif
                            </div>
                            
                            
                            <div class="panel-body" style="padding: 0px;">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="dataTable" style="margin-bottom: 0px;">
                                        <thead>
                                            <tr>
                                                <th>Inicio</th>
                                                <th>Fin</th>
                                                <th class="text-right">Duración</th>
                                                <th class="text-right">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($service->serviceTimes as $time)
                                                <tr>
                                                    <td>{{ date('d-m-Y h:i A', strtotime($time->start_time)) }}</td>
                                                    <td>
                                                        @if ($time->end_time)
                                                            {{ date('d-m-Y h:i A', strtotime($time->end_time)) }}
                                                        @else
                                                            <span class="live-indicator">
                                                                <span class="dot"></span>
                                                                En curso
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @if ($time->end_time)
                                                            @php
                                                                $start = \Carbon\Carbon::parse($time->start_time);
                                                                $end = \Carbon\Carbon::parse($time->end_time);
                                                                $duration = $end->diffInMinutes($start);
                                                            @endphp
                                                            {{ $duration }} minutos
                                                        @endif

                                                        @if (!$time->end_time)
                                                            <button type="button" class="btn btn-end-time" data-toggle="modal" data-target="#updateTimeModal-{{ $time->id }}">
                                                                <i class="voyager-edit"></i> Finalizar
                                                            </button>
                                                        @endif
                                                    </td>
                                       
                                                    <td class="text-right">{{ number_format($time->amount, 2, ',', '.') }}
                                                        Bs.
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">
                                                        <p class="text-center"
                                                            style="margin-top: 10px; margin-bottom: 10px;">No se han
                                                            registrado tiempos.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-right">Tiempo total:</th>
                                                <th class="text-right">
                                                    @php
                                                        $totalMinutes = 0;
                                                        foreach ($service->serviceTimes as $time) {
                                                            if ($time->end_time) {
                                                                $start = \Carbon\Carbon::parse($time->start_time);
                                                                $end = \Carbon\Carbon::parse($time->end_time);
                                                                $totalMinutes += $end->diffInMinutes($start);
                                                            }
                                                        }
                                                        $hours = floor($totalMinutes / 60);
                                                        $minutes = $totalMinutes % 60;
                                                    @endphp
                                                    {{ $hours }}h {{ $minutes }}m
                                                </th>
                                                {{-- <th></th> --}}
                                                <th class="text-right">
                                                    @php
                                                        $totalAmount = $service->serviceTimes->sum('amount');
                                                    @endphp
                                                    {{ number_format($totalAmount, 2, ',', '.') }} Bs.
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        

                        @if (!$canAddTime)
                            <div class="alert alert-info">
                                <i class="voyager-watch"></i> El servicio actual se encuentra en curso sin límite de tiempo. Para agregar un nuevo período, primero debe finalizar el servicio actual.
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Panel de Productos Consumidos --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel products-panel">
                            <div>
                                <h3 class="panel-title"><i class="voyager-basket"></i> Productos Consumidos</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th class="text-right">Precio</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalProductos = 0; @endphp
                                            @forelse ($service->serviceItems as $index => $item)
                                                <tr>
                                                    <td>{{ $item->itemStock->item->name }}</td>
                                                    <td class="text-right">{{ number_format($item->price, 2, ',', '.') }}.
                                                    </td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}
                                                        Bs.</td>
                                                </tr>
                                                @php $totalProductos += $item->amount; @endphp
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center" style="padding: 40px;">
                                                        <i class="voyager-bar-chart"
                                                            style="font-size: 3rem; opacity: 0.5;"></i>
                                                        <h4 style="margin-top: 10px;">No se han registrado productos.</h4>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        {{-- Panel para agregar productos --}}
                        <div class="panel panel-info">
                            <div>
                                <h3 class="panel-title"><i class="voyager-plus"></i> Agregar Productos al Servicio</h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('services.add_item', ['service' => $service->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label>Buscar producto</label>
                                            <select class="form-control" id="select-product_id"></select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Precio</label>
                                            <div class="input-group">
                                                <input type="number" name="price" id="input-price" class="form-control"
                                                    step="0.01" min="0.01" required />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Cantidad</label>
                                            <input type="number" name="quantity" id="input-quantity" class="form-control"
                                                step="1" min="1" required />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Subtotal</label>
                                            <div class="input-group">
                                                <input type="number" id="input-subtotal" class="form-control" readonly />
                                            </div>
                                        </div>
                                        <input type="hidden" name="item_stock_id" id="input-item_stock_id">
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary">Agregar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Historial de Pagos --}}
                <div class="panel panel-primary">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="panel-title" style="margin: 0;"><i class="voyager-dollar"></i> Historial de Pagos</h3>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addPaymentModal">
                                <i class="voyager-plus"></i> Agregar Adelanto
                            </button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dataTable">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; width: 25%;">Fecha y Hora</th>
                                        <th style="text-align: center; width: 15%;">Método de pago</th>
                                        <th>Detalle de Pago</th>                                    
                                        <th style="width: 15%" class="text-right">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($service->serviceTransactions as $transaction)
                                        <tr>
                                            <td style="text-align: center;">{{ $transaction->created_at->format('d/m/Y h:i a') }}</td>
                                            <td style="text-align: center;">
                                                @php
                                                    $paymentMethod = $transaction->paymentType;
                                                    $decodedMethod = json_decode($paymentMethod, true);
                                                @endphp

                                                @if (is_array($decodedMethod))
                                                    @if (isset($decodedMethod['efectivo']) && isset($decodedMethod['qr']))
                                                        Efectivo y QR
                                                    @endif
                                                @else
                                                    @switch($paymentMethod)
                                                        @case('efectivo')
                                                            Efectivo
                                                        @break

                                                        @case('qr')
                                                            QR
                                                        @break

                                                        @default
                                                            {{ ucfirst($paymentMethod) }}
                                                    @endswitch
                                                @endif
                                            </td>
                                            <td>
                                                @if ($transaction->observation)
                                                    {{ $transaction->observation }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($transaction->amount, 2, ',', '.') }}
                                                Bs.</td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center" style="padding: 20px;">
                                                    <i class="voyager-info-circled"
                                                        style="font-size: 2rem; opacity: 0.5;"></i>
                                                    <h5 style="margin-top: 10px;">No se han registrado pagos.</h5>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>



                </div>

                {{-- Panel de Resumen de Pago --}}
                <div class="col-md-4">
                    @php
                        $totalPagado = $service->serviceTransactions->sum('amount');
                        $deuda = round($service->total_amount - $totalPagado, 2);
                    @endphp
                    <div class="panel summary-panel">
                        <form id="form-finish" action="{{ route('services.finish', ['service' => $service->id]) }}" method="POST" onsubmit="btnSubmit.disabled = true; return true;">
                            @csrf
                            <div>
                                <h4 style="text-align: center; margin-top: 0; font-weight: 500; color: #4A4A4A;">Resumen de Pago
                                </h4>
                                <hr>
                                <div class="summary-item">
                                    <span>Monto de Productos:</span>
                                    <strong id="summary-products-amount">{{ number_format($totalProductos, 2, ',', '.') }} Bs.</strong>
                                </div>
                                <div class="summary-item">
                                    <span>Monto Sala:</span>
                                    <strong id="summary-room-amount">{{ number_format($service->amount_room, 2, ',', '.') }} Bs.</strong>
                                </div>
    
                                <div class="summary-total">
                                    <div class="summary-item">
                                        <span>Monto Total:</span>
                                        <strong id="summary-total-amount">{{ number_format($service->total_amount, 2, ',', '.') }} Bs.</strong>
                                    </div>
                                </div>
                                <div class="summary-total">
                                    <div class="summary-item">
                                        <span>Total Pagado:</span>
                                        <strong style="color: green;">{{ number_format($totalPagado, 2, ',', '.') }} Bs.</strong>
                                    </div>
                                </div>
                                <div class="summary-total">
                                    <div class="summary-item">
                                        <span>Deuda a Pagar:</span>
                                        <strong id="deuda-pagar" style="color: red;">{{ number_format($deuda, 2, ',', '.') }}
                                            Bs.</strong>
                                    </div>
                                </div>
    

                                @if ($deuda > 0)
                                    <div id="payment-section" style="margin-top: 15px;">
                                        <hr>
                                        <div class="form-group">
                                            <label for="payment_method">Método de Pago</label>
                                            <select name="payment_method" id="payment_method" class="form-control" required>
                                                <option value="" selected disabled>--Seleccione una opción--</option>
                                                <option value="efectivo">Efectivo</option>
                                                <option value="qr">QR</option>
                                                <option value="ambos">Ambos</option>
                                            </select>
                                        </div>
                                        <div id="payment-details" style="display: none;">
                                            <div class="form-group">
                                                <label for="amount_efectivo">Monto en Efectivo</label>
                                                <input type="number" name="amount_efectivo" id="amount_efectivo"
                                                    class="form-control" step="0.01" min="0" placeholder="0.00">
                                            </div>
                                            <div class="form-group">
                                                <label for="amount_qr">Monto con QR</label>
                                                <input type="number" name="amount_qr" id="amount_qr" class="form-control"
                                                    step="0.01" min="0" placeholder="0.00">
                                            </div>
                                        </div>
    
                                        <div id="calculator"
                                            style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                                            <div class="form-group">
                                                <label for="amount_received" style="font-weight: bold;">Monto Recibido
                                                    (Efectivo)</label>
                                                <input type="number" name="amount_received" id="amount_received"
                                                    class="form-control" step="0.01" min="0" placeholder="0.00">
                                            </div>
                                            <div class="summary-item"
                                                style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;">
                                                <strong style="font-size: 1.1rem;">Cambio a devolver:</strong>
                                                <strong class="amount" id="change_due"
                                                    style="font-size: 1.2rem; color: #28a745;">0.00 Bs.</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endif
    
                                <div style="margin-top: 20px; text-align: center;">
                                    @if ($canFinishService)
                                        <button type="button" class="btn btn-finish" disabled title="Debe registrar una hora de finalización para el tiempo en curso.">
                                            <i class="voyager-dollar"></i> Finalizar y Cobrar
                                        </button>
                                        <p class="text-warning" style="margin-top: 10px;">Finalice el tiempo en curso para poder cobrar.</p>
                                    @else
                                        <button type="submit" name="btnSubmit" class="btn btn-finish"><i class="voyager-dollar"></i> Finalizar y Cobrar</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal para actualizar tiempo --}}
            @foreach ($service->serviceTimes as $time)
                @if (!$time->end_time)
                <form action="{{ route('services.update_time', ['serviceTime' => $time->id]) }}" class="form-edit-add" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal fade" id="updateTimeModal-{{ $time->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">                            
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Finalizar Período de Tiempo</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Inicio: <strong>{{ date('d-m-Y h:i A', strtotime($time->start_time)) }}</strong></p>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="end_time_{{ $time->id }}">Fecha y Hora Fin</label>
                                            <div class="input-group">
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $startTime = \Carbon\Carbon::parse($time->start_time);
                                                    // Si la hora actual es menor que la de inicio, es probable que sea del día siguiente
                                                    $defaultDate = $now->lt($startTime) ? $startTime->copy()->addDay()->format('Y-m-d') : $now->format('Y-m-d');
                                                @endphp
                                                <input type="date" name="end_date" id="end_date_{{ $time->id }}" class="form-control end-date-input" value="{{ $defaultDate }}" required>
                                                <span class="input-group-addon" style="border-radius: 0px; border-left: 0px; border-right: 0px;"><i class="voyager-watch"></i></span>
                                                <input type="time" name="end_time" id="end_time_{{ $time->id }}" class="form-control end-time-input" value="{{ $now->format('H:i') }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12" style="margin-top: -10px;">
                                            <p>Duración del período: <strong id="duration_{{ $time->id }}">Calculando...</strong></p>
                                            <input type="hidden" class="start-time-value" value="{{ $time->start_time }}">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="amount_{{ $time->id }}">Monto a cobrar por este período</label>
                                            <input type="number" name="amount" id="amount_{{ $time->id }}" class="form-control amount-input" step="0.01" min="0.01" placeholder="0.00" required>
                                        </div>

                                        <div class="col-md-12 payment-method-group-update" style="display: block;">
                                            <hr>
                                            <div class="form-group">
                                                <label for="payment_method_update_{{ $time->id }}">Método de Pago</label>
                                                <select name="payment_method" id="payment_method_update_{{ $time->id }}" class="form-control payment-method-update" required>
                                                    <option value="" selected disabled>--Seleccione una opción--</option>
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="qr">QR</option>
                                                    <option value="ambos">Ambos</option>
                                                </select>
                                            </div>
                                            <div class="payment-details-update" style="display: none;">
                                                <div class="form-group"><label for="amount_efectivo_update_{{ $time->id }}">Monto en Efectivo</label><input type="number" name="amount_efectivo" id="amount_efectivo_update_{{ $time->id }}" class="form-control amount-efectivo-update" step="0.01" min="0" placeholder="0.00"></div>
                                                <div class="form-group"><label for="amount_qr_update_{{ $time->id }}">Monto con QR</label><input type="number" name="amount_qr" id="amount_qr_update_{{ $time->id }}" class="form-control amount-qr-update" step="0.01" min="0" placeholder="0.00"></div>
                                            </div>
                                            <div class="calculator-update" style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                                                <div class="form-group"><label for="amount_received_update_{{ $time->id }}" style="font-weight: bold;">Monto Recibido (Efectivo)</label><input type="number" name="amount_received" id="amount_received_update_{{ $time->id }}" class="form-control amount-received-update" step="0.01" min="0" placeholder="0.00"></div>
                                                <div class="summary-item" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;"><strong style="font-size: 1.1rem;">Cambio a devolver:</strong><strong class="amount change-due-update" style="font-size: 1.2rem; color: #28a745;">0.00 Bs.</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary btn-submit">Finalizar y Cobrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @endif
            @endforeach

            {{-- Modal para agregar adelanto --}}
            <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('services.add_payment', ['service' => $service->id]) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Agregar Adelanto</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="amount_adelanto">Monto del Adelanto</label>
                                    <input type="number" name="amount" id="amount_adelanto" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                                <div class="form-group">
                                    <label for="note_adelanto">Nota (Requerido)</label>
                                    <textarea name="observation" id="note_adelanto" class="form-control" rows="3" placeholder="Ingrese una nota..." required></textarea>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="payment_method_adelanto">Método de Pago</label>
                                    <select name="payment_method" id="payment_method_adelanto" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione una opción--</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="qr">QR</option>
                                        <option value="ambos">Ambos</option>
                                    </select>
                                </div>
                                <div id="payment-details-adelanto" style="display: none;">
                                    <div class="form-group">
                                        <label for="amount_efectivo_adelanto">Monto en Efectivo</label>
                                        <input type="number" name="amount_efectivo" id="amount_efectivo_adelanto" class="form-control" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    <div class="form-group">
                                        <label for="amount_qr_adelanto">Monto con QR</label>
                                        <input type="number" name="amount_qr" id="amount_qr_adelanto" class="form-control" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>
                                <div id="calculator-adelanto" style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                                    <div class="form-group">
                                        <label for="amount_received_adelanto" style="font-weight: bold;">Monto Recibido (Efectivo)</label>
                                        <input type="number" name="amount_received" id="amount_received_adelanto" class="form-control" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    <div class="summary-item" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;">
                                        <strong style="font-size: 1.1rem;">Cambio a devolver:</strong>
                                        <strong class="amount" id="change_due_adelanto" style="font-size: 1.2rem; color: #28a745;">0.00 Bs.</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Adelanto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal para Agregar Tiempo Adicional --}}
            @if ($canAddTime)
                <form action="{{ route('services.add_time', ['service' => $service->id]) }}" class="form-edit-add" method="POST">
                @csrf
                <div class="modal fade" id="addTimeModal" tabindex="-1" role="dialog" aria-labelledby="addTimeModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">                        
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="addTimeModalLabel"><i class="voyager-plus"></i> Agregar Tiempo Adicional</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="start_time">Fecha y Hora de Inicio</label>
                                        <div class="input-group">
                                            <input type="date" name="start_date" id="start_date_additional" class="form-control" value="{{ date('Y-m-d', strtotime($lastTime->end_time)) }}" required readonly>
                                            <span class="input-group-addon" style="border-radius: 0px; border-left: 0px; border-right: 0px;"><i class="voyager-watch"></i></span>
                                            <input type="time" name="start_time" id="start_time_additional" class="form-control" value="{{ date('H:i', strtotime($lastTime->end_time)) }}" required readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="end_time">Fecha y Hora Fin (opcional)</label>
                                        <div class="input-group">
                                            <input type="date" name="end_date" id="end_date_additional" class="form-control">
                                            <span class="input-group-addon" style="border-radius: 0px; border-left: 0px; border-right: 0px;"><i class="voyager-watch"></i></span>
                                            <input type="time" name="end_time" id="end_time_additional" class="form-control">
                                            <span class="input-group-btn">
                                                <button id="clear-end-time-additional" class="btn btn-default" style="margin: 0px" type="button" title="Limpiar Hora">
                                                    <i class="voyager-trash"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <small class="form-text text-muted">Dejar vacío para alquiler sin límite.</small>
                                    </div>
                                    <div class="form-group col-md-12" id="amount-group-additional" style="display: none;">
                                        <label for="amount" id="amount-label-additional">Monto adicional</label>
                                        <input type="number" name="amountSala" id="amount-additional" class="form-control" min="0" step="0.01" placeholder="0.00">
                                    </div>
                                    <div class="col-md-12" id="payment-method-group-additional" style="display: none;">
                                        <hr>
                                        <div class="form-group">
                                            <label for="payment_method_additional">Método de Pago</label>
                                            <select name="payment_method" id="payment_method_additional" class="form-control">
                                                <option value="" selected disabled>--Seleccione una opción--</option>
                                                <option value="efectivo">Efectivo</option>
                                                <option value="qr">QR</option>
                                                <option value="ambos">Ambos</option>
                                            </select>
                                        </div>
                                        <div id="payment-details-additional" style="display: none;">
                                            <div class="form-group"><label for="amount_efectivo_additional">Monto en Efectivo</label><input type="number" name="amount_efectivo" id="amount_efectivo_additional" class="form-control" step="0.01" min="0" placeholder="0.00"></div>
                                            <div class="form-group"><label for="amount_qr_additional">Monto con QR</label><input type="number" name="amount_qr" id="amount_qr_additional" class="form-control" step="0.01" min="0" placeholder="0.00"></div>
                                        </div>
                                        <div id="calculator-additional" style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                                            <div class="form-group"><label for="amount_received_additional" style="font-weight: bold;">Monto Recibido (Efectivo)</label><input type="number" name="amount_received" id="amount_received_additional" class="form-control" step="0.01" min="0" placeholder="0.00"></div>
                                            <div class="summary-item" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;"><strong style="font-size: 1.1rem;">Cambio a devolver:</strong><strong class="amount" id="change_due_additional" style="font-size: 1.2rem; color: #28a745;">0.00 Bs.</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success btn-submit">Agregar Tiempo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @endif

        @endsection

        @section('javascript')
            <script src="{{ asset('js/btn-submit.js') }}"></script>

            <script>
                // Lógica para el modal de adelanto
                $('#payment_method_adelanto').on('change', function() {
                    let paymentMethod = $(this).val();
                    $('#payment-details-adelanto').hide();
                    $('#calculator-adelanto').hide();

                    $('#amount_efectivo_adelanto').prop('required', false).prop('min', '');
                    $('#amount_qr_adelanto').prop('required', false).prop('min', '');

                    if (paymentMethod === 'ambos') {
                        $('#payment-details-adelanto').show();
                        $('#amount_efectivo_adelanto').prop('required', true).prop('min', 0.01);
                        $('#amount_qr_adelanto').prop('required', true).prop('min', 0.01);
                    } else if (paymentMethod === 'efectivo') {
                        $('#calculator-adelanto').show();
                    }
                });
                $('#amount_received_adelanto').on('keyup change', function() {
                    let adelanto = parseFloat($('#amount_adelanto').val()) || 0;
                    let received = parseFloat($(this).val()) || 0;
                    let change = received - adelanto;
                    if (change < 0) change = 0;
                    $('#change_due_adelanto').text(change.toFixed(2).replace('.', ',') + ' Bs.');
                });

                $(document).ready(function() {
                    var productSelected;

                    $('#select-product_id').select2({
                        width: '100%',
                        placeholder: '<i class="fa fa-search"></i> Buscar...',
                        escapeMarkup: function(markup) {
                            return markup;
                        },
                        language: {
                            inputTooShort: function(data) {
                                return `Por favor ingrese ${data.minimum - data.input.length} o más caracteres`;
                            },
                            noResults: function() {
                                return `<i class="far fa-frown"></i> No hay resultados encontrados`;
                            }
                        },
                        quietMillis: 250,
                        minimumInputLength: 2,
                        ajax: {
                            url: "{{ url('admin/items/stock/ajax') }}",
                            processResults: function(data) {
                                return {
                                    results: data
                                };
                            },
                            cache: true
                        },
                        templateResult: formatResultProducts,
                        templateSelection: (opt) => {
                            productSelected = opt;
                            return productSelected.name_item
                        }
                    }).change(function() {
                        if ($('#select-product_id option:selected').val()) {
                            let product = productSelected;
                            if (product.id) {
                                $('#input-stock').val(product.stock);
                                $('#input-price').val(product.priceSale);
                                $('#input-quantity').val(1);
                                $('#input-quantity').attr('max', product.stock);
                                $('#input-item_stock_id').val(product.id);
                                updateSubtotal();
                            }
                        } else {
                            $('#input-stock').val('');
                            $('#input-price').val('');
                            $('#input-quantity').val('');
                            $('#input-subtotal').val('');
                            $('#input-item_stock_id').val('');
                        }
                    });

                    $('#input-price, #input-quantity').on('keyup change', function() {
                        updateSubtotal();
                    });

                    $('#input-quantity').on('keyup change', function() {
                        let max = $('#input-stock').val() ? parseFloat($('#input-stock').val()) : 0;
                        let value = $(this).val() ? parseFloat($(this).val()) : 0;
                        if (value > max) {
                            $(this).val(max);
                            toastr.warning('La cantidad no puede ser mayor al stock', 'Advertencia');
                        }
                    });

                    @php
                        $totalPagado = $service->serviceTransactions->sum('amount');
                        $deuda = $service->total_amount - $totalPagado;
                    @endphp

                    @if ($deuda > 0)
                        const deuda = {{ $deuda }};
                        const finishButton = $('.btn-finish');
                        finishButton.prop('disabled', true);

                        $('#payment_method').on('change', function() {
                            let paymentMethod = $(this).val();
                            $('#payment-details').hide();
                            $('#calculator').hide();
                            finishButton.prop('disabled', true);

                            $('#amount_efectivo').prop('required', false).prop('min', '');
                            $('#amount_qr').prop('required', false).prop('min', '');

                            if (paymentMethod === 'ambos') {
                                $('#payment-details').show();
                                $('#amount_efectivo').prop('required', true).prop('min', 0.01);
                                $('#amount_qr').prop('required', true).prop('min', 0.01);
                            } else if (paymentMethod === 'efectivo') {
                                $('#calculator').show();
                            } else if (paymentMethod === 'qr') {
                                finishButton.prop('disabled', false);
                            }

                            $('#amount_received').val('').trigger('change');
                            $('#amount_efectivo').val('').trigger('change');
                            $('#amount_qr').val('').trigger('change');
                            checkPayment();
                        });

                        function checkPayment() {
                            let paymentMethod = $('#payment_method').val();
                            if (!paymentMethod) {
                                finishButton.prop('disabled', true);
                                return;
                            }

                            if (paymentMethod === 'qr') {
                                finishButton.prop('disabled', false);
                                return;
                            }

                            if (paymentMethod === 'efectivo') {
                                let received = parseFloat($('#amount_received').val()) || 0;
                                if (received >= deuda) {
                                    finishButton.prop('disabled', false);
                                } else {
                                    finishButton.prop('disabled', true);
                                }
                                let change = received - deuda;
                                if (change < 0) change = 0;
                                $('#change_due').text(change.toFixed(2).replace('.', ',') + ' Bs.');

                            } else if (paymentMethod === 'ambos') {
                                let efectivo = parseFloat($('#amount_efectivo').val()) || 0;
                                let qr = parseFloat($('#amount_qr').val()) || 0;

                                if ((efectivo + qr).toFixed(2) == deuda.toFixed(2)) {
                                    finishButton.prop('disabled', false);
                                } else {
                                    finishButton.prop('disabled', true);
                                }
                            }
                        }

                        $('#amount_received, #amount_efectivo, #amount_qr').on('keyup change', function() {
                            checkPayment();
                        });

                        $('#amount_qr, #amount_efectivo').on('keyup change', function() {
                            let efectivo = parseFloat($('#amount_efectivo').val()) || 0;
                            let qr = parseFloat($('#amount_qr').val()) || 0;

                            if ((efectivo + qr) > deuda) {
                                toastr.warning('El monto ingresado no puede ser mayor a la deuda.',
                                    'Monto excedido');
                                let changedInput = $(this).attr('id');
                                if (changedInput == 'amount_efectivo') {
                                    $('#amount_efectivo').val(deuda - qr);
                                } else {
                                    $('#amount_qr').val(deuda - efectivo);
                                }
                                checkPayment();
                            }
                        });


                        $('form[action="{{ route('services.finish', ['service' => $service->id]) }}"]').on('submit', function(e) {
                            if (finishButton.prop('disabled')) {
                                e.preventDefault();
                                toastr.error('Verifique los datos del pago.', 'Error en el pago');
                            }
                        });
                    @else
                        $('.btn-finish').prop('disabled', false);
                    @endif
                });

                function formatResultProducts(option) {
                    if (option.loading)
                        return '<span class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</span>';
                    let image = "{{ asset('images/default.jpg') }}";
                    if (option.item?.image) image =
                        `{{ asset('storage') }}/${option.item.image.replace(/\.([^.]+)$/, '-cropped.webp')}`;
                    const fallbackImage = '{{ asset('images/default.jpg') }}';
                    return $(
                        `<div style="display: flex; align-items: center; padding: 5px;"><img src="${image}" style="width: 50px; height: 50px; border-radius: 4px; margin-right: 10px; object-fit: cover;" onerror="this.onerror=null;this.src='${fallbackImage}';"/><div style="line-height: 1.2;"><div style="font-weight: bold;">${option.item.name}</div><small><b>Stock:</b> ${option.stock} Unid. | <b>Precio:</b> ${option.priceSale} Bs.</small></div></div>`
                    );
                }

                function updateSubtotal() {
                    let price = $('#input-price').val() ? parseFloat($('#input-price').val()) : 0;
                    let quantity = $('#input-quantity').val() ? parseFloat($('#input-quantity').val()) : 0;
                    $('#input-subtotal').val((price * quantity).toFixed(2));
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const endTimeInput = document.getElementById('end_time_additional');
                    const endDateInput = document.getElementById('end_date_additional');
                    const startTimeInput = document.getElementById('start_time_additional');
                    const startDateInput = document.getElementById('start_date_additional');
                    const amountLabel = document.getElementById('amount-label-additional');
                    const amountGroup = document.getElementById('amount-group-additional');
                    const amountInput = document.getElementById('amount-additional');
                    const paymentGroup = document.getElementById('payment-method-group-additional');

                    if(endTimeInput) {
                        document.getElementById('clear-end-time-additional').addEventListener('click', function() {
                            endDateInput.value = '';
                            endTimeInput.value = '';
                            updateAmountField();
                        });

                        function updateAmountField() {
                            if (endTimeInput.value) {
                                if (startTimeInput.value && endTimeInput.value < startTimeInput.value) {
                                    // Si la hora de fin es menor, asumimos que es del día siguiente
                                    let nextDay = new Date(startDateInput.value);
                                    nextDay.setDate(nextDay.getDate() + 2); // Se suma 2 por la forma en que JS maneja las fechas
                                    endDateInput.value = nextDay.toISOString().split('T')[0];
                                } else if (!endDateInput.value) {
                                    endDateInput.value = startDateInput.value;
                                } else if (new Date(endDateInput.value) < new Date(startDateInput.value)) {
                                    toastr.warning('La fecha de fin no puede ser anterior a la de inicio.', 'Fecha inválida');
                                    endDateInput.value = startDateInput.value;
                                }

                                if (!endTimeInput.value) {
                                    amountGroup.style.display = 'none';
                                    paymentGroup.style.display = 'none';
                                }
                                amountGroup.style.display = 'block';
                                paymentGroup.style.display = 'block';
                                amountLabel.textContent = 'Monto del alquiler';
                                amountInput.required = true; // Monto es requerido si hay hora fin
                                amountInput.min = 0.01;
                            } else {
                                amountGroup.style.display = 'none';
                                paymentGroup.style.display = 'none';
                                amountInput.required = false;
                            }
                            amountInput.value = '';
                            $('#payment_method_additional').val('').trigger('change');
                        }

                        $('#payment_method_additional').on('change', function() {
                            let paymentMethod = $(this).val();
                            $('#payment-details-additional').hide();
                            $('#calculator-additional').hide();

                            $('#amount_efectivo_additional').prop('required', false);
                            $('#amount_qr_additional').prop('required', false);
                            $('#payment_method_additional').prop('required', endTimeInput.value ? true : false);

                            if (paymentMethod === 'ambos') {
                                $('#payment-details-additional').show();
                                $('#amount_efectivo_additional').prop('required', true);
                                $('#amount_qr_additional').prop('required', true);
                            } else if (paymentMethod === 'efectivo') {
                                $('#calculator-additional').show();
                            }
                        });

                        function calculateChangeAdditional() {
                            let total = parseFloat($('#amount-additional').val()) || 0;
                            let paymentMethod = $('#payment_method_additional').val();
                            let received = 0;
                            let change = 0;
                        
                            if (paymentMethod === 'efectivo') {
                                received = parseFloat($('#amount_received_additional').val()) || 0;
                                change = received - total;
                            } else if (paymentMethod === 'ambos') {
                                let efectivo = parseFloat($('#amount_efectivo_additional').val()) || 0;
                                let qr = parseFloat($('#amount_qr_additional').val()) || 0;
                                let sum = efectivo + qr;
                        
                                if (sum > total) {
                                    toastr.warning('La suma de los montos no puede ser mayor al total.', 'Monto excedido', {timeOut: 1500});
                                    
                                    // Resetea el campo que se acaba de cambiar si la suma excede el total
                                    if ($(document.activeElement).is('#amount_efectivo_additional')) {
                                        $('#amount_efectivo_additional').val((total - qr).toFixed(2));
                                    } else if ($(document.activeElement).is('#amount_qr_additional')) {
                                        $('#amount_qr_additional').val((total - efectivo).toFixed(2));
                                    }
                                }
                            }
                        
                            if (change < 0) change = 0;
                            $('#change_due_additional').text(change.toFixed(2) + ' Bs.');
                        }

                        $('#amount-additional').on('keyup change', calculateChangeAdditional);
                        $('#amount_received_additional').on('keyup change', calculateChangeAdditional);
                        $('#amount_efectivo_additional').on('keyup change', calculateChangeAdditional);
                        $('#amount_qr_additional').on('keyup change', calculateChangeAdditional);

                        endTimeInput.addEventListener('change', updateAmountField);
                        updateAmountField(); // Llamada inicial para establecer el estado correcto
                    }

                    // Lógica para los modales de "Finalizar Tiempo"
                    $('.modal[id^="updateTimeModal-"]').each(function() {
                        const modal = $(this);
                        const paymentMethodSelect = modal.find('.payment-method-update');
                        const paymentDetails = modal.find('.payment-details-update');
                        const calculator = modal.find('.calculator-update');
                        const amountEfectivo = modal.find('.amount-efectivo-update');
                        const amountQr = modal.find('.amount-qr-update');
                        const amountTotal = modal.find('.amount-input');
                        const amountReceived = modal.find('.amount-received-update');
                        const changeDue = modal.find('.change-due-update');
                        const endDateInput = modal.find('.end-date-input');
                        const endTimeInput = modal.find('.end-time-input');
                        const durationDisplay = modal.find('[id^="duration_"]');
                        const startTimeValue = modal.find('.start-time-value').val();

                        paymentMethodSelect.on('change', function() {
                            const paymentMethod = $(this).val();
                            paymentDetails.hide();
                            calculator.hide();
                            amountEfectivo.prop('required', false);
                            amountQr.prop('required', false);

                            if (paymentMethod === 'ambos') {
                                paymentDetails.show();
                                amountEfectivo.prop('required', true);
                                amountQr.prop('required', true);
                            } else if (paymentMethod === 'efectivo') {
                                calculator.show();
                            }
                        });

                        function calculateChangeUpdate() {
                            let total = parseFloat(amountTotal.val()) || 0;
                            let paymentMethod = paymentMethodSelect.val();
                            let change = 0;

                            if (paymentMethod === 'efectivo') {
                                let received = parseFloat(amountReceived.val()) || 0;
                                change = received - total;
                            } else if (paymentMethod === 'ambos') {
                                let efectivo = parseFloat(amountEfectivo.val()) || 0;
                                let qr = parseFloat(amountQr.val()) || 0;
                                let sum = efectivo + qr;

                                if (sum > total) {
                                    toastr.warning('La suma de los montos no puede ser mayor al total.', 'Monto excedido', {timeOut: 1500});
                                    if ($(document.activeElement).is(amountEfectivo)) {
                                        amountEfectivo.val((total - qr).toFixed(2));
                                    } else if ($(document.activeElement).is(amountQr)) {
                                        amountQr.val((total - efectivo).toFixed(2));
                                    }
                                }
                            }
                            if (change < 0) change = 0;
                            changeDue.text(change.toFixed(2) + ' Bs.');
                        }

                        function calculateDuration() {
                            const startDate = new Date(startTimeValue);
                            const endDateStr = endDateInput.val();
                            const endTimeStr = endTimeInput.val();

                            if (endDateStr && endTimeStr) {
                                const endDate = new Date(`${endDateStr}T${endTimeStr}`);
                                if (endDate > startDate) {
                                    let diff = endDate.getTime() - startDate.getTime();
                                    let minutes = Math.floor(diff / 60000);
                                    let days = Math.floor(minutes / (24 * 60));
                                    minutes -= days * 24 * 60;
                                    let hours = Math.floor(minutes / 60);
                                    minutes -= hours * 60;

                                    durationDisplay.text(`${days} día(s), ${hours} hora(s) y ${minutes} minuto(s)`);
                                } else {
                                    durationDisplay.text('La fecha fin debe ser mayor a la de inicio.');
                                }
                            }
                        }

                        modal.find('.amount-input, .amount-received-update, .amount-efectivo-update, .amount-qr-update').on('keyup change', calculateChangeUpdate);
                        modal.find('.end-date-input, .end-time-input').on('change', calculateDuration);
                        calculateDuration(); // Calcular al abrir el modal
                    });
                });
            </script>
        @endsection
