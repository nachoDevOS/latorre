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
            <div class="col-md-5">
                @if ($room->status == 'Disponible')
                    {{-- PANEL PARA INICIAR UN NUEVO ALQUILER --}}
                    <div class="panel action-panel">
                        <div class="panel-heading"><h3 class="panel-title"><i class="voyager-play"></i> Iniciar Nuevo Alquiler</h3></div>
                        <div class="panel-body">
                            <form action="{{ route('services.rental.start') }}" method="POST">
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                <input type="hidden" name="rental_type" id="hidden_rental_type" value="por_hora">

                                <!-- Pestañas de Navegación -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#alquiler">Alquiler</a></li>
                                    <li><a data-toggle="tab" href="#consumo">Consumo</a></li>
                                </ul>

                                <!-- Contenido de las Pestañas -->
                                <div class="tab-content">
                                    <!-- Pestaña Alquiler -->
                                    <div id="alquiler" class="tab-pane fade in active">
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
                                    </div>
                                    <!-- Pestaña Consumo -->
                                    <div id="consumo" class="tab-pane fade">
                                        <div class="form-group">
                                            <label for="product_id">Buscar y añadir producto</label>
                                            <select class="form-control" id="select-product_id"></select>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="dataTable" class="table table-hover table-products">
                                                <thead>
                                                    <tr>
                                                        <th>Producto</th>
                                                        <th class="text-center" style="width: 100px;">Precio</th>
                                                        <th class="text-center" style="width: 100px;">Cantidad</th>
                                                        <th style="width: 50px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table-body">
                                                    <tr id="tr-empty">
                                                        <td colspan="4" class="empty-cart-message">
                                                            <i class="voyager-basket"></i>
                                                            <p>El carrito de consumo está vacío.</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success btn-action"><i class="voyager-play"></i> Iniciar Alquiler</button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- PANEL PARA UN ALQUILER ACTIVO --}}
                    <div class="panel action-panel">
                        <div class="panel-heading"><h3 class="panel-title"><i class="voyager-activity"></i> Alquiler en Curso</h3></div>
                        <div class="panel-body">
                            <div class="active-rental-info">
                                <div class="info-block">
                                    <small>Cliente</small>
                                    <p><i class="voyager-person"></i> {{ $activeRental->customer_name ?: 'No especificado' }}</p>
                                </div>
                                <div class="info-block">
                                    <small>Hora de Inicio</small>
                                    <p><i class="voyager-watch"></i> {{ \Carbon\Carbon::parse($activeRental->start_time)->format('h:i A') }}</p>
                                </div>
                            </div>

                            <div class="timer-container">
                                <label>Tiempo en Sala</label>
                                <div id="timer" class="timer-display">00:00:00</div>
                            </div>

                            {{-- Aquí podrías mostrar los productos consumidos si los guardas en la BD --}}
                            <div class="consumed-products">
                                <h5><i class="voyager-basket"></i> Consumo Registrado</h5>
                                <ul class="consumed-list">
                                    {{-- Ejemplo, esto debería venir de la base de datos --}}
                                    <li><span class="product-name">Gaseosa 2L</span> <span class="product-details">1 x 15.00 Bs.</span></li>
                                    <li><span class="product-name">Papas Fritas</span> <span class="product-details">2 x 10.00 Bs.</span></li>
                                    <li class="text-center text-muted" style="display: none;">No hay productos consumidos.</li>
                                </ul>
                            </div>

                            <form action="#" method="POST"> {{-- TODO: Cambiar a la ruta correcta --}}
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                <button type="submit" class="btn btn-danger btn-action"><i class="voyager-stop"></i> Finalizar y Cobrar</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Botón para volver, ahora fuera de los paneles condicionales para estar siempre visible --}}
                <a href="{{ route('services.index') }}" class="btn-back"><i class="voyager-angle-left"></i> Volver a la lista de salas</a>
            </div>
        </div>
    </div>

    @include('partials.modal-registerPerson')

@endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.min.js"></script>
    <script src="{{ asset('vendor/tippy/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/tippy/tippy-bundle.umd.min.js') }}"></script>

    <script src="{{ asset('js/include/person-select.js') }}"></script>
    <script src="{{ asset('js/include/person-register.js') }}"></script>
    <script>
        // Solo ejecutar el script si la sala está ocupada
        @if ($room->status == 'Ocupada' && $activeRental)
            document.addEventListener('DOMContentLoaded', function () {
                const timerDisplay = document.getElementById('timer');
                
                // Obtenemos la hora de inicio desde PHP y la convertimos a milisegundos para JavaScript
                const startTime = new Date('{{ $activeRental->start_time }}').getTime();

                function updateTimer() {
                    if (timerDisplay) {
                        const now = new Date().getTime();
                        const elapsedTime = now - startTime;

                        const hours = Math.floor((elapsedTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);

                        // Formatear para que siempre tengan dos dígitos
                        const formattedHours = hours.toString().padStart(2, '0');
                        const formattedMinutes = minutes.toString().padStart(2, '0');
                        const formattedSeconds = seconds.toString().padStart(2, '0');

                        timerDisplay.textContent = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                    }
                }

                if (timerDisplay) {
                    // Actualizar el cronómetro cada segundo
                    const timerInterval = setInterval(updateTimer, 1000);
                    updateTimer(); // Llamada inicial para que no espere 1 segundo en mostrarse
                }
            });
        @endif


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
        @if ($room->status == 'Disponible')
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
                        url: "{{ url('admin/sales/item/stock/ajax') }}",
                        processResults: function(data) {
                            return { results: data };
                        },
                        cache: true
                    },
                    templateResult: formatResultProducts,
                    templateSelection: (opt) => {
                        productSelected = opt;
                        return productSelected.id;
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
                            $('#table-body').append(`
                                <tr class="tr-item" id="tr-item-${product.id}" data-id="${product.id}">
                                    <td>
                                        <input type="hidden" name="products[${product.id}][id]" value="${product.id}"/>
                                        <div style="font-weight: 500;">${product.item.name}</div>
                                        <small>${product.item.brand.name}</small>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="number" name="products[${product.id}][price]" step="0.1" min="0.1" class="form-control input-sm text-right" value="${product.priceSale}" required/>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <div class="input-group" style="max-width: 100px; margin: auto;">
                                            <input type="number" name="products[${product.id}][quantity]" step="1" min="1" class="form-control input-sm text-right" value="1" max="${product.stock}" required/>
                                        </div>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <button type="button" onclick="removeTr(${product.id})" class="btn btn-link"><i class="voyager-trash text-danger"></i></button>
                                    </td>
                                </tr>
                            `);
                            setNumber();
                            toastr.success(`+1 ${product.item.name}`, 'Producto agregado');
                        } else {
                            toastr.info('El producto ya está agregado', 'Información');
                        }
                        $('#select-product_id').val('').trigger('change');
                    }
                });
            });

            function setNumber() {
                $('#tr-empty').toggle($('.tr-item').length === 0);
            }

            function removeTr(id) {
                $(`#tr-item-${id}`).remove();
                setNumber();
                toastr.warning('Producto eliminado de la lista', 'Eliminado');
            }

            function formatResultProducts(option) {
                if (option.loading) return '<span class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</span>';
                let image = "{{ asset('images/default.jpg') }}";
                if (option.item?.image) image = `{{ asset('storage') }}/${option.item.image.replace(/\.([^.]+)$/, '-cropped.webp')}`;
                const fallbackImage = '{{ asset('images/default.jpg') }}';
                return $(`<div style="display: flex; align-items: center; padding: 5px;"><img src="${image}" style="width: 50px; height: 50px; border-radius: 4px; margin-right: 10px; object-fit: cover;" onerror="this.onerror=null;this.src='${fallbackImage}';"/><div style="line-height: 1.2;"><div style="font-weight: bold;">${option.item.name}</div><small><b>Stock:</b> ${option.stock} Unid. | <b>Precio:</b> ${option.priceSale} Bs.</small></div></div>`);
            }
        @endif
    </script>
@endsection