@extends('voyager::master')

@section('page_title', 'Ver Accesorios / Items')

@section('page_header')
    <h1 class="page-title">
        <i class="fa-brands fa-steam-symbol"></i> Accesorios / Items &nbsp;
        <a href="{{ route('voyager.items.index') }}" class="btn btn-warning">
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
                                <h3 class="panel-title">Categoría</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $item->category?strtoupper($item->category->name):'SN' }} </p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Productos/ Items</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $item->name }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Observación / Descripción</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{$item->observation??'Sin Detalles'}}</small></p>
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
                                    Detalles del Inventario
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
                            <div class="col-sm-12">
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
        
        <div class="row">
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
        </div>
    </div>


    <form action="{{ route('items-stock.store', ['id' => $item->id]) }}" class="form-edit-add" method="POST">
        <div class="modal fade" data-backdrop="static" id="modal-register-stock" role="dialog">
            <div class="modal-dialog modal-success">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" style="color: #ffffff !important"><i class="voyager-plus" ></i> Registrar Stock</h4>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            {{-- <div class="form-group col-md-8">
                                <label for="branch_id">Surcursal</label>
                                <select name="branch_id" id="branch_id" class="form-control select2" required>
                                    <option value="" selected disabled>--Seleccione una opción--</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="form-group col-md-12">
                                <label for="lote">Lote</label>
                                <input style="text-align: right" type="text" name="lote" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="full_name">Cantidad</label>
                                <input style="text-align: right" type="number" step="1" min="1" name="quantity" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="full_name">P. Compra</label>
                                <input style="text-align: right" type="number" step="1" min="0" name="pricePurchase" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="full_name">P. Unitario</label>
                                <input style="text-align: right" type="number" step="1" min="1" name="priceSale" class="form-control" required>
                            </div>
                            {{-- <div class="form-group col-md-3">
                                <label for="full_name">P. al Por Mayor</label>
                                <input style="text-align: right" type="number" step="1" min="1" name="priceWhole" class="form-control" required>
                            </div> --}}
                        </div>    
                        <div class="form-group">
                            <label for="observation">Observación / Detalles</label>
                            <textarea name="observation" class="form-control" rows="3"></textarea>
                        </div>

                        <label class="checkbox-inline">
                            <input type="checkbox" required>Confirmar..!
                        </label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success btn-form-submit btn-submit">Sí, guardar</button>
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

        var countPage = 10, order = 'id', typeOrder = 'desc';
        var countPageSales = 10;
        $(document).ready(() => {
            list();
            listSales();

            $('#status').change(function(){
                list();
            });
            $('#branch').change(function(){
                list();
            });
            
            $('#input-search').on('keyup', function(e){
                if(e.keyCode == 13) {
                    list();
                }
            });

            $('#select-paginate').change(function(){
                countPage = $(this).val();               
                list();
            });

            $('#select-paginate-sales').change(function(){
                countPageSales = $(this).val();               
                listSales();
            });
        });

        function list(page = 1){
            $('#div-results').loading({message: 'Cargando...'});
            let url = '{{ url("admin/items/".$item->id."/stock/ajax/list") }}';
            let status =$("#status").val();            
            let branch =$("#branch").val();          

            $.ajax({
                url: `${url}?paginate=${countPage}&page=${page}&status=${status}&branch=${branch}`,

                type: 'get',
                
                success: function(result){
                    $("#div-results").html(result);
                    $('#div-results').loading('toggle');
                }
            });
        }

        function listSales(page = 1){
            $('#div-results-sales').loading({message: 'Cargando...'});
            let url = '{{ url("admin/items/".$item->id."/sales/ajax/list") }}';

            $.ajax({
                url: `${url}?paginate=${countPageSales}&page=${page}`,
                type: 'get',
                success: function(result){
                    $("#div-results-sales").html(result);
                    $('#div-results-sales').loading('toggle');
                }
            });
        }


        $(document).ready(function(){   
            $('.form-submit').submit(function(e){
                $('.btn-form-submit').attr('disabled', true);
                $('.btn-form-submit').val('Guardando...');
            });

            $('#delete_form').submit(function(e){
                $('.btn-form-delete').attr('disabled', true);
                $('.btn-form-delete').val('Eliminando...');
            });
        });

        function deleteItem(url){
            $('#delete_form').attr('action', url);
        }
    </script>
    
@stop