<!-- Submit Field -->
<div class="form-group col-sm-12">
    <div class="pull-right">
        <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
        <a href="{!! route('petitorios.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    </div>
</div>
@if($edit==1)
<!-- Codigo Field -->
<div class="form-group col-sm-3">
    {!! Form::label('codigo', 'Codigo:') !!}
    {!! Form::text('codigo_petitorio', null, ['class' => 'form-control','readonly'=>'readonly']) !!}
</div>
@endif
<!-- Codigo Field -->
<div class="form-group col-sm-3">
    {!! Form::label('codigo_nuevo', 'Codigo:') !!}
    {!! Form::text('codigo_nuevo', null, ['class' => 'form-control','maxlength'=>'15']) !!}
</div>
<!-- Codigo Field -->
<div class="form-group col-sm-3">
    {!! Form::label('codigo_siga', 'Codigo SIGA:') !!}
    {!! Form::text('codigo_siga', null, ['class' => 'form-control','maxlength'=>'15']) !!}
</div>

<!-- Id Tipo Dispositivo Field -->
<div class="form-group col-sm-3">
    {!! Form::label('tipo_dispositivo_id', 'Tipo Dispositivo:') !!}
    {!! Form::select('tipo_dispositivo_medicos_id', $tipo_dispositivo_medicos_id, null, ['class' => 'form-control select2']) !!}
</div>
<!-- Principio Activo Field -->
<div class="form-group col-sm-12">
    {!! Form::label('principio_activo', 'Principio Activo:') !!}
    {!! Form::text('principio_activo', null, ['class' => 'form-control']) !!}
</div>
<!-- Principio Activo Field -->
<div class="form-group col-sm-12">
    {!! Form::label('descripcion_siga', 'Descripcion SIGA:') !!}
    {!! Form::text('descripcion_siga', null, ['class' => 'form-control']) !!}
</div>
<!-- Id Nivel Field -->
<div class="form-group col-sm-3">
    {!! Form::label('nivel_id', 'Nivel:') !!}
    {!! Form::select('nivel_id', $nivel_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Unidad Medida Field -->
<div class="form-group col-sm-3">
    {!! Form::label('unidad_medida_id', 'Unidad Medida:') !!}
    {!! Form::select('unidad_medida_id', $unidad_medida_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Id Tipo Uso Field -->
<div class="form-group col-sm-3">
    {!! Form::label('tipo_uso_id', 'Tipo Uso:') !!}
    {!! Form::select('tipo_uso_id', $tipo_uso_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Precio Field -->
<div class="form-group col-sm-3">
    {!! Form::label('precio', 'Precio:') !!}
    {!! Form::text('precio', null, ['class' => 'form-control']) !!}
</div>


<!-- Concentracion Field -->
<div class="form-group col-sm-3">
    {!! Form::label('concentracion', 'Concentracion:') !!}
    {!! Form::text('concentracion', null, ['class' => 'form-control']) !!}
</div>

<!-- Form Farm Field -->
<div class="form-group col-sm-3">
    {!! Form::label('form_farm', 'Form Farm:') !!}
    {!! Form::text('form_farm', null, ['class' => 'form-control']) !!}
</div>

<!-- Presentacion Field -->
<div class="form-group col-sm-3">
    {!! Form::label('presentacion', 'Presentacion:') !!}
    {!! Form::text('presentacion', null, ['class' => 'form-control']) !!}
</div>
<!-- Id Tipo Uso Field -->
@if($edit==1)
<div class="form-group col-sm-3">
    {!! Form::label('covid_19', 'Covid-19:') !!}        
    <input type="checkbox" value="1" name="covid" <?php if($covid == 1)echo 'checked="checked"';?>/>
</div>
@endif

@if($edit==1)
<div class="form-group col-sm-3">
    {!! Form::label('activo', 'Activo:') !!}
    <input type="checkbox" value="1" name="estado" <?php if($estado == 1)echo 'checked="checked"';?>/>
</div>
@endif


<!-- Nombre Restriccion Field -->
<div class="form-group col-sm-12">
    {!! Form::label('descripcion', 'Tratamiento:') !!}
    {!! Form::textarea('descripcion_restriccion', null, ['class' => 'form-control','rows'=>3]) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
        <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
        <a href="{!! route('petitorios.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>
