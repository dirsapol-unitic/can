
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<div class="row col-sm-12">
    <!-- DNI -->
    <div class="form-group col-sm-2">
        {!! Form::label('dni', 'DNI:') !!}        
        <input name="dni" type="text" class="form-control" maxlength="8"  required="required" value="<?php echo $dni ?>" placeholder="DNI">
        {!! Form::text('dni', null, ['id'=>'dni','name'=>'dni', 'class' => 'form-control','maxlength'=>'8','required'=>'required']) !!}
         
    </div>

    <!-- NOMBRES -->
    <div class="form-group col-sm-4">
        {!! Form::label('nombres', 'Nombres:') !!}        
        <input name="nombres" type="text" class="form-control" required="required" value="<?php echo $nombres ?>" placeholder="Nombres">
    </div>

    <!-- APELLIDO PATERNO -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_paterno', 'Apellido Paterno:') !!}
        <input name="apellido_paterno" type="text" class="form-control" required="required" value="<?php echo $apellido_paterno ?>" placeholder="Apellido Paterno">
        
    </div>
    <!-- APELLIDO MATERNO -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_materno', 'Apellido Materno:') !!}
        <input name="apellido_materno" type="text" class="form-control" required="required" value="<?php echo $apellido_materno ?>" placeholder="Apellido Materno">
        
    </div>
</div>

<div class="row col-sm-12">
    <!-- GRADO -->
    <!--div class="form-group col-sm-4">
        {!! Form::label('grado', 'Grado:') !!}
        <input name="grado" type="text" class="form-control" required="required" value="<?php echo $grado ?>" placeholder="Grado">
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
    <!-- RUBRO -->
    <div class="form-group col-sm-4">
        {!! Form::label('servicio', 'Tipo Responsable:') !!}
        <?php 
            $idRol= $rol_id; ?>
            @if($nivel==1)
                <select class="form-control select2" tabindex="21" name="rol_id" id="rol_id">
                    @if($valor_id==0)
                    <option value="5">Jefe IPRESS</option>
                    @else
                        @if($valor_id==1)
                            <option value="4">Responsable y/o Farmacia</option>
                        @else
                            <option value="3">Responsable Rubro</option>    
                        @endif        
                    @endif
                </select>    
            @else
                <select class="form-control select2" tabindex="21" name="rol_id" id="rol_id">
                    @if($valor_id==0)
                    <option value="9">Jefe IPRESS</option>
                    @else
                        @if($valor_id==1)
                            <option value="7">Responsable y/o Farmacia</option>
                        @endif        
                    @endif
                </select>    
            @endif

    </div>
    <div class="form-group col-sm-4">
        {!! Form::label('servicio', 'Rubro:') !!}        
        <?php 
            $idRub= $servicio_id; ?>
            <select class="form-control select2" tabindex="21" name="servicio_id" id="servicio_id">
                @if($valor_id==2)
                <option value="1">Productos Farmacetuticos</option>
                @endif
                @if($valor_id==5)
                <option value="2">Material e insumos de Laboratorio</option>
                @endif
                @if($valor_id==3)
                <option value="3">Material Biomedico, instrumental 
                quirurgico y productos afines</option>                
                @endif
                @if($valor_id==4)
                <option value="4">Material e Insumos dentales</option>
                @endif
                @if($valor_id==6)
                <option value="5">Material Fotografico y Fonotecnico</option>
                @endif
                @if($valor_id==0 || $valor_id==1)                
                    <option value="6">No Aplica</option>
                @endif
            </select>                    
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('users.index_responsable',['can_id'=>$can_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
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

            $.ajax({
                url: '/buscar_personal_dni/' + nro_doc + '/' + 'DNI',
                success: function (result) {
                    $('#nombres').val("").attr("readonly", false);
                    $('#apellido_paterno').val("").attr("readonly", false);
                    $('#apellido_materno').val("").attr("readonly", false);
                    $('#grado').val("").attr("readonly", false);
                    
                    if(result["dni"]!=0){
                        $('#nombres').val(result["nombres"]).attr("readonly", true);
                        $('#apellido_paterno').val(result["paterno"]).attr("readonly", true);
                        $('#apellido_materno').val(result["materno"]).attr("readonly", true);                           
                        $('#grado').val(result["grado"]).attr("readonly", true);    
                    }

                }
            });

        }


    });
</script>


