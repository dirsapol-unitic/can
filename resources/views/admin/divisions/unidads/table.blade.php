<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Servicios</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($unidads as $key => $unidad)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $unidad->nombre_unidad !!}</td>
                            <td>
                                <div class='btn-group'>
                                    <a data-toggle="tooltip" title="Ver Servicios!" href="{!! route('unidads.ver_servicios', [$unidad->id,$division_id,$establecimiento_id]) !!}" class='btn bg-purple btn-xs'>
                                    <i class="fa fa-th-large"></i></a>
                                </div>
                            </td>  
                            <td>
                                {!! Form::open(['route' => ['divisions.eliminar_unidad', $unidad->id, $division_id, $establecimiento_id], 'method' => 'delete']) !!}
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
                            <th>Descripción</th>
                            <th>Servicios</th>
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>