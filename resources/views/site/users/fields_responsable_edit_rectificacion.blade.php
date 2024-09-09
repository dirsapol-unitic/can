<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<div class="row col-sm-12">
    <!-- DNI -->
    <div class="form-group col-sm-2">
        {!! Form::label('dni', 'DNI:') !!}
        {!! Form::text('dni_rectificacion', null, ['id'=>'dni_rectificacion','name'=>'dni_rectificacion', 'class' => 'form-control','maxlength'=>'8','required'=>'required']) !!}
         
    </div>

    <!-- NOMBRES -->
    <div class="form-group col-sm-4">
        {!! Form::label('nombres', 'Nombres:') !!}
        {!! Form::text('nombres_rectificacion', null, ['id'=>'nombres_rectificacion','name'=>'nombres_rectificacion','class' => 'form-control','required'=>'required']) !!}
    </div>

    <!-- APELLIDO PATERNO -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_paterno', 'Apellido Paterno:') !!}
        {!! Form::text('apellido_paterno_rectificacion', null, ['id'=>'apellido_paterno_rectificacion','name'=>'apellido_paterno_rectificacion','class' => 'form-control','required'=>'required']) !!}
    </div>
    <!-- APELLIDO MATERNO -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_materno', 'Apellido Materno:') !!}
        {!! Form::text('apellido_materno_rectificacion', null, ['id'=>'apellido_materno_rectificacion','name'=>'apellido_materno_rectificacion','class' => 'form-control','required'=>'required']) !!}
    </div>
</div>

<div class="row col-sm-12">
    <!-- GRADO -->
    <div class="form-group col-sm-4">
        {!! Form::label('grado', 'Grado:') !!}
        {!! Form::text('grado_rectificacion', null, ['id'=>'grado_rectificacion','name'=>'grado_rectificacion','class' => 'form-control']) !!}
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
            <select class="form-control select2" tabindex="21" name="servicio_id_rectificacion" id="servicio_id_rectificacion">
                @if($valor_id==0 || $valor_id==1)                
                    <option value="6">No Aplica</option>
                @endif
            </select>                    
    </div>

    <div class="form-group col-sm-2">
    {!! Form::label('activo', 'Activo:') !!}
    <input type="checkbox" value="1" name="estado" <?php if($estado == 1)echo 'checked="checked"';?>/>
    </div>
    
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('users.index_responsable_rectificacion') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
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

                    $('#nombres').val("").attr("readonly", false);
                    $('#apellido_paterno').val("").attr("readonly", false);
                    $('#apellido_materno').val("").attr("readonly", false);
                    $('#grado').val("").attr("readonly", false);
                    $('#nombres').val(result[0].nomafiliado).attr("readonly", false);
                    $('#apellido_paterno').val(result[0].apepatafiliado).attr("readonly", false);
                    $('#apellido_materno').val(result[0].apematafiliado).attr("readonly", false);
                    $('#grado').val(result[0].grado).attr("readonly", false);

                }
            });

        }


    });
</script>
