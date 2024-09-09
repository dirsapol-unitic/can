<script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
<link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

<script type="text/javascript">
    
    $("#nota_entrada_nro_guia").blur(function (event) {
            var concepto=$('#concepto').val();
            if(concepto==1)
                getCargaDatos();
        });

    function calculo() {
        var total = 0;
        
        $(".cpm").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total += 0;
            } else {
              total += parseFloat($(this).val());
            }
        });

        //total_necesidad=total*12;
        //redondeo=Math.round(total_necesidad);

        //document.getElementById('sNecesidad').innerHTML = total_necesidad;
                $("#necesidad_anual").val(redondeo);

        document.getElementById('sNecesidad').innerHTML = redondeo;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    function suma_meses() {
        var total1 = 0;
        
        
        $(".suma_mes").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo').innerHTML = total1;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    function sumar_necesidad() {
        var total1 = 0;
        var total2 = 0;
        var necesidadanual;
        
        necesidadanual=$("#necesidad_anual").val();
        //cpma=$("#cpma").val();

        /*if(necesidadanual>cpma*12)
            alert("Necesidad Anual debe ser menor a cpma*12");
        */
        $(".suma_mes").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        $(".necesidad").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total2 += 0;
            } else {
              total2 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo').innerHTML = total1;
        document.getElementById('sNecesidad').innerHTML = total2;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    
</script>
<!-- Cpma Field -->
<div class="form-group col-sm-12">
    {!! Form::label('descripcion', 'Nombre del Medicamento / Dispositivo:') !!}
    {!! Form::select('descripcion',$descripcion , null, ['class' => 'form-control select2', 'autofocus'=>'autofocus']) !!}
</div>

<!-- Cpma Field -->
<div class="form-group col-sm-4">
<!--div class="form-group col-sm-4">
    {!! Form::label('cpma', 'CPMA:') !!}
    {!! Form::number('cpma', 0, ['class' => 'form-control cpm','required'=>'required','min'=>'0','step'=>'any', 'onkeyup'=>'calculo()']) !!}
</div-->
</div>

<!-- Cpma Field -->
<div class="form-group col-sm-4">
<!--div class="form-group col-sm-4">
    {!! Form::label('stock', 'Stock Actual:') !!}
    {!! Form::number('stock', 0, ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div-->
</div>
<!-- Cpma Field -->
<div class="form-group col-sm-4">
    {!! Form::label('necesidad_anual', 'Necesidad Anual:') !!}
    {!! Form::number('necesidad_anual', 0, ['class' => 'form-control necesidad','required'=>'required','min'=>'0', 'onkeyup'=>'sumar_necesidad()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes1', 'Enero:') !!}
    {!! Form::number('mes1', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes2', 'Febrero:') !!}
    {!! Form::number('mes2', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes3', 'Marzo:') !!}
    {!! Form::number('mes3', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes4', 'Abril:') !!}
    {!! Form::number('mes4', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes5', 'Mayo:') !!}
    {!! Form::number('mes5', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes6', 'Junio:') !!}
    {!! Form::number('mes6', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes7', 'Julio:') !!}
    {!! Form::number('mes7', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes8', 'Agosto:') !!}
    {!! Form::number('mes8', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes9', 'Setiembre:') !!}
    {!! Form::number('mes9', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes10', 'Octubre:') !!}
    {!! Form::number('mes10', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes11', 'Noviembre:') !!}
    {!! Form::number('mes11', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('mes12', 'Diciembre:') !!}
    {!! Form::number('mes12', 0, ['class' => 'form-control suma_mes','required'=>'required','min'=>'0','onkeyup'=>'suma_meses()']) !!}
</div>
<!-- Fecha Vencimiento Field -->
<!--div class="form-group col-sm-9">
    <div class="input-group">
        <span class="input-group-addon"><b> Jusfificación</b></span>
        <textarea rows="3" id="justificacion" placeholder="Jusfificación"  name="justificacion" class="form-control">
        </textarea>
        <span class="help-block with-errors"></span>
    </div>                           
</div-->
<div class="col-md-12">
    <div class="col-md-1">
    </div>
    <div class="col-md-3 form-group">
        <b><span>Necesidad: </span> <span id="sNecesidad"></span></b>
    </div>
    <div class="col-md-3 form-group">
        <b><span>Total Prorrateo: </span> <span id="sProrroteo"></span></b>
    </div>
    <div class="col-md-5 form-group">
        <b><span>Falta para completar su Prorrateo: </span> <span id="sFalta"></span></b>
    </div>
    
</div>  

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    
    <a href="{!! route('estimacion_servicio.cargar_medicamentos_servicios',[$can_id,$establecimiento_id,$destino,1]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

