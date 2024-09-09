<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Descripción</th>
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
                    @foreach($estimaciones_rubros as $key => $estimacion_rubro)
                        <tr>
                            <td><small>{!! $estimacion_rubro->descripcion !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->cpma !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->necesidad_anual !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes1 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes3 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes4 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes5 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes6 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes7 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes8 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes9 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes10 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes11 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $estimacion_rubro->mes12 !!}</small></td>
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
             
    </div>    
</div>
