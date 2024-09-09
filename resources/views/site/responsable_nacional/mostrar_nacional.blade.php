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
                            <th>Nacional</th>
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Consolidado Nacional</td>
                            <td>
                                <div class='btn-group'>
                                        <a href="{!! route('nacional.descargar_consolidado_nacional',['tipo'=>1,'can_id'=>$can_id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                        <a href="{!! route('nacional.descargar_consolidado_nacional',['tipo'=>2,'can_id'=>$can_id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
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
                <h3 class="box-title">Consolidado CAN - {!!$ano!!}</h3>
            </div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Red de Salud / Región de Salud</th>
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regiones as $key => $region)
                        <tr>
                            <td>{!! $key+1 !!}</td>
                            <td>
                                <a href="{!! route('nacional.listar_red', [$can_id,$region->id]) !!}">{!! $region->descripcion !!}</a>
                            </td>
                            <td>
                                <div class='btn-group'>
                                    <a href="{!! route('nacional.descargar_consolidado_region',['tipo'=>1,'can_id'=>$can_id,$region->id]) !!}" class='btn bg-orange btn-xs'><i class="fa fa-medkit""></i></a>
                                    <a href="{!! route('nacional.descargar_consolidado_region',['tipo'=>2,'can_id'=>$can_id,$region->id]) !!}" class='btn bg-olive btn-xs'><i class="fa fa-stethoscope"></i></a>
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
