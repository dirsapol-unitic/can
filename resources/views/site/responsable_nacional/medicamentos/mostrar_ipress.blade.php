
            @foreach($estimacions as $key => $estimacion)
            <table id="example" class="table table-responsive table-striped">
                <tbody>
                    <tr>
                        <td>
                            <table id="example" class="table table-responsive table-striped">
                                <tbody>
                                    <tr>
                                        <td colspan="3" bgcolor="#EAF2F8" style="text-align:center;">CPMA</td>
                                        <td colspan="3" bgcolor="#EAF2F8" style="text-align:center;">Stock</td>
                                        <td colspan="3" bgcolor="#EAF2F8" style="text-align:center;">Necesidad</td>
                                    </tr> 
                                    <tr>
                                        <td colspan="3" bgcolor="#EAF2F8" style="text-align:center;"><b>{!! $estimacion->cpma !!}</b></td>
                                        <td colspan="3" bgcolor="#EAF2F8" style="text-align:center;"><b>{!! $estimacion->stock !!}</b></td>
                                        <td colspan="3" bgcolor="#EAF2F8" style="text-align:center;"><b>{!! $estimacion->necesidad_anual !!}</b></td>
                                    </tr>                                            
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table id="example" class="table table-responsive table-striped">
                            <!--table id="example" class="stripe row-border order-column" cellspacing="0" -->  

                                <tbody>
                                    <tr>
                                        <td colspan="2" bgcolor="#F2F2F2" style="text-align:center;">ESTABLECIMIENTO</td>
                                        <td colspan="3" bgcolor="#F2F2F2" style="text-align:center;">{!! $estimacion->nombre_establecimiento !!}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" bgcolor="#E6E6E6" style="text-align:center;">RESPONSABLE</td>
                                        <td colspan="3" bgcolor="#E6E6E6" style="text-align:center;">{!! $estimacion->nombre !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endforeach
            
