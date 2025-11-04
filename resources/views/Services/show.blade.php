@extends('voyager::master')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
            --primary-dark-color: #357ABD;
            --secondary-color: #50E3C2;
            --danger-color: #E94E77;
            --success-color: #50E3C2;
            --warning-color: #F5A623;
            --background-color: #f8f9fa;
            --card-background-color: #ffffff;
            --text-color: #4A4A4A;
            --text-light-color: #6c757d;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.08);
        }

        .page-content {
            background-color: var(--background-color);
            font-family: 'Roboto', sans-serif;
        }

        .panel-title-icon {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .details-panel, .action-panel {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow-color);
            margin-bottom: 25px;
            background-color: var(--card-background-color);
        }

        .panel-heading {
            border-bottom: 1px solid var(--border-color);
            padding: 20px 25px;
            background-color: #fff;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .panel-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .panel-body {
            padding: 25px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 1rem;
        }
        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-item strong {
            color: var(--text-light-color);
            font-weight: 500;
        }
        .detail-item span {
            font-weight: 500;
            color: var(--text-color);
        }

        .status-badge {
            font-size: 0.9em;
            padding: 6px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.8px;
        }
        .label-success.status-badge {
            background-color: rgba(80, 227, 194, 0.2);
            color: #1a9a7a;
        }
        .label-danger.status-badge {
            background-color: rgba(233, 78, 119, 0.15);
            color: #c7355d;
        }

        .timer-display {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--primary-dark-color);
            text-align: center;
            margin: 20px 0;
            letter-spacing: -2px;
        }

        .btn-action {
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-success.btn-action {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #fff;
        }
        .btn-success.btn-action:hover {
            background-color: #45d9b8;
            border-color: #45d9b8;
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(80, 227, 194, 0.3);
        }

        .btn-danger.btn-action {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        .btn-danger.btn-action:hover {
            background-color: #e43a6a;
            border-color: #e43a6a;
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(233, 78, 119, 0.3);
        }

        .form-group label {
            font-weight: 500;
            color: var(--text-light-color);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            height: auto;
            border: 1px solid var(--border-color);
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

    </style>
@endsection

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-7">
                <div class="panel details-panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-info-circled panel-title-icon"></i>Detalles de la Sala</h3>
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
                        <h3 class="panel-title"><i class="voyager-activity panel-title-icon"></i>Acciones de Alquiler</h3>
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
                        <a href="{{ route('voyager.services.index') }}" class="btn btn-default btn-block"><i class="voyager-angle-left"></i> Volver a la lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modal-registerPerson')

@endsection

@section('javascript')
    <script src="{{ asset('js/include/person-select.js') }}"></script>
    <script src="{{ asset('js/include/person-register.js') }}"></script>
    <script>
        // Solo ejecutar el script si la sala está ocupada
        @if ($room->status != 'Disponible')
            document.addEventListener('DOMContentLoaded', function () {
                const timerDisplay = document.getElementById('timer');
                
                // Obtenemos la hora de inicio desde PHP y la convertimos a milisegundos para JavaScript
                const startTime = new Date('{{ $activeRental->start_time }}').getTime();

                function updateTimer() {
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

                // Actualizar el cronómetro cada segundo
                setInterval(updateTimer, 1000);
                updateTimer(); // Llamada inicial para que no espere 1 segundo en mostrarse
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
    </script>
@endsection