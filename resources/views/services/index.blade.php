@extends('voyager::master')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
            /* Un azul más brillante y amigable */
            --primary-dark-color: #357ABD;
            --secondary-color: #50E3C2;
            /* Un verde menta vibrante */
            --danger-color: #E94E77;
            /* Un rosa/rojo para contraste */
            --background-color: #f8f9fa;
            --card-background-color: #ffffff;
            --text-color: #4A4A4A;
            --text-light-color: #6c757d;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.08);
        }

        /* General Page Style */
        .page-content {
            background-color: var(--background-color);
            font-family: 'Roboto', sans-serif;
            color: var(--text-color);
        }

        /* Welcome Header */
        .welcome-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            padding: 2.5rem 1.5rem;
            margin-bottom: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
        }

        .welcome-header h1 {
            font-weight: 600;
            /* Ligeramente menos negrita */
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-header .lead {
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.9;
        }

        /* Section Title */
        .section-title {
            font-size: 1.75rem;
            font-weight: 500;
            margin-bottom: 25px;
            color: var(--text-color);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .panel-title-icon {
            margin-right: 10px;
            color: var(--primary-color);
        }

        /* Room Card Style */
        .room-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow-color);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
            display: flex;
            /* Flexbox para alinear contenido */
            flex-direction: column;
            height: 100%;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px var(--shadow-color), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .room-card .panel-heading {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            display: flex;
            align-items: center;
        }

        .room-card .panel-title {
            font-weight: 600;
            color: var(--primary-dark-color);
            font-size: 1.25rem;
            margin: 0;
        }

        .room-card .room-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            color: var(--primary-color);
        }

        .room-card .panel-body {
            padding: 25px;
            text-align: center;
            /* Centrado de contenido */
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .room-card .room-type {
            color: var(--text-light-color);
            font-size: 0.95rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .room-card .status-badge {
            font-size: 0.9em;
            padding: 6px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.8px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .label-success.status-badge {
            background-color: rgba(80, 227, 194, 0.2);
            color: #1a9a7a;
        }

        .label-danger.status-badge {
            background-color: rgba(233, 78, 119, 0.15);
            color: #c7355d;
        }

        /* Solid badges for cards with background images for better readability */
        .room-card-bg .label-success.status-badge {
            background-color: var(--secondary-color);
            color: #fff;
            text-shadow: none;
            /* Remove shadow from badge text if it inherits */
        }

        .room-card-bg .label-danger.status-badge {
            background-color: var(--danger-color);
            color: #fff;
            text-shadow: none;
            /* Remove shadow from badge text if it inherits */
        }

        .room-card .btn-manage {
            margin-top: 20px;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .room-card .btn-manage:hover {
            background-color: var(--primary-dark-color);
            border-color: var(--primary-dark-color);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
            transform: translateY(-2px);
        }

        .room-card-bg {
            position: relative;
            color: #fff;
            /* Sombra de texto más pronunciada para mejorar la legibilidad en cualquier fondo */
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
            background-color: #2d3748;
            /* Fondo oscuro base para transparencia */
        }

        .room-bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0.7;
            /* Imagen un poco transparente */
            transition: transform 0.5s ease;
            z-index: 0;
        }

        .room-card:hover .room-bg-layer {
            transform: scale(1.1);
        }

        .room-card-bg::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            /* Gradiente para oscurecer áreas clave y mejorar contraste del texto sin tapar toda la imagen */
            background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.2) 40%, rgba(0, 0, 0, 0.2) 60%, rgba(0, 0, 0, 0.85) 100%);
            border-radius: 12px;
            z-index: 1;
        }

        .room-card-bg .panel-heading,
        .room-card-bg .panel-body {
            position: relative;
            z-index: 2;
            background-color: transparent;
            border-bottom: none;
        }

        .room-card-bg .panel-title {
            color: #333;
            /* Borde blanco para mejorar legibilidad */
            text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
        }

        .room-card-bg .room-icon {
            color: #333;
            text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
        }

        .room-card-bg .room-type {
            color: #333;
            font-weight: 600;
            text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
        }

        /* Estilo Glassmorphism Premium para el info del servicio */
        .service-info-glass {
            background: rgba(255, 255, 255, 0.25);
            /* Vidrio claro */
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            /* Borde sutil brillante */
            padding: 12px;
            border-radius: 15px;
            margin-top: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            /* Sombra profunda para dar elevación */
        }

        .service-info-glass p {
            color: #333;
            font-weight: 700;
            margin: 2px 0;
        }

        .timer-text {
            color: #c0392b;
            /* Rojo intenso para urgencia */
            font-family: 'Courier New', Courier, monospace;
            /* Fuente tipo digital */
            letter-spacing: 1px;
        }

        /* Empty State */
        .empty-state {
            border: 2px dashed var(--border-color);
            padding: 60px;
            border-radius: 12px;
            background-color: var(--card-background-color);
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .empty-state:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px var(--shadow-color);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            opacity: 0.6;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            font-weight: 500;
            color: var(--text-color);
        }

        .empty-state p {
            color: var(--text-light-color);
            font-size: 1.1rem;
        }

        .blinking {
            animation: blinking-background 1s infinite;
        }

        @keyframes blinking-background {
            0% {
                background-color: rgba(255, 0, 0, 0.5);
                color: white;
            }

            50% {
                background-color: rgba(255, 255, 255, 0.5);
                color: black;
            }

            100% {
                background-color: rgba(255, 0, 0, 0.5);
                color: white;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="welcome-header">
                    <h1><i class="voyager-rocket panel-title-icon"></i>Bienvenido al Módulo de Servicios</h1>
                    <p class="lead">Desde aquí podrás gestionar las salas de juegos y otros servicios.</p>
                </div>
            </div>
        </div>

        <h2 class="section-title"><i class="voyager-games panel-title-icon"></i>Salas de Juegos</h2>
        <div class="row">
            @forelse ($rooms as $room)
                <div class="col-md-3 col-sm-6">
                    @php
                        $imgUrl = $room->image ? asset('storage/' . $room->image) : asset('images/rooms/default.jpg');
                        $bgClass = 'room-card-bg';
                    @endphp
                    <div class="panel room-card {{ $bgClass }}">
                        <div class="room-bg-layer" style="background-image: url('{{ $imgUrl }}');"></div>
                        <div class="panel-heading">
                            @php
                                $icon = 'voyager-controller'; // Icono por defecto
                                if (
                                    stripos($room->type, 'pool') !== false ||
                                    stripos($room->type, 'billar') !== false
                                ) {
                                    $icon = 'voyager-dot-3';
                                } elseif (
                                    stripos($room->type, 'playstation') !== false ||
                                    stripos($room->type, 'video') !== false
                                ) {
                                    $icon = 'voyager-play';
                                } elseif (stripos($room->type, 'VIP') !== false) {
                                    $icon = 'voyager-star';
                                }
                            @endphp
                            <i class="{{ $icon }} room-icon"></i>
                            <h3 class="panel-title">{{ $room->name }}</h3>
                        </div>
                        <div class="panel-body">
                            <div>
                                <p class="room-type" style="font-size: 18px">{{ $room->type }}</p>

                                <div style="min-height: 100px;">
                                    @if ($room->status == 'Disponible')
                                        <span class="label label-success status-badge">Disponible</span>
                                    @else
                                        <span class="label label-danger status-badge">Ocupada</span>
                                        @if ($room->service)
                                            @php
                                                $lastServiceTime = $room->service->serviceTimes->last();
                                            @endphp
                                            <div id="service-info-{{ $room->id }}" class="service-info-glass"
                                                data-start-time="{{ date('Y-m-d H:i:s', strtotime($room->service->start_time)) }}"
                                                @if ($lastServiceTime && $lastServiceTime->end_time) data-end-time="{{ date('Y-m-d H:i:s', strtotime($lastServiceTime->end_time)) }}" @endif>
                                                <p style="font-size: 18px;" class="room-type">
                                                    <i class="voyager-watch"></i> Inicio:
                                                    <strong>{{ date('d/m/y h:i A', strtotime($room->service->start_time)) }}</strong>
                                                </p>
                                                @if ($lastServiceTime && $lastServiceTime->end_time)
                                                    <p style="font-size: 18px;" class="room-type">
                                                        <i class="voyager-alarm-clock"></i> Fin:
                                                        <strong>{{ date('d/m/y h:i A', strtotime($lastServiceTime->end_time)) }}</strong>
                                                    </p>
                                                @endif
                                                <div id="timer-{{ $room->id }}" class="timer-text"
                                                    style="font-size: 22px; font-weight: 900; margin-top: 5px;"></div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('services.show', $room->id) }}" class="btn btn-primary btn-manage">
                                    @if ($room->status == 'Disponible')
                                        Gestionar Sala
                                    @else
                                        Ver Detalles
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <div class="text-center empty-state">
                        <i class="voyager-joystick"></i>
                        <h4>No hay salas de juegos registradas.</h4>
                        <p>Puedes agregar nuevas salas desde el panel de administración.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@stop


