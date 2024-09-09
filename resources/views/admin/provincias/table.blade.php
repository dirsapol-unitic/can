<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Departamento</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>#</td>
                            <td>Provincia</td>
                            <td>Departamento</td>
                            <td>Operaciones</td>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($provincias as $key => $provincia)
                        <tr>
                            <td>{{$key+1}}</td>
                            <!--td>{!! $provincia->nombre_prov !!}</td-->
                            <td>{!! $provincia->departamento->nombre_dpto!!}</td>
                            <td>{!! $provincia->nombre_prov !!}</td>
                            <td>
                                {!! Form::open(['route' => ['provincias.destroy', $provincia->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a href="{!! route('provincias.show', [$provincia->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                    <a href="{!! route('provincias.edit', [$provincia->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
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
                            <th>Provincia</th>
                            <th>Departamento</th>
                            <th>Operaciones</th>   
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>