<?php 
use Carbon\Carbon;
//dd($consulta);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="col-xs-3 form-group">
            {!! Form::label('mes', 'Mes:') !!}
            {!! $can->meses->descripcion !!}
        </div>
        <div class="col-xs-3 form-group">
            {!! Form::label('ano', 'Año:') !!}
            {!! $can->ano !!}
        </div>        
    </div>    
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box-body">
            <div class="box-body chat" id="chat-box">
                <?php $x=1; ?>
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Establecimiento</th>
                                @if ($consulta->actualizacion == 1 && $consulta->can_id == 9)
                                    <th>Actualizo</th>
                                @endif
                                @if($rol==1)
                                    <th>Med.</th>
                                    <th>Disp.</th>  
                                    @if ($can->stock == 1)
                                        <th>Prod.s Modif.</th>
                                    @endif                                     
                                    <th>Act/Des</th>                                
                                    <th>Archivos</th>
                                @endif
                                @if ($can->stock == 1)
                                    <th>Medicamentos.</th>                                
                                    <th>Dispositivos</th>
                                    @if($rol==1)
                                        <th>Act/Des</th>
                                    @endif
                                @endif
                                @if ($can->extraordinario == 1)
                                    <th>Medicamentos Extraord.</th>
                                    <th>Dispositivos Extraord.</th>                                      
                                    @if($rol==1)  
                                        <th>Act/Des</th>                                        
                                    @endif
                                @endif
                                @if($rol==1)
                                    <th>Ampliar Tiempo</th>
                                @endif
                            </tr>                        
                        </thead>                
                        <tbody>               
                        @foreach($consulta as $id => $consulta)                        
                            <tr>
                                <td>{{$x++}}</td>
                                @if ($consulta->nivel_id == 1)         
                                    <td>{{ $consulta->nombre_establecimiento }}</td>
                                @else
                                    <td><a href="{!! route('cans.mostrar_servicios',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}">{{ $consulta->nombre_establecimiento }}</a></td>
                                @endif
                                @if ($consulta->actualizacion == 1 && $consulta->can_id == 9)
                                    <td>SI</td>
                                @else
                                    <td>NO</td>
                                @endif
                                <!--td>@foreach($login as $id => $ingreso)  
                                        @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)

                                        @if($consulta->establecimiento_id==$ingreso->establecimiento_id)
                                           <?php                                             
                                           /*
                                            $date = Carbon::now();
                                            $fecha = $date->format('d-m-Y H:i:s');
                                            $fechaFin = Carbon::parse($ingreso->fin_first_login);
                                            
                                            if($ingreso->fin_first_login!=null){
                                                
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
                                                            echo 'NO TERMINO SU CAN2020';
                                                        else
                                                        {   if($consulta->medicamento_cerrado==1 or $consulta->dispositivo_cerrado==2)
                                                                echo 'NO TERMINO SU CAN2020';
                                                            else
                                                            {   if($consulta->medicamento_cerrado==1 or $consulta->dispositivo_cerrado==1)
                                                                        echo 'NO TERMINO SU CAN2020';
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
                                            */
                                            ?>
                                        @endif

                                        @endif
                                    @endforeach
                                </td-->
                                @if($rol==1)
                                @if ($consulta->medicamento_cerrado == 1)  
                                    <td> <a href="{!! route('cans.medicamentos_estimaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-success btn-xs'><i class="fa fa-medkit"></i></a></td>
                                @else
                                    @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                        <td> <a href="{!! route('cans.medicamentos_consolidados',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-medkit"></i></a></td>
                                    @else
                                             <td> <a href="{!! route('cans.medicamentos_estimaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-medkit"></i></a></td>
                                    @endif    
                                @endif
                                @if ($consulta->dispositivo_cerrado == 1)  
                                    <td> <a href="{!! route('cans.dispositivos_estimaciones', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-success btn-xs'><i class="fa fa-stethoscope"></a></td>
                                @else
                                    @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                        <td> <a href="{!! route('cans.dispositivos_consolidados',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'><i class="fa fa-stethoscope"></i></a></td>
                                    @else
                                        <td> <a href="{!! route('cans.dispositivos_estimaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'><i class="fa fa-stethoscope"></i></a></td>
                                    @endif
                                @endif
                                @if ($can->stock == 1)
                                <td>
                                

                                @if($consulta->establecimiento_id>3 and $consulta->establecimiento_id!=30 and $consulta->establecimiento_id!=69)

                                    <?php 
                                        //use DB;

                                        $data=DB::table('estimacions')
                                            ->where('necesidad_anual','>',0)
                                            ->where('estado','>',0)
                                            ->where('can_id',$consulta->can_id)                        
                                            ->where('establecimiento_id',$consulta->establecimiento_id)//
                                            
                                            ->get();
                                        if(count($data)>0){
                                            ?>
                                            <a class="btn btn-success btn-xs" href="{!! route('cans.exportDataModificado',[$consulta->can_id,$consulta->establecimiento_id,'xlsx']) !!}"><i class="fa fa-file-excel-o"></i></a>
                                        <?php
                                        }
                                        else
                                        {
                                            echo "No modifico";
                                        }

                                    
                                    ?>
                                @endif
                                
                                </td>
                                @endif
                                <td>
                                    <a href="{!! route('cans.activar_can_establecimiento',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}"  class='btn bg-blue btn-xs'><i class="fa fa-fw fa-chain"></i> </a>
                                </td>
                                
                                <td>
                                    <a href="{!! route('cans.listar_archivos_can',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-info btn-xs'><i class="fa fa-eye"></i> </a>                                     
                                </td>
                                @endif
                                @if ($can->stock == 1)
                                    @if ($consulta->medicamento_cerrado_stock == 1)  
                                        <td> <a href="{!! route('cans.medicamentos_estimaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-warning btn-xs'> <i class="fa fa-fw fa-folder-open"> </a></td>
                                    @else
                                        @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                            <td> <a href="{!! route('cans.medicamentos_consolidados',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-archive"></i> </a></td>
                                        @else
                                                 <td> <a href="{!! route('cans.medicamentos_estimaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-archive"></i> </a></td>
                                        @endif    
                                    @endif
                                    @if ($consulta->dispositivo_cerrado_stock == 1)  
                                        <td> <a href="{!! route('cans.dispositivos_estimaciones', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-warning btn-xs'> <i class="fa fa-fw fa-folder-open"> </a></td>
                                    @else
                                        @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                            <td> <a href="{!! route('cans.dispositivos_consolidados',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-archive"></i>  </a></td>
                                        @else
                                            <td> <a href="{!! route('cans.dispositivos_estimaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-archive"></i> </a></td>
                                        @endif
                                    @endif

                                    @if($rol==1)
                                    <td>                                
                                        <a href="{!! route('cans.activar_can_establecimiento_stock',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn bg-blue btn-xs'><i class="fa fa-fw fa-chain"></i> </a>                                
                                    </td>
                                    @endif
                                @endif

                                @if ($can->extraordinario == 1)   
                                    @if ($consulta->medicamento_cerrado_rectificacion == 1)  
                                        <td> <a href="{!! route('cans.medicamentos_rectificaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn bg-olive btn-xs'> <i class="fa fa-fw fa-edit"> </a></td>
                                    @else
                                        @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                            <td> <a href="{!! route('cans.medicamentos_rectificaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i> </a></td>
                                        @else
                                                 <td> <a href="{!! route('cans.medicamentos_rectificaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i> </a></td>
                                        @endif    
                                    @endif
                                
                                    @if ($consulta->dispositivo_cerrado_rectificacion == 1)  
                                        <td> <a href="{!! route('cans.dispositivos_rectificaciones', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn bg-olive btn-xs'> <i class="fa fa-fw fa-edit"> </a></td>
                                    @else
                                        @if($consulta->establecimiento_id>3 || $consulta->establecimiento_id==30 || $consulta->establecimiento_id==69)
                                            <td> <a href="{!! route('cans.dispositivos_rectificaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i>  </a></td>
                                        @else
                                            <td> <a href="{!! route('cans.dispositivos_rectificaciones',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn btn-danger btn-xs'> <i class="fa fa-fw fa-file-archive-o"></i> </a></td>
                                        @endif
                                    @endif                                   
                                    
                                    <td>                                
                                        <a href="{!! route('cans.activar_can_establecimiento_rectificacion',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn bg-blue btn-xs'><i class="fa fa-fw fa-chain"></i> </a>                                
                                    </td>
                                @endif
                                @if($rol==1)
                                    <td> 
                                        <a href="{!! route('cans.ampliacion_ipress',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-clock-o"></i> </a>
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
</div>

