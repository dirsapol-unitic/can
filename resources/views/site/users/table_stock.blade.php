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
    
?>
<p>A continuacion colocar el Responsable de la IPRESS y el responsable de Farmacia</p>
<br/><br/><br/>
<div class="table-responsive">
    <table id="example" class="table table-bordered table-striped">
        <thead>
            <tr>
                <td>#</td>           
                <td>Tipo Responsable</td>                
                <td>Nombre</td>                
                
                @if($extra==0)
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
                <td>{!! $user[0][0] !!}</td>
                @if($extra==0)  
                    <td>
                        <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[0][2],'valor_id'=>0]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    </td>
                @endif
            </tr>
            @if($establecimiento_id!=79)
            <tr>
                <td>2.-</td>
                <td>RESPONSABLE O JEFE DE FARMACIA</td>
                <td>{!! $user[1][0] !!}</td>
                                   
                @if($extra==0)
                        <td>
                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable', [$user[1][2],'valor_id'=>1]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                @endif
            </tr>
            @endif
        </tbody>
    </table>
</div>