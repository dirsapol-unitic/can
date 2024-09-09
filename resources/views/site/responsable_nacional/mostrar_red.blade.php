<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Consolidado CAN - {!!$ano!!}</h3>
            </div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Red de Salud / Región de Salud</th>
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{!! $nombre_region_red_salud !!}</td>
                            <td>
                                <div class='btn-group'>
                                        <a href="{!! route('nacional.descargar_consolidado_region',['tipo'=>1,'can_id'=>$can_id,$region_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                        <a href="{!! route('nacional.descargar_consolidado_region',['tipo'=>2,'can_id'=>$can_id,$region_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>
<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Listado</h3>
            </div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Responsable de Farmacia</th>
                            <th>Grado</th>
                            <th>Celular</th>
                            <th>Descripción</th>
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($establecimientos as $key => $establecimiento)
                        <tr>
                            <td>{{$key+1}}</td>                            
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($responsable->establecimiento_id == $establecimiento->id)
                                        {!! $responsable->nombre !!}<br/>
                                    @endif
                                @endforeach    
                            </td>
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($responsable->establecimiento_id == $establecimiento->id)
                                        {!! $responsable->descripcion_grado !!}<br/>
                                    @endif
                                @endforeach    
                            </td>
                            <td>
                                @foreach($responsables as $key => $responsable)
                                    @if ($responsable->establecimiento_id == $establecimiento->id)
                                        {!! $responsable->celular !!}<br/>
                                    @endif
                                @endforeach    
                            </td>                            
                            <td>
                                <div class='btn-group'>
                                    <a href="{!! route('nacional.listar_distribucion', [$can_id,$establecimiento->id ]) !!}">{!! $establecimiento->nombre_establecimiento !!}</a>
                                </div>
                            </td>
                            <td>
                                <div class='btn-group'>
                                    <a href="{!! route('nacional.descargar_consolidado_ipress',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento->id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    <a href="{!! route('nacional.descargar_consolidado_ipress',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento->id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
                                </div>
                            </td>    
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>
