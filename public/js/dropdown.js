$("#departamento").change(event => {
	$.get(`provincias/${event.target.value}`, function(res, sta){
		$("#provincia").empty();
		res.forEach(element => {
			$("#provincia").append(`<option value=${element.id}> ${element.name} </option>`);
		});
	});
});

$("#establecimiento_id").change(event => {
	$.get(`farmacias/${event.target.value}`, function(res, sta){
		$("#servicio_id").empty();
		res.forEach(element => {
			$("#farmcia_id").append(`<option value=${element.id}> ${element.descripcion} </option>`);
		});
	});
});