<!-- Cpma Field -->
<div class="form-group col-sm-6">
    {!! Form::label('descripcion', 'Nombre del Medicamento:') !!}
    {!! Form::text('descripcion', null, ['class' => 'form-control','disabled']) !!}
</div>

<!-- Cpma Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cpma', 'CPMA:') !!}
    {!! Form::number('cpma', null, ['class' => 'form-control', 'autofocus'=>'autofocus']) !!}
</div>

<!-- Stock Inestimacional Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stock_inestimacional', 'Stock Inestimacional:') !!}
    {!! Form::number('stock_inestimacional', null, ['class' => 'form-control','id'=>'txt_suma_1','onchange'=>'sumar(this.value);']) !!}
</div>

<!-- Unidad Ingreso Field -->
<div class="form-group col-sm-6">
    {!! Form::label('almacen_central', 'Ingreso de Almacén Central Saludpol (DIRSAPOL):') !!}
    {!! Form::number('almacen_central', null, ['class' => 'form-control','id'=>'txt_suma_2','onchange'=>'sumar(this.value);']) !!}
</div>

<!-- Unidad Consumo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ingreso_proveedor', 'Ingreso directo del Proveedor:') !!}
    {!! Form::number('ingreso_proveedor', null, ['class' => 'form-control','id'=>'txt_suma_3','onchange'=>'sumar(this.value);']) !!}
</div>

<!-- Transferencia Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ingreso_transferencia', 'Ingreso por Transferencia:') !!}
    {!! Form::number('ingreso_transferencia', null, ['class' => 'form-control','id'=>'txt_suma_4','onchange'=>'sumar(this.value);']) !!}
</div>

<!-- Unidad de Consumo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unidad_consumo', 'Unidad de Consumo:') !!}
    {!! Form::number('unidad_consumo', null, ['class' => 'form-control','id'=>'txt_resta_1','onchange'=>'restar(this.value);']) !!}
</div>

<!-- Merma Field -->
<div class="form-group col-sm-6">
    {!! Form::label('salida_transferencia', 'Salida por Transferencia:') !!}
    {!! Form::number('salida_transferencia', null, ['class' => 'form-control','id'=>'txt_resta_2','onchange'=>'restar(this.value);']) !!}
</div>

<!-- Salida Transferencia Field -->
<div class="form-group col-sm-6">
    {!! Form::label('merma', 'Pérdida/Merma:') !!}
    {!! Form::number('merma', null, ['class' => 'form-control','id'=>'txt_resta_2','onchange'=>'restar(this.value);']) !!}
</div>

<!-- Fecha Vencimiento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fecha_vencimiento', 'Fecha Vencimiento Próxima:') !!}
    <div class="input-group date">
      <div class="input-group-addon">
        <i class="fa fa-calendar"></i>
      </div>
        {!! Form::date('fecha_vencimiento', null, ['class' => 'form-control pull-right'  ]) !!}  
    </div>    
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
    @if ($estimacion->tipo_dispositivo_id==1)
        <a href="{!! route('estimacion.cargar_medicamentos',[0,$estimacion->establecimiento_id]) !!}" class="btn btn-danger">Cancelar</a>
    
    @else
    
        <a href="{!! route('estimacion.cargar_dispositivos',[0,$estimacion->establecimiento_id]) !!}" class="btn btn-danger">Cancelar</a>
    @endif
    
    {!! Form::hidden('precio', null, ['class' => 'form-control']) !!}
    {!! Form::hidden('establecimiento_id', null, ['class' => 'form-control']) !!}
    {!! Form::hidden('tipo_dispositivo_id', null, ['class' => 'form-control']) !!}
</div>

<span>El resultado es: </span> <span id="spTotal"></span>
<span>El resultado es: </span> <span id="spRestar"></span>


@section('scripts')
<script type="text/javascript">
    
function sumar (valor1) {
    var total1 = 0;  
    valor1 = parseInt(valor1); // Convertir el valor a un entero (número).
    
    total1 = document.getElementById('spTotal').innerHTML;
    
    // Aquí valido si hay un valor previo, si no hay datos, le pongo un cero "0".
    total1 = (total1 == null || total1== undefined || total1 == "") ? 0 : total1;
    
    /* Esta es la suma. */
    total1 = (parseInt(total1) + parseInt(valor1));
    
    // Colocar el resultado de la suma en el control "span".
    document.getElementById('spTotal').innerHTML = total1;
}

function restar (valor2) {
    var total2 = 0;  
    valor2 = parseInt(valor2); // Convertir el valor a un entero (número).
    
    total2 = document.getElementById('spRestar').innerHTML;
    
    // Aquí valido si hay un valor previo, si no hay datos, le pongo un cero "0".
    total2 = (total2 == null || total2 == undefined || total2 == "") ? 0 : total2;
    
    /* Esta es la suma. */
    total2 = (parseInt(total2) + parseInt(valor2));
    
    // Colocar el resultado de la suma en el control "span".
    document.getElementById('spRestar').innerHTML = total2;
}


  $(function () {
    //Initialize Select2 Elements
    

    

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })

    
  })
</script>
@stop




  