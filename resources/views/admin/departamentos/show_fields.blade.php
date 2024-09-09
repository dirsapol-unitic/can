<!-- Nombre Dpto Field -->
<div class="form-group">
    {!! Form::label('nombre_dpto', 'Nombre Dpto:') !!}
    <p>{!! $departamento->nombre_dpto !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Creado:') !!}
    <p>{!! $departamento->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Modificado:') !!}
    <p>{!! $departamento->updated_at !!}</p>
</div>

