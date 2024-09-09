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
                <h3 class="box-title">Registro CAN</h3>
            </div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">                
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripcion</th>                            
                            @if (Auth::user()->rol==7 )
                                <th>

                                    Med./
                                    
                                    Disp.
                                </th>                                
                            @endif
                            <th>Descargar</th>
                            <th>Observ.</th>
                            <th>Responsables</th>
                            @if($nivel==1)
                            <th>Atenciones</th>
                            @endif
                            @if (Auth::user()->rol==7 )
                                <th>
                                    Subir Stocks
                                </th>                                
                            @endif
                            <th>Descargar Stock</th>   
                            <th>Responsables Stocks</th>
                            <th>Ver/Subir PDF CAN</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x=1; ?>
                        @foreach($cans as $can)
                            @if($can->id==3)
                                <tr>
                                    <td><?php echo $x++;?></td>
                                    @if($nivel!=1)   
                                        <td><a href="{!! route('farmacia.listar_distribucion', ['can_id'=>$can->id]) !!}"> CAN2020 - Ratificacion Julio ,2020</a></td>
                                    @else
                                        <td> CAN2020 - Ratificacion Julio ,2020</a></td>
                                    @endif
                                    <td>
                                        @if($nivel==1)
                                            
                                                @if ($can->extraordinario==1 )                                                        
                                                    @if ($can->medicamento_cerrado_rectificacion==1 )
                                                        @if ($diasDiferencia!=0 )
                                                                <a href="{!! route('farmacia.cargar_medicamentos_rectificacion', ['can_id'=>$can->id,'tipo'=>1]) !!}" class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                                <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                    @else
                                                            <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                @endif
                                            
                                            @if ($can->extraordinario==1 )                                                    
                                                @if ($can->dispositivo_cerrado_rectificacion==1)
                                                    @if ($diasDiferencia!=0 )
                                                                <a href="{!! route('farmacia.cargar_medicamentos_rectificacion', ['can_id'=>$can->id,'tipo'=>2]) !!}" class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                                <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @endif
                                                @else
                                                            <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a>
                                                @endif
                                            @endif
                                        @else
                                            
                                                @if ($can->extraordinario==1 )    
                                                    @if ($can->medicamento_cerrado_rectificacion==1 )
                                                        @if ($diasDiferencia!=0 )
                                                                <a href="{!! route('farmacia.cargar_medicamentos_rectificacion', ['can_id'=>$can->id,'tipo'=>1]) !!}" class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                                <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                     @else
                                                            <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                @endif
                                            
                                            @if ($can->extraordinario==1 )   
                                                @if ($can->dispositivo_cerrado_rectificacion==1)
                                                    @if ($diasDiferencia!=0 )
                                                            <a href="{!! route('farmacia.cargar_medicamentos_rectificacion', ['can_id'=>$can->id,'tipo'=>2]) !!}" class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                            <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                    @endif
                                                @else
                                                        <a href="#" disabled class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($nivel==1)
                                            <div class='btn-group'>
                                                
                                                    @if ($can->extraordinario==1 )
                                                        @if ($can->medicamento_cerrado_rectificacion==2 )
                                                                <a href="{!! route('farmacia.ver_rectificacion_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                                <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                    @endif
                                                
                                                @if ($can->extraordinario==1 )                                                     
                                                    @if ($can->dispositivo_cerrado_rectificacion==2)
                                                            <a href="{!! route('farmacia.ver_rectificacion_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                            <a href="#" disabled class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <div class='btn-group'>
                                                
                                                    @if ($can->extraordinario==1 )
                                                        @if ($can->medicamento_cerrado_rectificacion==2)
                                                                    <a href="{!! route('farmacia.ver_rectificacion_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                                <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                    @endif        
                                                
                                                @if ($can->extraordinario==1 )
                                                    @if ($can->dispositivo_cerrado_rectificacion==2)
                                                                <a href="{!! route('farmacia.ver_rectificacion_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                            <a href="#" disabled class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{!! route('farmacia_servicios.listar_observaciones_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-purple btn-md'><i class="fa fa-tripadvisor"></i></a>
                                    </td>       
                                    <td>
                                        @if ($can->extraordinario==1 )
                                                <a href="{!! route('users.index_responsable_rectificacion',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>
                                        @endif
                                    </td>     
                                    <td></td>
                                    @if($nivel==1)
                                        <td></td>
                                    @endif
                                    <td></td>    
                                    <td></td>                        
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><?php echo $x++;                                        
                                    ?></td>
                                    @if($nivel!=1)   
                                        <td><a href="{!! route('farmacia.listar_servicios', ['can_id'=>$can->id]) !!}"> {!! $can->nombre_can!!} - {!! $can->desc_mes!!}, {!! $can->ano!!}</a></td>
                                    @else
                                        <td> {!! $can->nombre_can!!} - {!! $can->desc_mes!!}, {!! $can->ano!!}</td>
                                    @endif
                                    @if ( Auth::user()->rol==7 )
                                        <td>
                                                                                      
                                            @if ($can->medicamento_cerrado==1 )
                                                    @if ($diasDiferencia!=0 )
                                                        <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>1]) !!}" class='btn bg-purple btn-md'><i class="fa fa-medkit"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                 @else
                                                    <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                                @endif
                                            
                                            @if ($can->dispositivo_cerrado==1)
                                                    @if ($diasDiferencia!=0 )
                                                        <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>2]) !!}" class='btn bg-purple btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                    @endif
                                            @else
                                                    <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a> 
                                            @endif
                                            
                                            
                                        </td>
                                        <td>
                                            <div class='btn-group'>
                                                
                                                    @if ($can->medicamento_cerrado==2)
                                                            <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-orange btn-md'><i class="fa fa-medkit"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-orange btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                
                                                @if ($can->dispositivo_cerrado==2)
                                                        <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                                @else
                                                    <a href="#" disabled class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{!! route('farmacia_servicios.listar_observaciones_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-purple btn-md'><i class="fa fa-tripadvisor"></i></a>
                                        </td>
                                        <td>
                                            @if($nivel!=1)
                                                <a href="{!! route('farmacia_servicios.listar_responsables_servicios',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>
                                            @else
                                                <a href="{!! route('users.index_responsable',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>
                                            @endif                                                                                   
                                        </td>
                                        @if($nivel==1)
                                        <td></td>
                                        @endif
                                        <td>
                                            
                                                @if ($can->stock==1 )
                                                    @if ($can->medicamento_cerrado_stock==1 )
                                                        @if ($diasDiferencia!=0 )
                                                            <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>1]) !!}" class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                            <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                     @else
                                                        <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                
                                                @endif
                                            
                                            @if ($can->stock==1 )
                                                @if ($can->dispositivo_cerrado_stock==1)
                                                    @if ($diasDiferencia!=0 )
                                                        <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>2]) !!}" class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                    @endif
                                                @else
                                                    <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                @endif
                                            
                                            @endif
                                        </td>

                                        <td>
                                            <div class='btn-group'>
                                                
                                                    @if ($can->stock==1 )
                                                        @if ($can->medicamento_cerrado_stock==2)
                                                                <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                            <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                    
                                                    @endif
                                                
                                                @if ($can->stock==1 )
                                                    @if ($can->dispositivo_cerrado_stock==2)
                                                            <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @endif
                                                
                                                @endif
                                            </div>
                                        </td>     
                                        <td>
                                            @if ($can->stock==1 )
                                                <a href="{!! route('users.index_responsable_stock',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>                                                                                    
                                            @endif
                                        </td>                           
                                        <td>
                                            @if($can->id==$can_id)
                                                @if ($can->medicamento_cerrado!=1 and $can->dispositivo_cerrado!=1)
                                                    <a href="{!! route('farmacia_servicios.listar_archivos_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-upload"></i></a>
                                                @else
                                                    <a href="#" disabled class='btn btn-primary btn-md'><i class="fa fa-upload"></i></a>
                                                @endif
                                            @else
                                                <a href="{!! route('farmacia_servicios.listar_archivos_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-eye"></i></a>
                                            @endif
                                            
                                        </td>
                                    @endif
                                </tr>
                                
                            @else
                                <tr>
                                    <td><?php echo $x++;?></td>
                                    @if($nivel!=1)   
                                        <td><a href="{!! route('farmacia.listar_servicios', ['can_id'=>$can->id]) !!}"> {!! $can->nombre_can!!} - {!! $can->desc_mes!!}, {!! $can->ano!!}</a></td>
                                    @else

                                        <td> {!! $can->nombre_can!!} - {!! $can->desc_mes!!}, {!! $can->ano!!} <?php if($can->id==$can_id): ?> <small class="label label-danger"> nuevo </small> <?php endif; ?></td>
                                    @endif
                                    @if ( Auth::user()->rol==7 )
                                        <td>
                                            
                                                @if ($can->medicamento_cerrado==1 )
                                                    @if ($diasDiferencia!=0 )
                                                        <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>1]) !!}" class='btn bg-purple btn-md'><i class="fa fa-medkit"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                @else
                                                    <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-medkit"></i></a>
                                                @endif
                                            
                                                @if ($can->dispositivo_cerrado==1)
                                                    @if ($diasDiferencia!=0 )
                                                        <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>2]) !!}" class='btn bg-purple btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                    @endif
                                                @else
                                                    <a href="#" disabled class='btn bg-red btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                @endif

                                        </td>
                                        
                                        <td>
                                            <div class='btn-group'>
                                                
                                                    @if ($can->medicamento_cerrado==2)
                                                            <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-orange btn-md'><i class="fa fa-medkit"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-orange btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                
                                                @if ($can->dispositivo_cerrado==2)
                                                        <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                                @else
                                                    <a href="#" disabled class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{!! route('farmacia_servicios.listar_observaciones_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-purple btn-md'><i class="fa fa-tripadvisor"></i></a>
                                        </td>
                                        <td>
                                            @if($nivel!=1)
                                                <a href="{!! route('farmacia_servicios.listar_responsables_servicios',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>
                                            @else
                                                <a href="{!! route('users.index_responsable',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>
                                            @endif                                                                                      
                                        </td>
                                        @if($nivel==1)
                                        <td> 
                                                @if($can->id>6 and $can->medicamento_cerrado==1 and $can->dispositivo_cerrado==1)
                                                    <a href="{!! route('farmacia.atencion_consultorios',['can_id'=>$can->id]) !!}" class='btn bg-orange btn-md'> <i class="fa fa-fw fa-edit"></i></a>
                                                @else
                                                    @if($can->id>6 )
                                                        <a href="#" disabled class='btn bg-orange btn-md'><i class="fa fa-edit"></i></a>
                                                    @endif
                                                @endif
                                            
                                        <td>
                                        @endif
                                            
                                                @if ($can->stock==1 )
                                                    @if ($can->medicamento_cerrado_stock==1 )
                                                        @if ($diasDiferencia!=0 )
                                                            <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>1]) !!}" class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                            <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                     @else
                                                        <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                @endif
                                            
                                            @if ($can->stock==1 )
                                                @if ($can->dispositivo_cerrado_stock==1)
                                                    @if ($diasDiferencia!=0 )
                                                        <a href="{!! route('farmacia.cargar_medicamentos', ['can_id'=>$can->id,'tipo'=>2]) !!}" class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                    @endif
                                                @else
                                                    <a href="#" disabled class='btn btn-info btn-md'><i class="fa fa-stethoscope"></i></a> 
                                                @endif
                                            
                                            @endif
                                        </td>
                                        <td>
                                            <div class='btn-group'>
                                                
                                                    @if ($can->stock==1 )
                                                        @if ($can->medicamento_cerrado_stock==2)
                                                                <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                            <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                    
                                                    @endif
                                                
                                                @if ($can->stock==1 )
                                                    @if ($can->dispositivo_cerrado_stock==2)
                                                            <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    @endif
                                                
                                                @endif
                                            </div>
                                        </td>     
                                        <td>
                                            @if ($can->stock==1 )
                                                <a href="{!! route('users.index_responsable_stock',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>                                                                                    
                                            @endif
                                        </td>                           
                                        <td>
                                            @if($can->id==$can_id)
                                                @if ($can->medicamento_cerrado!=1 and $can->dispositivo_cerrado!=1)
                                                    <a href="{!! route('farmacia_servicios.listar_archivos_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-upload"></i></a>
                                                @else
                                                    <a href="#" disabled class='btn btn-primary btn-md'><i class="fa fa-upload"></i></a>
                                                @endif
                                            @else
                                                <a href="{!! route('farmacia_servicios.listar_archivos_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-eye"></i></a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>

                            @endif
                            
                        @endforeach
                    </tbody>
                     
                </table>
            </div>            
        </div>        
    </div>    
</div>
