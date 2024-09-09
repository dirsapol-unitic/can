<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Reportes de Productos en Disponibilidad, {!! $mes !!} - {!!$ano!!}</h3>
                <a class="btn btn-success pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('abastecimiento.exportreportes',['can_id'=>$can_id,8,'xlsx']) !!}">Descargar <i class="fa fa-file-excel-o"></i></a>
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
                            <th></th>    
                            <th></th>                  
                        </tr>
                        <tr>
                            <td>#</td>
                            <td>Región/Red</td>
                            <td>Nombre_Establecimiento</td>                            
                            <td>Nivel</td>
                            <td>Cantidad</td>
                            <td>Porcentaje</td>                  
                            <td>Puntaje</td>                  
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
                            <td>    <a href="{!! route('cans.descargar_productos',['can_id'=>$indicador->can_id,'establecimiento_id'=>$indicador->establecimiento_id,8]) !!}"><small>{!! $indicador->disponible_cantidad !!}</small></a>
                            </td>
                            @if($indicador->disponible_porcentaje > 90)                            
                            <td bgcolor="#4B8A08" style="text-align:center;">{!! number_format($indicador->disponible_porcentaje, 2, '.', ',').' %' !!}</td>
                            @endif
                            @if($indicador->disponible_porcentaje >= 70 && $indicador->disponible_porcentaje <= 90)
                            <td bgcolor="#FFFF00" style="text-align:center;">{!! number_format($indicador->disponible_porcentaje, 2, '.', ',').' %' !!}</td>
                            @endif
                            @if($indicador->disponible_porcentaje < 70)
                            <td bgcolor="#FE2E2E" style="text-align:center;">{!! number_format($indicador->disponible_porcentaje, 2, '.', ',').' %' !!}</td>
                            @endif
                            <td>{!! number_format($indicador->disponible_puntaje, 2, '.', ',') !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>            
        </div>        
    </div>    
</div>