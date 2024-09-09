                <table id="example" class="table table-responsive table-striped">
                <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->
                    <tbody>
                        <tr>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>CONSOLIDA</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>RUBRO</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>RESPONSABLE</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">CPMA</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">STOCK</td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">NECESESIDAD</td>
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
                            </tr>                                            
                        @endforeach
                    </tbody>
                </table>
                <br/>
            
            
