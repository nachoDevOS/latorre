@extends('voyager::master')

@section('page_title', 'Ver Caja')


@section('page_header')
    <h1 class="page-title">
        <i class="voyager-dollar"></i> Viendo Caja
        <a href="{{ route('cashiers.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Volver a la lista
        </a>
        @if ($cashier->status == 'Cierre Pendiente')
            <a href="{{ route('cashiers.confirm_close', ['cashier' => $cashier->id]) }}" title="Ver"
                class="btn btn-sm btn-info">
                <i class="voyager-lock"></i> <span class="hidden-xs hidden-sm">Confirmar Cierre de Caja</span>
            </a>
        @endif
        @if ($cashier->status == 'Cerrada')
            <a href="{{ route('cashiers.print', $cashier->id) }}" title="Imprimir" target="_blank"
                class="btn btn-sm btn-danger">
                <i class="fa fa-print"></i> <span class="hidden-xs hidden-sm">Imprimir</span>
            </a>
        @endif
        {{-- <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-print"></span> Impresión <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ route('print.open', ['cashier' => $cashier->id]) }}" target="_blank">Apertura</a></li>
                @if ($cashier->status == 'Cerrada')
                <li><a href="{{ route('print.close', ['cashier' => $cashier->id]) }}" target="_blank">Cierre</a></li>
                @endif
            </ul>
        </div> --}}
    </h1>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- ##### INFORMACIÓN GENERAL DE LA CAJA ##### -->
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Cajero</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Tipo de Caja</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->sale }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Descripción</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->title }}</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr style="margin:0;">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Observaciones</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->observations ?? 'Ninguna' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ##### INGRESOS Y EGRESOS ##### -->
            <div class="col-md-7">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-logbook"></i> Ventas Realizadas</h3>
                    </div>
                    <div class="panel-body" style="padding: 0px">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-bordered table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center">N&deg;</th>
                                        <th style="text-align: center">Código</th>
                                        <th style="text-align: center">Cliente</th>
                                        <th style="text-align: center">Fecha</th>
                                        <th style="text-align: center">Ticket</th>
                                        <th style="text-align: right">Pago Qr</th>
                                        <th style="text-align: right">Pago Efectivo</th>
                                        <th style="text-align: right">Total</th>
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
                                    @forelse ($cashier->sales->sortByDesc('created_at') as $item)
                                        <tr
                                            @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                            <td style="text-align: center; font-size: 11px">{{ $count }}</td>
                                            <td style="font-size: 11px; text-align: center">
                                                @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                    <a href="#"
                                                        onclick="deleteItem('{{ route('sales.destroy', ['sale' => $item->id]) }}')"
                                                        title="Eliminar" data-toggle="modal" data-target="#modal-delete"
                                                        class="btn btn-sm btn-danger delete">
                                                        <i class="voyager-trash"></i>
                                                    </a>
                                                @endif
                                                <br>
                                                {{ $item->code }}
                                            </td>
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
                                                $pagoQr = $item->saleTransactions
                                                    ->where('paymentType', 'Qr')
                                                    ->sum('amount');
                                                $pagoEfectivo = $item->saleTransactions
                                                    ->where('paymentType', 'Efectivo')
                                                    ->sum('amount');
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
                                            <td style="text-align: center" colspan="8">No hay datos disponibles en la
                                                tabla</td>
                                        </tr>
                                    @endempty
                                    <tr>
                                        <td colspan="7" class="text-right"><span class="text-danger"><b>TOTAL
                                                    ANULADO</b></span></td>
                                        <td class="text-right"><b
                                                class="text-danger">{{ number_format($total_movements_deleted, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right"><b>TOTAL COBROS</b></td>
                                        <td class="text-right">
                                            <b>{{ number_format($total_movements, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right"><b>TOTAL QR/TRANSFERENCIA</b></td>
                                        <td class="text-right">
                                            <b>{{ number_format($total_movements_qr, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right"><b>TOTAL EFECTIVO</b></td>
                                        <td class="text-right">
                                            <b>{{ number_format($total_movements_efectivo, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>

            <div class="col-md-5">
                <!-- ##### DINERO ABONADO ##### -->
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-double-up"></i> Dinero Abonado (Ingreso)</h3>
                    </div>
                    <div class="panel-body" style="padding: 0px">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="margin-bottom: 0px">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">N&deg;</th>
                                        <th>Detalle</th>
                                        <th style="text-align: right">Monto</th>
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
                                            <td>
                                                {{ $item->description }} <br>
                                                <small>{{ $item->user->name }} - {{ date('d/m/Y H:i', strtotime($item->created_at)) }}</small>
                                                @if ($item->transferCashier_id)
                                                    <label class="label label-info">Trasferencia</label>
                                                @endif
                                            </td>
                                            <td style="text-align: right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $cashierInput += $item->amount;
                                            $count++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="3">No hay datos</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align: right"><b>TOTAL</b></td>
                                        <td style="text-align: right"><b>{{ number_format($cashierInput, 2, ',', '.') }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ##### GASTOS ##### -->
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-double-down"></i> Gastos (Egreso)</h3>
                    </div>
                    <div class="panel-body" style="padding: 0px">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="margin-bottom: 0px">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">N&deg;</th>
                                        <th>Detalle</th>
                                        <th style="text-align: right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 1;
                                        $total_expense = 0;
                                    @endphp
                                    @forelse ($cashier->expenses->where('deleted_at', null) as $item)
                                        <tr>
                                            <td>{{ $count }}</td>
                                            <td>
                                                {{ $item->categoryExpense->name }} - {{ $item->observation }} <br>
                                                <small>{{ date('d/m/Y H:i', strtotime($item->date)) }}</small>
                                            </td>
                                            <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $total_expense += $item->amount;
                                            $count++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td style="text-align: center" colspan="3">No hay datos</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right"><b>TOTAL GASTOS</b></td>
                                        <td class="text-right"><b>{{ number_format($total_expense, 2, ',', '.') }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ##### RESUMEN FINAL ##### -->
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-pie-chart"></i> Resumen de Caja</h3>
                    </div>
                    <div class="panel-body">
                        <dl class="dl-horizontal">
                            <dt>Dinero abonado</dt>
                            <dd>{{ number_format($cashierInput, 2, ',', '.') }}</dd>
                            <dt>Cobros en efectivo</dt>
                            <dd>{{ number_format($total_movements_efectivo, 2, ',', '.') }}</dd>
                            <dt>Cobros mediante QR</dt>
                            <dd>{{ number_format($total_movements_qr, 2, ',', '.') }}</dd>
                            <dt>Gastos realizados</dt>
                            <dd class="text-danger"> {{ number_format($total_expense, 2, ',', '.') }}</dd>
                        </dl>
                        <hr>
                        <dl class="dl-horizontal">
                            <dt style="font-size: 1.2em">Efectivo en Caja</dt>
                            <dd style="font-size: 1.2em; font-weight: bold;">{{ number_format($cashierInput + $total_movements_efectivo - $total_expense, 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    @include('partials.modal-delete')




@stop

@section('javascript')
<script>

    function deleteItem(url) {
        $('#delete_form').attr('action', url);
    }

    $(document).ready(function() {
        $('.btn-delete').click(function() {
            let loan_id = $(this).data('id');
            $(`#form-delete input[name="loan_id"]`).val(loan_id);
        });
    });
</script>
@stop
