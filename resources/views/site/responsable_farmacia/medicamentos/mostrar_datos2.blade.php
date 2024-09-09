
            
            
                <table id="example" class="table table-responsive table-striped">
                <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->  

                    <tbody>
                        <tr>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>CONSOLIDA</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>RUBRO</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>RESPONSABLE</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">CPMA</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">STOCK</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">NECES.</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>ENE</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>FEB</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>MAR</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>ABR</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>MAY</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>JUN</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>JUL</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>AGO</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>SET</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>OCT</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>NOV</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>DIC</small></td>                            
                        </tr>
                        @foreach($estimaciones as $key => $estimacion)
                            <tr>
                                @if($estimacion->consolidado==1)
                                    <td><small>FARMACIA</small></td>
                                @else
                                    <td><small>ALMACEN</small></td>
                                @endif
                                <td><small>{!! $estimacion->nombre_rubro !!}</small></td>
                                <td><small>{!! $estimacion->nombre !!}</small></td>
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
                <br/>
            
            
