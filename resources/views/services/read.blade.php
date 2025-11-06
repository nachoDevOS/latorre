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
                    <div>
                        <h3 class="panel-title"><i class="voyager-basket"></i> Productos Consumidos</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dataTable">
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

                {{-- Panel para agregar productos --}}
                <div class="panel panel-info">
                    <div>
                        <h3 class="panel-title"><i class="voyager-plus"></i> Agregar Productos al Servicio</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('services.add_item', ['service' => $service->id]) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Buscar producto</label>
                                    <select class="form-control" id="select-product_id"></select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Stock</label>
                                    <input type="number" id="input-stock" class="form-control" readonly />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Precio</label>
                                    <div class="input-group">
                                        <input type="number" name="price" id="input-price" class="form-control" step="0.01" min="0.01" required />
                                        <span class="input-group-addon">Bs.</span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Cantidad</label>
                                    <input type="number" name="quantity" id="input-quantity" class="form-control" step="1" min="1" required />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Subtotal</label>
                                    <div class="input-group">
                                        <input type="number" id="input-subtotal" class="form-control" readonly />
                                        <span class="input-group-addon">Bs.</span>
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
@endsection

@section('javascript')
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
