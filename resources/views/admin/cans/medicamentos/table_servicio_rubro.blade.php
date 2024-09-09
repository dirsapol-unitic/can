<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Descripción</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Observacion</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">CPMA</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad Actual</th>
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
                        <?php $x=0; ?>
                    @foreach($estimaciones_servicios as $key => $estimacion_servicio)
                        <tr>
                            <td><small>{!! $estimacion_servicio->descripcion !!}</small></td>
                            <td style="text-align:center;"><small>
                                <?php
                                    
                                    $necesidad_anterior=$estimacion_servicio->stock_cero+$estimacion_servicio->stock_dos+$estimacion_servicio->stock_cuatro;
                                    if($estimacion_servicio->necesidad_anual==$necesidad_anterior)
                                        echo '<span>'.' '.'<span>';

                                    if($estimacion_servicio->necesidad_anual==$estimacion_servicio->stock_dos)
                                        echo '<span style="color:red;">'.' Eliminado '.'<span>';

                                    if($estimacion_servicio->necesidad_anual==$estimacion_servicio->stock_uno){
                                        echo '<span style="color:blue;">'.' Nuevo '.'<span>';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($estimacion_servicio->necesidad_anual!=$necesidad_anterior){
                                        $cpma_anterior=$estimacion_servicio->cpma_cero+$estimacion_servicio->cpma_dos+$estimacion_servicio->cpma_cuatro;

                                        if($x!=1)                                            
                                        echo '<span style="color:green;">'.' Actualizado, cpma_ant='.$cpma_anterior.' nec_ant='.$necesidad_anterior.'<span>';
                                        
                                    }
                                 ?>
                            </small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->cpma !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->necesidad_anual !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_servicio->necesidad_actual !!}</small></td>
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
                            
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
             
    </div>    
</div>
