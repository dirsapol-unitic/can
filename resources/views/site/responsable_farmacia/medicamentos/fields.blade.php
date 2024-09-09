<div class="form-group col-sm-12">
    <div class="pull-right">
        <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
        @if($redireccion==1)
        <a href="{!! route('farmacia.cargar_medicamentos',[$can_id,$tipo]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>    
        @else
            @if($redireccion==0)
                <a href="{!! route('farmacia.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
            @else
                <a href="{!! route('farmacia.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
            @endif
        @endif
    </div>    
</div>

<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <?php $x=1; ?>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th><input type="checkbox" id="checkTodos" /></th>
                        </tr>
                    </thead>                
                    <tbody>  
                                          
                    @foreach($petitorios as $id => $descripcion)

                        <tr>
                            <td>{{$x++}}</td>
                            <td>{{ $descripcion }}</td>
                            <td><input 
                                type="checkbox" 
                                value="{{ $id }}" 
                                
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
    <a href="{!! route('farmacia.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

