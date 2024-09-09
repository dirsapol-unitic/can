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
                                @if($cerrado_stock==2)
                                <th rowspan="2" style="text-align:center;">Stock</th>
                                @endif
                                @if($nivel>1)
                                    <!--th rowspan="2" style="text-align:center;">Stock Actual</th-->
                                @endif
                                <th rowspan="2" style="text-align:center;">CPMA</th>
                                <th rowspan="2" style="text-align:center;">Necesidad Anual</th>
                                @if($nivel>1)
                                    <!--th rowspan="2" style="text-align:center;">Necesidad Actual</th-->
                                @endif
                                <th colspan="12" bgcolor="#D4E6F1" style="text-align:center;">
                                PRORRATEO</th>
                                <!--th rowspan="2" style="text-align:center;">Justificación</th-->
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
                            </tr>
                        </thead>                
                        <tbody>

                    <?php  }
                    ?>
                        
                            <tr>
                                <td><small>{!! $estimacion->descripcion !!}</small></td>
                                @if($cerrado_stock==2)
                                <td><small>{!! $estimacion->stock !!}</small></td>
                                @endif
                                @if($nivel>1)
                                    <!--td><small>{!! $estimacion->stock_actual !!}</small></td-->
                                @endif
                                <td><small>{!! $estimacion->cpma !!}</small></td>
                                <td><small>{!! $estimacion->necesidad_anual !!}</small></td>
                                @if($nivel>1)
                                    <!--td><small>{!! $estimacion->necesidad_actual !!}</small></td-->
                                @endif
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
                                <!--td><small>{!! $estimacion->justificacion !!}</small></td-->
                            </tr>

                        
                @endforeach
                   </tbody>
                            </table>
            </div>      
    </div>    
</div>

