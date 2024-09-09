var url = "http://localhost:8000/abastecimiento";

// muestra el formulario modal para la edición del producto
$(document).on('click', '.open_modal', function () {
    var abastecimiento_id = $(this).val();

    $.get(url + '/' + abastecimiento_id, function (data) {
        //success data
        console.log(data);
        $('#abastecimiento_id').val(data.id);
        $('#descripcion').val(data.descripcion);
        $('#btn-save').val("update");
        $('#myModal').modal('show');
    })
});
// muestra el formulario modal para crear un nuevo producto
$('#btn_add').click(function () {
    $('#btn-save').val("add");
    $('#frmabastecimientos').trigger("reset");
    $('#myModal').modal('show');
});
// eliminar el producto y eliminarlo de la lista
$(document).on('click', '.delete-abastecimiento', function () {
    var abastecimiento_id = $(this).val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    $.ajax({
        type: "DELETE",
        url: url + '/' + abastecimiento_id,
        success: function (data) {
            console.log(data);
            $("#abastecimiento" + abastecimiento_id).remove();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
// crear nuevo producto / actualizar producto existente
$("#btn-save").click(function (e) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    e.preventDefault();
    var formData = {
        descripcion: $('#descripcion').val(),
    }
    // utilizado para determinar el metodo http que se va a utilizar [add = POST], [update = PUT]
    var state = $('#btn-save').val();
    var type = "POST"; // para crear un nuevo recurso
    var abastecimiento_id = $('#abastecimiento_id').val();;
    var my_url = url;
    if (state == "update") {
        type = "PUT"; // para actualizar recursos existentes
        my_url += '/' + abastecimiento_id;
    }
    console.log(formData);
    $.ajax({
        type: type,
        url: my_url,
        data: formData,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var abastecimiento = '<tr id="abastecimiento' + data.id + '"><td>' + data.id + '</td><td>' + data.descripcion + '</td>';
            abastecimiento += '<td><button class="btn btn-warning btn-detail open_modal" value="' + data.id + '">Editar</button>';
            abastecimiento += ' <button class="btn btn-danger btn-delete delete-abastecimiento" value="' + data.id + '">Eliminar</button></td></tr>';
            if (state == "add") { // para actualizar recursos existentes...
                $('#abastecimientos-list').append(abastecimiento);
            } else { // si el usuario actualiza un registro existente
                $("#abastecimiento" + abastecimiento_id).replaceWith(abastecimiento);
            }
            $('#frmabastecimientos').trigger("reset");
            $('#myModal').modal('hide')
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});