<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button id="btnRegistrar" type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('users.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

<div class="row col-sm-12">
    <!-- DNI -->
    <div class="form-group col-sm-2">
        {!! Form::label('dni', 'DNI:') !!}
        {!! Form::text('dni', null, ['id'=>'dni','name'=>'dni', 'class' => 'form-control','maxlength'=>'8','required'=>'required']) !!}
         
    </div>
    <!-- EMAIL -->
    <div class="form-group col-sm-6">
        {!! Form::label('email', 'Email:') !!}
        {!! Form::email('email', null, ['id'=>'email','name'=>'email','class' => 'form-control','required'=>'required']) !!}
    </div>
    <!-- NOMBRES -->
    <div class="form-group col-sm-4">
        {!! Form::label('nombres', 'Nombres:') !!}
        {!! Form::text('nombres', null, ['id'=>'nombres','name'=>'nombres','class' => 'form-control','required'=>'required']) !!}
    </div>

   
</div>

<div class="row col-sm-12">
     <!-- APELLIDO PATERNO -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_paterno', 'Apellido Paterno:') !!}
        {!! Form::text('apellido_paterno', null, ['id'=>'apellido_paterno','name'=>'apellido_paterno','class' => 'form-control','required'=>'required']) !!}
    </div>
    <!-- APELLIDO MATERNO -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_materno', 'Apellido Materno:') !!}
        {!! Form::text('apellido_materno', null, ['id'=>'apellido_materno','name'=>'apellido_materno','class' => 'form-control','required'=>'required']) !!}
    </div>
    <!-- CIP -->
    <div class="form-group col-sm-2">
        {!! Form::label('cip', 'CIP:') !!}
        {!! Form::text('cip', null, ['id'=>'cip','name'=>'cip','class' => 'form-control','maxlength'=>'8']) !!}
         
    </div>
    <!-- GRADO -->
    <!--div class="form-group col-sm-4">
        {!! Form::label('grado', 'Grado:') !!}
        {!! Form::text('grado', null, ['id'=>'grado','name'=>'grado','class' => 'form-control']) !!}
    </div-->
    <div class="form-group col-sm-4">
        {!! Form::label('grado', 'Grado:') !!}
        <select class="form-control select2" name="grado" id="grado">
            <?php $idGrado = $grado_id;?>
            <option value="">- Seleccione -</option>
            @foreach($grado as $gr)
                <?php $idGr = $gr->id;?>
                <option value="{{$gr->id}}" <?php if($idGr == $idGrado) {echo " selected";}?>>{{$gr->descripcion}}</option>
            @endforeach
        </select>        
    </div>

    
</div>

<div class="row col-sm-12">
    <!-- TELEFONO -->
    <div class="form-group col-sm-2">
        {!! Form::label('telefono', 'Telefono:') !!}
        {!! Form::text('telefono', null, ['class' => 'form-control','maxlength'=>'9','required'=>'required']) !!}
    </div>
    <!-- ESTABLECIMIENTO -->
    <div class="form-group col-sm-5">
        {!! Form::label('establecimiento', 'Establecimiento:') !!}
        <select class="form-control select2" name="establecimiento_id" id="establecimiento_id">
            <?php $idEstablecimiento = $establecimiento_id;?>
            <option value="">- Seleccione -</option>
            @foreach($establecimiento as $est)
                <?php $idEst = $est->id;?>
                <option value="{{$est->id}}" <?php if($idEst == $idEstablecimiento) {echo " selected";}?>>{{$est->nombre_establecimiento}}</option>
            @endforeach
        </select>        
    </div>
    <!-- SERVICIO -->
    <div class="form-group col-sm-5">
        {!! Form::label('servicio', 'Rubro:') !!}
        @if($tipo==1)
            <select class="form-control select2" tabindex="21" name="servicio_id" id="servicio_id">
                <option value="">-Seleccione-</option>
            </select>
        @else
            <?php if ($muestra==0):?>
                <select id="servicio_id" name="servicio_id" class="form-control select2"  required="" >
                    <option value="0">NO APLICA</option>
                </select>
            <?php else: ?>
                <?php 
                $idRub= $servicio_id; ?>
                <select id="servicio_id" name="servicio_id" class="form-control select2"  required="">
                @foreach($servicio as $dest)
                    <?php 
                    $idrubro= $dest->id;?>
                    <option value="{{$dest->id}}" <?php if($idRub == $idrubro) {echo " selected";}?>>{{$dest->nombre_servicio}}</option>
                @endforeach
                </select>
            <?php endif; ?>
        @endif
                            
    </div>
</div>
<div class="row col-sm-12">

<!-- CONTRASEÑA -->
@if ($tipo==1)
<div class="form-group col-sm-3">
    {!! Form::label('password', 'Contraseña:') !!}
    {!! Form::password('password',['class' => 'form-control ','required'=>'required']) !!}
</div>
@else
<div class="form-group col-sm-3">
    {!! Form::label('password', 'Contraseña:') !!}
    {!! Form::password('password',['class' => 'form-control ']) !!}
</div>
@endif
<!-- Establecimiento Field -->
<div class="form-group col-sm-3">
    <br/>
    <input 
        
        type="radio"  
        value="1" 
        @if ($tipo==1)
            name="rol" checked="checked">
        @else
            {{ $user->rol==1 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('administrador', 'Administrador:') !!}
</div>
<div class="form-group col-sm-3">
    <br/>
    <input         
        type="radio" 
        value="7" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==7 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_ambos', 'Farmacia I') !!}
    <br>
</div>
<div class="form-group col-sm-3">
    <br/>
    <input         
        type="radio" 
        value="2" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==2 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_ambos', 'Llena Servicio II - III') !!}
    <br>
</div>
</div>
<div class="row col-sm-12">
    <div class="form-group col-sm-4">
    <br/>
    <input         
        type="radio" 
        value="3" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==3 ? 'checked' : '' }}
            name="rol">
        @endif
        {!! Form::label('responsable_ambos', 'Productos Farmaceuticos') !!}
    
    <br>
