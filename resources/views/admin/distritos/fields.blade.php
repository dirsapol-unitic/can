<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<div class="form-group col-sm-6">
    {!! Form::label('departamento', 'Departamento:') !!}
    {!! Form::select('departamento_id',[''=>'--- Seleccione ---']+$departamento,null,['class'=>'form-control','id'=>'departamento_id']) !!}      
</div>

<!-- Provincia Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('provincia', 'Provincia:') !!}
    {!! Form::select('provincia_id',[''=>'--- Seleccione ---']+$provincia,null,['class'=>'form-control','id'=>'provincia_id']) !!}      
</div>

<!-- Nombre Dist Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre_dist', 'Distrito:') !!}
    {!! Form::text('nombre_dist', null, ['class' => 'form-control']) !!}
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('distritos.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

<script type="text/javascript">
  $("#departamento_id").change(event => {
  $.get(`miprovincia/${event.target.value}`, function(res, sta){
    $("#provincia_id").empty();
    res.forEach(element => {
      $("#provincia_id").append(`<option value=${element.id}> ${element.nombre_prov} </option>`);
    });
  });
});

</script>