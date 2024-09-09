<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Nombre Rubro</th>
                            <th>Medicamentos</th>
                            <th>Dispositivos</th>
                            <th>Exportar</th>
                            <th>Operación</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($servicios as $key => $servicio)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $servicio->codigo !!}</td>
                            <td>{!! $servicio->nombre_servicio !!}</td>
                            <td><a data-toggle="tooltip" title="Editar Medicamentos!" href="{!! route('servicios.ver_medicamentos', [$servicio->id]) !!}" class='btn bg-purple btn-xs'><i class="fa fa-medkit"></i></a>
                            </td>
                            <td><a data-toggle="tooltip" title="Editar Dispositivos!" href="{!! route('servicios.ver_dispositivos', [$servicio->id]) !!}" class='btn bg-navy btn-xs'><i class="fa fa-stethoscope"></i></a></td>
                            <td>
                                 <a data-toggle="tooltip" title="Descargar Excel!" href="{!! route('servicios.exportServicio',['xlsx','servicio_id'=>$servicio->id,'tipo'=>3]) !!}" class='btn btn-success btn-xs'><i class="fa fa-file-excel-o"></i></a>
                                <a target="_blank" data-toggle="tooltip" title="Descargar PDF!" href="{!! route('servicios.pdf_servicio',['servicio_id'=>$servicio->id,'tipo'=>3]) !!}" class='btn btn-danger btn-xs'><i class="fa fa-file-pdf-o""></i></a>
                            </td>                         
                            <td>
                                {!! Form::open(['route' => ['servicios.destroy', $servicio->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a data-toggle="tooltip" title="Editar Servicio!" href="{!! route('servicios.edit', [$servicio->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Esta Seguro?, eliminará de la base de datos todos los datos relacionado con este servicio')"]) !!}
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