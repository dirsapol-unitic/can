<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            <?php $x=1;?>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Año</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>#</td>
                            <td>Mes</td>
                            <td>Año</td>
                            <td>Medicamento</td>
                            <td>Dispositivo</td>
                            <td>Descargar</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $id_establecimiento = Auth::user()->establecimiento_id ?>
                    
                    @foreach($cans as $can)
                        <tr>
                            <td>{!! $x++ !!}</td>
                            <td>{!! $can->desc_mes !!}</td>
                            <td>{!! $can->ano !!}</td>
                            <td>
                                <div class='btn-group'>
                                @if ($can->medicamento_cerrado==1) <!--cerrado = 2 abierto=1-->
                                    @if ($items_medicamentos>0)
                                        <a href="{!! route('estimacion_servicio.cargar_medicamentos', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>1,'medicamento_cerrado'=>1]) !!}" class='btn btn-success btn-xs'><i class="fa fa-unlock""></i> Abierto </a>
                                    @else
                                        <small class="label label-default"> No Habilitado</small>         
                                    @endif 
                                @else
                                    <small class="label label-danger"><i class="fa fa-lock"></i> Cerrado</small>                                    
                                @endif
                                </div>        
                            </td>
                            
                            <td>    
                                <div class='btn-group'>
                                @if ($can->dispositivo_cerrado==1)
                                    @if ($items_dispositivos>0)
                                        <a href="{!! route('estimacion_servicio.cargar_medicamentos', ['can_id'=>$can->can_id,'establecimiento_id'=>$establecimiento_id,'tipo'=>2,'medicamento_cerrado'=>1]) !!}" class='btn btn-success btn-xs'><i class="fa fa-unlock"></i> Abierto</a>
                                    @else
                                        <small class="label label-default">No Habilitado</small>            
                                    @endif    

                                @else
                                    <small class="label label-danger"><i class="fa fa-lock"></i> Cerrado</small>                                    
                                @endif        
                                </div>
                            </td>
                            <td>
                                <div class='btn-group'>
                                    @if ($can->medicamento_cerrado==1)
                                        <a href="" class='btn bg-orange btn-xs' disabled ><i class="fa fa-medkit""></i></a>
                                    @else
                                        <a href="{!! route('estimacion_servicio.descargar_estimacion',['tipo'=>1,'can_id'=>$can->can_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    @endif
                                    @if ($can->dispositivo_cerrado==1)
                                        <a href="" class='btn bg-olive btn-xs' disabled ><i class="fa fa-stethoscope"></i></a>
                                    @else
                                        <a href="{!! route('estimacion_servicio.descargar_estimacion',['tipo'=>2,'can_id'=>$can->can_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>#</td>
                            <td>Mes</td>
                            <td>Año</td>
                            <td>Medicamento</td>
                            <td>Dispositivo</td>
                            <td>Descargar</td>
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>


