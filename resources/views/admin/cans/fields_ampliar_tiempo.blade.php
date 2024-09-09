<div class="col-sm-12">
            <div class="box-body">
               <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group col-sm-3">
                            {!! Form::label('cerrado_medicamento', 'Dias') !!}
                             <select class="form-control select2" name="tiempo_id" id="tiempo_id">
                                    <option value="0" > 0 dia </option>
                                    <option value="1" > 1 dia </option>
                                    <option value="2" > 2 dias </option>
                                    <option value="3" > 3 dias </option>
                                    <option value="4" > 4 dias </option>
                                    <option value="5" > 5 dias </option>
                                    <option value="6" > 6 dias </option>
                                    <option value="7" > 7 dias </option>
                            </select> 
                        </div>

                        <div class="form-group col-sm-3">
                            {!! Form::label('cerrado_medicamento', 'Horas') !!}
                             <select class="form-control select2" name="hora_id" id="hora_id">
                                
                                    <option value="0" > 0 hora </option>
                                    <option value="1" > 1 hora </option>
                                    <option value="2" > 2 horas </option>
                                    <option value="3" > 3 horas </option>
                                    <option value="4" > 4 horas </option>
                                    <option value="5" > 5 horas </option>
                                    <option value="6" > 6 horas </option>
                                    <option value="7" > 7 horas </option>
                                    <option value="8" > 8 horas </option>
                                    <option value="9" > 9 horas </option>
                                    <option value="10" > 10 horas </option>
                                    <option value="11" > 11 horas </option>
                                    <option value="12" > 12 horas </option>
                                    <option value="13" > 13 horas </option>
                                    <option value="14" > 14 horas </option>
                                    <option value="15" > 15 horas </option>
                                    <option value="16" > 16 horas </option>
                                    <option value="17" > 17 horas </option>
                                    <option value="18" > 18 horas </option>
                                    <option value="19" > 19 horas </option>
                                    <option value="20" > 20 horas </option>
                                    <option value="21" > 21 horas </option>
                                    <option value="22" > 22 horas </option>
                                    <option value="23" > 23 horas </option>
                            </select> 
                        </div>
                    </div>
                </div>
            </div>        
    
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('cans.show',[$can->id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    
</div>


