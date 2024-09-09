<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Codigo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('codigo_establecimiento', 'Código:') !!}
    {!! Form::text('codigo_establecimiento', null, ['class' => 'form-control']) !!}
</div>

<!-- Nombre Establecimiento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre_establecimiento', 'Nombre Establecimiento:') !!}
    {!! Form::text('nombre_establecimiento', null, ['class' => 'form-control']) !!}
</div>

<!-- Region Red Field -->
<div class="form-group col-sm-6">
    {!! Form::label('region_red', 'Región/Red:') !!}
    {!! Form::select('region_id', $region_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Nivel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nivel', 'Nivel:') !!}
    {!! Form::select('nivel_id', $nivel_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Categoria Field -->
<div class="form-group col-sm-6">
    {!! Form::label('categoria', 'Categoría:') !!}
    {!! Form::select('categoria_id', $categoria_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Tipo Ipress Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tipo_ipress', 'Tipo Establecimiento:') !!}
    {!! Form::select('tipo_establecimiento_id', $tipo_establecimiento_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Tipo Internamiento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tipo_internamiento', 'Tipo Internamiento:') !!}
    {!! Form::select('tipo_internamiento_id', $tipo_internamiento_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Departamento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departamento', 'Departamento:') !!}
    <select class="form-control select2" name="departamento_id" id="departamento_id">
            <?php $idDepartamento = $departamento_id;?>
            <option value="">- Seleccione -</option>
            @foreach($departamento as $dpto)
                <?php $idDpto = $dpto->id;?>
                <option value="{{$dpto->id}}" <?php if($idDpto == $idDepartamento) {echo " selected";}?>>{{$dpto->nombre_dpto}}</option>
            @endforeach
        </select>        

</div>

<!-- Provincia Field -->
<div class="form-group col-sm-6">
    @if($tipo==1)
        <select class="form-control select2" tabindex="21" name="provincia_id" id="provincia_id">
            <option value="">-Seleccione-</option>
        </select>
    @else
        {!! Form::label('provincia', 'Provincia:') !!}
        <select class="form-control select2" name="provincia_id" id="provincia_id">
            <?php $idProvincia = $provincia_id;?>
            <option value="">- Seleccione -</option>
            @foreach($provincia as $prov)
                <?php $idProv = $prov->id;?>
                <option value="{{$prov->id}}" <?php if($idProv == $idProvincia) {echo " selected";}?>>{{$prov->nombre_prov}}</option>
            @endforeach
        </select>        

    @endif
    
</div>

<!-- Distrito Field -->
<div class="form-group col-sm-6">
    @if($tipo==1)
        <select class="form-control select2" tabindex="21" name="distrito_id" id="distrito_id">
            <option value="">-Seleccione-</option>
        </select>
    @else
        {!! Form::label('distrito', 'Distrito:') !!}
        <select class="form-control select2" name="distrito_id" id="distrito_id">
            <?php $idDistrito = $distrito_id;?>
            <option value="">- Seleccione -</option>
            @foreach($distrito as $dist)
                <?php $idDist = $dist->id;?>
                <option value="{{$dist->id}}" <?php if($idDist == $idDistrito) {echo " selected";}?>>{{$dist->nombre_dist}}</option>
            @endforeach
        </select>        

    @endif
    
</div>

<!-- Disa Field -->
<div class="form-group col-sm-6">
    {!! Form::label('disa', 'DISA:') !!}
    {!! Form::select('disa_id', $disa_id, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Norte Field -->
<div class="form-group col-sm-6">
    {!! Form::label('norte', 'Norte:') !!}
    {!! Form::text('norte', null, ['class' => 'form-control']) !!}
</div>

<!-- Este Field -->
<div class="form-group col-sm-6">
    {!! Form::label('este', 'Este:') !!}
    {!! Form::text('este', null, ['class' => 'form-control']) !!}
</div>

@if($tipo!=1)
<div class="form-group col-sm-6">
    {!! Form::label('activo', 'Activo:') !!}
    <input type="checkbox" value="1" name="estado" <?php if($estado == 1)echo 'checked="checked"';?>/>
</div>
@endif

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
    <a href="{!! route('establecimientos.index') !!}" class="btn btn-danger">Cancelar</a>
</div>

<script type="text/javascript">
    
    $(document).ready(function () {
        
        $cmbid_departamento = $("#frm_establecimiento").find("#departamento_id");
        $cmbid_departamento.select2();
        $cmbid_provincia = $("#frm_establecimiento").find("#provincia_id");
        $cmbid_provincia.select2();
        $cmbid_distrito = $("#frm_establecimiento").find("#distrito_id");
        $cmbid_distrito.select2();

        
        $cmbid_departamento.change(function () {    
            $this = $(this); 
            cmbid_departamento = $cmbid_departamento.val();
            $cmbid_provincia.html('');
            option = {
                url: '/cargarprovincias/' + cmbid_departamento,
                type: 'GET',
                dataType: 'json',
                data: {}
            };
            $.ajax(option).done(function (data) {  
                cargarComboDestino($cmbid_provincia, data);
                $cmbid_provincia.val(null).trigger("change");                                           
            });
        });        

        $cmbid_provincia.change(function () {    
            $this = $(this); 
            cmbid_departamento = $cmbid_departamento.val();
            cmbid_provincia = $cmbid_provincia.val();
            $cmbid_distrito.html('');
            option = {
                url: '/cargardistritos/' + cmbid_departamento+'/'+ cmbid_provincia,
                type: 'GET',
                dataType: 'json',
                data: {}
            };
            $.ajax(option).done(function (data) {  
                cargarComboDestino($cmbid_distrito, data);
                $cmbid_distrito.val(null).trigger("change");                                           
            });
        });        
        
        function cargarComboDestino($select, data) {
            $select.html('');
            $(data).each(function (ii, oo) {
                $select.append('<option value="' + oo.id + '">' + oo.nombre + '</option>')
            });
        }
    });
</script>