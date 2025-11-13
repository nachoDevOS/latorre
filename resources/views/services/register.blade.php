@extends('voyager::master')

@section('css')
    {{-- Enlazamos la hoja de estilos externa --}}
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <style>
        .detail-card strong {
            font-size: 1.2rem !important; /* Aumento drástico para los títulos */
        }
        .detail-card span.text-muted,
        .detail-card span.badge {
            font-size: 1.0rem !important; /* Aumento drástico para los valores y badges */
        }
        .detail-card .voyager-tag, .detail-card .voyager-bookmark, .detail-card .voyager-check-circle, .detail-card .voyager-bubble-hear {
            font-size: 1rem; /* Agrandar también los iconos */
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
                <input type="hidden" name="amount_product" id="amount_product" value="0">

                <div class="col-md-8">
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
                                            <th style="width: 5%"></th>
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

                <div class="col-md-4">
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
                                <div class="input-group">
                                    <input type="time" name="end_time" id="end_time" class="form-control">
                                    <span class="input-group-btn">
                                        <button id="clear-end-time" class="btn btn-default" style="margin: 0px" type="button" title="Limpiar Hora">
                                            <i class="voyager-trash"></i>
                                        </button>
                                    </span>                             
                                </div>
                                <small class="form-text text-muted">Dejar vacío para alquiler sin limite.</small>
                            </div>
                            <div class="form-group" id="monto-group">
                                <label for="amount" id="amount-label">Registrar un adelanto de la sala</label>
                                <input type="number" name="amountSala" id="amount" class="form-control"  min="1" placeholder="0.00" required>
                            </div>


                            <div class="summary-section">
                                <div class="summary-item">
                                    <span>Adelanto de la Sala:</span>
                                    <span class="amount" id="summary-advance">0,00 Bs.</span>
                                </div>
                                <div class="summary-item">
                                    <span>Total Consumo:</span>
                                    <span class="amount" id="summary-consumption">0,00 Bs.</span>
                                </div>
                                <hr>
                                <div class="summary-total">
                                    <strong>Total General:</strong>
                                    <strong class="amount" id="summary-total">0,00 Bs.</strong>
                                </div>
                            </div>
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
                                    <input type="number" name="amount_efectivo" id="amount_efectivo" class="form-control" min="0" placeholder="0.00">
                                </div>
                                <div class="form-group">
                                    <label for="amount_qr">Monto con QR</label>
                                    <input type="number" name="amount_qr" id="amount_qr" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>

                            <div id="calculator" style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                                <div class="form-group">
                                    <label for="amount_received" style="font-weight: bold;">Monto Recibido (Efectivo)</label>
                                    <input type="number" name="amount_received" id="amount_received" class="form-control" min="0" placeholder="0.00">
                                </div>
                                <div class="summary-item" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;">
                                    <strong style="font-size: 1.1rem;">Cambio a devolver:</strong>
                                    <strong class="amount" id="change_due" style="font-size: 1.2rem; color: #28a745;">0.00 Bs.</strong>
                                </div>
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
            const amountLabel = document.getElementById('amount-label');
            const amountInput = document.getElementById('amount');

            document.getElementById('clear-end-time').addEventListener('click', function() {
                endTimeInput.value = '';
                const event = new Event('change');
                endTimeInput.dispatchEvent(event);
            });

            // Asumimos que el precio de la sala está disponible. Ajusta 'price' si el atributo se llama diferente.
            const roomPricePerHour = {{ $room->price ?? 0 }};

            // Set current time for start_time input
            const now = new Date();
            const currentHours = now.getHours().toString().padStart(2, '0');
            const currentMinutes = now.getMinutes().toString().padStart(2, '0');
            startTimeInput.value = `${currentHours}:${currentMinutes}`;

            // Actualiza el tipo de alquiler y la UI basado en la hora de fin
            function updateRentalType() {
                if (endTimeInput.value) {
                    hiddenRentalTypeInput.value = 'tiempo_fijo';
                    amountLabel.textContent = 'Monto del alquiler de la sala';
                } else {
                    hiddenRentalTypeInput.value = 'por_hora';
                    amountLabel.textContent = 'Registrar un adelanto de la sala';
                }
                // Limpiamos el valor para que el usuario siempre lo ingrese manualmente
                amountInput.value = '';
            }

            // Escuchar cambios en los inputs de tiempo
            endTimeInput.addEventListener('change', updateRentalType);
            updateRentalType(); // Llamada inicial para establecer el estado correcto
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
                                    <div style="display: flex; align-items: center;">
                                        <input type="hidden" name="products[${product.id}][id]" value="${product.id}"/>
                                        <img src="${image}" alt="${product.item.name}" style="width: 40px; height: 40px; border-radius: 4px; margin-right: 10px; object-fit: cover;"
                                             onerror="this.onerror=null;this.src='{{ asset('images/default.jpg') }}';">
                                        <div style="font-weight: 500;">${product.item.name}</div>
                                    </div>
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
                                <td class="text-center" style="vertical-align: middle; width: 5%">
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
            $('#amount_product').val(total.toFixed(2));
            updateTotalSummaries();
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

        function updateTotalSummaries() {
            let advance = parseFloat($('#amount').val()) || 0;
            let consumption = parseFloat($('#label-total').text()) || 0;
            let total = advance + consumption;

            $('#summary-advance').text(advance.toFixed(2) + ' Bs.');
            $('#summary-consumption').text(consumption.toFixed(2) + ' Bs.');
            $('#summary-total').text(total.toFixed(2) + ' Bs.');

            // Clear the inputs for "Ambos"
            $('#amount_efectivo').val('');
            $('#amount_qr').val('');

            // Limpiar también el monto recibido para 'efectivo' y recalcular el cambio
            $('#amount_received').val('').trigger('change');
        }

        $(document).ready(function() {
            $('#amount').on('keyup change', updateTotalSummaries);
            updateTotalSummaries();

            $('#payment_method').on('change', function() {
                let paymentMethod = $(this).val();
                $('#payment-details').hide();
                $('#calculator').hide();

                // Remove required and min attributes
                $('#amount_efectivo').prop('required', false).prop('min', '');
                $('#amount_qr').prop('required', false).prop('min', '');

                if (paymentMethod === 'ambos') {
                    $('#payment-details').show();
                    // Add required and min attributes
                    $('#amount_efectivo').prop('required', true).prop('min', 1);
                    $('#amount_qr').prop('required', true).prop('min', 1);
                } else if (paymentMethod === 'efectivo') {
                    $('#calculator').show();
                }
                
                $('#amount_received').val('').trigger('change');
            });

            $('#amount_received, #amount_efectivo').on('keyup change', function() {
                let total = parseFloat($('#summary-total').text().replace(' Bs.', '')) || 0;
                let paymentMethod = $('#payment_method').val();
                let received = 0;

                if (paymentMethod === 'efectivo') {
                    received = parseFloat($('#amount_received').val()) || 0;
                } else if (paymentMethod === 'ambos') {
                    received = parseFloat($('#amount_efectivo').val()) || 0;
                }

                let change = received - total;
                if (change < 0) change = 0;

                $('#change_due').text(change.toFixed(2) + ' Bs.');
            });

            $('#amount_qr').on('keyup change', function() {
                let total = parseFloat($('#summary-total').text().replace(' Bs.', '')) || 0;
                let efectivo = parseFloat($('#amount_efectivo').val()) || 0;
                let qr = parseFloat($('#amount_qr').val()) || 0;

                if ((efectivo + qr) > total) {
                    toastr.warning('El monto ingresado no puede ser mayor al total.', 'Monto excedido', {timeOut: 500});
                    $(this).val('');
                }
            });

            $('form').on('submit', function(e) {
                let paymentMethod = $('#payment_method').val();
                let total = parseFloat($('#summary-total').text().replace(' Bs.', '')) || 0;
                let efectivo = parseFloat($('#amount_efectivo').val()) || 0;
                let qr = parseFloat($('#amount_qr').val()) || 0;

                if (paymentMethod === 'efectivo') {
                    let received = parseFloat($('#amount_received').val()) || 0;
                    if (received < total) {
                        e.preventDefault();
                        toastr.error('El monto recibido no puede ser menor al total a pagar.', 'Error en el pago');
                    }
                } else if (paymentMethod === 'ambos') {
                    if ((efectivo + qr) < total) {
                        e.preventDefault();
                        toastr.error('La suma de los montos en efectivo y QR no puede ser menor al total a pagar.', 'Error en el pago');
                    }
                }
            });
        });
    </script>
@endsection