<!-- Cpma Field -->
<div class="form-group col-sm-12">
    {!! Form::label('descripcion', 'Nombre del Medicamento / Dispositivo:') !!}
    {!! Form::text('descripcion', $descripcion, ['class' => 'form-control','required'=>'required','disabled'=>'disabled']) !!}
</div>

<!-- Cpma Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cpma', 'CPMA:') !!}
    {!! Form::number('cpma', $cpma, ['class' => 'form-control','required'=>'required','min'=>'1', 'autofocus'=>'autofocus']) !!}
</div>

<!-- Stock Incanal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stock_incanal', 'Stock Incanal:') !!}
    {!! Form::number('stock_incanal', $stock_incanal , ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div>

<!-- Unidad Ingreso Field -->
<div class="form-group col-sm-6">
    {!! Form::label('almacen_central', 'Ingreso de Almacén Central Saludpol (DIRSAPOL):') !!}
    {!! Form::number('almacen_central', $almacen_central, ['class' => 'form-control','min'=>'0','required'=>'required','min'=>'0']) !!}
</div>

<!-- Unidad Ingreso Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ingreso_almacen2', 'Ingreso de su Almacén:') !!}
    {!! Form::number('ingreso_almacen2', $ingreso_almacen2, ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div>

<!-- Unidad Consumo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ingreso_proveedor', 'Ingreso directo del Proveedor:') !!}
    {!! Form::number('ingreso_proveedor', $ingreso_proveedor, ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div>

<!-- Transferencia Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ingreso_transferencia', 'Ingreso por Transferencia:') !!}
    {!! Form::number('ingreso_transferencia', $ingreso_transferencia, ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div>

<!-- Unidad de Consumo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unidad_consumo', 'Unidad de Consumo:') !!}
    {!! Form::number('unidad_consumo', $unidad_consumo, ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div>

<!-- Merma Field -->
<div class="form-group col-sm-6">
    {!! Form::label('salida_transferencia', 'Salida por Transferencia:') !!}
    {!! Form::number('salida_transferencia', $salida_transferencia, ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div>

<!-- Salida Transferencia Field -->
<div class="form-group col-sm-6">
    {!! Form::label('merma', 'Pérdida/Merma:') !!}
    {!! Form::number('merma', $merma, ['class' => 'form-control','required'=>'required','min'=>'0']) !!}
</div>

<!-- Fecha Vencimiento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fecha_vencimiento', 'Fecha Vencimiento Próxima:') !!}
    <div class="input-group date">
      <div class="input-group-addon">
        <i class="fa fa-calendar"></i>
      </div>
        {!! Form::date('fecha_vencimiento', $fecha_vencimiento, ['class' => 'form-control pull-right','required'=>'required']) !!}  
    </div>    
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    
    @if($destino==1)
        @if($servicio_id==0)
            <a href="{!! route('cans.medicamentos',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
        @else
            <a href="{!! route('cans.medicamentos_farmacia',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
        @endif
    @else
        @if($servicio_id==0)
            <a href="{!! route('cans.dispositivos',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
        @else
            <a href="{!! route('cans.dispositivos_farmacia',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
        @endif        
    @endif
</div>





  