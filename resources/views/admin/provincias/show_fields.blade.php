<!-- Nombre Prov Field -->
<div class="form-group">
    {!! Form::label('nombre_prov', 'Nombre Prov:') !!}
    <p>{!! $provincia->nombre_prov !!}</p>
</div>

<!-- Departamento Id Field -->
<div class="form-group">
    {!! Form::label('departamento_id', 'Departamento:') !!}
    <p>{!! $provincia->departamento->nombre_dpto !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Creado:') !!}
    <p>{!! $provincia->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Modificado:') !!}
    <p>{!! $provincia->updated_at !!}</p>
</div>

