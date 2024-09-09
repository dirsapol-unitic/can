<!-- Codigo Field -->
<div class="form-group col-sm-3">
    {!! Form::label('codigo', 'Codigo:') !!}
    {!! Form::text('codigo', null, ['class' => 'form-control']) !!}
</div>

<!-- Nombre Servicio Field -->
<div class="form-group col-sm-9">
    {!! Form::label('nombre_servicio', 'Nombre Servicio:') !!}
    {!! Form::text('nombre_servicio', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
        <a href="{!! route('servicios.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

