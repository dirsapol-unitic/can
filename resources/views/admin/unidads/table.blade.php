<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombre Departamento</th>
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($unidads as $unidad)
                        <tr>
                            <td>{!! $unidad->nombre_unidad !!}</td>
                            <td>
                                {!! Form::open(['route' => ['unidads.destroy', $unidad->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a data-toggle="tooltip" title="Editar Unidad!" href="{!! route('unidads.edit', [$unidad->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>