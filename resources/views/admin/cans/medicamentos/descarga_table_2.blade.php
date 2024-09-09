<div class="row">
    <div class="col-xs-12">        
            <div class="box-body">                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Descripción</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Observacion</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">STOCK</th>
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
                            <th bgcolor="#3c8dbc" style="text-align:center;">CPMA</th>
                            <th bgcolor="#3c8dbc" style="text-align:center;">Necesidad</th>
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
                        </tr>
                    </thead>                
                    <tbody>
                    @foreach($estimaciones as $key => $estimacion)
                        <tr>
                            <td><small>{!!$estimacion->descripcion!!}</small></td>
                            <td style="text-align:center;"><small>
                                <?php
                                    switch ($estimacion->estado_necesidad) {
                                        case 0: echo " "; break;
                                        case 1: echo '<span style="color:blue;">'.' Nuevo '.'<span>';  break;
                                        case 2: echo '<span style="color:red;">'.' Eliminado '.'<span>';  break;
                                        case 3: if($estimacion->necesidad_anterior!=$estimacion->necesidad_anual):
                                                    echo '<span style="color:green;">'.' Actualizado, cpma_ant='.$estimacion->cpma_anterior.' nec_ant='.$estimacion->necesidad_anterior.'<span>';  
                                                else:
                                                    echo '<span>'.' Ratificado '.'<span>';
                                                endif;
                                    }
                                 ?>
                            </small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->stock !!}</small></td>
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
                            <td style="text-align:center;"><small>{!! $estimacion->cpma_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->necesidad_anual_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes1_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes2_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes3_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes4_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes5_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes6_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes7_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes8_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes9_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes10_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes11_1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion->mes12_1 !!}</small></td>
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
             
    </div>    
</div>