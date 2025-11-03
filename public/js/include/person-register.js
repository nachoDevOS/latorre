$(document).ready(function(){   
    $('#create-form-person').submit(function(e){
        e.preventDefault();
        $('.btn-save-person').attr('disabled', true);
        $('.btn-save-person').val('Guardando...');

        let form = $(this);
        
        // $.post($(this).attr('action'), $(this).serialize(), function(data){
        $.post(form.attr('action'), $(this).serialize(), function(data){
            if(data.person.id){
                toastr.success('Usuario creado', 'Éxito');
                // $(this).trigger('reset');
                form[0].reset();
            }else{
                toastr.error(data.error, 'Error');
            }
        })
        .always(function(){
            $('.btn-save-person').attr('disabled', false);
            $('.btn-save-person').val('Guardar');
            $('#modal-create-person').modal('hide');
        });
    });
});
