@extends('voyager::master')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .details-panel .panel-heading, .products-panel .panel-heading {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .details-panel .panel-title, .products-panel .panel-title {
            font-weight: 500;
        }
        .panel {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
                                <div class="detail-card">
                                    <i class="voyager-person icon"></i>
                                    <span>Cliente: <strong>{{ $service->person ? $service->person->name : 'No especificado' }}</strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <i class="voyager-watch icon"></i>
                                    <span>Inicio: <strong>{{ date('h:i A', strtotime($service->start_time)) }}</strong></span>
                                </div>
                                <div class="detail-card">
                                    <i class="voyager-alarm-clock icon"></i>
                                    @if ($service->serviceTimes->isNotEmpty() && $service->serviceTimes->first()->end_time)
                                        <span>Fin: <strong>{{ date('h:i A', strtotime($service->serviceTimes->first()->end_time)) }}</strong></span>
                                    @else
                                        <span>Tipo: <strong>Tiempo sin límite</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel de Productos Consumidos --}}
                <div class="panel products-panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-basket"></i> Productos Consumidos</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
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
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->itemStock->item->name }}</td>
                                            <td class="text-right">{{ number_format($item->price, 2, ',', '.') }} Bs.</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }} Bs.</td>
                                        </tr>
                                        @php $totalProductos += $item->amount; @endphp
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center" style="padding: 40px;">
                                                <i class="voyager-bar-chart" style="font-size: 3rem; opacity: 0.5;"></i>
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

            {{-- Panel de Resumen de Pago --}}
            <div class="col-md-4">
                <div class="panel summary-panel">
                    <h4 style="text-align: center; margin-top: 0; font-weight: 500; color: #4A4A4A;">Resumen de Pago</h4>
                    <hr>
                    <div class="summary-item">
                        <span>Subtotal Productos:</span>
                        <strong>{{ number_format($totalProductos, 2) }} Bs.</strong>
                    </div>
                    <div class="summary-item">
                        <span>Adelanto/Monto Sala:</span>
                        <strong>{{ number_format($service->amount_room, 2) }} Bs.</strong>
                    </div>
                    
                    <div class="summary-total">
                        <div class="summary-item">
                            <span>Monto Total:</span>
                            <strong>{{ number_format($service->total_amount, 2) }} Bs.</strong>
                        </div>
                    </div>
                    <div class="summary-total">
                        <div class="summary-item">
                            <span>Total Pagado:</span>
                            @php
                                $totalPagado = $service->serviceTransactions->sum('amount');
                            @endphp
                            <strong style="color: green;">{{ number_format($totalPagado, 2) }} Bs.</strong>
                        </div>
                    </div>
                    <div class="summary-total">
                        <div class="summary-item">
                            <span>Deuda a Pagar:</span>
                            <strong style="color: red;">{{ number_format($service->total_amount - $totalPagado, 2) }} Bs.</strong>
                        </div>
                    </div>
                    <div style="margin-top: 20px; text-align: center;">
                        <a href="#" class="btn btn-finish"><i class="voyager-dollar"></i> Finalizar y Cobrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
