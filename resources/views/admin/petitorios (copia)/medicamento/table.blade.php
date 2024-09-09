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
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Operaciones</th>                  
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($petitorios as $key => $petitorio)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $petitorio->codigo_petitorio !!}</td>
                            <td>{!! $petitorio->descripcion !!}</td>
                            <td>{!! $petitorio->precio !!}</td>
                            <td>{!! Form::open(['route' => ['petitorios.destroy', $petitorio->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('petitorios.show', [$petitorio->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                <a href="{!! route('petitorios.edit', [$petitorio->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
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