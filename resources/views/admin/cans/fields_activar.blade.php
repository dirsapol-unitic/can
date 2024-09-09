<!-- Mes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cerrado_medicamento', 'Medicamento Cerrado:') !!}
    <label class="checkbox-inline">
        <input type="checkbox" name="cerrado_medicamento" value="{{ $cerrado_medicamento }}" 
    @if ($cerrado_medicamento ==2) 
    checked 
    @endif>
    </label>
</div>

<div class="form-group col-sm-6">
    {!! Form::label('cerrado_dispositivo', 'Dispositivo Cerrado:') !!}
    <label class="checkbox-inline">
        <input type="checkbox" name="cerrado_dispositivo" value="{{ $cerrado_dispositivo }}" 
    @if ($cerrado_dispositivo ==2) 
    checked 
    @endif>
    </label>
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('cans.show',$can->id) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    
</div>

