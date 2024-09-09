<div class="row">
    <div class="col-xs-12">
        <div>
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
                    @foreach($grados as $key => $grado)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $grado->descripcion !!}</td>
                            <td>
                                {!! Form::open(['route' => ['grados.destroy', $grado->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a href="{!! route('grados.show', [$grado->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                    <a href="{!! route('grados.edit', [$grado->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Esta seguro?')"]) !!}
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