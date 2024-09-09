<!-- Can Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('can_id', 'Can Id:') !!}
    {!! Form::text('can_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Establecimiento Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('establecimiento_id', 'Establecimiento Id:') !!}
    {!! Form::text('establecimiento_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Cod Establecimiento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cod_establecimiento', 'Cod Establecimiento:') !!}
    {!! Form::text('cod_establecimiento', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('femas.index') !!}" class="btn btn-default">Cancel</a>
</div>
