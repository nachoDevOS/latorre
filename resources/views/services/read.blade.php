@extends('voyager::master')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
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
        .btn-finish:disabled {
            background: #a5d6a7;
            cursor: not-allowed;
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
                                    <span>Cliente: <strong>{{ $service->person ? $service->person->name : 'No especificado' }}</strong></span>
                                </div>
                            </div>
                        </div>
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
                                                    <td class="text-right">{{ number_format($item->price, 2, ',', '.') }}.</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }} Bs.</td>
                                                </tr>
                                                @php $totalProductos += $item->amount; @endphp
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center" style="padding: 40px;">
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
         
                    <div class="col-md-6">
                        {{-- Panel para agregar productos --}}
                        <div class="panel panel-info">
                            <div>
                                <h3 class="panel-title"><i class="voyager-plus"></i> Agregar Productos al Servicio</h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('services.add_item', ['service' => $service->id]) }}"  method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label>Buscar producto</label>
                                            <select class="form-control" id="select-product_id"></select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Precio</label>
                                            <div class="input-group">
                                                <input type="number" name="price" id="input-price" class="form-control" step="0.01" min="0.01" required />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Cantidad</label>
                                            <input type="number" name="quantity" id="input-quantity" class="form-control" step="1" min="1" required />
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
                        <h3 class="panel-title"><i class="voyager-dollar"></i> Historial de Pagos</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Método de pago</th>
                                        <th class="text-right">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($service->serviceTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('d/m/Y h:i a') }}</td>
                                            <td>
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
                                            <td class="text-right">{{ number_format($transaction->amount, 2, ',', '.') }} Bs.</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center" style="padding: 20px;">
                                                <i class="voyager-info-circled" style="font-size: 2rem; opacity: 0.5;"></i>
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
                    $deuda = $service->total_amount - $totalPagado;
                @endphp
                <form action="{{ route('services.finish', ['service' => $service->id]) }}" method="POST">
                    @csrf
                    <div class="panel summary-panel">
                        <h4 style="text-align: center; margin-top: 0; font-weight: 500; color: #4A4A4A;">Resumen de Pago</h4>
                        <hr>
                        <div class="summary-item">
                            <span>Subtotal Productos:</span>
                            <strong>{{ number_format($totalProductos, 2, ',', '.') }} Bs.</strong>
                        </div>
                        <div class="summary-item">
                            <span>Adelanto/Monto Sala:</span>
                            <strong>{{ number_format($service->amount_room, 2, ',', '.') }} Bs.</strong>
                        </div>
                        
                        <div class="summary-total">
                            <div class="summary-item">
                                <span>Monto Total:</span>
                                <strong>{{ number_format($service->total_amount, 2, ',', '.') }} Bs.</strong>
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
                                <strong id="deuda-pagar" style="color: red;">{{ number_format($deuda, 2, ',', '.') }} Bs.</strong>
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
                                        <input type="number" name="amount_efectivo" id="amount_efectivo" class="form-control" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    <div class="form-group">
                                        <label for="amount_qr">Monto con QR</label>
                                        <input type="number" name="amount_qr" id="amount_qr" class="form-control" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>

                                <div id="calculator" style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                                    <div class="form-group">
                                        <label for="amount_received" style="font-weight: bold;">Monto Recibido (Efectivo)</label>
                                        <input type="number" name="amount_received" id="amount_received" class="form-control" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    <div class="summary-item" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;">
                                        <strong style="font-size: 1.1rem;">Cambio a devolver:</strong>
                                        <strong class="amount" id="change_due" style="font-size: 1.2rem; color: #28a745;">0.00 Bs.</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div style="margin-top: 20px; text-align: center;">
                            <button type="submit" class="btn btn-finish"><i class="voyager-dollar"></i> Finalizar y Cobrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/btn-submit.js') }}"></script>

    <script>
        $(document).ready(function() {
            var productSelected;

            $('#select-product_id').select2({
                width: '100%',
                placeholder: '<i class="fa fa-search"></i> Buscar...',
                escapeMarkup: function(markup) { return markup; },
                language: {
                    inputTooShort: function(data) { return `Por favor ingrese ${data.minimum - data.input.length} o más caracteres`; },
                    noResults: function() { return `<i class="far fa-frown"></i> No hay resultados encontrados`; }
                },
                quietMillis: 250,
                minimumInputLength: 2,
                ajax: {
                    url: "{{ url('admin/items/stock/ajax') }}",
                    processResults: function(data) {
                        return { results: data };
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
                    if(product.id){
                        $('#input-stock').val(product.stock);
                        $('#input-price').val(product.priceSale);
                        $('#input-quantity').val(1);
                        $('#input-quantity').attr('max', product.stock);
                        $('#input-item_stock_id').val(product.id);
                        updateSubtotal();
                    }
                }else{
                    $('#input-stock').val('');
                    $('#input-price').val('');
                    $('#input-quantity').val('');
                    $('#input-subtotal').val('');
                    $('#input-item_stock_id').val('');
                }
            });

            $('#input-price, #input-quantity').on('keyup change', function(){
                updateSubtotal();
            });

            $('#input-quantity').on('keyup change', function(){
                let max = $('#input-stock').val() ? parseFloat($('#input-stock').val()) : 0;
                let value = $(this).val() ? parseFloat($(this).val()) : 0;
                if(value > max){
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
                        
                        if ( (efectivo + qr).toFixed(2) == deuda.toFixed(2) ) {
                            finishButton.prop('disabled', false);
                        } else {
                            finishButton.prop('disabled', true);
                        }
                    }
                }

                $('#amount_received, #amount_efectivo, #amount_qr').on('keyup change', function(){
                    checkPayment();
                });

                $('#amount_qr, #amount_efectivo').on('keyup change', function() {
                    let efectivo = parseFloat($('#amount_efectivo').val()) || 0;
                    let qr = parseFloat($('#amount_qr').val()) || 0;

                    if ((efectivo + qr) > deuda) {
                        toastr.warning('El monto ingresado no puede ser mayor a la deuda.', 'Monto excedido');
                        let changedInput = $(this).attr('id');
                        if(changedInput == 'amount_efectivo') {
                            $('#amount_efectivo').val(deuda - qr);
                        } else {
                            $('#amount_qr').val(deuda - efectivo);
                        }
                        checkPayment();
                    }
                });


                $('form').on('submit', function(e) {
                    if(finishButton.prop('disabled')) {
                        e.preventDefault();
                        toastr.error('Verifique los datos del pago.', 'Error en el pago');
                    }
                });
            @else
                $('.btn-finish').prop('disabled', false);
            @endif
        });

        function formatResultProducts(option) {
            if (option.loading) return '<span class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</span>';
            let image = "{{ asset('images/default.jpg') }}";
            if (option.item?.image) image = `{{ asset('storage') }}/${option.item.image.replace(/\.([^.]+)$/, '-cropped.webp')}`;
            const fallbackImage = '{{ asset('images/default.jpg') }}';
            return $(`<div style="display: flex; align-items: center; padding: 5px;"><img src="${image}" style="width: 50px; height: 50px; border-radius: 4px; margin-right: 10px; object-fit: cover;" onerror="this.onerror=null;this.src='${fallbackImage}';"/><div style="line-height: 1.2;"><div style="font-weight: bold;">${option.item.name}</div><small><b>Stock:</b> ${option.stock} Unid. | <b>Precio:</b> ${option.priceSale} Bs.</small></div></div>`);
        }

        function updateSubtotal(){
            let price = $('#input-price').val() ? parseFloat($('#input-price').val()) : 0;
            let quantity = $('#input-quantity').val() ? parseFloat($('#input-quantity').val()) : 0;
            $('#input-subtotal').val((price * quantity).toFixed(2));
        }
    </script>
@endsection
