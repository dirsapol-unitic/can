<!-- Nombre Prov Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre_prov', 'Nombre Prov:') !!}
    {!! Form::text('nombre_prov', null, ['class' => 'form-control']) !!}
</div>

<!-- Departamento Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departamento_id', 'Departamento:') !!}
    {!! Form::select('departamento_id',  $departamento_id, null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('provincias.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>
