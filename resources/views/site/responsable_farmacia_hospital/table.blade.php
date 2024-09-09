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
<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Establecimiento</th>
                            @if (Auth::user()->rol==3 )
                                <th>Medicamentos</th>
                            @else
                                <th>Dispositivos</th>
                            @endif
                            <th>Observaciones</th>
                            <th>Descargar</th>
                            <th>Responsables</th>
                            <th>Subir PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $x=1; ?>
                        @foreach($cans as $can) 
                                <tr>
                                    <td><?php echo $x++;?></td>
                                   <td><a href="{!! route('farmacia_servicios.listar_medicos', ['can_id'=>$can->id]) !!}"> {!! $can->nombre_can!!} - {!! $can->desc_mes!!}, {!! $can->ano!!}</a></td>
                                    @if (Auth::user()->rol==3)
                                    <td>
                                        @if ($diasDiferencia!=0 )
                                            @if ($can->rubro_pf==1 )
                                                    <a href="{!! route('farmacia_servicios.ver_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-purple'><i class="fa fa-medkit"></i></a>
                                            @else
                                                    <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                            @endif
                                        @else
                                            @if ($can->rubro_pf==1 )
                                                <a href="#" disabled class='btn bg-red'>No puede cerrar su petitorio, comuniquese con la UGPFDMPS para la activacion</a>
                                            @else
                                                    <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                            @endif                                        
                                        @endif
                                    </td>
                                    @else
                                        @if (Auth::user()->rol==4)
                                            <td>
                                                @if ($diasDiferencia!=0 )                                        
                                                    @if ($can->rubro_mb_iq_pa==1 )
                                                        <a href="{!! route('farmacia_servicios.ver_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-purple'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                    @endif                                        
                                                @else
                                                    @if ($can->rubro_mb_iq_pa==1 )
                                                        <a href="#" disabled class='btn bg-red'>No puede cerrar su petitorio, comuniquese con la UGPFDMPS para la activacion</a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                    @endif                          
                                                @endif
                                            </td>
                                        @else
                                            @if (Auth::user()->rol==5)
                                            <td>
                                                @if ($diasDiferencia!=0 )                                    
                                                    @if ($can->rubro_mid==1 )
                                                        <a href="{!! route('farmacia_servicios.ver_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-purple'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                            <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                    @endif   
                                                @else
                                                    @if ($can->rubro_mid==1 )
                                                        <a href="#" disabled class='btn bg-red'>No puede cerrar su petitorio, comuniquese con la UGPFDMPS para la activacion</a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                    @endif   
                                                @endif
                                            </td>
                                            @else
                                                @if (Auth::user()->rol==6)
                                                <td>
                                                    @if ($diasDiferencia!=0 )   
                                                        @if ($can->rubro_mil==1 )
                                                            <a href="{!! route('farmacia_servicios.ver_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-purple'><i class="fa fa-stethoscope"></i></a>
                                                        @else
                                                            <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                        @endif   
                                                    @else
                                                        @if ($can->rubro_mil==1 )
                                                            <a href="#" disabled class='btn bg-red'>No puede cerrar su petitorio, comuniquese con la UGPFDMPS para la activacion</a>
                                                        @else
                                                            <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                    @endif
                                                </td>
                                                @else
                                                    @if (Auth::user()->rol==8)
                                                    <td>
                                                        @if ($diasDiferencia!=0 )   
                                                            @if ($can->rubro_mff==1 )
                                                                <a href="{!! route('farmacia_servicios.ver_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-purple'><i class="fa fa-stethoscope"></i></a>
                                                            @else
                                                                    <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                            @endif   
                                                        @else
                                                            @if ($can->rubro_mff==1 )
                                                                <a href="#" disabled class='btn bg-red'>No puede cerrar su petitorio, comuniquese con la UGPFDMPS para la activacion</a>
                                                            @else
                                                                    <a href="#" disabled class='btn bg-red'><i class="fa fa-medkit"></i></a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    @endif                                           
                                                @endif   
                                            @endif   
                                        @endif                            
                                    @endif                            
                                    <td>
                                        <a href="{!! route('farmacia_servicios.listar_observaciones_comite',['can_id'=>$can->can_id]) !!}" class='btn bg-purple btn-md'><i class="fa fa-tripadvisor"></i></a>
                                    </td>
                                    <td>
                                    
                                        @if (Auth::user()->rol==3)
                                        <div class='btn-group'>
                                            @if ($can->rubro_pf==2)
                                                <a href="{!! route('farmacia_servicios.ver_consolidado_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-orange'><i class="fa fa-medkit"></i></a>
                                            @else
                                                <a href="#" disabled class='btn bg-orange'><i class="fa fa-medkit"></i></a>
                                            @endif
                                        </div>
                                        @else
                                            @if (Auth::user()->rol==4)
                                                @if ($can->rubro_mb_iq_pa==2)
                                                    <div class='btn-group'>
                                                        <a href="{!! route('farmacia_servicios.ver_consolidado_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-olive'><i class="fa fa-stethoscope"></i></a>
                                                    </div>
                                                @endif
                                            @else
                                                @if (Auth::user()->rol==5)
                                                    @if ($can->rubro_mid==2)
                                                        <div class='btn-group'>
                                                            <a href="{!! route('farmacia_servicios.ver_consolidado_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-olive'><i class="fa fa-stethoscope"></i></a>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if (Auth::user()->rol==6)
                                                        @if ($can->rubro_mil==2)
                                                            <div class='btn-group'>
                                                                <a href="{!! route('farmacia_servicios.ver_consolidado_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-olive'><i class="fa fa-stethoscope"></i></a>
                                                            </div>
                                                        @endif
                                                    @else
                                                        @if (Auth::user()->rol==8)
                                                            @if ($can->rubro_mff==2)
                                                                <div class='btn-group'>
                                                                    <a href="{!! route('farmacia_servicios.ver_consolidado_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can->can_id]) !!}" class='btn bg-olive'><i class="fa fa-stethoscope"></i></a>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{!! route('farmacia_servicios.listar_responsables_servicios',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>
                                    </td>
                                    <td>
                                        <a href="{!! route('farmacia_servicios.listar_archivos_comite',['can_id'=>$can->can_id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-upload"></i></a>
                                    </td>                                
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>