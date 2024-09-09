<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $establecimientos->id !!}</p>
</div>

<!-- Codigo Field -->
<div class="form-group">
    {!! Form::label('codigo', 'Código:') !!}
    <p>{!! $establecimientos->cod_establecimiento !!}</p>
</div>

<!-- Nombre Establecimiento Field -->
<div class="form-group">
    {!! Form::label('nombre_establecimiento', 'Nombre Establecimiento:') !!}
    <p>{!! $establecimientos->nombre_establecimiento !!}</p>
</div>

<!-- Region Red Field -->
<div class="form-group">
    {!! Form::label('region', 'Región/Red:') !!}
    <p>{!! $establecimientos->est_region->descripcion !!}</p>
</div>

<!-- Nivel Field -->
<div class="form-group">
    {!! Form::label('nivel', 'Nivel:') !!}
    <p>{!! $establecimientos->est_nivel->descripcion !!}</p>
</div>

<!-- Categoria Field -->
<div class="form-group">
    {!! Form::label('categoria', 'Categoria:') !!}
    <p>{!! $establecimientos->est_categoria->descripcion !!}</p>
</div>

<!-- Tipo Ipress Field -->
<div class="form-group">
    {!! Form::label('tipo_ipress', 'Tipo Ipress:') !!}
    <p>{!! $establecimientos->est_tipo->descripcion !!}</p>
</div>

<!-- Tipo Internamiento Field -->
<div class="form-group">
    {!! Form::label('tipo_internamiento', 'Tipo Internamiento:') !!}
    <p>{!! $establecimientos->est_internamiento->descripcion !!}</p>
</div>

<!-- Departamento Field -->
<div class="form-group">
    {!! Form::label('departamento', 'Departamento:') !!}
    <p>{!! $establecimientos->est_departamento->nombre_dpto !!}</p>
</div>

<!-- Provincia Field -->
<div class="form-group">
    {!! Form::label('provincia', 'Provincia:') !!}
    <p>{!! $establecimientos->est_provincia->nombre_prov !!}</p>
</div>

<!-- Distrito Field -->
<div class="form-group">
    {!! Form::label('distrito', 'Distrito:') !!}
    <p>{!! $establecimientos->est_distrito->nombre_dist !!}</p>
</div>

<!-- Disa Field -->
<div class="form-group">
    {!! Form::label('disa', 'Disa:') !!}
    <p>{!! $establecimientos->est_disa->descripcion!!}</p>
</div>

<!-- Norte Field -->
<div class="form-group">
    {!! Form::label('norte', 'Norte:') !!}
    <p>{!! $establecimientos->norte !!}</p>
</div>

<!-- Este Field -->
<div class="form-group">
    {!! Form::label('este', 'Este:') !!}
    <p>{!! $establecimientos->este !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Creado:') !!}
    <p>{!! $establecimientos->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Modificado:') !!}
    <p>{!! $establecimientos->updated_at !!}</p>
</div>

