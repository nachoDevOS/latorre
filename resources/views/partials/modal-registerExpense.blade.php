<form class="form-edit-add" action="{{ route('expenses.store') }}"  method="POST">
    <div class="modal fade" id="modal-create-expense" role="dialog">
        <div class="modal-dialog modal-success">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" style="color: #ffffff !important"><i class="voyager-plus"></i> Registrar
                        Gastos Extras</h4>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        {{-- <div class="form-group col-md-6">
                            <label for="full_name">Tipo de Gastos</label>
                            <select name="categoryExpense_id" class="form-control select2" required>
                                <option value="" disabled selected>--Seleccione una Opción--</option>
                                @foreach ( App\Models\CategoryExpense::where('deleted_at', null)->where('status', 1)->get() as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="form-group col-md-6">
                            <label for="full_name">Monto</label>
                            <input type="number" style="text-align: right" min="0.5" step="0.5" name="amount"
                                class="form-control" placeholder="0.0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observation">Detalle o Descripción del gasto</label>
                        <textarea name="observation" class="form-control" rows="3" placeholder="Compra de platos desachables"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-submit">Si, Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>

