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
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{!! $nombre_establecimiento !!}</td>
                            <td>
                                <div class='btn-group'>
                                    @if ($medicamento_cerrado_consolidado==2)
                                        <a href="{!! route('nacional.descargar_consolidado_ipress',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @else
                                        <a href="#" disabled class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @endif
                                    @if ($dispositivo_cerrado_consolidado==2)
                                        <a href="{!! route('nacional.descargar_consolidado_ipress',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
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
                            @if (Auth::user()->rol==4)
                                <th>Medicamentos</th>
                                <th>Dispositivos</th>
                            @endif
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($servicios as $key => $servicio)
                        <tr>
                            <td>{{$key+1}}</td>                            
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($nivel==1)
                                        @if ($responsable->servicio_id == $servicio->rubro_id)
                                            {!! $responsable->nombre !!}<br/>
                                        @endif
                                    @else
                                        @if ($responsable->servicio_id == $servicio->servicio_id)
                                            {!! $responsable->nombre !!}<br/>
                                        @endif
                                    @endif

                                @endforeach    
                            </td>
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($nivel==1)
                                        @if ($responsable->servicio_id == $servicio->rubro_id)
                                            {!! $responsable->descripcion_grado !!}<br/>
                                        @endif
                                    @else
                                        @if ($responsable->servicio_id == $servicio->servicio_id)
                                            {!! $responsable->descripcion_grado !!}<br/>
                                        @endif
                                    @endif
                                @endforeach    
                            </td>
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($nivel==1)
                                        @if ($responsable->servicio_id == $servicio->rubro_id)
                                            {!! $responsable->celular !!}<br/>
                                        @endif
                                    @else
                                        @if ($responsable->servicio_id == $servicio->servicio_id)
                                            {!! $responsable->celular !!}<br/>
                                        @endif
                                    @endif
                                @endforeach    
                            </td>                            
                            <td> 
                                @if ($nivel==1)
                                    {!! $servicio->descripcion !!} 
                                @else
                                    {!! $servicio->nombre_servicio !!} 
                                @endif
                            </td>
                            <td>
                                <div class='btn-group'>
                                    @if ($servicio->medicamento_cerrado ==2)
                                        @if ($nivel==1)
                                            <a href="{!! route('nacional.ver_rubros_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->rubro_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                        @else
                                            <a href="{!! route('nacional.ver_rubros_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                        @endif       
                                    @else
                                        @if ($servicio->medicamento_cerrado ==3)
                                            <a disabled='disabled' href="#" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                        @else
                                            <small class="label label-default">No Habilitado</small>
                                        @endif
                                    @endif    

                                    @if ($servicio->dispositivo_cerrado ==2)
                                         @if ($nivel==1)
                                            <a href="{!! route('nacional.ver_rubros_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->rubro_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <a href="{!! route('nacional.ver_rubros_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                        @endif
                                        
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
