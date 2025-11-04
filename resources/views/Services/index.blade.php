@extends('voyager::master')

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <h4 class="text-center">Bienvenido al módulo de servicios</h4>
                            <p class="text-center">Desde aquí podrás gestionar las salas de juegos y otros servicios.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">Salas de juegos</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            @forelse ($rooms as $room)
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">{{ $room->name }}</h3>
                                        </div>
                                        <div class="panel-body">
                                            <p>Tipo: {{ $room->type }}</p>
                                            <p>Estado: 
                                                @if ($room->status)
                                                    <label class="label label-success">Disponible</label>
                                                @else
                                                    <label class="label label-danger">Ocupada</label>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <h4 class="text-center">No hay salas de juegos registradas</h4>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
