
                <table id="example" class="table table-responsive table-striped">
                <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->
                    <tbody>
                        <tr>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>SERVICIO</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>RESPONSABLE</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;">NECESIDAD</td>
                        </tr>
                        <?php $name='OK'; ?>
                        @foreach($estimaciones as $key => $estimacion)
                            <tr>
                                <?php if($name!=$estimacion->nombre_servicio): 
                                        $name=$estimacion->nombre_servicio;
                                    ?>
                                    <td><small>{!! $estimacion->nombre_servicio!!}</small></td>
                                    <td>
                                        @foreach($estimaciones as $key => $estimacion2)
                                            <?php if($name==$estimacion2->nombre_servicio): ?>
                                                <small>{!! $estimacion2->nombre !!}  ,</small>
                                            <?php endif; ?>
                                        @endforeach
                                    </td>
                                    <td><small><p align="center"> {!! $estimacion->necesidad_anual !!}</p></small></td>
                                <?php endif; ?>
                            </tr>                                            
                        @endforeach
                    </tbody>
                </table>

                

                

            