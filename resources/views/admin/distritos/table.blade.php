<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Departamento</th>
                            <th>Provincia</th>
                            <th></th>            
                            <th></th>
                        </tr>
                        <tr>
                            <td>#</td>
                            <td>Departamento</td>
                            <td>Provincia</td>
                            <td>Distrito</td>            
                            <td>Operaciones</td>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($distritos as $key =>$distrito)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $distrito->departamento->nombre_dpto !!}</td>
                            <td>{!! $distrito->provincia->nombre_prov !!}</td>
                            <td>{!! $distrito->nombre_dist !!}</td>
                            <td>
                                {!! Form::open(['route' => ['distritos.destroy', $distrito->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a href="{!! route('distritos.show', [$distrito->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                    <a href="{!! route('distritos.edit', [$distrito->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
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
                            <th>Departamento</th>
                            <th>Provincia</th>
                            <th>Distrito</th>
                            <th>Operaciones</th>   
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>