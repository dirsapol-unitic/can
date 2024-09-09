<!-- Nombre Unidad Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre_unidad', 'Nombre del Departamento:') !!}
    {!! Form::text('nombre_unidad', null, ['class' => 'form-control','required'=>'required']) !!}
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('unidads.index') !!}" class="btn btn-danger">Cancelar<i class="glyphicon glyphicon-remove"></i></a>

</div>
