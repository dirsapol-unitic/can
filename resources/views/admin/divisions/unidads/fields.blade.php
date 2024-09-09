<div class="form-group col-sm-6">
    {!! Form::label('descripcion', 'Departamento:') !!}
    {!! Form::select('descripcion',$descripcion , null, ['class' => 'form-control select2', 'autofocus'=>'autofocus']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('divisions.ver_departamentos',[$divisions->id,$establecimiento_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

