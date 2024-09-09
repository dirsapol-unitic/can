<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($servicios as $key => $servicio)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $servicio->codigo !!}</td>
                            <td>{!! $servicio->nombre_servicio !!}</td>
                            <td>
                                {!! Form::open(['route' => ['unidads.eliminar_servicio', $servicio->id, $unidad_id, $division_id, $establecimiento_id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Esta seguro que desea eliminar?')"]) !!}
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>                  
                            <th>#</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Operaciones</th>
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>