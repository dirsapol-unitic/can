<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $restricion->id !!}</p>
</div>

<!-- Codigo Field -->
<div class="form-group">
    {!! Form::label('codigo', 'Codigo:') !!}
    <p>{!! $restricion->codigo !!}</p>
</div>

<!-- Nombre Restriccion Field -->
<div class="form-group">
    {!! Form::label('descripcion', 'Nombre Restriccion:') !!}
    <p>{!! $restricion->descripcion !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $restricion->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $restricion->updated_at !!}</p>
</div>

