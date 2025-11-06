<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="text-align: center; width: 3%">Id</th>
                    <th style="text-align: center; width: 30%">Nombre</th>
                    <th style="text-align: center">Detalles</th>
                    <th style="text-align: center">Descripción</th>
                    <th style="text-align: center; width: 5%">Disponibilidad</th>
                    <th style="text-align: center; width: 15%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $image = asset('images/default.jpg');
                        if($item->image){
                            $image = asset('storage/' . str_replace('.avif', '', $item->image) . '-cropped.webp');
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center">{{ $item->id }}</td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <img src="{{ $image }}" alt="{{ $item->name }}"
                                    class="image-expandable"
                                    style="width: 70px; height: 70px; border-radius: 8px; margin-right: 10px; object-fit: cover; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div>
                                    <strong>{{ strtoupper($item->name) }}</strong> <br>
                                    <small>Tipo:</small> {{ $item->type?strtoupper($item->type):'SN' }} <br>
                                    {{-- <small>PRESENTACION:</small> {{ $item->presentation?strtoupper($item->presentation->name):'SN' }}  --}}
                                </div>
                            </div>
                        </td>
                        <td> 
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th style="font-size: 10px;">Imagen</th>
                                        <th style="font-size: 10px;">Nombre</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($item->roomDetails as $roomDetail)
                                        <tr>
                                            <td style="text-align: center">
                                                @if ($roomDetail->image)
                                                    <img src="{{ asset('storage/'.$roomDetail->image) }}" class="image-expandable"style="width: 30px; height: 30px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                                @else
                                                    <img src="{{ asset('images/default.jpg') }}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                                @endif
                                            </td>
                                            <td style="font-size: 10px;">{{ $roomDetail->name }}</td>                                            
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No hay detalles registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </td>
                        <td> 
                            <p>{{ $item->observation }}</p>
                        </td>


                        <td style="text-align: center">
                            @if ($item->status=='Disponible')  
                                <label class="label label-success">Disponible</label>
                            @else
                                <label class="label label-danger">Ocupada</label>
                            @endif                        
                        </td>
                        <td class="no-sort no-click bread-actions text-right">
                            @if (auth()->user()->hasPermission('read_rooms'))
                                <a href="{{ route('voyager.rooms.show', ['id' => $item->id]) }}" title="Ver" class="btn btn-sm btn-warning view">
                                    <i class="voyager-eye"></i> 
                                </a>
                            @endif
                            @if (auth()->user()->hasPermission('edit_rooms'))
                                <a href="{{ route('voyager.rooms.edit', ['id' => $item->id]) }}" title="Editar" class="btn btn-sm btn-primary edit">
                                    <i class="voyager-edit"></i>
                                </a>
                            @endif
                            @if (auth()->user()->hasPermission('delete_rooms'))
                                <a href="#" onclick="deleteItem('{{ route('voyager.rooms.destroy', ['id' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                    <i class="voyager-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
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