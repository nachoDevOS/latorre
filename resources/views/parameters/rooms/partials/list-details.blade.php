<div class="col-md-12">
    <div class="panel panel-bordered">
        <div class="panel-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th style="text-align: center">Imagen</th>
                            <th>Detalle</th>
                            <th class="actions text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td style="text-align: center">
                                    @if ($item->image)
                                        <img src="{{ asset('storage/'.$item->image) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                    @else
                                        <img src="{{ asset('images/default.jpg') }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                    @endif
                                </td>
                                <td>{{ $item->observation }}</td>
                                <td class="no-sort no-click bread-actions text-right">
                                    <a href="javascript:;" title="Eliminar" class="btn btn-sm btn-danger pull-right delete" data-id="{{ $item->id }}" id="delete-{{ $item->id }}" data-toggle="modal" data-target="#modal-delete" onclick="deleteItem('{{ route('rooms-detail.destroy', ['id' => $item->id]) }}')">
                                        <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Eliminar</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay datos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
