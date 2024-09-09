@extends('layouts.app')

@section('content')
    <section class="content-header">
        <br/>
        <h1 class="pull-left">{!!$nombre_establecimiento!!}</h1>
        <br/><br/><br/>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
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
<section class="content">
    <div class="row">  
        <div class="row">
            <div class="col-xs-12">
                <div>
                    <div class="box-header">
                        <h3 class="box-title">Listado</h3>
                    </div>
                    <div class="box-body">
                        <table id="example" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Responsable del Llenado del CAN</th>
                                    <th>Grado</th>
                                    <th>Celular</th>
                                    <th>Servicio</th>
                                @if (Auth::user()->rol==3)
                                    <th>Medicamentos</th>
                                    @if ($cans->extraordinario==1)
                                        <th>Medi. Rect/Rat</th>
                                    @endif
                                @else
                                    <th>Dispositivos</th>
                                    @if ($cans->extraordinario==1)
                                        <th>Disp. Rect/Rat</th>
                                    @endif
                                @endif
                                    <th>Descargar</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($servicios as $key => $servicio)
                                <tr>
                                        <td>{{$key+1}}</td>                            
                                        <td>
                                            @foreach($responsables as $key => $responsable)
                                                @if ($responsable->servicio_id == $servicio->servicio_id)
                                                    {!! $responsable->nombre !!}<br/>
                                                @endif
                                            @endforeach    
                                        </td>
                                        <td>
                                            @foreach($responsables as $key => $responsable)
                                                @if ($responsable->servicio_id == $servicio->servicio_id)
                                                    {!! $responsable->grado !!}<br/>
                                                @endif
                                            @endforeach    
                                        </td>
                                        <td>
                                            @foreach($responsables as $key => $responsable)
                                                @if ($responsable->servicio_id == $servicio->servicio_id)
                                                    {!! $responsable->telefono !!}<br/>
                                                @endif
                                            @endforeach    
                                        </td>                            
                                        <td>
                                            @if ($diasDiferencia!=0 )   
                                                @if (Auth::user()->rol==3)  
                                                    @if ($servicio->medicamento_cerrado ==3)
                                                            {!! $servicio->nombre_servicio !!}
                                                    @else
                                                        @if ($servicio->medicamento_cerrado ==2 )
                                                            @if ($cans->rubro_pf==1)
                                                                <a href="{!! route('farmacia_servicios.activar_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,1]) !!}">{!! $servicio->nombre_servicio !!}</a>
                                                            @else
                                                                {!! $servicio->nombre_servicio !!}
                                                            @endif
                                                        @else
                                                            {!! $servicio->nombre_servicio !!}
                                                        @endif  
                                                    @endif                                    
                                                @else                                            
                                                    @if ($servicio->dispositivo_cerrado ==3)
                                                        {!! $servicio->nombre_servicio !!}
                                                    @else
                                                        @if ($servicio->dispositivo_cerrado ==2 )
                                                            @if (Auth::user()->rol==4)  
                                                                @if ($cans->rubro_mb_iq_pa==1)
                                                                    <a href="{!! route('farmacia_servicios.activar_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,2]) !!}">{!! $servicio->nombre_servicio !!}</a>              
                                                                @else
                                                                    {!! $servicio->nombre_servicio !!}                  
                                                                @endif
                                                            @else
                                                                @if (Auth::user()->rol==5)
                                                                    @if ($cans->rubro_mid==1)
                                                                        <a href="{!! route('farmacia_servicios.activar_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,2]) !!}">{!! $servicio->nombre_servicio !!}</a>
                                                                    @else
                                                                        {!! $servicio->nombre_servicio !!}                  
                                                                    @endif
                                                                @else
                                                                    @if (Auth::user()->rol==6)
                                                                        @if ($cans->rubro_mil==1)
                                                                            <a href="{!! route('farmacia_servicios.activar_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,2]) !!}">{!! $servicio->nombre_servicio !!}</a>
                                                                        @else
                                                                            {!! $servicio->nombre_servicio !!}                  
                                                                        @endif
                                                                    @else
                                                                        @if (Auth::user()->rol==8)
                                                                            @if ($cans->rubro_mff==1)
                                                                                <a href="{!! route('farmacia_servicios.activar_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,2]) !!}">{!! $servicio->nombre_servicio !!}</a>
                                                                            @else
                                                                                {!! $servicio->nombre_servicio !!}                  
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @else
                                                            {!! $servicio->nombre_servicio !!}
                                                        @endif       
                                                    @endif                                            
                                                @endif
                                            @else
                                                {!! $servicio->nombre_servicio !!}
                                            @endif       
                                        
                                        </td>                            
                                        <td>
                                        @if (Auth::user()->rol==3)                                      
                                            @if ($servicio->medicamento_cerrado ==3)
                                                <small class="label label-default">N/A</small>
                                            @else
                                               @if ($servicio->medicamento_cerrado ==2)
                                                    <small class="label label-danger">Cerrado</small>
                                                @else
                                                    <small class="label label-success">Abierto</small>
                                                @endif        
                                            @endif    
                                        @else
                                        
                                            @if ($servicio->dispositivo_cerrado ==3)
                                                    <small class="label label-default">N/A</small>
                                            @else
                                                @if ($servicio->dispositivo_cerrado ==2)
                                                    <small class="label label-danger">Cerrado</small>
                                                @else
                                                    <small class="label label-success">Abierto</small>
                                                @endif
                                            @endif                                   
                                        </td>
                                        @endif
                                        @if ($cans->extraordinario==1)
                                            <td>
                                            @if (Auth::user()->rol==3)                                      
                                                @if ($servicio->medicamento_cerrado_rectificacion ==3)
                                                    <small class="label label-default">N/A</small>
                                                @else
                                                   @if ($servicio->medicamento_cerrado_rectificacion ==2)
                                                        <small class="label label-danger">Cerrado</small>
                                                    @else
                                                        <small class="label label-success">Abierto</small>
                                                    @endif        
                                                @endif                               
                                            
                                            @else      
                                                @if ($servicio->dispositivo_cerrado_rectificacion ==3)
                                                        <small class="label label-default">N/A</small>
                                                @else
                                                    @if ($servicio->dispositivo_cerrado_rectificacion ==2)
                                                        <small class="label label-danger">Cerrado</small>
                                                    @else
                                                        <small class="label label-success">Abierto</small>
                                                    @endif
                                                @endif
                                            @endif
                                            </td>
                                        @endif
                                        <td>
                                            <div class='btn-group'>
                                                @foreach($responsables as $key => $responsable)
                                                    @if ($responsable->servicio_id == $servicio->servicio_id)
                                                        <?php $id_user= $responsable->id?>
                                                    @endif
                                                @endforeach    
                                            @if (Auth::user()->rol==3)
                                                @if ($servicio->medicamento_cerrado ==2)
                                                    <a href="{!! route('farmacia_servicios.descargar_estimacion_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,'id_user'=>$id_user]) !!}" class='btn bg-blue btn'><i class="fa fa-medkit"></i></a>
                                                @else
                                                    @if ($servicio->medicamento_cerrado ==1)
                                                        <a disabled='disabled' href="#" class='btn bg-blue btn-md'><i class="fa fa-medkit"></i></a>
                                                    @endif
                                                @endif    
                                            @else
                                                @if ($servicio->dispositivo_cerrado ==2)
                                                    <a href="{!! route('farmacia_servicios.descargar_estimacion_farmacia_servicios',['tipo'=>$tipo_servicio_id,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id,'id_user'=>$id_user]) !!}" class='btn bg-maroon btn'><i class="fa fa-stethoscope"></i></a>
                                                @else
                                                    @if ($servicio->dispositivo_cerrado ==1)
                                                        <a disabled='disabled' href="#" class='btn bg-maroon btn-md'><i class="fa fa-stethoscope"></i></a>
                                                    
                                                    @endif
                                                @endif  
                                            @endif  
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
    </div>
</div>
@endsection