@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(function() {
                @foreach ($rooms as $room)
                    @if ($room->status != 'Disponible' && $room->service)
                        var timerElement = document.getElementById('timer-{{ $room->id }}');
                        if (timerElement && !timerElement.hasAttribute('data-stopped')) {
                            var serviceInfoDiv = document.getElementById(
                                'service-info-{{ $room->id }}');
                            var startTimeString = serviceInfoDiv.dataset.startTime;
                            var endTimeString = serviceInfoDiv.dataset.endTime;

                            var now = new Date();
                            var startTime = new Date(startTimeString.replace(/-/g,
                            '/')); // Reemplazar guiones para compatibilidad

                            var targetTime = now;
                            var shouldStop = false;

                            if (endTimeString) {
                                var endTime = new Date(endTimeString.replace(/-/g, '/'));
                                if (now > endTime) {
                                    targetTime = endTime;
                                    shouldStop = true;
                                    if (serviceInfoDiv) serviceInfoDiv.classList.add('blinking');
                                }
                            }

                            var elapsedTime = targetTime - startTime;
                            if (elapsedTime < 0) elapsedTime = 0;

                            var hours = Math.floor(elapsedTime / (1000 * 60 * 60));
                            elapsedTime -= hours * (1000 * 60 * 60);
                            var minutes = Math.floor(elapsedTime / (1000 * 60));
                            elapsedTime -= minutes * (1000 * 60);
                            var seconds = Math.floor(elapsedTime / 1000);

                            timerElement.innerText = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-
                                2) + ':' + ('0' + seconds).slice(-2);

                            if (shouldStop) {
                                timerElement.setAttribute('data-stopped', 'true');
                            }
                        }
                    @endif
                @endforeach
            }, 1000);
        });
    </script>
@endsection
