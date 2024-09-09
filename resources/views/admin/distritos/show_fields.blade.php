<!-- Nombre Dist Field -->
<div class="form-group">
    {!! Form::label('nombre_dist', 'Nombre Dist:') !!}
    <p>{!! $distrito->nombre_dist !!}</p>
</div>

<!-- Provincia Id Field -->
<div class="form-group">
    {!! Form::label('provincia_id', 'Provincia:') !!}
    <p>{!! $distrito->provincia->nombre_prov !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Creado:') !!}
    <p>{!! $distrito->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Modificado:') !!}
    <p>{!! $distrito->updated_at !!}</p>
</div>

