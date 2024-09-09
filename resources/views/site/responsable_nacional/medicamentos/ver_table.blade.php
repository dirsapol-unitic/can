<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                
                <table id="example" class="table table-responsive table-striped">
                <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->  

                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align:center;">Descripción</th>
                            <th rowspan="2" style="text-align:center;">CPMA</th>
                            <th rowspan="2" style="text-align:center;">Stock Actual</th>
                            <th rowspan="2" style="text-align:center;">Necesidad Anual</th>
                            <th colspan="12" bgcolor="#D4E6F1" style="text-align:center;">
                            PRORRATEO</th>
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
                    @foreach($estimacions as $key => $estimacion)
                        <tr>
                            <td><small><a href="{!! route('nacional.ver_servicio_rubro',[$tipo,$can_id,$establecimiento_id,$estimacion->petitorio_id ]) !!}">{!! $estimacion->descripcion !!}</a></small></td>
                            <td><small>{!! $estimacion->cpma !!}</small></td>
                            <td><small>{!! $estimacion->stock !!}</small></td>
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