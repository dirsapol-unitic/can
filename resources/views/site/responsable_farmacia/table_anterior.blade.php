<div class="row">
    <?php
    use Carbon\Carbon;

    $date = Carbon::now();
            $fecha = $date->now();       
            $fechaFin = Carbon::parse(Auth::user()->fin_first_login);
                                         
            $fechaActual = Carbon::parse($fecha);
            
            if($fechaActual<=$fechaFin)
                $diasDiferencia = $fechaActual->diffInMinutes($fechaFin);
            else
                $diasDiferencia =0;           

    ?>
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title">Registro CAN - 2020</h3>
            </div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                <!--table id="example" class="table table-bordered table-striped"-->
                    <thead>
                        <tr>
                            <th>Establecimiento</th>
                            @if (Auth::user()->rol==7 )
                                @if($establecimiento_id!=79)
                                    <th>Medic.</th>
                                @endif
                                <th>Dispos.</th>                                
                            @endif
                            <th>Observaciones</th>
                            <th>Descargar</th>
                            @if (Auth::user()->rol==7 )
                                @if($establecimiento_id!=79)
                                    <th>Stock Medic.</th>
                                @endif
                                <th>Stock Dispos.</th>                                
                            @endif
                            <th>Descargar Stock</th>
                            <th>Subir CAN2020</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{!! $nombre_establecimiento !!}</td>
                            @if ( Auth::user()->rol==7 )
                                @if($establecimiento_id!=79)
                                <td>
                                    @if ($medicamento_cerrado==1 )
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can_id,'tipo'=>1]) !!}" class='btn bg-purple btn-md'><i class="fa fa-medkit"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-red btn-md'>No puede editar, su tiempo ha concluido</a>
                                        @endif
                                     @else
                                        <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    @if ($dispositivo_cerrado==1)
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can_id,'tipo'=>2]) !!}" class='btn bg-purple btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-red btn-md'>No puede editar, su tiempo ha concluido</a>
                                        @endif
                                    @else
                                        <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a> 
                                    @endif
                                </td>
                                <td>
                                    <a href="{!! route('farmacia_servicios.listar_observaciones_nivel1') !!}" class='btn bg-purple btn-md'><i class="fa fa-tripadvisor"></i></a>
                                </td>
                                <td>
                                    <div class='btn-group'>
                                        @if($establecimiento_id!=79)
                                            @if ($medicamento_cerrado==2)
                                                    <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>1,'can_id'=>$can_id]) !!}" class='btn bg-orange btn-md'><i class="fa fa-medkit""></i></a>
                                            @else
                                                <a href="#" disabled class='btn bg-orange btn-md'><i class="fa fa-medkit""></i></a>
                                            @endif
                                        @endif
                                        @if ($dispositivo_cerrado==2)
                                                <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>2,'can_id'=>$can_id]) !!}" class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <a href="#" disabled class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @endif
                                    </div>
                                </td>
                                @if($establecimiento_id!=79)
                                <td>
                                    @if ($medicamento_cerrado_stock==1 )
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can_id,'tipo'=>1]) !!}" class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                        @else
                                            <a href="#" disabled class='btn btn-info btn-md'>No Editar</a>
                                        @endif
                                     @else
                                        <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    @if ($dispositivo_cerrado_stock==1)
                                        @if ($diasDiferencia!=0 )
                                            <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can_id,'tipo'=>2]) !!}" class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <a href="#" disabled class='btn btn-info btn-md'>No Editar</a>
                                        @endif
                                    @else
                                        <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a> 
                                    @endif
                                </td>
                                <td>
                                    <div class='btn-group'>
                                        @if($establecimiento_id!=79)
                                            @if ($medicamento_cerrado_stock==2)
                                                    <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>1,'can_id'=>$can_id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit""></i></a>
                                            @else
                                                <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit""></i></a>
                                            @endif
                                        @endif
                                        @if ($dispositivo_cerrado_stock==2)
                                                <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>2,'can_id'=>$can_id]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @else
                                            <a href="#" disabled class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                        @endif
                                    </div>
                                </td>                            
                                <td>
                                    <a href="{!! route('farmacia_servicios.listar_archivos_nivel1') !!}" class='btn bg-blue btn-md'><i class="fa fa-upload"></i></a>
                                    
                                </td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>
