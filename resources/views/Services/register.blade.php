@extends('voyager::master')

@section('css')
    {{-- Enlazamos la hoja de estilos externa --}}
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
@endsection

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-7">
                <div class="panel details-panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-info-circled panel-title-icon"></i> Detalles de la Sala</h3>
                    </div>
                    <div class="panel-body">
                        <div class="detail-item"><strong>Nombre:</strong> <span>{{ $room->name }}</span></div>
                        <div class="detail-item"><strong>Tipo:</strong> <span>{{ $room->type }}</span></div>
                        <div class="detail-item">
                            <strong>Estado:</strong>
                            @if ($room->status == 'Disponible')
                                <span class="label label-success status-badge">Disponible</span>
                            @else
                                <span class="label label-danger status-badge">Ocupada</span>
                            @endif
                        </div>
                        <div class="detail-item"><strong>Observación:</strong> <span>{{ $room->observation ?: 'Ninguna.' }}</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel action-panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-activity panel-title-icon"></i> Acciones de Alquiler</h3>
                    </div>
                    <div class="panel-body">
                        @if ($room->status == 'Disponible')
                            {{-- FORMULARIO PARA INICIAR ALQUILER --}}
                            <h4>Iniciar Nuevo Alquiler</h4>
                            <p>La sala está libre. Completa los datos para iniciar un nuevo servicio.</p>
                            <form action="{{ route('services.rental.start') }}" method="POST">
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                <div class="form-group">
                                        <label for="person_id">Cliente</label>
                                        <div class="input-group">
                                            <select name="person_id" id="select-person_id" class="form-control"></select>
                                            <span class="input-group-btn">
                                                <button id="trash-person" class="btn btn-default" title="Quitar Cliente"
                                                    style="margin: 0px" type="button">
                                                    <i class="voyager-trash"></i>
                                                </button>
                                                <button class="btn btn-primary" title="Nuevo cliente"
                                                    data-target="#modal-create-person" data-toggle="modal" style="margin: 0px"
                                                    type="button">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
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
                                    <small class="form-text text-muted">Dejar vacío para alquiler por hora (sin tiempo fijo).</small>
                                </div>
                                {{-- Campo oculto para el tipo de alquiler, se actualizará con JavaScript --}}
                                <input type="hidden" name="rental_type" id="hidden_rental_type" value="por_hora">

                                {{-- SECCIÓN PARA AÑADIR PRODUCTOS --}}
                                <fieldset class="products-fieldset">
                                    <legend><i class="voyager-basket"></i> Añadir Productos (Opcional)</legend>
                                    <div class="form-group">
                                            <label for="product_id">Buscar producto</label>
                                            <select class="form-control" id="select-product_id"></select>
                                        </div>
                                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                            <table id="dataTable" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30px">N&deg;</th>
                                                        <th>Detalles</th>
                                                        <th style="text-align: center; width:15%">Precio</th>
                                                        <th style="text-align: center; width:12%">Cantidad</th>
                                                        <th style="width: 30px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table-body">
                                                    <tr id="tr-empty">
                                                        <td colspan="5">
                                                            <div class="empty-cart-message">
                                                                <i class="glyphicon glyphicon-shopping-cart"></i>
                                                                <h4 class="text-muted">
                                                                Lista de venta vacía
                                                                </h4>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                </fieldset>
                                {{-- FIN SECCIÓN PRODUCTOS --}}

                                <button type="submit" class="btn btn-success btn-action"><i class="voyager-play"></i> Iniciar Alquiler</button>
                            </form>
                        @else
                            {{-- VISTA DE ALQUILER ACTIVO --}}
                            <h4>Alquiler en Curso</h4>
                            <p>Esta sala se encuentra actualmente ocupada.</p>
                            
                            <div class="detail-item">
                                <strong>Cliente:</strong> 
                                <span>{{ $activeRental->customer_name ?: 'No especificado' }}</span>
                            </div>

                            <div class="detail-item">
                                <strong>Tipo de Alquiler:</strong> 
                                <span>{{ $activeRental->rental_type == 'por_hora' ? 'Por Hora' : 'Tiempo Fijo' }}</span>
                            </div>

                            



                            <div class="detail-item">
                                <strong>Inicio:</strong>
                                <span>{{ \Carbon\Carbon::parse($activeRental->start_time)->format('h:i A') }}</span>
                            </div>

                            <div class="text-center">
                                <label><strong>Tiempo Transcurrido</strong></label>
                                <div id="timer" class="timer-display">00:00:00</div>
                            </div>

                            <form action="#" method="POST"> {{-- TODO: Cambiar a la ruta correcta --}}
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                <button type="submit" class="btn btn-danger btn-action"><i class="voyager-stop"></i> Finalizar y Cobrar</button>
                            </form>
                        @endif
                        <hr>
                        <a href="{{ route('services.index') }}" class="btn btn-default btn-block"><i class="voyager-angle-left"></i> Volver a la lista</a>
                    </div>
                </div>
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
                                <tr class="tr-item" id="tr-item-${product.id}">
                                    <td class="td-item"></td>
                                    <td>
                                        <input type="hidden" name="products[${product.id}][id]" value="${product.id}"/>
                                        <div style="display: flex; align-items: center;">
                                            <div style="margin-right: 10px; flex-shrink: 0;">
                                                <img src="${image}" width="50px" style="border-radius: 4px;"/>
                                            </div>
                                            <div style="line-height: 1.2;">
                                                <div style="font-size: 13px; font-weight: bold;">${product.item.name}</div>
                                                <small><b>Marca:</b> ${product.item.brand.name}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="100px" style="vertical-align: middle;">
                                        <input type="number" name="products[${product.id}][price]" step="0.1" min="0.1" style="text-align: right" class="form-control" value="${product.priceSale}" required/>
                                    </td>
                                    <td width="100px" style="vertical-align: middle;">
                                        <input type="number" name="products[${product.id}][quantity]" step="1" min="1" style="text-align: right" class="form-control" value="1" max="${product.stock}" required/>
                                    </td>
                                    <td width="50px" class="text-right" style="vertical-align: middle;">
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
                var length = 0;
                $(".td-item").each(function(index) {
                    $(this).text(index + 1);
                    length++;
                });
                $('#tr-empty').css('display', length > 0 ? 'none' : 'table-row');
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