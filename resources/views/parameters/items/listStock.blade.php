<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width:5px">N&deg;</th>
                    {{-- <th style="text-align: center; width:20%">Sucursal</th> --}}
                    <th style="text-align: center; width:8%">Lote</th>
                    <th style="text-align: center; width:8%">Cant. Ingresada</th>
                    <th style="text-align: center; width:8%">Stock Disponible</th>
                    <th style="text-align: center; width:8%">Precio Unitario</th>
                    <th style="text-align: center">Detalles</th>                     
                    <th style="text-align: center; width:10%">Estado</th>
                    <th style="text-align: center; width:5%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i=1;
                @endphp 
                @forelse ($data as $value)
                    <tr>
                        <td>{{ $i }}</td>
                        {{-- <td>{{ $value->branch->name }} <br>
                            @if ($value->incomeDetail_id == null)
                                <small>Ingreso Manual</small>
                            @else
                                <small>Ingreso Mediante Adquisición</small>
                            @endif
                        </td> --}}
                        <td>
                            {{$value->lote?$value->lote:'SN'}}
                        </td>
                        <td style="text-align: right">     
                            {{number_format($value->quantity, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right">    
                            {{number_format($value->stock, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right">    
                            Bs. {{number_format($value->priceSale, 2, ',', '.')}}
                        </td>
                                                
                        <td>                                                    
                            {{$value->observation}}
                        </td>
                        <td style="text-align: center">
                            @if ($value->stock==0)
                                <label class="label label-danger" style="color: white !important; font-weight: bold;">Stock Agotado</label> 
                            @else
                                <label class="label label-success" style="color: white !important; font-weight: bold;">Stock Disponible</label> 
                            @endif
                        </td>

                        <td style="text-align: center">                                                   
                            @if ($value->deleted_at)
                                <span style="color: red">Eliminado</span>
                            @else
                                @if ($value->quantity == $value->stock)
                                    <a href="#" onclick="deleteItem('{{ route('items-stock.destroy', ['id' => 1, 'stock'=>$value->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                        <i class="voyager-trash"></i>
                                    </a>                         
                                @endif                                                            
                            @endif
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @empty
                    <tr>
                        <td colspan="8">
                            <h5 class="text-center" style="margin-top: 50px">
                                <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                                <br><br>
                                No hay resultados
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x:auto">
        @if(count($data)>0)
            <p class="text-muted">Mostrando del {{$data->firstItem()}} al {{$data->lastItem()}} de {{$data->total()}} registros.</p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x:auto">
        <nav class="text-right">
            {{ $data->links() }}
        </nav>
    </div>
</div>

<script>
   
   var page = "{{ request('page') }}";
    $(document).ready(function(){
        $('.page-link').click(function(e){
            e.preventDefault();
            let link = $(this).attr('href');
            if(link){
                page = link.split('=')[1];
                list(page);
            }
        });
    });
</script>