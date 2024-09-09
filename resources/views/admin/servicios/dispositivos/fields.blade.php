<!-- Submit Field -->
<div class="form-group col-sm-12">
   <div class="pull-right">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('servicios.ver_dispositivos',[$servicios->id]) !!}"  class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <?php $x=1; ?>
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Nivel</th>
                            <th>Tipo</th>
                            <th>Uso</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>#</td>
                            <td>Descripción</td>
                            <td>Nivel</td>
                            <td>Tipo</td>
                            <td>Uso</td>
                            <td><input type="checkbox" id="checkTodos" /></td>
                        </tr>
                    </thead>                
                    <tbody>  
                                          
                    @foreach($petitorios as $id => $petitorio)

                        <tr>
                            <td>{{$x++}}</td>
                            <td>{{  $petitorio->descripcion }}</td>
                            <td>{{  $petitorio->descripcion_nivel }}</td>
                            <td>{{ $petitorio->descripcion_tipo_dispositivo }}</td>
                            <td>{{  $petitorio->descripcion_tipo_uso }}</td>
                            <td>
                                <input 
                                id="petitorios"
                                type="checkbox" 
                                value="{{ $petitorio->id }}" 
                                
                                {{ $servicios->petitorios->pluck('id')->contains($petitorio->id) ? 'checked' : '' }}
                                name="petitorios[]">
                            </td>
                        </tr>
                        
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('servicios.ver_dispositivos',[$servicios->id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>
