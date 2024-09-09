<table id="example" class="table table-responsive table-striped">
<!--table id="example" class="stripe row-border order-column" cellspacing="0" -->
    <tbody>
        <tr>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>#</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>ESTABLECIMIENTO</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;">NECESESIDAD</td>
            <td bgcolor="#EAF2F8" style="text-align:center;">NECESESIDAD AJUSTE</td>
        </tr>
        <?php $total=0;$total1=0; $x=1;?>
        @for($i=0;$i<4;$i++)
            @if($estimaciones[$i][2]!='')
                <tr>
                    <td><small>{!! $x !!}</small></td>
                    <td><small>{!! $estimaciones[$i][2] !!}</small></td>
                    <td style="text-align:center;"><small>{!! $estimaciones[$i][3] !!}</small></td>
                    <td style="text-align:center;"><small>{!! $estimaciones[$i][0] !!}</small></td>
                </tr>                                            
                <?php $total=$total+$estimaciones[$i][0]; ?>
                <?php $total1=$total1+$estimaciones[$i][3];  $x++;?>
            @endif
        @endfor
        
        <tr>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small></small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;"><small>TOTAL</small></td>
            <td bgcolor="#EAF2F8" style="text-align:center;">{!! $total1 !!}</td>
            <td bgcolor="#EAF2F8" style="text-align:center;">{!! $total !!}</td>
        </tr>
    </tbody>
</table>

            
