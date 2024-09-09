<div class="row">
    <div class="col-xs-12">
            <?php $x=1; ?>
            <div class="box-header">
                <a class="btn btn-success pull-left" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('petitorios.exportPetitorio',['xlsx']) !!}">Descargar <i class="fa fa-file-excel-o"></i></a>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example" class="table table-responsive table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>                            
                                <th></th>
                                <th></th>
                                <th>Tipo_Dispositivo</th>
                                <th>Nivel</th>
                                <th>Tipo Uso</th>
                                <th></th>                  
                            </tr>
                            <tr>
                                <td><b>N°</b></td>
                                <td><b>Código</b></td>                            
                                <td><b>Descripción</b></td>
                                <td><b>Precio</b></td>
                                <td><b>Tipo_Dispositivo</b></td>
                                <td><b>Nivel</b></td>
                                <td><b>Tipo Uso</b></td>
                                <td><b>Ver/Editar/Eliminar</b></td>                  
                            </tr>
                        </thead>                
                        <tbody>                        
                        @foreach($petitorios as $key => $petitorio)
                            <tr>
                                <td>{{$x++}}</td>
                                <td>{!! $petitorio->codigo_petitorio !!}</td>
                                <td>{!! $petitorio->descripcion !!}</td>
                                <td>{!! $petitorio->precio !!}</td>
                                <td>{!! $petitorio->descripcion_tipo_dispositivo !!}</td>
                                <td>{!! $petitorio->descripcion_nivel !!}</td>
                                <td>{!! $petitorio->descripcion_tipo_uso !!}</td>
                                <td>{!! Form::open(['route' => ['petitorios.destroy', $petitorio->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a data-toggle="tooltip" title="Ver Registro!" href="{!! route('petitorios.show', [$petitorio->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                    <a data-toggle="tooltip" title="Editar Registro División!" href="{!! route('petitorios.edit', [$petitorio->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
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
</div>