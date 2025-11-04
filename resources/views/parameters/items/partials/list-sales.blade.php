<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nro de Venta</th>
                    <th>Cliente</th>
                    <th style="text-align: right">Cantidad</th>
                    <th style="text-align: right">Precio</th>
                    <th style="text-align: right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $item)
                    <tr>
                        <td>{{ date('d-m-Y H:i', strtotime($item->created_at)) }}</td>
                        <td>{{ $item->sale->id }}</td>
                        <td>{{ $item->sale->person->name ?? 'No definido' }}</td>
                        <td style="text-align: right">{{ $item->quantity }}</td>
                        <td style="text-align: right">{{ $item->price }}</td>
                        <td style="text-align: right">{{ $item->quantity * $item->price }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="col-md-12">
    <div class="col-xs-12" style="padding: 0px;">
        <div class="dataTables_info" role="status" aria-live="polite">Mostrando {{ $sales->firstItem() }} a {{ $sales->lastItem() }} de {{ $sales->total() }} registros</div>
    </div>
    <div class="col-xs-12" style="padding: 0px;">
        <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
            {{ $sales->links() }}
        </div>
    </div>
</div>

<script>
    $('.page-link').click(function(e){
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        listSales(page);
    });
</script>