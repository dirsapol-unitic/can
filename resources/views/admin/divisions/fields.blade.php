<!-- Nombre Division Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre_division', 'Nombre Division:') !!}
    {!! Form::text('nombre_division', null, ['class' => 'form-control','required'=>'required']) !!}
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('divisions.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>
