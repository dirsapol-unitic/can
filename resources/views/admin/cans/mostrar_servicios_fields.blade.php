<?php 
use Carbon\Carbon;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box-body">
            <div class="box-body chat" id="chat-box">
                <?php $x=1; ?>
                <table id="example1" class="table table-responsive table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Servicios</th>
                            @if($rol==1)
                            <th>Tiempo Faltante</th>
                            @endif
                            <th>Medicamento</th>
                            <th>Dispositivo</th>        
                            @if($rol==1)
                            <th>Act/Des</th>                            
                            <th>Hab/Desh</th>  
                            <th>Archivos</th>
                            @endif
                            @if($can_id==3)
                            <th>Medicamentos Extraord.</th>
                            <th>Dispositivos Extraord.</th>    
                            @endif
                            @if($can_id==3)
                            <th>Act/Des</th>                                                                                
                            @endif
                            <th>Ampliar Tiempo</th>  
                        </tr>                        
                    </thead>                
                    <tbody>               
                    @foreach($consultas as $id => $consulta)                        
                        <tr>
                            <td>{{$x++}}</td>
                            
                            <td>{{ $consulta->nombre_servicio }}</td>
                            @if($rol==1)
                            <td>@foreach($login as $id => $ingreso)  
                                    @if($consulta->nombre_servicio==$ingreso->nombre_servicio)
                                           <?php                                             
                                            $date = Carbon::now();
                                            $fecha = $date->format('d-m-Y H:i:s');
                                            $fechaFin = Carbon::parse($ingreso->fin_first_login);
                                            
                                            if($ingreso->first_login!=null){
                                                
                                                $fechaActual = Carbon::parse($fecha);
                                                if($fechaActual<=$fechaFin){
                                                    $dias = $fechaActual->diffInDays($fechaFin);
                                                    $hor = $fechaActual->diffInHours($fechaFin);
                                                    $horas=$hor%24;
                                                    $min = $fechaActual->diffInMinutes($fechaFin);
                                                    $minutos = $min%60;
                                                    $valida= 1;
                                                }
                                                else{
                                                    $dias =0;           
                                                    $horas =0;           
                                                    $minutos =0;   

                                                    if($dias==0 and $horas==0 and $minutos==0)
                                                        $valida=0;        
                                                }


                                                if($valida==0)
                                                {   if($consulta->medicamento_cerrado==2 and $consulta->dispositivo_cerrado==2)
                                                        echo 'TERMINADO';
                                                    else
                                                    {   if($consulta->medicamento_cerrado==2 or $consulta->dispositivo_cerrado==1)
                                                            echo 'NO TERMINO SU CAN';
                                                        else
                                                        {   if($consulta->medicamento_cerrado==1 or $consulta->dispositivo_cerrado==2)
                                                                echo 'NO TERMINO SU CAN';
                                                            else
                                                            {   if($consulta->medicamento_cerrado==1 or $consulta->dispositivo_cerrado==1)
                                                                        echo 'NO TERMINO SU CAN';
                                                                else
                                                                        echo 'NO INGRESO';
                                                            }
                                                        }
                                                    }
                                                }
                                                else
                                                    echo $dias.' dia(s) '.$horas.' hora(s) '.$minutos.' min ';
                                            }
                                            else
                                                echo 'NO INGRESO';
                                            
                                            ?>
                                    

                                    @endif
                                @endforeach
                                </td>
                                @endif
                            <td>
                            @if ($consulta->medicamento_cerrado == 1)    
                                 <a href="{!! route('cans.medicamentos_servicios',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-success btn-xs'> <i class="fa fa-medkit"></i> </a>
                            @else
                                @if ($consulta->medicamento_cerrado == 2)
                                    <a href="{!! route('cans.medicamentos_servicios',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-medkit"></i></a>
                                @else
                                    <small class="label label-default">N/H</small>
                                @endif
                            @endif
                            </td>
                            <td>
                            @if ($consulta->dispositivo_cerrado == 1)           
                                 <a href="{!! route('cans.dispositivos_servicios', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-success btn-xs'> <i class="fa fa-stethoscope"></i></a>
                            @else
                                @if ($consulta->dispositivo_cerrado == 2)
                                    <a href="{!! route('cans.dispositivos_servicios', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-stethoscope"></i> </a>
                                @else
                                    <small class="label label-default">N/H</small>
                                @endif
                            @endif
                            </td>
                            @if($rol==1)
                            <td>
                                    <a href="{!! route('cans.activar_servicio_establecimiento',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                
                            </td>
                            <td>
                                @if ( $consulta->medicamento_cerrado == 1 || $consulta->dispositivo_cerrado == 1)  
                                    <a href="{!! route('cans.habilitar_servicio_establecimiento',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-warning btn-xs'><i class="fa fa-fw fa-thumbs-down"></i> </a>
                                @else
                                    <a href="{!! route('cans.habilitar_servicio_establecimiento',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-primary btn-xs'><i class="fa fa-fw fa-thumbs-up"></i> </a>
                                @endif 
                            </td>
                            <td>
                                
                                    <a href="{!! route('cans.listar_archivos_can_servicio',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-info btn-xs'><i class="fa fa-eye"></i> </a>
                                
                            </td>
                            @endif
                            @if($consulta->extraordinario==1)
                                @if ($consulta->medicamento_cerrado_rectificacion == 1)  
                                    <td> 
                                    <a href="{!! route('cans.medicamentos_servicios_rectificacion',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}"  class='btn bg-olive btn-xs'> <i class="fa fa-fw fa-edit"> </a></td>
                                @else
                                    @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                        <td> 
                                            <a href="{!! route('cans.medicamentos_servicios_rectificacion',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}"  class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i> </a></td>
                                    @else
                                             <td> <a href="{!! route('cans.medicamentos_servicios_rectificacion',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}"  class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i> </a></td>
                                    @endif    
                                @endif
                                @if ($consulta->dispositivo_cerrado_rectificacion == 1)  
                                    <td> 
                                        <a href="{!! route('cans.dispositivos_servicios_rectificacion', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn bg-olive btn-xs'> <i class="fa fa-fw fa-edit"> </a></td>
                                @else
                                    @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                        <td> 
                                            <a href="{!! route('cans.dispositivos_servicios_rectificacion', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i>  </a></td>
                                    @else
                                        <td>
                                        <a href="{!! route('cans.dispositivos_servicios_rectificacion', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i>  </a></td>
                                    @endif
                                @endif
                            @endif
                            
                                @if($consulta->extraordinario==1)
                                    <td>                                
                                        <a href="{!! route('cans.activar_servicio_rectificacion_establecimiento',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" disabled class='btn bg-blue btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                    </td>                       
                                @endif
                                @if($rol==1)
                                    <td>                                  
                                        <a href="{!! route('cans.ampliacion_servicio',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'servicio_id'=>$consulta->servicio_id]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-clock-o"></i> </a>
                                    </td>
                                @endif
                            
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>  
    </div>    
</div>