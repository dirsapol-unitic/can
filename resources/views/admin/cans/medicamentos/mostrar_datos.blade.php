<table id="example" class="table table-responsive table-striped">
<!--table id="example" class="stripe row-border order-column" cellspacing="0" -->
    <tbody>
        <tr>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>SERVICIO</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>RESPONSABLE</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;">NECESESIDAD</td>
        </tr>
        @foreach($estimaciones as $key => $estimacion)
            <tr>
                <td><small>{!! $estimacion->nombre_servicio !!}</small></td>
                <td><small>{!! $estimacion->nombre !!}</small></td>
                <td><small>{!! $estimacion->necesidad_anual !!}</small></td>
            </tr>                                            
        @endforeach
    </tbody>
</table>

            
