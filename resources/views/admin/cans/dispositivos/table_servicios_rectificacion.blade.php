<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                <?php $tipo_dispositivo_anterior=0; $i=1;?>
                @foreach($estimaciones_servicios as $key => $estimacion)
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
                                <th bgcolor="#D4E6F1" style="text-align:center;" width="80%">Descripción</th>
                                
                                
                                <th  style="text-align:center;">Necesidad Anual</th>
                                <th  style="text-align:center;">Necesidad Actual</th>
                                
                                <!--th rowspan="2" style="text-align:center;">Justificación</th-->
                            </tr>
                            
                        </thead>                
                        <tbody>

                    <?php  }
                    ?>
                        
                            <tr>
                                <td>{!! $estimacion->descripcion !!}</td>
                                
                                <td>{!! $estimacion->necesidad_anual !!}</td>
                                <td style="text-align:center;">
                                <?php if($estimacion->necesidad_anual!=$estimacion->necesidad_actual){ ?>
                                    <span style="color:red;"> {!! $estimacion->necesidad_actual !!}</span> 
                                <?php }else{ echo $estimacion->necesidad_actual;}
                                 ?>                                
                            </td>
                            

                            
                            </tr>

                        
                @endforeach
                   </tbody>
                            </table>
            </div>      
    </div>    
</div>