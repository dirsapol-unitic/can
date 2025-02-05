<div class="form-group col-sm-12">
    <div class="pull-right">
        <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
        <a href="{!! route('establecimientos.ver_rubros',[$establecimiento->id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
        
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
                                          
                    @foreach($rubros as $id => $nombre_servicio)

                        <tr>
                            <td>{{$x++}}</td>
                            <td>{{ $nombre_servicio }}</td>
                            <td><input 
                                type="checkbox" 
                                value="{{ $id }}" 
                                {{ $establecimiento->servicios->pluck('id')->contains($id) ? 'checked' : '' }}
                                name="rubros[]">
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
    <a href="{!! route('establecimientos.ver_rubros',[$establecimiento->id]) !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
</div>

