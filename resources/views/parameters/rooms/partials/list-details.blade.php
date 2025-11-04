<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th style="text-align: center">Nombre</th>
                    <th style="text-align: center">Imagen</th>
                    <th style="text-align: center">Observaci\u00f3n</th>
                    <th style="text-align: center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            @if ($item->image)
                                <img src="{{ asset('storage/'.$item->image) }}" width="50px">
                            @endif
                        </td>
                        <td>{{ $item->observation }}</td>
                        <td class="no-sort no-click bread-actions text-right">
                            <a href="#" onclick="deleteItem('{{ route('voyager.rooms.destroy', ['id' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                <i class="voyager-trash"></i>
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
