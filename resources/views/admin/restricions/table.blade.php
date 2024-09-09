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
                        <th>Codigo</th>
                        <th>Descripción Restriccion</th>
                        <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($restricions as $restricion)
                        <tr>
                            <td>{!! $restricion->codigo !!}</td>
                            <td>{!! $restricion->descripcion !!}</td>
                            <td>
                                {!! Form::open(['route' => ['restricions.destroy', $restricion->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a href="{!! route('restricions.show', [$restricion->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                    <a href="{!! route('restricions.edit', [$restricion->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
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