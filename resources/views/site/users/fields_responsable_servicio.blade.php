<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<div class="row col-sm-12">
    <!-- DNI -->
    <div class="form-group col-sm-2">
        {!! Form::label('dni', 'DNI:') !!}
        {!! Form::text('dni', null, ['id'=>'dni','name'=>'dni', 'class' => 'form-control','maxlength'=>'8','required'=>'required']) !!}
         
    </div>

    <!-- NOMBRES -->
    <div class="form-group col-sm-4">
        {!! Form::label('nombres', 'Nombres:') !!}
        {!! Form::text('nombres', null, ['id'=>'nombres','name'=>'nombres','class' => 'form-control','required'=>'required']) !!}
    </div>

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
</div>

<div class="row col-sm-12">
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
@if($servicio_id!=0)
    <!-- EMAIL -->
    <div class="form-group col-sm-6">
        {!! Form::label('email', 'Email:') !!}
        {!! Form::email('email', null, ['id'=>'email','name'=>'email','class' => 'form-control','required'=>'required']) !!}
    </div>
@endif;
</div>

<div class="row col-sm-12">
    @if($servicio_id!=0)
        <!-- TELEFONO -->
        <div class="form-group col-sm-2">
            {!! Form::label('telefono', 'Telefono:') !!}
            {!! Form::text('telefono', null, ['class' => 'form-control','maxlength'=>'9','required'=>'required']) !!}
        </div>    
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
        @if ($tipo!=1)
        <div class="form-group col-sm-2">
        <br/>
            {!! Form::label('activo', 'Activo:') !!}
            <input type="checkbox" value="1" name="estado" <?php if($user->estado == 1)echo 'checked="checked"';?>/>
            <input type="hidden" class="form-control" name="rol"  value="{{$rol}}"> 
        </div>
        @endif
    @else
        @if ($tipo!=1)
            <input type="hidden" class="form-control" name="rol"  value="{{$rol}}"> 
            <input type="hidden" class="form-control" name="estado"  value="1"> 
        @endif
    @endif
</div>


</div>

<input type="hidden" class="form-control" name="establecimiento_id"  value="{{$establecimiento_id}}"> 
<input type="hidden" class="form-control" name="servicio_id"  value="{{$servicio_id}}"> 

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button id="btnRegistrar" type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('farmacia_servicios.listar_responsables_servicios',['can_id'=>$can_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
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
                        $('#btnRegistrar').prop('disabled', false);
                    }
                    else
                    {
                        alert("DNI no encontrado, ingrese otro DNI");
                        $('#nombres').val("").attr("readonly", true);
                        $('#apellido_paterno').val("").attr("readonly", true);
                        $('#apellido_materno').val("").attr("readonly", true);
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