</div>

<div class="form-group col-sm-4">
    <br/>
    <input         
        type="radio" 
        value="5" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==5 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_ambos', 'Material e Insumos Dentales') !!}
    <br>
</div>
<div class="form-group col-sm-4">
    <br/>
    <input         
        type="radio" 
        value="6" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==6 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_ambos', 'Material e Insumos de Laboratorios') !!}
    <br>
</div>

</div>
<div class="row col-sm-12">
<div class="form-group col-sm-6">
    <br/>
    <input         
        type="radio" 
        value="4" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==4 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_ambos', 'Material Biomedico, Instrumental Quirugico y Productos Afines') !!}
    <br>
</div>
<div class="form-group col-sm-4">
    <br/>
    <input         
        type="radio" 
        value="8" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==8 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_ambos', 'Material Fotografico y Fonotecnico') !!}
    <br>
</div>
<div class="form-group col-sm-4">
    <br/>
    <input         
        type="radio" 
        value="11" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==11 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_ambos', 'Ver Reportes') !!}
    <br>
</div>

@if ($tipo!=1)
<div class="form-group col-sm-2">
    <br/>
    {!! Form::label('activo', 'Activo:') !!}
    <input type="checkbox" value="1" name="estado" <?php if($estado == 1)echo 'checked="checked"';?>/>
</div>
@endif
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button id="btnRegistrar" type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('users.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

<script type="text/javascript">
    
    $(document).ready(function () {
        $("#dni").blur(function (event) {
                getPersonalByDni();
        });   


        $cmbid_establecimiento = $("#frm_usuario").find("#establecimiento_id");
        $cmbid_establecimiento.select2();
        $cmbid_distribucion = $("#frm_usuario").find("#servicio_id");
        $cmbid_distribucion.select2();
        
        
        function getPersonalByDni() {

            var nro_doc = $('#dni').val();        

            $('.table tbody').html("");
            $.ajax({
                url: '/buscar_personal_dni/' + nro_doc + '/' + 'DNI',
                success: function (result) {

                    
                    
                    if(result["dni"]!=0){

                        $('#nombres').val(result["nombres"]).attr("readonly", true);
                        $('#apellido_paterno').val(result["paterno"]).attr("readonly", true);
                        $('#apellido_materno').val(result["materno"]).attr("readonly", true);   
                        $('#cip').val(result["carne"]).attr("readonly", false);
                        $('#btnRegistrar').prop('disabled', false);
                        //$('#grado').val(result["grado"]).attr("readonly", true);
                    }
                    else
                    {
                        alert("DNI no encontrado, ingrese otro DNI");
                        $('#nombres').val("").attr("readonly", true);
                        $('#apellido_paterno').val("").attr("readonly", true);
                        $('#apellido_materno').val("").attr("readonly", true);
                        $('#cip').val("").attr("readonly", true);
                        $('#btnRegistrar').prop('disabled', true);
                        
                    }
                }
            });

        }

        $cmbid_establecimiento.change(function () {    
            $this = $(this); 
            cmbid_establecimiento = $cmbid_establecimiento.val();
            $cmbid_distribucion.html('');
            option = {
                url: '/cargarrubros/' + cmbid_establecimiento,
                type: 'GET',
                dataType: 'json',
                data: {}
            };
            $.ajax(option).done(function (data) {  
                cargarComboDestino($cmbid_distribucion, data);
                $cmbid_distribucion.val(null).trigger("change");                                           
            });
        });        
        
        function cargarComboDestino($select, data) {
            $select.html('');
            $(data).each(function (ii, oo) {
                $select.append('<option value="' + oo.id + '">' + oo.nombre_servicio + '</option>')
            });
        }
    });
</script>
