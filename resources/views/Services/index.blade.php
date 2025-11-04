@extends('voyager::master')

@section('page_title', 'Viendo Servicios')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-8" style="padding: 0px">
                            <h1 class="page-title">
                                <i class="fa-solid fa-gamepad"></i> Salas de Juegos
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px">
                            <a href="{{ route('voyager.rooms.index') }}" class="btn btn-primary">
                                <i class="fa-solid fa-eye"></i> <span>Ver Salas</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

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
    </div>
@stop
