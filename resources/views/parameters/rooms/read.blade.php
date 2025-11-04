@extends('voyager::master')

@section('page_title', 'Ver Sala de juegos')

@section('page_header')
    <h1 class="page-title">
        <i class="fa-solid fa-gamepad"></i> Salas De Juegos &nbsp;
        <a href="{{ route('voyager.rooms.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Volver a la lista
        </a> 
    </h1>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Tipo</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $room->type?strtoupper($room->type):'SN' }} </p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Nombre</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $room->name }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Observación / Descripción</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{$item->room??'Sin Detalles'}}</small></p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                    </div>                    
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4>
                                    Detalles de la Sala
                                </h4>
                            </div>
                            <div class="col-sm-6 text-right">
                                @if (auth()->user()->hasPermission('browse_items'))
                                    <button class="btn btn-success"                                      
                                        data-target="#modal-register-stock" data-toggle="modal" data-toggle="modal" style="margin: 0px">
                                        <i class="fa-solid fa-plus"></i> Agregar                                  
                                    </button>       
                                @endif                         
                            </div>  
                        </div>

                        <div class="row">
                            <div class="col-sm-7">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Mostrar <select id="select-paginate" class="form-control input-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> registros</label>
                                </div>
                            </div>
                           
                        </div>
                        <div class="row" id="div-results" style="min-height: 120px"></div>

                    </div>
                </div>
            </div>
        </div>
        
        {{-- <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <h4>
                                    Historial de Ventas
                                </h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-7">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Mostrar <select id="select-paginate-sales" class="form-control input-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> registros</label>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="div-results-sales" style="min-height: 120px"></div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>


    <form action="{{ route('rooms-detail.store', ['id' => $room->id]) }}" class="form-submit" method="POST" enctype="multipart/form-data">
        <div class="modal fade" data-backdrop="static" id="modal-register-stock" role="dialog">
            <div class="modal-dialog modal-success">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" style="color: #ffffff !important"><i class="voyager-plus" ></i> Registrar Detalle</h4>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name">Nombre</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="image">Imagen</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="observation">Detalle (opcional)</label>
                                <textarea name="observation" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-success btn-form-submit" value="Guardar">
                    </div>
                </div>
            </div>
        </div>
    </form>
    @include('partials.modal-delete')
    
@stop

@section('css')
    <style>

    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/btn-submit.js') }}"></script>

    <script>
        var page = 1;
        var paginate = 10;

        $(document).ready(() => {
            listDetails();
            $('#select-paginate').change(function(){
                paginate = $(this).val();
                listDetails();
            });
        });

        function listDetails(){
            let url = '{{ url("admin/rooms/".$room->id."/details/ajax/list") }}';
            $.ajax({
                url: `${url}?page=${page}&paginate=${paginate}`,
                type: 'get',
                success: function(response){
                    $('#div-results').html(response);
                }
            });
        }

        function deleteItem(url){
            $('#delete_form').attr('action', url);
        }
    </script>
    
@stop