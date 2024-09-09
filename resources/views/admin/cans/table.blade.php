<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-body">
                <?php $x=1; ?>
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Descripcion</th>
                            <th rowspan="2">Mes</th>
                            <th rowspan="2">Año</th>
                            <th rowspan="2">CAN</th>
                            <th colspan="6"><p align="center">Reportes</p></th>
                        </tr>
                        <tr>
                            <th><p align="center">Nacional</p></th>                            
                            <th><p align="center">Establecimiento</p></th>
                            <th><p align="center">Servicios</p></th>
                            <th><p align="center">Nivel</p></th>
                            <th><p align="center">Tipo</p></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($cans as $can)
                        <tr>
                            <td><br/>{{$x++}}</td>
                            <td><br/><a data-toggle="tooltip" title="Descripcion" href="{!! route('cans.edit', [$can->id]) !!}">{!! $can->nombre_can !!}</a></td>
                            <td><br/>{!! $can->meses->descripcion !!}</td>
                            <td><br/>{!! $can->ano !!}</td>
                            <td>
                                <a data-toggle="tooltip" title="Por IPRESS!" href="{!! route('cans.show', [$can->id]) !!}" class='btn btn-md bg-maroon btn-flat margin'><i class="fa fa-fw fa-industry "></i></a>
                            </td> 
                            <td>
                                <a data-toggle="tooltip" title="Producto Farmaceutico Nacional!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,1]) !!}" class='btn btn-md bg-primary btn-flat margin'><i class="fa fa-medkit"></i></a>
                                <a data-toggle="tooltip" title="Dispositivo Medico Nacional!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,2]) !!}" class='btn btn-md bg-primary btn-flat margin'><i class="fa fa-stethoscope"></i></a> 
                            </td>
                            <td>
                                <a data-toggle="tooltip" title="Producto Farmaceutico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,1]) !!}" class='btn btn-md bg-purple btn-flat margin'><i class="fa fa-medkit"></i></a>
                                <a data-toggle="tooltip" title="Dispositivo Medico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,2]) !!}" class='btn btn-md bg-purple btn-flat margin'><i class="fa fa-stethoscope"></i></a> 
                            </td>
                            <td>
                                <a data-toggle="tooltip" title="Producto Farmaceutico por Servicio!" href="{!! route('cans.establecimientos_servicio_can', [$can->id,1]) !!}" class='btn btn-md bg-navy btn-flat margin'><i class="fa fa-medkit"></i></a>
                                <a data-toggle="tooltip" title="Dispositivo Medico por Servicio!" href="{!! route('cans.establecimientos_servicio_can', [$can->id,2]) !!}" class='btn btn-md bg-navy btn-flat margin'><i class="fa fa-stethoscope"></i></a> 
                            </td>
                            <td><a data-toggle="tooltip" title="Consolidado por Nivel!" href="{!! route('cans.nivel_total', [$can->id]) !!}" class='btn btn-md bg-orange btn-flat margin'><i class="fa fa-fw fa-level-up "></i></a>
                            </td>
                            
                            <td>                            
                                <a data-toggle="tooltip" title="Establecimientos por Tipo (Productos Farmaceutico / Dispositivo Medico)  !" href="{!! route('cans.establecimientos_can_2020', [$can->id]) !!}" class='btn btn-md bg-olive btn-flat margin'><i class="fa fa-fw fa-flask"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>


