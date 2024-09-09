<div class="row">
    <div class="col-xs-12">
            <?php $x=1; ?>
            <div class="box-header">
                <a class="btn btn-success pull-left" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('petitorios.exportPetitorio',['xlsx']) !!}">Descargar <i class="fa fa-file-excel-o"></i></a>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <!--table id="example" class="table table-responsive table-striped"-->
                    <table id="example" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>                            
                                
                                <th></th>
                                <th>Tipo_Dispositivo</th>
                                <th>Nivel</th>
                                <th></th>
                                <th></th> 
                                <th></th> 
                                <th></th>                  
                            </tr>
                            <tr>
                                <td><b>N°</b></td>
                                <td><b>Código</b></td>                            
                                <td><b>Descripción</b></td>
                                
                                <td><b>Tipo_Dispositivo</b></td>
                                <td><b>Nivel</b></td>
                                <td><b>Uso</b></td>
                                <td><b>VEN</b></td>
                                <td><b>Estado</b></td>    
                                <td><b>Covid-19</b></td>    
                                @if($rol==1)              
                                <td><b>Ver/Editar</b></td>
                                @else
                                <td><b>Ver</b></td>
                                @endif

                            </tr>
                        </thead>                
                        <tbody>                        
                        @foreach($petitorios as $key => $petitorio)
                            <tr>
                                <td>{{$x++}}</td>
                                <td>{!! $petitorio->codigo_petitorio !!}</td>
                                <td>{!! $petitorio->descripcion !!}</td>
                                
                                <td>{!! $petitorio->descripcion_tipo_dispositivo !!}</td>
                                <td>{!! $petitorio->descripcion_nivel !!}</td>
                                <td>{!! $petitorio->descripcion_tipo_uso !!}</td>
                                <td>{!! $petitorio->ven !!}</td>
                                <td><?php if ($petitorio->estado==1) echo 'ACTIVO'; else echo 'DESACTIVADO';?></td> 
                                <td><?php if ($petitorio->covid==1) echo 'SI'; else echo 'NO';?></td> 
                                <td>
                                @if($rol==1)
                                <div class='btn-group'>
                                    <a data-toggle="tooltip" title="Ver Registro!" href="{!! route('petitorios.show', [$petitorio->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                    <a data-toggle="tooltip" title="Editar Registro División!" href="{!! route('petitorios.edit', [$petitorio->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                </div>
                                @else
                                <div class='btn-group'>
                                    <a data-toggle="tooltip" title="Ver Registro!" href="{!! route('petitorios.show', [$petitorio->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>                                    
                                </div>
                                @endif
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