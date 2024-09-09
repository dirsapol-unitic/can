<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Consolidado CAN - 2019</h3>
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
                                    @if ($medicamento_cerrado==2)
                                        <a href="{!! route('ipress.ver_consolidado_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'user_id'=>$id_user_responsable]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @else
                                        <a href="#" disabled class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @endif
                                    @if ($dispositivo_cerrado==2)
                                        <a href="{!! route('ipress.ver_consolidado_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'user_id'=>$id_user_responsable]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
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
                <div class="table-responsive">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Responsable de Consolidado</th>
                            <th>Grado</th>
                            <th>Celular</th>
                            <th>Descripción</th>
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($responsables_farmacia as $key => $responsable)
                        <tr>
                            <td>{{$key+1}}</td>                            
                            <td>
                                @if ($nivel==1)
                                        {!! $responsable->nombre !!}<br/>
                                @else
                                        {!! $responsable->nombre !!}<br/>
                                @endif
                            </td>
                            <td>
                                @if ($nivel==1)
                                        {!! $responsable->descripcion_grado !!}<br/>
                                @else
                                        {!! $responsable->descripcion_grado !!}<br/>
                                @endif
                            </td>
                            <td>
                                @if ($nivel==1)
                                        {!! $responsable->celular !!}<br/>
                                @else
                                        {!! $responsable->celular !!}<br/>
                                @endif
                            </td>                            
                            <td>
                                @if ($responsable->rol == 7)
                                        FARMACIA/ALMACEN<br/>
                                @else
                                    @if ($responsable->rol == 4)
                                            FARMACIA<br/>
                                    @else
                                        @if ($responsable->rol == 3)
                                            ALMACEN<br/>
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class='btn-group'>
                                    @if ($responsable->rol == 7)
                                        @if ($medicamento_cerrado ==2)
                                            @if ($nivel==1)
                                                <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,4,'user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                            @else
                                                <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,4,'user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                            @endif       
                                        @else
                                            @if ($medicamento_cerrado ==1)
                                                <a disabled='disabled' href="#" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                            @else
                                                <small class="label label-default">No Habilitado</small>
                                            @endif
                                        @endif    

                                        @if ($dispositivo_cerrado ==2)
                                             @if ($nivel==1)
                                                <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,3,'user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                            @else
                                                <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,3,'user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                            @endif
                                            
                                        @else
                                            @if ($dispositivo_cerrado ==1)
                                                <a disabled='disabled' href="#" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                            @else
                                                <small class="label label-default">No Habilitado</small>
                                            @endif
                                        @endif  
                                    @else
                                        @if ($responsable->rol == 3)
                                            @if ($medicamento_cerrado_consolidado_almacen ==2)
                                                @if ($nivel==1)
                                                    <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,3,'user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                                @else
                                                    <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'A','user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                                @endif       
                                            @else
                                                @if ($medicamento_cerrado_consolidado_almacen ==1)
                                                    <a disabled='disabled' href="#" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                                @else
                                                    <small class="label label-default">No Habilitado</small>
                                                @endif
                                            @endif    

                                            @if ($dispositivo_cerrado_consolidado_almacen ==2)
                                                 @if ($nivel==1)
                                                    <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,3,'user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                                @else
                                                    <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'A','user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                                @endif
                                                
                                            @else
                                                @if ($dispositivo_cerrado_consolidado_almacen ==1)
                                                    <a disabled='disabled' href="#" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                                @else
                                                    <small class="label label-default">No Habilitado</small>
                                                @endif
                                            @endif  
                                        @else
                                            @if ($responsable->rol == 4)
                                                @if ($medicamento_cerrado_consolidado ==2)
                                                    @if ($nivel==1)
                                                        <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,4,'user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                                    @else
                                                        <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'F','user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                                    @endif       
                                                @else
                                                    @if ($medicamento_cerrado_consolidado ==1)
                                                        <a disabled='disabled' href="#" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                                    @else
                                                        <small class="label label-default">No Habilitado</small>
                                                    @endif
                                                @endif    

                                                @if ($dispositivo_cerrado_consolidado ==2)
                                                     @if ($nivel==1)
                                                        <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,4,'user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'F','user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                                    @endif
                                                    
                                                @else
                                                    @if ($dispositivo_cerrado_consolidado ==1)
                                                        <a disabled='disabled' href="#" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <small class="label label-default">No Habilitado</small>
                                                    @endif
                                                @endif  
                                            @endif
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
</div>
@if($nivel>1)
<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Listado</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
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
                                            <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->rubro_id,'user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                        @else
                                            @foreach($responsables as $key => $responsable)
                                                @if ($responsable->servicio_id == $servicio->servicio_id)
                                                    <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,'user_id'=>$responsable->user_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                                @endif
                                            @endforeach    
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
                                            <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->rubro_id,'user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            @foreach($responsables as $key => $responsable)
                                                @if ($responsable->servicio_id == $servicio->servicio_id)
                                                    <a href="{!! route('ipress.descargar_estimacion_farmacia_servicios',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,'user_id'=>$responsable->user_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                                @endif
                                            @endforeach    
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
</div>
@endif