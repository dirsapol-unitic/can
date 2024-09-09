<!-- Descripcion Field -->
<div class="form-group col-sm-9">
    {!! Form::label('descripcion', 'Descripcion:') !!}
    {!! Form::text('descripcion', null, ['class' => 'form-control']) !!}
</div>
    
    <!-- Mes Field -->
	<div class="form-group col-sm-12">
		<input 
	        type="radio"  
	        value="1" 
	        @if ($tipo==1)
	            name="consolidado" checked="checked">
	        @else
	            {{ $consolidado==1 ? 'checked' : '' }}
	            name="consolidado">
	        @endif
	    {!! Form::label('farmacia', 'Consolidado Farmacia:') !!}
	    <br>
	    <input 
	        type="radio" 
	        value="2" 
	        @if ($tipo==1)
	            name="consolidado">
	        @else
	            {{ $consolidado==2 ? 'checked' : '' }}
	            name="consolidado">
	        @endif    
	    {!! Form::label('almacen', 'Consolidado Almacén:') !!}
	</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('rubros.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

