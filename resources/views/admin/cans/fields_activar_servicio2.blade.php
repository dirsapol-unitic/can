<div class="col-sm-12">
    <div class="col-sm-6">
            <div class="box-body">
               <div class="row">
                    <div class="col-sm-12">
                        <!-- Mes Field -->

                        @if($cerrado_medicamento!=3)
                            <div class="form-group col-sm-12">
                                {!! Form::label('cerrado_medicamento', 'Medicamento Cerrado:') !!}
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="cerrado_medicamento" value="{{ $cerrado_medicamento }}" 
                                        @if($cerrado_medicamento==2) 
                                            checked 
                                        @endif
                                    >
                                </label>
                            </div>
                        @else
                            <input name="cerrado_medicamento" value="3" class="form-control" type="hidden">
                        @endif
                        @if($cerrado_dispositivo!=3)
                            <div class="form-group col-sm-12">
                                {!! Form::label('cerrado_dispositivo', 'Dispositivo Cerrado:') !!}
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="cerrado_dispositivo" value="{{ $cerrado_dispositivo }}" 
                                        @if($cerrado_dispositivo==2) 
                                            checked 
                                        @endif
                                    >
                                </label>
                            </div>
                        @else
                            <input name="cerrado_dispositivo" value="3" class="form-control" type="hidden">                        
                        @endif
                    </div>
                </div>
            </div>
        
    </div>
    
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('cans.mostrar_servicios',[$can->id,$establecimiento_id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    
</div>


