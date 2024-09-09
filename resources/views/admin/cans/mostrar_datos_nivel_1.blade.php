@if($opcion==1)
<table id="example" class="table table-responsive table-striped">
<!--table id="example" class="stripe row-border order-column" cellspacing="0" -->
    <tbody>
        <tr>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>#</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>ESTABLECIMIENTO</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;">NECESIDAD</td>
        </tr>
        <?php $total=0; $x=1;?>
        @foreach($estimaciones as $key => $estimacion)
            <tr>
                <td><small>{!! $x !!}</small></td>
                <td><small>{!! $estimacion->nombre_establecimiento !!}</small></td>
                <td style="text-align:center;"><small>{!! $estimacion->necesidad_anual !!}</small></td>
            </tr>                                            
            <?php $total=$total+$estimacion->necesidad_anual;  $x++;?>
        @endforeach
        
        <tr>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small></small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>TOTAL</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;">{!! $total !!}</td>
        </tr>
    </tbody>
</table>
@else
<table id="example" class="table table-responsive table-striped">
<!--table id="example" class="stripe row-border order-column" cellspacing="0" -->
    <tbody>
        <tr>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>#</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>ESTABLECIMIENTO</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;">NECESIDAD</td>
        </tr>
        <?php $total=0; $x=1;?>
        @foreach($estimaciones as $key => $estimacion)
            @if($estimacion->necesidad_actual>0)
            <tr>
                <td><small>{!! $x !!}</small></td>
                <td><small>{!! $estimacion->nombre_establecimiento !!}</small></td>
                <td style="text-align:center;"><small>{!! $estimacion->necesidad_actual !!}</small></td>
            </tr>                                            
            <?php $total=$total+$estimacion->necesidad_actual;  $x++;?>
            @endif
        @endforeach
        
        <tr>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small></small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>TOTAL</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;">{!! $total !!}</td>
        </tr>
    </tbody>
</table>

@endif
            
