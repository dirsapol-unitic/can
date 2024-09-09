<div class="row">
<?php
    use Carbon\Carbon;

    $date = Carbon::now();
            //$fecha = $date->format('d-m-Y HH:mm:ss');            
            $fecha = $date->now();
            $fechaFin = Carbon::parse(Auth::user()->fin_first_login);
                                         
            $fechaActual = Carbon::parse($fecha);
            
            if($fechaActual<=$fechaFin)
                $diasDiferencia = $fechaActual->diffInMinutes($fechaFin);
            else
                $diasDiferencia =0;       

            

    ?>
    <div class="col-xs-12">
        <div>
            <?php $x=1;?>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Descripcion</td>   
                            <th>Actualizado</th>                                   
                            <td>Medicamento</td>
                            <td>Dispositivo</td>
                            <td>Atenciones</td>
                            <td>Observacion</td>
                            <td>Descargar</td>
                            <td>Subir CAN Firmado</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $id_establecimiento = Auth::user()->establecimiento_id; $x=1; ?>                    
                    @foreach($cans as $can)
                        @if($can->can_id==3)                        
                            <tr>
                                <td><?php echo $x++;?></td>
                                <td> CAN2020 - Ratificacion Julio ,2020</a></td>
                                <td></td>
                                <td>
                                    @if ($can->medicamento_cerrado_rectificacion==1 )
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('estimacion_servicio.cargar_productos_rectificacion', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>1,'medicamento_cerrado'=>1]) !!}" class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-maroon btn-md'></a>
                                        @endif
                                     @else
                                        <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                    @endif
                                </td>
                                <td>
                                    @if ($can->dispositivo_cerrado_rectificacion==1)
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('estimacion_servicio.cargar_productos_rectificacion', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>2,'medicamento_cerrado'=>1]) !!}" class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-maroon btn-md'></a>
                                        @endif
                                    @else
                                        <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a>
                                    @endif
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class='btn-group'>                                 
                                        @if ($can->medicamento_cerrado_rectificacion==2)
                                                <a href="{!! route('estimacion_servicio.descargar_servicio_rectificacion',['tipo'=>1,'can_id'=>$can->can_id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                        @endif
                                        @if ($can->dispositivo_cerrado_rectificacion==2)
                                                <a href="{!! route('estimacion_servicio.descargar_servicio_rectificacion',['tipo'=>2,'can_id'=>$can->can_id]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <a href="#" disabled class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @endif
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{!! $x++ !!}</td>
                                <td>{!! $can->nombre_can !!} - {!! $can->desc_mes !!}, {!! $can->ano !!}</td>
                                <td>
                                    <div class='btn-group'>
                                    @if ($can->medicamento_cerrado==1) <!--cerrado = 2 abierto=1 no habilitado=3-->
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('estimacion_servicio.cargar_medicamentos_servicios', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>1]) !!}" class='btn btn-success btn-md'><i class="fa fa-medkit"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                        @endif
                                    @else
                                        @if ($can->medicamento_cerrado==3)
                                            <small class="label label-default"></small>         
                                        @else
                                            <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                        @endif
                                    @endif
                                    
                                    </div>      
                                </td>
                                <td>    
                                    <div class='btn-group'>
                                        @if ($can->dispositivo_cerrado==1) <!--cerrado = 2 abierto=1 no habilitado=3-->
                                            @if ($diasDiferencia!=0 )
                                                <a href="{!! route('estimacion_servicio.cargar_medicamentos_servicios', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>2
                                                    ]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                            @else
                                                <a href="#" disabled class='btn bg-red btn-md'></a>
                                        @endif
                                        @else
                                            @if ($can->dispositivo_cerrado==3)
                                                <small class="label label-default"></small>         
                                            @else
                                                <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a>                                 
                                            @endif
                                        @endif      
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class='btn-group'>
                                        @if ($can->medicamento_cerrado==1)
                                            <a href="" class='btn bg-orange btn' disabled ><i class="fa fa-medkit"></i></a>
                                        @else
                                            @if ($can->medicamento_cerrado==2)
                                                <a href="{!! route('estimacion_servicio.descargar_servicio',['tipo'=>1,'can_id'=>$can->can_id]) !!}" class='btn bg-orange btn'><i class="fa fa-medkit"></i></a>
                                            @endif
                                        @endif
                                        @if ($can->dispositivo_cerrado==1)
                                            <a href="" class='btn bg-olive btn' disabled ><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            @if ($can->dispositivo_cerrado==2)
                                                <a href="{!! route('estimacion_servicio.descargar_servicio',['tipo'=>2,'can_id'=>$can->can_id]) !!}" class='btn bg-olive btn'><i class="fa fa-stethoscope"></i></a>
                                            @endif
                                        @endif
                                    </div>
                                </td>                                
                                 <td>
                                    @if($can->can_id==$can_id)
                                        @if ($can->medicamento_cerrado!=1 and $can->dispositivo_cerrado!=1)
                                            <a href="{!! route('farmacia_servicios.listar_archivos_nivel2y3',['can_id'=>$can->can_id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-upload"></i></a>
                                        @else
                                            <a href="#" disabled class='btn btn-primary btn-md'><i class="fa fa-upload"></i></a>
                                        @endif
                                    @else
                                        <a href="{!! route('farmacia_servicios.listar_archivos_nivel2y3',['can_id'=>$can->can_id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-eye"></i></a>
                                    @endif
                                    
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>{!! $x++ !!}</td>
                                <td>{!! $can->nombre_can !!} - {!! $can->desc_mes !!}, {!! $can->ano !!}</td>
                                <td>
                                    <div class='btn-group'>
                                    @if ($can->medicamento_cerrado==1) <!--cerrado = 2 abierto=1 no habilitado=3-->
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('estimacion_servicio.cargar_medicamentos_servicios', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>1]) !!}" class='btn btn-success btn'><i class="fa fa-medkit"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                        @endif
                                    @else
                                        @if ($can->medicamento_cerrado==3)
                                            <small class="label label-default"> No Habilitado</small>         
                                        @else
                                           <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>                                  
                                        @endif
                                    @endif
                                    
                                    </div>      
                                </td>
                                <td>    
                                    <div class='btn-group'>
                                        @if ($can->dispositivo_cerrado==1) <!--cerrado = 2 abierto=1 no habilitado=3-->
                                            @if ($diasDiferencia!=0 )
                                                <a href="{!! route('estimacion_servicio.cargar_medicamentos_servicios', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>2]) !!}" class='btn btn-success btn'><i class="fa fa-stethoscope"></i></a>
                                            @else
                                                <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a> 
                                        @endif
                                        @else
                                            @if ($can->dispositivo_cerrado==3)
                                                <small class="label label-default"> No Habilitado</small>         
                                            @else
                                                <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a>                                   
                                            @endif
                                        @endif      
                                    </div>
                                </td>
                                <td>@if($can->can_id>6)
                                        @if ($can->medicamento_cerrado==1 and $can->dispositivo_cerrado==1)
                                            <a href="{!! route('estimacion_servicio.editar_atenciones_c',['can_id'=>$can->can_id]) !!}" class='btn bg-orange btn-md'><i class="fa fa-edit"></i></a></td>
                                        @else
                                            <a href="" class='btn bg-orange btn-md' disabled ><i class="fa fa-edit"></i></a></td>
                                        @endif
                                    @endif
                                <td>
                                    <a href="{!! route('estimacion_servicio.listar_observaciones_nivel2y3',['can_id'=>$can->can_id]) !!}" class='btn bg-purple btn-md'><i class="fa fa-tripadvisor"></i></a>
                                </td>       
                                <td>
                                    <div class='btn-group'>
                                        @if ($can->medicamento_cerrado==1)
                                            <a href="" class='btn bg-orange btn' disabled ><i class="fa fa-medkit"></i></a>
                                        @else
                                            @if ($can->medicamento_cerrado==2)
                                                <a href="{!! route('estimacion_servicio.descargar_servicio',['tipo'=>1,'can_id'=>$can->can_id]) !!}" class='btn bg-orange btn'><i class="fa fa-medkit"></i></a>
                                            @endif
                                        @endif
                                        @if ($can->dispositivo_cerrado==1)
                                            <a href="" class='btn bg-olive btn' disabled ><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            @if ($can->dispositivo_cerrado==2)
                                                <a href="{!! route('estimacion_servicio.descargar_servicio',['tipo'=>2,'can_id'=>$can->can_id]) !!}" class='btn bg-olive btn'><i class="fa fa-stethoscope"></i></a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                
                                 <td>
                                    @if($can->can_id==$can_id)
                                        @if ($can->medicamento_cerrado!=1 and $can->dispositivo_cerrado!=1)
                                            <a href="{!! route('farmacia_servicios.listar_archivos_nivel2y3',['can_id'=>$can->can_id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-upload"></i></a>
                                        @else
                                            <a href="#" disabled class='btn btn-primary btn-md'><i class="fa fa-upload"></i></a>
                                        @endif
                                    @else
                                        <a href="{!! route('farmacia_servicios.listar_archivos_nivel2y3',['can_id'=>$can->can_id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-eye"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>                    
                </table>
            </div>            
        </div>        
    </div>    
</div>


