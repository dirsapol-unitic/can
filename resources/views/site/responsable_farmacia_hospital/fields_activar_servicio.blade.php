    @if($cerrado_medicamento!=3)
        <div class="form-group col-sm-12">
            {!! Form::label('cerrado_medicamento', 'Medicamento Cerrado:') !!}
            <label class="checkbox-inline">
                <input type="checkbox" name="cerrado_medicamento" value="1" 
                    @if($cerrado_medicamento==2) 
                        checked 
                    @endif
                >
            </label>
        </div>
    @else
        <input type="hidden" name="cerrado_medicamento" value="3">
    @endif
    @if($cerrado_dispositivo!=3)
        <div class="form-group col-sm-12">
            {!! Form::label('cerrado_dispositivo', 'Dispositivo Cerrado:') !!}
            <label class="checkbox-inline">
                <input type="checkbox" name="cerrado_dispositivo" value="1" 
                    @if($cerrado_dispositivo==2) 
                        checked 
                    @endif
                >
            </label>
        </div> 
    @else
        <input type="hidden" name="cerrado_dispositivo" value="3">
    @endif
    
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('farmacia.listar_servicios',[$can->id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>    
</div>

