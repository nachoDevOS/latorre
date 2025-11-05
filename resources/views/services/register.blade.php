@extends('voyager::master')

@section('css')
    {{-- Enlazamos la hoja de estilos externa --}}
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <style>
        .detail-card strong {
            font-size: 1.5rem !important; /* Aumento drástico para los títulos */
        }
        .detail-card span.text-muted,
        .detail-card span.badge {
            font-size: 1.2rem !important; /* Aumento drástico para los valores y badges */
        }
        .detail-card .voyager-tag, .detail-card .voyager-bookmark, .detail-card .voyager-check-circle, .detail-card .voyager-bubble-hear {
            font-size: 1.5rem; /* Agrandar también los iconos */
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
@endsection

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-12">
                <div class="panel details-panel rounded shadow-lg">
                    <div class="panel-heading bg-primary text-white text-center p-3">
                        <h3 class="panel-title" style="margin: 0;"><i class="voyager-info-circled me-2"></i> Detalles de la Sala</h3>
                    </div>
                    <div class="panel-body p-4">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="detail-card p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                    <div><i class="voyager-tag me-2 text-primary"></i> <strong class="fw-bold">Nombre:</strong></div>
                                    <span class="text-muted text-end">{{ $room->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="detail-card p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                    <div><i class="voyager-bookmark me-2 text-primary"></i> <strong class="fw-bold">Tipo:</strong></div>
                                    <span class="text-muted text-end">{{ $room->type }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="detail-card p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                    <div><i class="voyager-check-circle me-2 text-primary"></i> <strong class="fw-bold">Estado:</strong></div>
                                    <div class="text-end">
                                        @if ($room->status == 'Disponible')
                                            <span class="badge badge-success badge-pill text-uppercase">Disponible</span>
                                        @else
                                            <span class="badge badge-danger badge-pill text-uppercase">Ocupada</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="detail-card p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                    <div><i class="voyager-bubble-hear me-2 text-primary"></i> <strong class="fw-bold">Observación:</strong></div>
                                    <span class="text-muted text-end">{{ $room->observation ?: 'Ninguna.' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('services.rental.start') }}" method="POST">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">
                <input type="hidden" name="rental_type" id="hidden_rental_type" value="por_hora">

                <div class="col-md-6">
                    {{-- PANEL PARA EL CARRITO DE CONSUMO --}}
                    <div class="panel action-panel">
                        <div class="panel-heading"><h3 class="panel-title"><i class="voyager-basket"></i> Consumo</h3></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="product_id">Buscar y añadir producto</label>
                                <select class="form-control" id="select-product_id"></select>
                            </div>
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-hover table-products">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px">N&deg;</th>
                                            <th>Detalles</th>
                                            <th style="text-align: center; width:15%">Precio</th>
                                            <th style="text-align: center; width:12%">Cantidad</th>
                                            <th style="text-align: center; width:10%">Subtotal</th>
                                            <th style="width: 30px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body">
                                        <tr id="tr-empty">
                                            <td colspan="6" style="height: 320px">
                                                <h4 class="text-center text-muted" style="margin-top: 50px">
                                                    <i class="glyphicon glyphicon-shopping-cart"
                                                        style="font-size: 50px"></i> <br><br>
                                                    Lista de venta vacía
                                                </h4>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right"><h5>TOTAL A PAGAR</h5></th>
                                            <th class="text-center"><h5 id="label-total">0.00</h5></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    {{-- PANEL PARA INICIAR UN NUEVO ALQUILER --}}
                    <div class="panel action-panel">
                        <div class="panel-heading"><h3 class="panel-title"><i class="voyager-play"></i> Iniciar Nuevo Alquiler</h3></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="person_id">Cliente</label>
                                <div class="input-group">
                                    <select name="person_id" id="select-person_id" class="form-control"></select>
                                    <span class="input-group-btn">
                                        <button id="trash-person" class="btn btn-default" title="Quitar Cliente" style="margin: 0px" type="button">
                                            <i class="voyager-trash"></i>
                                        </button>
                                        <button class="btn btn-primary" title="Nuevo cliente" data-target="#modal-create-person" data-toggle="modal" style="margin: 0px" type="button">
                                            <i class="voyager-plus"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_time">Hora de Inicio</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="end_time">Hora Fin (opcional)</label>
                                <input type="time" name="end_time" id="end_time" class="form-control">
                                <small class="form-text text-muted">Dejar vacío para alquiler por hora.</small>
                            </div>
                            <button type="submit" class="btn btn-success btn-block btn-action"><i class="voyager-play"></i> Iniciar Alquiler</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('partials.modal-registerPerson')

@endsection

@section('javascript')
    <script src="{{ asset('vendor/tippy/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/tippy/tippy-bundle.umd.min.js') }}"></script>

    <script src="{{ asset('js/include/person-select.js') }}"></script>
    <script src="{{ asset('js/include/person-register.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const hiddenRentalTypeInput = document.getElementById('hidden_rental_type');

            // Set current time for start_time input
            const now = new Date();
            const currentHours = now.getHours().toString().padStart(2, '0');
            const currentMinutes = now.getMinutes().toString().padStart(2, '0');
            startTimeInput.value = `${currentHours}:${currentMinutes}`;

            // Update hidden rental_type based on end_time
            function updateRentalType() {
                if (endTimeInput.value) {
                    hiddenRentalTypeInput.value = 'tiempo_fijo';
                } else {
                    hiddenRentalTypeInput.value = 'por_hora';
                }
            }

            // Listen for changes on end_time
            endTimeInput.addEventListener('change', updateRentalType);
            endTimeInput.addEventListener('keyup', updateRentalType); // For manual typing
            updateRentalType(); // Initial call in case there's a pre-filled value (though unlikely for new rental)
        });

        $('#trash-person').on('click', function() {
            // $('#input-dni').val('');
            $('#select-person_id').val(null).trigger('change');

            toastr.success('Cliente eliminado', 'Eliminado');
        });

        // =================================================================
        // ================== LÓGICA DEL CARRITO DE VENTAS =================
        // =================================================================
        $(document).ready(function() {
            var productSelected;
            getTotal();

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
                    return productSelected.id
                }
            }).change(function() {
                if ($('#select-product_id option:selected').val()) {
                    let product = productSelected;
                    let image = "{{ asset('images/default.jpg') }}";
                    if(product.image){
                        image = "{{ asset('storage') }}/"+product.image.replace('.avif','-cropped.webp');
                    } else if (product.item.image) {
                        image = "{{ asset('storage') }}/"+product.item.image.replace('.avif','-cropped.webp');
                    }

                    if ($('.table').find(`#tr-item-${product.id}`).length === 0) {
                        let subtotal = parseFloat(product.priceSale) * 1;
                        $('#table-body').append(`
                            <tr class="tr-item" id="tr-item-${product.id}">
                                <td class="td-item"></td>
                                <td>
                                    <input type="hidden" name="products[${product.id}][id]" value="${product.id}"/>
                                    <div style="font-weight: 500;">${product.item.name}</div>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <input type="number" name="products[${product.id}][price]" step="0.1" min="0.1" class="form-control input-sm text-right input-price" id="input-price-${product.id}" value="${product.priceSale}" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})" required/>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <div class="input-group" style="max-width: 100px; margin: auto;">
                                        <input type="number" name="products[${product.id}][quantity]" step="1" min="1" class="form-control input-sm text-right input-quantity" id="input-quantity-${product.id}" value="1" max="${product.stock}" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})" required/>
                                    </div>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <h5 class="label-subtotal" id="label-subtotal-${product.id}">${subtotal.toFixed(2)}</h5>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <button type="button" onclick="removeTr(${product.id})" class="btn btn-link"><i class="voyager-trash text-danger"></i></button>
                                </td>
                            </tr>
                        `);
                        setNumber();
                        getSubtotal(product.id);
                        toastr.success(`+1 ${product.item.name}`, 'Producto agregado');
                    } else {
                        toastr.info('El producto ya está agregado', 'Información');
                    }
                    $('#select-product_id').val('').trigger('change');
                }
            });
        });

        function getSubtotal(id) {
            let price = $(`#input-price-${id}`).val() ? parseFloat($(`#input-price-${id}`).val()) : 0;
            let quantity = $(`#input-quantity-${id}`).val() ? parseInt($(`#input-quantity-${id}`).val()) : 0;
            let stock = parseInt($(`#input-quantity-${id}`).attr('max')) || 0;

            if (quantity > stock) {
                $(`#input-quantity-${id}`).val(stock);
                quantity = stock;
                toastr.warning(`La cantidad no puede ser mayor al stock (${stock})`, 'Stock insuficiente');
            }
            let subtotal = price * quantity;
            $(`#label-subtotal-${id}`).text(subtotal.toFixed(2));
            getTotal();
        }

        function getTotal() {
            let total = 0;
            $(".label-subtotal").each(function() { total += parseFloat($(this).text()) || 0; });
            $('#label-total').text(total.toFixed(2));
        }

        function setNumber() {
            var length = 0;
            $(".td-item").each(function(index) {
                $(this).text(index + 1);
                length++;
            });
            if (length > 0) {
                $('#tr-empty').css('display', 'none');
            } else {
                $('#tr-empty').fadeIn('fast');
            }
        }

        function removeTr(id) {
            $(`#tr-item-${id}`).remove();
            $('#select-product_id').val("").trigger("change");
            setNumber();
            getTotal();
            toastr.info('Producto eliminado del carrito', 'Eliminado');
        }

        function formatResultProducts(option) {
            if (option.loading) return '<span class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</span>';
            let image = "{{ asset('images/default.jpg') }}";
            if (option.item?.image) image = `{{ asset('storage') }}/${option.item.image.replace(/\.([^.]+)$/, '-cropped.webp')}`;
            const fallbackImage = '{{ asset('images/default.jpg') }}';
            return $(`<div style="display: flex; align-items: center; padding: 5px;"><img src="${image}" style="width: 50px; height: 50px; border-radius: 4px; margin-right: 10px; object-fit: cover;" onerror="this.onerror=null;this.src='${fallbackImage}';"/><div style="line-height: 1.2;"><div style="font-weight: bold;">${option.item.name}</div><small><b>Stock:</b> ${option.stock} Unid. | <b>Precio:</b> ${option.priceSale} Bs.</small></div></div>`);
        }
    </script>
@endsection