<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="text-align: center">ID</th>
                    <th style="text-align: center">Cliente</th>
                    <th style="text-align: center">Tipo de Servicio</th>
                    <th style="text-align: center">Detalles</th>
                    <th style="text-align: center">Monto Total</th>
                    <th style="text-align: center">Fecha</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        @if($item->person)
                            {{ $item->person->first_name }} {{ $item->person->paternal_surname }}
                        @else
                            <span class="text-muted">No especificado</span>
                        @endif
                    </td>
                    <td style="text-align: center">
                        @if ($item->room_id)
                            <label class="label label-info">Alquiler de Sala</label>
                        @else
                            <label class="label label-primary">Venta de Productos</label>
                        @endif
                    </td>
                    <td>
                        @if ($item->room_id && $item->room)
                            Sala: <strong>{{ $item->room->name }}</strong><br>
                        @endif
                        <small>{{ $item->observation }}</small>
                    </td>
                    <td style="text-align: right;">{{ number_format($item->total_amount, 2, ',', '.') }} Bs.</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                    <td style="text-align: center">
                        @if ($item->status == 'Finalizado')
                            <label class="label label-success">Finalizado</label>
                        @elseif($item->status == 'Vigente')
                            <label class="label label-warning">Vigente</label>
                        @else
                            <label class="label label-default">{{ $item->status }}</label>
                        @endif
                    </td>
                    <td style="width: 18%" class="no-sort no-click bread-actions text-right">
                        {{-- El botón de Ver siempre está disponible --}}
                        @if ($item->room_id)
                            <a href="{{ route('services.show', ['id' => $item->room_id]) }}" title="Ver" class="btn btn-sm btn-warning view">
                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                            </a>
                        @else
                             <a href="{{ route('services-sales.show', ['id' => $item->id]) }}" title="Ver" class="btn btn-sm btn-warning view">
                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                            </a>
                        @endif

                        {{-- El botón de Editar solo aparece si es una venta de productos (room_id es null) --}}
                        @if (!$item->room_id && auth()->user()->hasPermission('edit_services-sales'))
                            <a href="{{ route('services-sales.edit', ['id' => $item->id]) }}" title="Editar" class="btn btn-sm btn-primary edit">
                                <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                            </a>
                        @endif

                    </td>
                </tr>
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