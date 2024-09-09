
                <table id="example" class="table table-responsive table-striped">
                <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->
                    <tbody>
                        <tr>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>CONSOLIDA</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>RESPONSABLE</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">NECESIDAD</td>
                        </tr>
                        @foreach($estimaciones as $key => $estimacion)
                            <tr>
                                @if ($estimacion->consolidado==3)
                                    <td><small>ALMACEN</small></td>
                                @else
                                    <td><small>FARMACIA</small></td>
                                @endif
                                <td><small>{!! $estimacion->nombre !!}</small></td>
                                <td><small><p align="center"> {!! $estimacion->necesidad_anual !!}</p></small></td>
                            </tr>                                            
                        @endforeach
                    </tbody>
                </table>

            
