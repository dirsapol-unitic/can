<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                <?php $tipo_dispositivo_anterior=0; $i=1;?>
                @foreach($estimaciones as $key => $estimacion)
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
                                <th bgcolor="#D4E6F1" style="text-align:center;">Descripción</th>
                                <th bgcolor="#D4E6F1" style="text-align:center;">Observacion</th>
                                <!--th bgcolor="#D4E6F1" style="text-align:center;">STOCK</th-->
                                <th bgcolor="#D4E6F1" style="text-align:center;">CPMA</th>
                                <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad</th>
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
                                
                            </tr>
                        </thead>                
                        <tbody>

                    <?php  }
                    ?>
                        
                            <tr>
                                <td><small>{!! $estimacion->descripcion !!}</small></td>
                                <!--td style="text-align:center;"><small-->
                                <?php
                                    /*switch ($estimacion->estado) {
                                        case 0: echo " "; break;
                                        case 1: echo '<span style="color:blue;">'.' Nuevo '.'<span>';  break;
                                        case 2: echo '<span style="color:red;">'.' Eliminado '.'<span>';  break;
                                        case 3: echo '<span style="color:green;">'.' Actualizado, cpma_ant='.$estimacion->cpma_anterior.' nec_ant='.$estimacion->necesidad_anterior.'<span>';  break;
                                    }*/
                                 ?>
                            <!--/small></td-->
                            <td><small>
                                <?php
                                    switch ($estimacion->estado_necesidad) {
                                        case 0: echo '<span>'.' Ratificado '.'<span>'; break;
                                        case 1: echo '<span style="color:blue;">'.' Nuevo '.'<span>';  break;
                                        case 2: echo '<span style="color:red;">'.' Eliminado '.'<span>';  break;
                                        case 3: if($estimacion->necesidad_anterior!=$estimacion->necesidad_anual):
                                                    echo '<span style="color:green;">'.' Actualizado, cpma_ant='.$estimacion->cpma_anterior.' nec_ant='.$estimacion->necesidad_anterior.'<span>';  
                                                else:
                                                    echo '<span>'.' Ratificado '.'<span>';
                                                endif; break;
                                    }
                                 ?>
                                <?php
                                   /* 
                                    $necesidad_anterior=$estimacion->necesidad_anterior;

                                    if($estimacion->estado==2 && $estimacion->estado_necesidad==0 ){
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($estimacion->necesidad_anual==$necesidad_anterior && $necesidad_anterior!=0){
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
                                        
                                    }*/
                                 ?></small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->cpma !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->necesidad_anual !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes3 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes4 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes5 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes6 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes7 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes8 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes9 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes10 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes11 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes12 !!}</small></td>
                            
                            </tr>

                        
                @endforeach
                   </tbody>
                            </table>
            </div>      
    </div>    
</div>