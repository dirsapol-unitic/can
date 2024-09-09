<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            <?php $x=1;?>
            <div class="box-body">
                <div class="table-responsive">
                <table id="example" class="table table-responsive table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Región/Red</th>
                            <th>Categoría</th>
                            <th>T_Ipress</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>#</td>
                            <td>Código</td>
                            <td>Nombre_Establecimiento</td>
                            <td>Región/Red</td>
                            <td>Categoría</td>
                            <td>T_Ipress</td>
                            <td>Estado</td>
                            <td>Editar</td>    
                            
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($establecimientos as $key => $establecimiento)
                        <tr>
                            <td> <?php echo $x++; ?></td>
                            <td> {!! $establecimiento->codigo_establecimiento !!}</td>
                            <td>{!! $establecimiento->nombre_establecimiento !!}</td>
                            <td>{!! $establecimiento->est_region->descripcion !!}</td>
                            <td>{!! $establecimiento->est_categoria->descripcion !!}</td>
                            <td>{!! $establecimiento->est_tipo->descripcion !!}</td>
                            <td><?php if ($establecimiento->estado==1) echo 'ACTIVO'; else echo 'DESACTIVADO';?></td>                                 
                            <td>
                                @if ($establecimiento->nivel_id > 1)
                                    <div class='btn-group'>
                                        <a  data-toggle="tooltip" title="Asignar Rubro!" href="{!! route('establecimientos.ver_rubros', [$establecimiento->id]) !!}" class='btn bg-purple btn-flat margin btn-xs'>R</a>
                                    </div>
                                @endif
                                <a data-toggle="tooltip" title="Editar Establecimiento!" href="{!! route('establecimientos.edit', [$establecimiento->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>            
        </div>        
    </div>    
</div>