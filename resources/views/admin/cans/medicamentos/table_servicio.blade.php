<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>
                            
                                <th rowspan="2" style="text-align:center;">Descripción</th>
                                <th rowspan="2"bgcolor="#D4E6F1" style="text-align:center;">Observacion</th>
                                <th rowspan="2" bgcolor="#D4E6F1" style="text-align:center;">Necesidad</th>
                                <th colspan="12" bgcolor="#D4E6F1" style="text-align:center;">
                                PRORRATEO</th>
                                <th rowspan="2"bgcolor="#3c8dbc" style="text-align:center;">Observacion</th>
                                <th rowspan="2" bgcolor="#3c8dbc" style="text-align:center;">Necesidad</th>
                                <th colspan="12" bgcolor="#3c8dbc" style="text-align:center;">
                                PRORRATEO</th>
                                <th rowspan="2"bgcolor="#3c763d" style="text-align:center;">Observacion</th>
                                <th rowspan="2" bgcolor="#3c763d" style="text-align:center;">Necesidad</th>
                                <!--th rowspan="2" style="text-align:center;">Justificación</th-->
                                <th colspan="12" bgcolor="#3c763d" style="text-align:center;">
                                PRORRATEO</th>
                            </tr>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Enero</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Febrero</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Marzo</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Abril</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Mayo</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Junio</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Julio</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Agosto</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Setiembre</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Octubre</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Noviembre</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Diciembre</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Enero</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Febrero</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Marzo</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Abril</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Mayo</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Junio</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Julio</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Agosto</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Setiembre</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Octubre</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Noviembre</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Diciembre</th> 
                            <th bgcolor="#3c763d" style="text-align:center;">Enero</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Febrero</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Marzo</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Abril</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Mayo</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Junio</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Julio</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Agosto</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Setiembre</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Octubre</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Noviembre</th>
                            <th bgcolor="#3c763d" style="text-align:center;">Diciembre</th> 
                        </tr>
                    </thead>                
                    <tbody>
                    @foreach($estimaciones_servicios as $key => $estimacion_servicio)
                        <tr>
                            <td><small>{!! $estimacion_servicio->descripcion !!}</small></td>
                            
                            <td><small><?php
                                    
                                    $necesidad_anterior=$estimacion_servicio->necesidad_anterior;

                                    if($estimacion_servicio->estado==2 && $estimacion_servicio->estado_necesidad==0 && $necesidad_anterior==0){
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($estimacion_servicio->necesidad_anual==$necesidad_anterior && $necesidad_anterior!=0){
                                        if($y==0){
                                            echo '<span>'.' Ratificado '.'<span>';    
                                        }
                                        else
                                        {
                                            $y=0;
                                        }
                                    }

                                    if($necesidad_anterior==0 and $estimacion_servicio->estado!=2){
                                        echo '<span style="color:blue;">'.' Nuevo '.'<span>';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($estimacion_servicio->necesidad_anual!=$necesidad_anterior){
                                        
                                        if($x!=1)                                            
                                        echo '<span style="color:green;">'.' Actualizado,  nec_ant='.$necesidad_anterior.'<span>';
                                        
                                    }
                                 ?></small></td>                           
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->necesidad_anual !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes3 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes4 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes5 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes6 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes7 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes8 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes9 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes10 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes11 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes12 !!}</small></td>
                            <td><small><?php
                                    
                                    $necesidad_anterior_1=$estimacion_servicio->necesidad_anterior_1;

                                    if($estimacion_servicio->estado==2 && $estimacion_servicio->estado_necesidad==0 && $necesidad_anterior_1==0){
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($estimacion_servicio->necesidad_anual_1==$necesidad_anterior_1 && $necesidad_anterior_1!=0){
                                        if($y==0){
                                            echo '<span>'.' Ratificado '.'<span>';    
                                        }
                                        else
                                        {
                                            $y=0;
                                        }
                                    }

                                    if($necesidad_anterior_1==0 and $estimacion_servicio->estado!=2){
                                        echo '<span style="color:blue;">'.' Nuevo '.'<span>';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($estimacion_servicio->necesidad_anual_1!=$necesidad_anterior_1){
                                        
                                        if($x!=1)                                            
                                        echo '<span style="color:green;">'.' Actualizado,  nec_ant='.$necesidad_anterior_1.'<span>';
                                        
                                    }
                                 ?></small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->necesidad_anual_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes1_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes2_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes3_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes4_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes5_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes6_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes7_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes8_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes9_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes10_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes11_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes12_1 !!}</small></td>
                            <td><small><?php
                                    
                                    $necesidad_anterior_2=$estimacion_servicio->necesidad_anterior_2;

                                    if($estimacion_servicio->estado==2 && $estimacion_servicio->estado_necesidad==0 && $necesidad_anterior_2==0){
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($estimacion_servicio->necesidad_anual_2==$necesidad_anterior_2 && $necesidad_anterior_2!=0){
                                        if($y==0){
                                            echo '<span>'.' Ratificado '.'<span>';    
                                        }
                                        else
                                        {
                                            $y=0;
                                        }
                                    }

                                    if($necesidad_anterior_2==0 and $estimacion_servicio->estado!=2){
                                        echo '<span style="color:blue;">'.' Nuevo '.'<span>';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($estimacion_servicio->necesidad_anual_2!=$necesidad_anterior_2){
                                        
                                        if($x!=1)                                            
                                        echo '<span style="color:green;">'.' Actualizado,  nec_ant='.$necesidad_anterior_2.'<span>';
                                        
                                    }
                                 ?></small></td> 
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->necesidad_anual_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes1_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes2_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes3_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes4_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes5_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes6_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes7_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes8_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes9_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes10_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes11_2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->mes12_2 !!}</small></td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
             
    </div>    
</div>
