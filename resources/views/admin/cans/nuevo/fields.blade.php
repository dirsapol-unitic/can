<!-- Cpma Field -->
<div class="form-group col-sm-12">
    {!! Form::label('descripcion', 'Nombre del Medicamento / Dispositivo:') !!}
    {!! Form::select('descripcion',$descripcion , null, ['class' => 'form-control select2', 'autofocus'=>'autofocus']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>    
    @if($nivel==1)
	    @if($destino==1)
	        <a href="{!! route('cans.medicamentos_estimaciones',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
	        
	    @else        
	        <a href="{!! route('cans.dispositivos_estimaciones',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
	        
	    @endif
	@else
	    @if($destino==1)
	        <a href="{!! route('cans.medicamentos_servicios',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,$destino]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
	        
	    @else        
	        <a href="{!! route('cans.dispositivos_servicios',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,$destino]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
	        
	    @endif
	@endif
</div>