<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                <?php $tipo_dispositivo_anterior=0; $i=1;?>
                @foreach($estimacions as $key => $estimacion)
                    <?php 

                        if($tipo_dispositivo_anterior!=$estimacion->tipo_dispositivo_id){

                          $descripcion_tipo_dispositivo=DB::table('tipo_dispositivo_medicos')->where('id',$estimacion->tipo_dispositivo_id)->get();

                            $nombre_dispositivo=$descripcion_tipo_dispositivo->get(0)->descripcion;
                            $name_tabla='id=example'.$i;
                            
                            $i++;
                            if($tipo_dispositivo_anterior!=0){ ?>
                                </tbody>
                            </table>
                    <?php
                            }
                            $tipo_dispositivo_anterior=$estimacion->tipo_dispositivo_id;

                    ?>
                        <h3>{!!$nombre_dispositivo!!}</h3>
                        <table id="" class="display">
                    <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->  
                        <thead>
                            <tr>
                                <th rowspan="2" style="text-align:center;">Descripción</th>
                                <th rowspan="2" bgcolor="#D4E6F1"  style="text-align:center;">Observacion</th>
                                <th rowspan="2" bgcolor="#D4E6F1"  style="text-align:center;">CPMA</th>
                                <th rowspan="2" bgcolor="#D4E6F1"  style="text-align:center;">Necesidad Anual</th>
                                <th colspan="12" bgcolor="#D4E6F1" style="text-align:center;">
                                PRORRATEO AÑO 1</th>
                                <th rowspan="2" bgcolor="#3c8dbc" style="color: white; text-align:center;">Observacion</th>
                                <th rowspan="2" bgcolor="#3c8dbc" style="color: white; text-align:center;">CPMA</th>
                                <th rowspan="2" bgcolor="#3c8dbc" style="color: white; text-align:center;">Necesidad Anual</th>
                                <th colspan="12" bgcolor="#3c8dbc" style="color: white; text-align:center;">
                                PRORRATEO AÑO 2</th>
                                <th rowspan="2" bgcolor="#3c763d" style="color: white; text-align:center;">Observacion</th>
                                <th rowspan="2" bgcolor="#3c763d" style="color: white; text-align:center;">CPMA</th>
                                <th rowspan="2" bgcolor="#3c763d" style="color: white; text-align:center;">Necesidad Anual</th>
                                <th colspan="12" bgcolor="#3c763d" style="color: white; text-align:center;">
                                PRORRATEO AÑO 3</th>
                            </tr>
                            <tr>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Enero</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Febrero</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Marzo</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Abril</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Mayo</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Junio</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Julio</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Agosto</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Setiembre</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Octubre</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Noviembre</small></th>
                                <th bgcolor="#EAF2F8" style="text-align:center;"><small>Diciembre</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Enero</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Febrero</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Marzo</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Abril</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Mayo</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Junio</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Julio</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Agosto</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Setiembre</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Octubre</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Noviembre</small></th>
                                <th bgcolor="#3c8dbc" style="color: white; text-align:center;"><small>Diciembre</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Enero</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Febrero</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Marzo</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Abril</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Mayo</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Junio</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Julio</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Agosto</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Setiembre</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Octubre</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Noviembre</small></th>
                                <th bgcolor="#3c763d" style="color: white; text-align:center;"><small>Diciembre</small></th>
                            </tr>
                        </thead>                
                        <tbody>

                    <?php  }
                    ?>
                        
                            <tr>
                                <td><small>{!! $estimacion->descripcion !!}</small></td>
                                <td><small>

                            <?php
                                    
                                    $necesidad_anterior=$estimacion->necesidad_anterior;

                                    if($estimacion->estado==2 && $estimacion->estado_necesidad==0 ){
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($estimacion->necesidad_anual==$necesidad_anterior){
                                        if($y==0){
                                            echo '<span>'.' Ratificado '.'<span>';    
                                        }
                                        else
                                        {
                                            $y=0;
                                        }
                                    }

                                    if($necesidad_anterior==0 and $estimacion->estado!=2){
                                        echo '<span style="color:blue;">'.' Nuevo '.'<span>';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($estimacion->necesidad_anual!=$necesidad_anterior){
                                        
                                        if($x!=1 && $y==0)                                 
                                        echo '<span style="color:green;">'.' Actualizado,  nec_ant='.$necesidad_anterior.'<span>';
                                        
                                    }
                                 ?>
                                </small></td>
                                <td><small>{!! $estimacion->cpma !!}</small></td>
                                <td><small>{!! $estimacion->necesidad_anual !!}</small></td>
                                <td><small>{!! $estimacion->mes1 !!}</small></td>
                                <td><small>{!! $estimacion->mes2 !!}</small></td>
                                <td><small>{!! $estimacion->mes3 !!}</small></td>
                                <td><small>{!! $estimacion->mes4 !!}</small></td>
                                <td><small>{!! $estimacion->mes5 !!}</small></td>
                                <td><small>{!! $estimacion->mes6 !!}</small></td>
                                <td><small>{!! $estimacion->mes7 !!}</small></td>
                                <td><small>{!! $estimacion->mes8 !!}</small></td>
                                <td><small>{!! $estimacion->mes9 !!}</small></td>
                                <td><small>{!! $estimacion->mes10 !!}</small></td>
                                <td><small>{!! $estimacion->mes11 !!}</small></td>
                                <td><small>{!! $estimacion->mes12 !!}</small></td>
                                <td><small><?php
                                    
                                    $necesidad_anterior_1=$estimacion->necesidad_anterior_1;

                                    if($estimacion->estado==2 && $estimacion->estado_necesidad==0 ){
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($estimacion->necesidad_anual_1==$necesidad_anterior_1){
                                        if($y==0){
                                            echo '<span>'.' Ratificado '.'<span>';    
                                        }
                                        else
                                        {
                                            $y=0;
                                        }
                                    }

                                    if($necesidad_anterior_1==0 and $estimacion->estado!=2){
                                        echo '<span style="color:blue;">'.' Nuevo '.'<span>';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($estimacion->necesidad_anual_1!=$necesidad_anterior_1){
                                        
                                        if($x!=1 && $y==0)                                   
                                        echo '<span style="color:green;">'.' Actualizado,  nec_ant='.$necesidad_anterior_1.'<span>';
                                        
                                    }
                                 ?></small></td>
                                <td><small>{!! $estimacion->cpma !!}</small></td>
                                <td><small>{!! $estimacion->necesidad_anual !!}</small></td>
                                <td><small>{!! $estimacion->mes1 !!}</small></td>
                                <td><small>{!! $estimacion->mes2 !!}</small></td>
                                <td><small>{!! $estimacion->mes3 !!}</small></td>
                                <td><small>{!! $estimacion->mes4 !!}</small></td>
                                <td><small>{!! $estimacion->mes5 !!}</small></td>
                                <td><small>{!! $estimacion->mes6 !!}</small></td>
                                <td><small>{!! $estimacion->mes7 !!}</small></td>
                                <td><small>{!! $estimacion->mes8 !!}</small></td>
                                <td><small>{!! $estimacion->mes9 !!}</small></td>
                                <td><small>{!! $estimacion->mes10 !!}</small></td>
                                <td><small>{!! $estimacion->mes11 !!}</small></td>
                                <td><small>{!! $estimacion->mes12 !!}</small></td>
                                <td><small><?php
                                    
                                    $necesidad_anterior_2=$estimacion->necesidad_anterior_2;

                                    if($estimacion->estado==2 && $estimacion->estado_necesidad==0 ){
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($estimacion->necesidad_anual_2==$necesidad_anterior_2){
                                        if($y==0){
                                            echo '<span>'.' Ratificado '.'<span>';    
                                        }
                                        else
                                        {
                                            $y=0;
                                        }
                                    }

                                    if($necesidad_anterior_2==0 and $estimacion->estado!=2){
                                        echo '<span style="color:blue;">'.' Nuevo '.'<span>';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($estimacion->necesidad_anual_2!=$necesidad_anterior_2){
                                        
                                        if($x!=1 && $y==0)                                 
                                        echo '<span style="color:green;">'.' Actualizado,  nec_ant='.$necesidad_anterior_2.'<span>';
                                        
                                    }
                                 ?></small></td>
                                <td><small>{!! $estimacion->cpma !!}</small></td>
                                <td><small>{!! $estimacion->necesidad_anual !!}</small></td>
                                <td><small>{!! $estimacion->mes1 !!}</small></td>
                                <td><small>{!! $estimacion->mes2 !!}</small></td>
                                <td><small>{!! $estimacion->mes3 !!}</small></td>
                                <td><small>{!! $estimacion->mes4 !!}</small></td>
                                <td><small>{!! $estimacion->mes5 !!}</small></td>
                                <td><small>{!! $estimacion->mes6 !!}</small></td>
                                <td><small>{!! $estimacion->mes7 !!}</small></td>
                                <td><small>{!! $estimacion->mes8 !!}</small></td>
                                <td><small>{!! $estimacion->mes9 !!}</small></td>
                                <td><small>{!! $estimacion->mes10 !!}</small></td>
                                <td><small>{!! $estimacion->mes11 !!}</small></td>
                                <td><small>{!! $estimacion->mes12 !!}</small></td>
                                
                            </tr>

                        
                @endforeach
                   </tbody>
                            </table>
            </div>      
    </div>    
</div>

