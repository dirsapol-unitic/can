<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Operaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($divisions as $division)
                            <tr>
                                <td>{!! $division->nombre_division !!}</td>
                                <td>
                                    {!! Form::open(['route' => ['divisions.destroy', $division->id], 'method' => 'delete']) !!}
                                    <div class='btn-group'>
                                        
                                        <a data-toggle="tooltip" title="Editar División!" href="{!! route('divisions.edit', [$division->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Esta seguro que desea eliminar?')"]) !!}
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