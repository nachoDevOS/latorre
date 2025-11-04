@extends('voyager::master')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #10b981;
            --danger-color: #ef4444;
            --background-color: #f8f9fa;
            --card-background-color: #ffffff;
            --text-color: #333;
            --text-light-color: #6c757d;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.05);
        }

        /* General Page Style */
        .page-content {
            background-color: var(--background-color);
            font-family: 'Roboto', sans-serif;
        }

        /* Welcome Header */
        .welcome-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: #fff;
            padding: 2rem 1.5rem; /* Reducido para hacerlo más compacto */
            margin-bottom: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
        }
        .welcome-header h1 {
            font-weight: 600; /* Ligeramente menos negrita */
            font-size: 2rem;   /* Tamaño de fuente reducido */
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .welcome-header .lead {
            font-size: 1rem;   /* Tamaño de fuente reducido */
            font-weight: 300;
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
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px var(--shadow-color), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .room-card .panel-heading {
            background-color: var(--card-background-color);
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            display: flex;
            align-items: center;
        }
        .room-card .panel-title {
            font-weight: 600;
            color: var(--text-color);
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
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .room-card .room-type {
            color: var(--text-light-color);
            font-size: 1rem;
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
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--secondary-color);
        }
        .label-danger.status-badge {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
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
            background-color: #4338ca;
            border-color: #4338ca;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            border: 2px dashed #ddd;
            padding: 60px;
            border-radius: 12px;
            background-color: #fff;
            margin-top: 20px;
        }
        .empty-state i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 20px;
        }
        .empty-state h4 {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text-color);
        }
        .empty-state p {
            color: var(--text-light-color);
            font-size: 1.1rem;
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
                    <div class="panel room-card">
                        <div class="panel-heading">
                            @php
                                $icon = 'voyager-controller'; // Icono por defecto
                                if (stripos($room->type, 'pool') !== false || stripos($room->type, 'billar') !== false) {
                                    $icon = 'voyager-dot-3';
                                } elseif (stripos($room->type, 'playstation') !== false || stripos($room->type, 'video') !== false) {
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
                                <p class="room-type">{{ $room->type }}</p>
                                <p>
                                    @if ($room->status)
                                        <span class="label label-success status-badge">Disponible</span>
                                    @else
                                        <span class="label label-danger status-badge">Ocupada</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <a href="#" class="btn btn-primary btn-manage">Gestionar</a>
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
@endsection
