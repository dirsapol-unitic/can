<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Operaciones</th>                  
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($roles as $key =>$role)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{ $role->name }}</td>
                            <td>{!! Form::open(['route' => ['roles.destroy', $role->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('roles.show', [$role->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                <a href="{!! route('roles.edit', [$role->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Estas Seguro que deseas eliminar el rol?')"]) !!}
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
                            <th>Operaciones</th>     
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>

