<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Consolidado CAN - {!!$ano!!}</h3>
            </div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Establecimiento</th>
                            @if (Auth::user()->rol==3 || Auth::user()->rol==4 )
                                @if ($nivel_id>1)
                                    <th>Medicamentos</th>
                                    <th>Dispositivos</th>
                                @endif    
                            @endif
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{!! $nombre_establecimiento !!}</td>
                            @if (Auth::user()->rol==3 || Auth::user()->rol==4 )
                                <td>
                                    @if ($medicamento_cerrado_consolidado==1 )
                                        <a href="{!! route('farmacia_servicios.editar_consolidado_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id]) !!}" class='btn bg-purple btn-xs'><i class="fa fa-medkit"></i></a>
                                     @else
                                        <a href="#" disabled class='btn bg-red btn-xs'><i class="fa fa-medkit"></i></a>
                                    @endif
                                </td>
                                <td>
                                    @if ($dispositivo_cerrado_consolidado==1)
                                        <a href="{!! route('farmacia_servicios.editar_consolidado_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id]) !!}" class='btn bg-purple btn-xs'><i class="fa fa-stethoscope"></i></a>
                                    @else
                                        <a href="#" disabled class='btn bg-red btn-xs'><i class="fa fa-stethoscope"></i></a> 
                                    @endif
                                </td>
                            @endif
                            <td>
                                <div class='btn-group'>
                                    @if ($medicamento_cerrado_consolidado==2)
                                        <a href="{!! route('farmacia_servicios.ver_consolidado_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @else
                                        <a href="#" disabled class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @endif
                                    @if ($dispositivo_cerrado_consolidado==2)
                                        <a href="{!! route('farmacia_servicios.ver_consolidado_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                    @else
                                        <a href="#" disabled class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>
<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Listado</h3>
            </div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Responsable del Llenado del CAN</th>
                            <th>Grado</th>
                            <th>Celular</th>
                            <th>Descripción</th>
                            <th>Medicamentos</th>
                            <th>Dispositivos</th>
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($servicios as $key => $servicio)
                        <tr>
                            <td>{{$key+1}}</td>                            
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($responsable->servicio_id == $servicio->servicio_id)
                                        {!! $responsable->nombre !!}<br/>
                                    @endif
                                @endforeach    
                            </td>
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($responsable->servicio_id == $servicio->servicio_id)
                                        {!! $responsable->descripcion_grado !!}<br/>
                                    @endif
                                @endforeach    
                            </td>
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($responsable->servicio_id == $servicio->servicio_id)
                                        {!! $responsable->celular !!}<br/>
                                    @endif
                                @endforeach    
                            </td>                            
                            <td>
                                @if ($medicamento_cerrado_consolidado==2 && $dispositivo_cerrado_consolidado==2 )
                                    {!! $servicio->nombre_servicio !!}
                                @else
                                    @if ($servicio->medicamento_cerrado ==2 || $dispositivo_cerrado_consolidado==2)
                                        <a href="{!! route('farmacia_servicios.activar_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}"'>{!! $servicio->nombre_servicio !!}</a>
                                    @else
                                        {!! $servicio->nombre_servicio !!}
                                    @endif
                                @endif
                            </td>                            
                            <td>                  
                                @if ($servicio->medicamento_cerrado ==1)
                                    <small class="label label-default">No Habilitado</small>
                                @else
                                   @if ($servicio->medicamento_cerrado ==2)
                                        <small class="label label-danger">Cerrado</small>
                                    @else
                                        <small class="label label-success">Abierto</small>
                                    @endif        
                                @endif                               
                            </td>
                            <td>
                                @if ($servicio->dispositivo_cerrado ==1)
                                        <small class="label label-default">No Habilitado</small>
                                @else
                                    @if ($servicio->dispositivo_cerrado ==2)
                                        <small class="label label-danger">Cerrado</small>
                                    @else
                                        <small class="label label-success">Abierto</small>
                                    @endif
                                @endif    
                            </td>
                            <td>
                                <div class='btn-group'>
                                    @if ($servicio->medicamento_cerrado ==2)
                                        <a href="{!! route('farmacia_servicios.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @else
                                        @if ($servicio->medicamento_cerrado ==3)
                                            <a disabled='disabled' href="#" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                        @else
                                            <small class="label label-default">No Habilitado</small>
                                        @endif
                                    @endif    

                                    @if ($servicio->dispositivo_cerrado ==2)
                                        <a href="{!! route('farmacia_servicios.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                    @else
                                        @if ($servicio->dispositivo_cerrado ==3)
                                            <a disabled='disabled' href="#" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <small class="label label-default">No Habilitado</small>
                                        @endif
                                    @endif  
                                </div>
                            </td>    
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>
