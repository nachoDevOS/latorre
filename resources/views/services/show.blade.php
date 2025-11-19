@extends('voyager::master')

@section('css')
    <style>
        .detail-card strong {
            font-size: 1.2rem !important;
        }
        .detail-card span.text-muted,
        .detail-card span.badge {
            font-size: 1.0rem !important;
        }
        .detail-card .voyager-tag, .detail-card .voyager-bookmark, .detail-card .voyager-check-circle, .detail-card .voyager-bubble-hear {
            font-size: 1rem;
        }
        .summary-section {
            margin-bottom: 15px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 10px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .summary-item span {
            font-size: 1.1rem;
        }
        .summary-item .amount {
            font-weight: bold;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.2rem;
        }
        .summary-total .amount {
            color: #333;
        }
        .summary-item .amount#summary-advance {
            color: #28a745; /* Green */
        }
        .summary-item .amount#summary-consumption {
            color: #dc3545; /* Red */
        }
        .summary-total .amount#summary-total {
            color: #007bff; /* Blue */
        }
    </style>
@endsection

@section('page_title', 'Detalles del Servicio #'.$service->id)

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-basket"></i> Detalles del Servicio #{{ $service->id }}
    </h1>
    <a href="{{ route('services-sales.index') }}" class="btn btn-warning">
        <i class="voyager-list"></i> <span class="hidden-xs hidden-sm">Volver al listado</span>
    </a>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h4 class="panel-title">Información General del Servicio</h4>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <table class="table table-hover">
                            <tr>
                                <th>ID del Servicio:</th>
                                <td>{{ $service->id }}</td>
                            </tr>
                            <tr>
                                <th>Cliente:</th>
                                <td>{{ $service->person ? $service->person->first_name . ' ' . $service->person->paternal_surname : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    @if ($service->room_id && $service->serviceItems->count() > 0)
                                        <span class="label label-info">Alquiler de Sala y Venta de Productos</span>
                                    @elseif ($service->room_id)
                                        <span class="label label-info">Alquiler de Sala</span>
                                    @else
                                        <span class="label label-primary">Venta de Productos</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Monto Total:</th>
                                <td>{{ number_format($service->total_amount, 2, ',', '.') }} Bs.</td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    @if ($service->status == 'Finalizado')
                                        <span class="label label-success">Finalizado</span>
                                    @elseif($service->status == 'Vigente')
                                        <span class="label label-warning">Vigente</span>
                                    @else
                                        <span class="label label-default">{{ $service->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha de Registro:</th>
                                <td>{{ \Carbon\Carbon::parse($service->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            @if ($service->observation)
                                <tr>
                                    <th>Observación:</th>
                                    <td>{{ $service->observation }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Detalles del Alquiler de Sala --}}
                @if ($service->room_id && $service->room)
                    <div class="panel panel-bordered" style="padding-bottom:5px;">
                        <div class="panel-heading" style="border-bottom:0;">
                            <h4 class="panel-title">Detalles del Alquiler de Sala ({{ $service->room->name }})</h4>
                        </div>
                        <div class="panel-body" style="padding-top:0;">
                            <table class="table table-hover">
                                <tr>
                                    <th>Sala:</th>
                                    <td>{{ $service->room->name }} ({{ $service->room->type }})</td>
                                </tr>
                                <tr>
                                    <th>Monto de la Sala:</th>
                                    <td>{{ number_format($service->amount_room, 2, ',', '.') }} Bs.</td>
                                </tr>
                                @if ($service->serviceTimes->count() > 0)
                                    <tr>
                                        <th>Tiempos de Alquiler:</th>
                                        <td>
                                            <ul>
                                                @foreach ($service->serviceTimes as $time)
                                                    <li>
                                                        Inicio: {{ \Carbon\Carbon::parse($time->start_time)->format('d/m/Y H:i') }} |
                                                        Fin: {{ $time->end_time ? \Carbon\Carbon::parse($time->end_time)->format('d/m/Y H:i') : 'N/A' }} |
                                                        Duración: {{ $time->total_time ?? 'N/A' }} |
                                                        Monto: {{ number_format($time->amount, 2, ',', '.') }} Bs.
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Detalles de los Productos Vendidos --}}
                @if ($service->serviceItems->count() > 0)
                    <div class="panel panel-bordered" style="padding-bottom:5px;">
                        <div class="panel-heading" style="border-bottom:0;">
                            <h4 class="panel-title">Productos Vendidos</h4>
                        </div>
                        <div class="panel-body" style="padding-top:0;">
                            <table class="table table-hover table-products">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-right">Precio Unitario</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($service->serviceItems as $item)
                                        <tr>
                                            <td>{{ $item->itemStock->item->name ?? 'Producto Eliminado' }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-right">{{ number_format($item->price, 2, ',', '.') }} Bs.</td>
                                            <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }} Bs.</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total Productos:</strong></td>
                                        <td class="text-right"><strong>{{ number_format($service->amount_products, 2, ',', '.') }} Bs.</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Detalles de los Pagos --}}
                @if ($service->serviceTransactions->count() > 0)
                    <div class="panel panel-bordered" style="padding-bottom:5px;">
                        <div class="panel-heading" style="border-bottom:0;">
                            <h4 class="panel-title">Detalles de Pagos</h4>
                        </div>
                        <div class="panel-body" style="padding-top:0;">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Método de Pago</th>
                                        <th class="text-right">Monto</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($service->serviceTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->paymentType }}</td>
                                            <td class="text-right">{{ number_format($transaction->amount, 2, ',', '.') }} Bs.</td>
                                            <td>{{ $transaction->type }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@stop

@section('javascript')
    {{-- Puedes añadir scripts específicos si son necesarios para la vista de detalles --}}
@endsection