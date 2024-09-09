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
                            <th>Descripcion</th>
                            @if (Auth::user()->rol==7 )
                                <th>
                                    @if($establecimiento_id!=79)
                                        Med./
                                    @endif
                                    Disp.
                                </th>                                
                            @endif
                            <th>Observ.</th>
                            <th>Descargar</th>
                            <th>Responsables</th>
                            @if (Auth::user()->rol==7 )
                                <th>
                                    Subir Stocks
                                </th>                                
                            @endif
                            
                            <th>Descargar Stock</th>   
                            <th>Responsables Stocks</th>                         
                            @if (Auth::user()->rol==7 )                                
                                @if($nivel!=1)                                
                                    <th>
                                        Actualizar Stock
                                    </th>
                                @else
                                    <th>Rectificar o Ratificar </th>
                                @endif    
                            @endif                    
                            <th>Descargar</th>
                            <th>Responsables Extraordinario</th>
                            <th>Subir PDF CAN</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cans as $can)
                                <tr>
                                    @if($nivel!=1)   
                                        <td><a href="#"> {!! $can->nombre_can!!} - {!! $can->desc_mes!!}, {!! $can->ano!!}</a></td>
                                    @else
                                        <td> {!! $can->nombre_can!!} - {!! $can->desc_mes!!}, {!! $can->ano!!}</td>
                                    @endif
                                    @if ( Auth::user()->rol==7 )
                                        @if($establecimiento_id!=79)
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
                                        @endif
                                        <td>
                                            <a href="{!! route('farmacia_servicios.listar_observaciones_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-purple btn-md'><i class="fa fa-tripadvisor"></i></a>
                                        </td>
                                        <td>
                                            <div class='btn-group'>
                                                @if($establecimiento_id!=79)
                                                    @if ($can->medicamento_cerrado==2)
                                                            <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-orange btn-md'><i class="fa fa-medkit"></i></a>
                                                    @else
                                                        <a href="#" disabled class='btn bg-orange btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                @endif
                                                @if ($can->dispositivo_cerrado==2)
                                                        <a href="{!! route('farmacia.ver_consolidado_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                                @else
                                                    <a href="#" disabled class='btn bg-olive btn-md'><i class="fa fa-stethoscope"></i></a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{!! route('users.index_responsable',['can_id'=>$can->id]) !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>                                                                                    
                                        </td>
                                        <td>
                                            @if($establecimiento_id!=79)
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
                                                @if($establecimiento_id!=79)
                                                    @if ($can->stock==1 )
                                                        @if ($can->medicamento_cerrado_stock==2)
                                                                <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @else
                                                            <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                        @endif
                                                    
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
                                            @if($nivel==1)
                                                @if($establecimiento_id!=79)
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
                                                @if($can->establecimiento_id!=79)
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
                                                    @if($establecimiento_id!=79)
                                                        @if ($can->extraordinario==1 )
                                                            @if ($can->medicamento_cerrado_rectificacion==2 )
                                                                <a href="{!! route('farmacia.ver_rectificacion_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                            @else
                                                                <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                            @endif
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
                                                    @if($establecimiento_id!=79)
                                                        @if ($can->extraordinario==1 )
                                                            @if ($can->medicamento_cerrado_rectificacion==2)
                                                                    <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>1,'can_id'=>$can->id]) !!}" class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                            @else
                                                                <a href="#" disabled class='btn bg-yellow btn-md'><i class="fa fa-medkit"></i></a>
                                                            @endif
                                                        @endif        
                                                    @endif
                                                    @if ($can->extraordinario==1 )
                                                        @if ($can->dispositivo_cerrado_rectificacion==2)
                                                                <a href="{!! route('farmacia.ver_stock_farmacia',['tipo'=>2,'can_id'=>$can->id]) !!}" class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                        @else
                                                            <a href="#" disabled class='btn btn-success btn-md'><i class="fa fa-stethoscope"></i></a>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endif
                                        </td>       
                                        <td>
                                            @if ($can->extraordinario==1 )
                                                <a href="{!! route('users.index_responsable_rectificacion') !!}" class='btn bg-black btn-md'> <i class="fa fa-fw fa-user"></i></a>
                                            @endif
                                        </td>                             
                                        <td>
                                            <a href="{!! route('farmacia_servicios.listar_archivos_nivel1',['can_id'=>$can->id]) !!}" class='btn bg-blue btn-md'><i class="fa fa-upload"></i></a>
                                            
                                        </td>

                                    @endif

                                </tr>
                            
                        @endforeach
                    </tbody>                     
                </table>
            </div>            
        </div>        
    </div>    
</div>
