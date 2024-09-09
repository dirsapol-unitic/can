<!-- Nombre Dpto Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre_dpto', 'Nombre Dpto:') !!}
    {!! Form::text('nombre_dpto', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('departamentos.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>
