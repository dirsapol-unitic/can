<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>
                            
                            <th rowspan="3" style="text-align:center;">Descripción</th>
                            <th rowspan="3" style="text-align:center;">Precio</th>
                            <th rowspan="3" style="text-align:center;">CPMA</th>
                            <th colspan="7" bgcolor="#D4E6F1" style="text-align:center;">INGRESOS</th>
                            <th colspan="5" bgcolor="#D4EFDF" style="text-align:center;">SALIDAS</th>
                            <th colspan="3" bgcolor="#FEF5E7" style="text-align:center;">STOCK</th>
                            <th rowspan="2" colspan="2" bgcolor="#FDEDEC" style="text-align:center;">SOBRESTOCK</th>
                                          
                        </tr>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;" ><small>Stock Incanal</small></th>
                            <th bgcolor="#D4E6F1" style="text-align:center;"><small>Almacen Saludpol</small></th>
                            <th bgcolor="#D4E6F1" style="text-align:center;"><small>Ingreso Central</small></th>
                            <th bgcolor="#D4E6F1" style="text-align:center;"><small>Directo Proveedor</small></th>
                            <th bgcolor="#D4E6F1" style="text-align:center;"><small>Transferencia</small></th>
                            <th colspan="2" bgcolor="#D4E6F1" style="text-align:center;">TOTAL INGRESOS</th>
                            <th colspan="2" bgcolor="#D4EFDF" style="text-align:center;">CONSUMO</th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Transferencia</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Pérdida/Merma</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Total Salidas</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Final</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Fecha Vencimiento</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Disponibilidad</small></th>
                            
                                          
                        </tr>
                        <tr>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Valor</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Valor</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Fecha</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Meses</small></th>
                            <th bgcolor="#FDEDEC" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#FDEDEC" style="text-align:center;"><small>Valor</small></th>
                                          
                        </tr>
                    </thead>                
                    <tbody>
                    @foreach($abastecimientos as $key => $abastecimiento)
                        <tr>
                            <td><small>{!! $abastecimiento->descripcion !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->precio !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->cpma !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->stock_incanal !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->almacen_central !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->ingreso_almacen2 !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->ingreso_proveedor !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->ingreso_transferencia !!}</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>{!! $abastecimiento->unidad_ingreso !!}</small></td>
                            <td bgcolor="#D4E6F1" style="text-align:center;"><small>{!! number_format($abastecimiento->valor_ingreso,2) !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->unidad_consumo !!}</small></td>
                            <td bgcolor="#D4E6F1" style="text-align:center;"><small>{!! number_format($abastecimiento->valor_consumo,2) !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->salida_transferencia !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->merma !!}</small></td>
                            <td bgcolor="#D4EFDF" style="text-align:center;"><small>{!! $abastecimiento->total_salidas !!}</small></td>
                            <?php $color = ($abastecimiento->stock_final < 0) ? 'bgcolor="#FF0000"' : '#bgcolor="#FEF5E7"'; ?>
                            <td <?php echo $color; ?> style="text-align:center;"><small>{!! $abastecimiento->stock_final !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->fecha_vencimiento !!}</small></td>
                            <?php $color = ($abastecimiento->disponibilidad < 0) ? 'bgcolor="#FF0000"' : '#bgcolor="#FEF5E7"'; ?>
                            <td <?php echo $color; ?> style="text-align:center;"><small>{!! number_format($abastecimiento->disponibilidad,2) !!}</small></td>
                            <td bgcolor="#FDEDEC" style="text-align:center;"><small>{!! $abastecimiento->unidades_sobrestock !!}</small></td>
                            <td bgcolor="#FDEDEC" style="text-align:center;"><small>{!! number_format($abastecimiento->valor_sobrestock,2) !!}</small></td>                            
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
             
    </div>    
</div>