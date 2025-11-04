@extends('layouts-print.template-print')

@section('page_title', 'Cierre de caja')


@section('content')
    <div class="report-header">
        <div class="logo-container">
            <?php
                $admin_favicon = Voyager::setting('admin.icon_image');
            ?>
            @if ($admin_favicon == '')
                <img src="{{ asset('images/icon.png') }}" alt="{{ Voyager::setting('admin.title') }}" width="70px">
            @else
                <img src="{{ Voyager::image($admin_favicon) }}" alt="{{ Voyager::setting('admin.title') }}" width="70px">
            @endif
        </div>

        <div class="title-container">
            <h1 class="report-title">{{ Voyager::setting('admin.title') }}</h1>
            <h2 class="report-subtitle">CIERRE DE CAJA</h2>
            <p class="report-date">
                <b>Fecha de cierre:</b> {{ date('d/m/Y', strtotime($cashier->closed_at)) }}
            </p>
        </div>

        <div class="qr-container">
            {!! QrCode::size(80)->generate(
                'Caja Nro ' . $cashier->id . ', usuario ' . $cashier->user->name . '. Monto de cierre ' . $cashier->amount_real,
            ) !!} <br>
            <strong>Caja N&deg; {{ $cashier->id }}</strong>
            <p class="print-info">Impreso por: {{ Auth::user()->name }}<br>{{ date('d/m/Y h:i:s a') }}</p>
        </div>
    </div>
    <div class="content">

        <table width="100%">
            <tr>
                <td><b>Descripción</b></td>
                <td>{{ $cashier->title }}</td>
                <td><b>Cajero</b></td>
                <td>{{ $cashier->user->name }}</td>
            </tr>
            <tr>
                <td><b>Observaciones</b></td>
                <td>{{ $cashier->observations ?? 'Ninguna' }}</td>
            </tr>
            @if ($cashier->amount)
                <tr>
                    <td><b>Monto de cierre</b></td>
                    <td><b>{{ $cashier->amount_real }}</b></td>
                    <td><b>Saldo</b></td>
                    <td><b
                            class="@if ($cashier->balance > 0) text-success @endif @if ($cashier->balance < 0) text-danger @endif">{{ $cashier->balance }}</b>
                    </td>
                </tr>
            @endif
        </table>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th colspan="5">DINERO ABONADO</th>
                </tr>
                <tr>
                    <th>N&deg;</th>
                    <th>Hora de Registro</th>
                    <th>Registrado Por</th>
                    <th>Detalle</th>
                    <th style="width: 80px">Monto</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $count = 1;
                    $cashierInput = 0;
                @endphp
                @forelse ($cashier->movements->where('type', 'Ingreso')->where('deleted_at', null) as $item)
                    <tr>
                        <td>{{ $count }}</td>
                        <td>{{ date('h:i:s', strtotime($item->created_at)) }}</td>
                        <td>{{ $item->user->name }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">{{ $item->amount }}</td>
                    </tr>
                    @php
                        $count++;
                        $cashierInput += $item->amount;
                    @endphp
                @empty
                    <tr>
                        <td class="text-center" colspan="5">No hay datos</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="4"><b>TOTAL</b></td>
                    <td class="text-right"><b>{{ number_format($cashierInput, 2, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th colspan="4">Gastos</th>
                </tr>
                <tr>
                    <th>N&deg;</th>
                    <th>Detalle</th>
                    <th>Descripción</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $count = 1;
                    $total_gastos = 0;
                @endphp
                @forelse ($cashier->expenses as $item)
                    <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                        <td style="text-align: center; font-size: 11px">{{ $count }}</td>
                        <td style="font-size: 11px">{{ $item->categoryExpense->name }}</td>
                        <td style="font-size: 11px">
                            {{$item->observation}}
                        </td>
                        <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                    </tr>
                    @php
                        $count++;
                        $total_gastos+=$item->amount;                        
                    @endphp
                @empty
                    <tr>
                        <td style="text-align: center" colspan="4">No hay datos disponibles en la tabla</td>
                    </tr>
                @endempty
                <tr>
                    <td colspan="3" class="text-right"><b>TOTAL GASTOS</b></td>
                    <td class="text-right"><b>{{ number_format($total_gastos, 2, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th colspan="8">Ventas</th>
                </tr>
                <tr>
                    <th>N&deg;</th>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Ticket</th>
                    <th>Pago Qr</th>
                    <th>Pago Efectivo</th>
                    <th>Total</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $count = 1;
                    $total_movements = 0;
                    $total_movements_qr = 0;
                    $total_movements_efectivo = 0;
                    $total_movements_deleted = 0;
                @endphp
                @forelse ($cashier->sales as $item)
                    <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                        <td style="text-align: center; font-size: 11px">{{ $count }}</td>
                        <td style="font-size: 11px">{{ $item->code }}</td>
                        <td style="font-size: 11px">
                            @if ($item->person)
                                {{ strtoupper($item->person->first_name) }}
                                {{ $item->person->middle_name ? strtoupper($item->person->middle_name) : '' }}
                                {{ strtoupper($item->person->paternal_surname) }}
                                {{ strtoupper($item->person->maternal_surname) }}
                            @else
                                Sin Datos
                            @endif
                        </td>
                        <td style="text-align: center; font-size: 11px">
                            {{ date('d/m/Y h:i a', strtotime($item->dateSale)) }}
                        </td>
                        <td style="text-align: center; font-size: 11px">{{ $item->ticket }}</td>

                        @php
                            $pagoQr = $item->saleTransactions->where('paymentType', 'Qr')->sum('amount');
                            $pagoEfectivo = $item->saleTransactions->where('paymentType', 'Efectivo')->sum('amount');
                            if ($item->deleted_at == null) {
                                $total_movements_qr += $pagoQr;
                                $total_movements_efectivo += $pagoEfectivo;

                                $total_movements += $pagoQr + $pagoEfectivo;
                            } else {
                                $total_movements_deleted += $item->amount;
                            }
                        @endphp
                        <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>


                    </tr>
                    @php
                        $count++;
                    @endphp
                @empty
                    <tr>
                        <td style="text-align: center" colspan="8">No hay datos disponibles en la tabla</td>
                    </tr>
                @endempty
                <tr>
                    <td colspan="7" class="text-right"><span class="text-danger"><b>TOTAL ANULADO</b></span></td>
                    <td class="text-right"><b
                            class="text-danger">{{ number_format($total_movements_deleted, 2, ',', '.') }}</b></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><b>TOTAL COBROS</b></td>
                    <td class="text-right">
                        <b>{{ number_format($total_movements, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><b>TOTAL QR/TRANSFERENCIA</b></td>
                    <td class="text-right"><b>{{ number_format($total_movements_qr, 2, ',', '.') }}</b></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><b>TOTAL EFECTIVO</b></td>
                    <td class="text-right"><b>{{ number_format($total_movements_efectivo, 2, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('css')
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        border: 1px solid;
        padding: 3px 5px;
    }

    .text-right {
        text-align: right
    }

    .label {
        border-radius: 5px;
        color: white;
        padding: 1px 3px;
        font-size: 10px
    }

    .label-primary {
        background-color: #1F618D;
    }

    .label-danger {
        background-color: #A93226;
    }

    .label-success {
        background-color: #229954;
    }

    .text-danger {
        color: #A93226;
    }

    .text-success {
        color: #229954;
    }
</style>
<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo-container img {
        max-height: 80px;
    }

    .title-container {
        text-align: center;
        flex-grow: 1;
    }

    .report-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #2c3e50;
    }

    .report-subtitle {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #7f8c8d;
    }

    .report-date {
        font-size: 14px;
        color: #95a5a6;
    }

    .qr-container {
        text-align: right;
    }

    .print-info {
        font-size: 10px;
        color: #95a5a6;
        margin-top: 5px;
    }
</style>
@endsection
