<!-- Nivel Field -->
<div class="form-group">
    {!! Form::label('descripcion', 'Nivel:') !!}
    <p>{!! $nivel->descripcion !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Creado:') !!}
    <p>{!! $nivel->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Modificado:') !!}
    <p>{!! $nivel->updated_at !!}</p>
</div>

