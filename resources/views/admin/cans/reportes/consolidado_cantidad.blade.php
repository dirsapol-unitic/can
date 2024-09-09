<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Reportes de Productos consolidado, {!! $mes !!} - {!!$ano!!}</h3>
                <a class="btn btn-success pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('abastecimiento.exportConsolidado',[1,'xlsx']) !!}">Descargar <i class="fa fa-file-excel-o"></i></a>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                <table id="example" class="table table-responsive table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Región/Red</th>
                            <th></th>
                            <th>Nivel</th>
                            <th></th>
                            <th colspan="2"></th>
                            <th colspan="2"></th>
                            <th colspan="2"></th>
                            <th colspan="2"></th>
                            <th colspan="2"></th>
                            <th colspan="2"></th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <td rowspan="2" style="text-align:center;">#</td>
                            <td rowspan="2" style="text-align:center;">Región/Red</td>
                            <td rowspan="2" style="text-align:center;">Nombre_Establecimiento</td>
                            <td rowspan="2" style="text-align:center;">Nivel</td>
                            <td rowspan="2" style="text-align:center;">Total</td>
                            <td colspan="2" style="text-align:center;">Desabastecimiento</td>
                            <td colspan="2" style="text-align:center;">Normostock</td>                  
                            <td colspan="2" style="text-align:center;">Substock</td>                  
                            <td colspan="2" style="text-align:center;">Sobrestock</td>
                            <td colspan="2" style="text-align:center;">Sinrotacion</td>
                            <td colspan="2" style="text-align:center;">Existente</td>
                            <td colspan="2" style="text-align:center;">Disponibilidad</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;" ><small>Cant.</small></td>
                            <td style="text-align:center;" ><small> % </small></td>
                            <td style="text-align:center;" ><small>Cant.</small></td>
                            <td style="text-align:center;" ><small> % </small></td>
                            <td style="text-align:center;" ><small>Cant.</small></td>
                            <td style="text-align:center;" ><small> % </small></td>
                            <td style="text-align:center;" ><small>Cant.</small></td>
                            <td style="text-align:center;" ><small> % </small></td>
                            <td style="text-align:center;" ><small>Cant.</small></td>
                            <td style="text-align:center;" ><small> % </small></td>
                            <td style="text-align:center;" ><small>Cant.</small></td>
                            <td style="text-align:center;" ><small> % </small></td>
                            <td style="text-align:center;" ><small>Cant.</small></td>
                            <td style="text-align:center;" ><small> % </small></td>
                        </tr>
                    </thead>                
                    <tbody>             
                    <?php $i=1;?>
                    @foreach($indicadores as $key => $indicador)
                        <tr>
                            <td>{!! $i++ !!}</td>
                            <td>{!! $indicador->ind_region->descripcion !!}</td>
                            <td>{!! $indicador->ind_establecimiento->nombre_establecimiento !!}</td>
                            <td>{!! $indicador->ind_nivel->descripcion !!}</td>
                            <td>{!! $indicador->total_items !!}</td>
                            
                            @if($indicador->desabastecido_porcentaje <= 10)                            
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! $indicador->desabastecido_cantidad !!}</td>
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! number_format($indicador->desabastecido_porcentaje, 2, '.', ',').' %' !!}</td>
                            @else
                                @if($indicador->desabastecido_porcentaje > 10 && $indicador->desabastecido_porcentaje <= 30)
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! $indicador->desabastecido_cantidad !!}</td>
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! number_format($indicador->desabastecido_porcentaje, 2, '.', ',').' %' !!}</td>
                                @else
                                    @if($indicador->desabastecido_porcentaje > 30)
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! $indicador->desabastecido_cantidad !!}</td>
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! number_format($indicador->desabastecido_porcentaje, 2, '.', ',').' %' !!}</td>
                                    @endif
                                @endif
                            @endif

                            @if($indicador->normostock_porcentaje > 80)                            
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! $indicador->normostock_cantidad !!}</td>
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! number_format($indicador->normostock_porcentaje, 2, '.', ',').' %' !!}</td>
                            @else
                                @if($indicador->normostock_porcentaje >= 60 && $indicador->normostock_porcentaje <= 80)
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! $indicador->normostock_cantidad !!}</td>
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! number_format($indicador->normostock_porcentaje, 2, '.', ',').' %' !!}</td>
                                @else
                                    @if($indicador->normostock_porcentaje < 60)
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! $indicador->normostock_cantidad !!}</td>
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! number_format($indicador->normostock_porcentaje, 2, '.', ',').' %' !!}</td>
                                    @endif
                                @endif
                            @endif

                            @if($indicador->substock_porcentaje < 10)                            
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! $indicador->substock_cantidad !!}</td>
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! number_format($indicador->substock_porcentaje, 2, '.', ',').' %' !!}</td>
                            @else
                                @if($indicador->substock_porcentaje >= 10 && $indicador->substock_porcentaje <= 20)
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! $indicador->substock_cantidad !!}</td>
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! number_format($indicador->substock_porcentaje, 2, '.', ',').' %' !!}</td>
                                @else
                                    @if($indicador->substock_porcentaje > 20)
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! $indicador->substock_cantidad !!}</td>
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! number_format($indicador->substock_porcentaje, 2, '.', ',').' %' !!}</td>
                                    @endif
                                @endif
                            @endif

                            @if($indicador->sobrestock_porcentaje < 10)                            
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! $indicador->sobrestock_cantidad !!}</td>
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! number_format($indicador->sobrestock_porcentaje, 2, '.', ',').' %' !!}</td>
                            @else
                                @if($indicador->sobrestock_porcentaje >= 10 && $indicador->sobrestock_porcentaje <= 20)
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! $indicador->sobrestock_cantidad !!}</td>
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! number_format($indicador->sobrestock_porcentaje, 2, '.', ',').' %' !!}</td>
                                @else
                                    @if($indicador->sobrestock_porcentaje > 20)
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! $indicador->sobrestock_cantidad !!}</td>
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! number_format($indicador->sobrestock_porcentaje, 2, '.', ',').' %' !!}</td>
                                    @endif
                                @endif
                            @endif    

                            @if($indicador->sinrotacion_porcentaje == 0)                            
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! $indicador->sinrotacion_cantidad !!}</td>
                                <td bgcolor="#4B8A08" style="text-align:center;">{!! number_format($indicador->sinrotacion_porcentaje, 2, '.', ',').' %' !!}</td>
                            @else
                                @if($indicador->sinrotacion_porcentaje > 0 && $indicador->sinrotacion_porcentaje < 10)
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! $indicador->sinrotacion_cantidad !!}</td>
                                    <td bgcolor="#FFFF00" style="text-align:center;">{!! number_format($indicador->sinrotacion_porcentaje, 2, '.', ',').' %' !!}</td>
                                @else
                                    @if($indicador->sinrotacion_porcentaje >= 10)
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! $indicador->sinrotacion_cantidad !!}</td>
                                        <td bgcolor="#FE2E2E" style="text-align:center;">{!! number_format($indicador->sinrotacion_porcentaje, 2, '.', ',').' %' !!}</td>
                                    @endif
                                @endif
                            @endif    
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="5" style="text-align:right">Total:</th>
                            <th></th>
                            
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>            
        </div>        
    </div>    
</div>