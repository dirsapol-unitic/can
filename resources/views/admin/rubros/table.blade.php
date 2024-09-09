<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Descripcion</th>
                            <th>Medicamentos</th>
                            <th>Dispositivos</th>
                            <th>Exportar</th>
                            <th>Operación</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($rubros as $rubro)
                        <tr>
                            <td>{!! $rubro->descripcion !!}</td>
                            <td><a data-toggle="tooltip" title="Listar Medicamentos!" href="{!! route('rubros.ver_medicamentos', [$rubro->id]) !!}" class='btn bg-purple btn-xs'><i class="fa fa-medkit"></i></a></td>
                            <td><a data-toggle="tooltip" title="Listar Dispositivos!" href="{!! route('rubros.ver_dispositivos', [$rubro->id]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-stethoscope"></i></a></td>
                            <td>
                                <a data-toggle="tooltip" title="Descargar Excel!" href="{!! route('rubros.exportRubro',['xlsx','rubro_id'=>$rubro->id,'tipo'=>3]) !!}" class='btn btn-success btn-xs'><i class="fa fa-file-excel-o"></i></a>
                                <a target="_blank" data-toggle="tooltip" title="Descargar PDF!" href="{!! route('rubros.pdf_rubro',['rubro_id'=>$rubro->id,'tipo'=>3]) !!}" class='btn btn-danger btn-xs'><i class="fa fa-file-pdf-o""></i></a>
                            </td>                         
                            <td>
                                {!! Form::open(['route' => ['rubros.destroy', $rubro->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a href="{!! route('rubros.edit', [$rubro->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Esta Seguro?, eliminará de la base de datos todos los datos relacionado con este rubro')"]) !!}
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>