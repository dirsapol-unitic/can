<!--table id="example1" class="table table-bordered table-striped"-->
<?php
use Carbon\Carbon;

    $date = Carbon::now();
            $fecha = $date->format('d-m-Y H:i:s');            
            $fechaFin = Carbon::parse(Auth::user()->fin_first_login);
                                         
            $fechaActual = Carbon::parse($fecha);
            if($fechaActual<=$fechaFin){
                $dias = $fechaActual->diffInDays($fechaFin);
                $horas = $fechaActual->diffInHours($fechaFin);
                $min = $fechaActual->diffInMinutes($fechaFin);
                $minutos =1;           
            }
            else{
                $dias =0;           
                $horas =0;           
                $minutos =0;           
            }

    $establecimiento_can = DB::table('can_establecimiento')
                ->where('can_id',$can_id)
                ->where('establecimiento_id',Auth::user()->establecimiento_id)
                ->get();

            $cerrado_medicamento=$establecimiento_can->get(0)->medicamento_cerrado;
        
            $cerrado_dispositivo=$establecimiento_can->get(0)->dispositivo_cerrado;

            
            

?>
<p>A continuacion se muestra un listado de los Rubros para ingresar los responsables del llenado del CUADRO ANUAL DE NECESIDAD</p>
<ul><li>Para cada Rubro deberan registrar a un responsables por rubros que van a pedir en su CAN.</li>
<li>Puede un responsable estar en uno o varios RUBROS ASIGNADOS.</li>
</ul>
<div class="table-responsive">
    <table id="example" class="table table-bordered table-striped">
        <thead>
            <tr>
                <td>#</td>           
                <td>Tipo Responsable</td>    
                <td>Grado</td>                
                <td>Nombre</td>                
                @if($can_id==$can_id_ultimo)
                    <td>Editar</td>   
                @endif                
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1.-</td>
                @if($establecimiento_id!=79)
                    <td>JEFE DE LA IPRESS</td>
                @else
                    <td>JEFE O RESPONSABLE DE LA UNIDAD</td>
                @endif
                <td>{!! $user[0][1] !!}</td>
                <td>{!! $user[0][0] !!}</td>
                @if($can_id==$can_id_ultimo)
                    <td><?php if ($user[0][2]==''): ?>
                            <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable',[$can_id,'valor_id'=>0]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                        <?php else: ?>
                        <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[0][2],'valor_id'=>0]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        <?php endif; ?>
                    </td>
                @endif
            </tr>
            @if($establecimiento_id!=79)
            <tr>
                <td>2.-</td>
                <td>RESPONSABLE O JEFE DE FARMACIA</td>
                <td>{!! $user[1][1] !!}</td>
                <td>{!! $user[1][0] !!}</td>
                @if($can_id==$can_id_ultimo)      
                        <td><?php if ($user[1][2]==''): ?>
                            <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable',[$can_id,'valor_id'=>1]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                        <?php else: ?>
                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[1][2],'valor_id'=>1]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        <?php endif; ?>
                        </td>                    
                @endif
            </tr>
            @endif
            @if($establecimiento_id!=79)
            <tr>
                <td>3.-</td>
                <td>RESPONSABLE PRODUCTO FARMACEUTICO</td>
                <td>{!! $user[2][1] !!}</td>
                <td>{!! $user[2][0] !!}</td>
                @if($can_id==$can_id_ultimo)
                        <td><?php if ($user[2][2]==''): ?>
                                <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable',[$can_id,'valor_id'=>2]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                            <?php else: ?>
                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[2][2],'valor_id'=>2]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            <?php endif; ?>
                        </td>                   
                @endif
            </tr>
            @endif
            <tr>
                @if($establecimiento_id!=79)
                <td>4.-</td>
                 @else
                 <td>2.-</td>
                 @endif
                <td>RESPONSABLE MATERIAL BIOMEDICO, INSTRUMENTAL QUIRURGICO Y PRODUCTOS AFINES</td>    
                <td>{!! $user[3][1] !!}</td>   
                <td>{!! $user[3][0] !!}</td>   
                @if($can_id==$can_id_ultimo)                                 
                        <td><?php if ($user[3][2]==''): ?>
                                <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable',[$can_id,'valor_id'=>3]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                            <?php else: ?>
                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[3][2],'valor_id'=>3]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            <?php endif; ?>
                        </td>                   
                @endif
            </tr>
            <tr>
                @if($establecimiento_id!=79)
                    <td>5.-</td>
                @else
                    <td>3.-</td>
                @endif
                <td>RESPONSABLE MATERIAL E INSUMOS DENTALES</td>
                <td>{!! $user[4][1] !!}</td>
                <td>{!! $user[4][0] !!}</td>
                @if($can_id==$can_id_ultimo)                                 
                        <td><?php if ($user[4][2]==''): ?>
                                <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable',[$can_id,'valor_id'=>4]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                            <?php else: ?>
                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[4][2],'valor_id'=>4]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            <?php endif; ?>
                        </td>                   
                @endif
            </tr>
            <tr>
                @if($establecimiento_id!=79)
                    <td>6.-</td>
                @else
                    <td>4.-</td>
                @endif
                <td>RESPONSABLE MATERIAL E INSUMOS DE LABORATORIO</td>
                <td>{!! $user[5][1] !!}</td>
                <td>{!! $user[5][0] !!}</td>    
                @if($can_id==$can_id_ultimo)
                        <td><?php if ($user[5][2]==''): ?>
                                <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable',[$can_id,'valor_id'=>5]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                            <?php else: ?>
                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[5][2],'valor_id'=>5]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            <?php endif; ?>
                        </td>                    
                @endif
            </tr>
            <tr>
                @if($establecimiento_id!=79)
                    <td>7.-</td>
                @else
                    <td>5.-</td>
                @endif
                <td>RESPONSABLE MATERIAL FOTOGRAFICO Y FONOTECNICO</td>
                <td>{!! $user[6][1] !!}</td>  
                <td>{!! $user[6][0] !!}</td>  
                @if($can_id==$can_id_ultimo)        
                        <td><?php if ($user[6][2]==''): ?>
                                <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable',[$can_id,'valor_id'=>6]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                            <?php else: ?>
                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[6][2],'valor_id'=>6]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            <?php endif; ?>
                        </td>                                   
                @endif
            </tr>
        </tbody>
    </table>
</div>