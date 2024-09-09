
            @foreach($estimacions as $key => $estimacion)
            
                <table id="example" class="table table-responsive table-striped">
                <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->  

                    <tbody>
                        <tr>
                            <td bgcolor="#EAF2F8" style="text-align:center;">CPMA</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">Stock</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">Necesidad</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Enero</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Febrero</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Marzo</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Abril</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Mayo</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Junio</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Julio</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Agosto</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Setiembre</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Octubre</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Noviembre</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>Diciembre</small></td>
                        </tr>
                        <tr>
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
                        <tr>
                            <td colspan="2" bgcolor="#F2F2F2" style="text-align:center;">RUBRO</td>
                            @if($nivel==1)
                                <td colspan="4" bgcolor="#F2F2F2" style="text-align:center;">{!! $estimacion->nombre_rubro !!}</td>
                            @else
                                <td colspan="4" bgcolor="#F2F2F2" style="text-align:center;">{!! $estimacion->nombre_servicio !!}</td>
                            @endif
                            <td colspan="3" bgcolor="#E6E6E6" style="text-align:center;">RESPONSABLE</td>
                            <td colspan="6" bgcolor="#E6E6E6" style="text-align:center;">{!! $estimacion->nombre !!}</td>
                        </tr>
                    </tbody>
                </table>
                <br/>
            @endforeach
            
