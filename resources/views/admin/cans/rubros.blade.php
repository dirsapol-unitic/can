<?php 
use Carbon\Carbon;
?>
<div class="row">
    <div class="col-xs-12">
            <!-- Mes Field -->
        <div class="col-xs-3 form-group">
            {!! Form::label('mes', 'Mes:') !!}
            {!! $can->mes !!}
        </div>
        <!-- Ano Field -->
        <div class="col-xs-3 form-group">
            {!! Form::label('ano', 'Año:') !!}
            {!! $can->ano !!}
        </div> 
        <div class="col-xs-6 form-group">
            {!! Form::label('establecimiento', 'Establecimiento:') !!}
            {!! $establecimientos->nombre_establecimiento !!}
        </div>        
    </div>    
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box-body">
            <div class="box-body chat" id="chat-box">
                <?php $x=1; ?>
                <table id="example" class="table table-responsive table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Rubros</th>
                            @if($rol==1)
                            <th>Tiempo Faltante</th>
                            @endif
                            <th>Medicamento</th>
                            <th>Dispositivo</th>        
                            @if($rol==1)
                            <th>Act/Des</th>    
                            <th>Archivos</th>
                            <th>Ampliar Tiempo</th>                                                  
                            @endif
                        </tr>                        
                    </thead>                
                    <tbody>               
                        
                        <tr>
                            <td>1.-</td>
                            <td>Productos Farmaceuticos</td>
                            @if($rol==1)
                            <td>@foreach($login_comite as $id => $ingreso)  
                                    @if($ingreso->rol== 3)
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
                                                {   if($rubro_pf==2)
                                                        echo 'TERMINADO';
                                                    else
                                                    {   if($rubro_pf==1)
                                                            echo 'NO TERMINO SU CAN2020';
                                                        
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
                            @if ($rubro_pf == 1)    
                                 <a href="{!! route('cans.medicamentos_servicios_rubros',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>1]) !!}" class='btn btn-success btn-xs'> Abierto </a>
                            @else
                                @if ($rubro_pf == 2)
                                    <a href="{!! route('cans.medicamentos_servicios_rubros',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>1]) !!}" class='btn btn-danger btn-xs'> Cerrado </a>
                                @endif    
                            @endif
                            </td>
                            <td>
                                <small class="label label-default">N/A</small>
                            </td>
                            @if($rol==1)
                            <td>  
                                    <a href="{!! route('cans.activar_rubro_establecimiento',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>1]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                
                            </td>
                            <td>
                                    <a href="{!! route('cans.listar_archivos_can_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>1]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-eye"></i> </a>
                                
                            </td>
                            <td>
                                <a href="{!! route('cans.ampliacion_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>3]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-clock-o"></i> </a>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td>2.-</td>
                            <td>Material Biomedico, Instrumental Quirurgico y Productos Afines</td>
                            @if($rol==1)
                            <td>@foreach($login_comite as $id => $ingreso)  
                                    @if($ingreso->rol==4)
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
                                                {   if($rubro_mb_iq_pa==2)
                                                        echo 'TERMINADO';
                                                    else
                                                    {   if($rubro_mb_iq_pa==1)
                                                            echo 'NO TERMINO SU CAN2020';
                                                        
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
                                <small class="label label-default">N/A</small>
                            </td>
                            <td>
                            @if ($rubro_mb_iq_pa == 1)    
                            
                                 <a href="{!! route('cans.dispositivos_servicios_rubros',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>2]) !!}" class='btn btn-success btn-xs'> Abierto </a>
                            @else
                                @if ($rubro_mb_iq_pa == 2)
                                      <a href="{!! route('cans.dispositivos_servicios_rubros', ['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>2]) !!}" class='btn btn-danger btn-xs'> Cerrado </a>
                                @else
                                    <small class="label label-default">N/A</small>
                                @endif
                            @endif
                            </td>
                            @if($rol==1)
                            <td>
                                  
                                    <a href="{!! route('cans.activar_rubro_establecimiento',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>2]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                
                            </td>
                            <td>
                                    <a href="{!! route('cans.listar_archivos_can_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>2]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-eye"></i> </a>
                                
                            </td>
                            <td>
                                <a href="{!! route('cans.ampliacion_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>4]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-clock-o"></i> </a>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td>3.-</td>
                            <td>Material e Insumos Dentales</td>
                            @if($rol==1)
                            <td>@foreach($login_comite as $id => $ingreso)  
                                    @if($ingreso->rol==5)
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
                                                {   if($rubro_mid==2)
                                                        echo 'TERMINADO';
                                                    else
                                                    {   if($rubro_mid==1)
                                                            echo 'NO TERMINO SU CAN2020';
                                                        
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
                                <small class="label label-default">N/A</small>
                            </td>
                            <td>
                            @if ($rubro_mid == 1)    
                                 <a href="{!! route('cans.dispositivos_servicios_rubros',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>3]) !!}" class='btn btn-success btn-xs'> Abierto </a>
                            @else
                                @if ($rubro_mid == 2)
                                      <a href="{!! route('cans.dispositivos_servicios_rubros', ['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>3]) !!}" class='btn btn-danger btn-xs'> Cerrado </a>
                                @else
                                    <small class="label label-default">N/A</small>
                                @endif
                            @endif
                            </td>
                            @if($rol==1)
                            <td>
                                  
                                    <a href="{!! route('cans.activar_rubro_establecimiento',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>3]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                
                            </td>
                            <td>
                                
                                    <a href="{!! route('cans.listar_archivos_can_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>3]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-eye"></i> </a>
                                
                            </td>
                            <td>
                                <a href="{!! route('cans.ampliacion_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>5]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-clock-o"></i> </a>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td>4.-</td>
                            <td>Material e Insumos de Laboratorio</td>
                            @if($rol==1)
                            <td>@foreach($login_comite as $id => $ingreso)  
                                    @if($ingreso->rol==6)
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
                                                {   if($rubro_mil==2)
                                                        echo 'TERMINADO';
                                                    else
                                                    {   if($rubro_mil==1)
                                                            echo 'NO TERMINO SU CAN2020';
                                                        
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
                                <small class="label label-default">N/A</small>
                            </td>
                            <td>
                            @if ($rubro_mil == 1)    
                                  <a href="{!! route('cans.dispositivos_servicios_rubros', ['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>4]) !!}" class='btn btn-success btn-xs'> Abierto </a>
                            @else
                                @if ($rubro_mil == 2)
                                     <a href="{!! route('cans.dispositivos_servicios_rubros', ['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>4]) !!}" class='btn btn-danger btn-xs'> Cerrado </a>
                                @else
                                    <small class="label label-default">N/A</small>
                                @endif
                            @endif
                            </td>
                            @if($rol==1)
                            <td>
                                  
                                    <a href="{!! route('cans.activar_rubro_establecimiento',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>4]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                
                            </td>
                            <td>
                                
                                <a href="{!! route('cans.listar_archivos_can_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>4]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-eye"></i> </a>
                                
                            </td>
                            <td>
                                <a href="{!! route('cans.ampliacion_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>6]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-clock-o"></i> </a>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td>5.-</td>
                            <td>Material Fotografico y Fonotecnico</td>
                            @if($rol==1)
                            <td>@foreach($login_comite as $id => $ingreso)                              
                                    @if($ingreso->rol==8)
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
                                                {   if($rubro_mff==2)
                                                        echo 'TERMINADO';
                                                    else
                                                    {   if($rubro_mff==1)
                                                            echo 'NO TERMINO SU CAN2020';
                                                        
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
                                <small class="label label-default">N/A</small>
                            </td>

                            <td>
                            @if ($rubro_mff == 1)    
                                  <a href="{!! route('cans.dispositivos_servicios_rubros', ['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>5]) !!}" class='btn btn-success btn-xs'> Abierto </a>
                            @else
                                @if ($rubro_mff == 2)
                                     <a href="{!! route('cans.dispositivos_servicios_rubros', ['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>5]) !!}" class='btn btn-danger btn-xs'> Cerrado </a>
                                @else
                                    <small class="label label-default">N/A</small>
                                @endif
                            @endif
                            </td>
                            @if($rol==1)
                            <td>
                                  
                                    <a href="{!! route('cans.activar_rubro_establecimiento',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>5]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                
                            </td>
                            <td>
                                <a href="{!! route('cans.listar_archivos_can_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>5]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-eye"></i> </a>
                            </td>
                            <td>
                                <a href="{!! route('cans.ampliacion_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>8]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-clock-o"></i> </a>
                            </td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>            
        </div>  

    </div>    
</div>



