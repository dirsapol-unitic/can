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
                                    <td>#</td>           
                                    <td>JEFE DE LA IPRESS</td>    
                                    <td>Grado</td>  
                                    <td>Editar</td> 
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.-</td>
                                    <?php if (is_object($jefe_ipress)): ?>
                                        <td>{!! $jefe_ipress->name !!}</td>
                                        <td>{!! $jefe_ipress->grado !!}</td>
                                        <td><a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable_servicio', [$jefe_ipress->id,'servicio_id'=>0]) !!}" class='btn btn-primary btn-xs'>{!! $jefe_ipress->dni !!}<br/></a></td>
                                    <?php else: ?>    
                                        <td></td>
                                        <td></td>
                                        <td><a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable_servicios',[$can_id,'servicio_id'=>0,'establecimiento_id'=>$establecimiento_id]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                                        </td>
                                    <?php endif; ?>
                                        
                                    
                                </tr>
                            </tbody>
                        </table>

                        <table id="example" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @if($can_activo==1)
                                        <th>DNI</th>
                                        <th>Correo</th>
                                    @endif
                                    <th>Responsable del Llenado del CAN</th>
                                    <th>Grado</th>                                    
                                    <th>Celular</th>
                                    <th>Servicio</th> 
                                    @if($can_activo==1)
                                        <th>Nuevo</th> 
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($servicios as $key => $servicio)
                                <tr>                                    
                                    <td>{{$key+1}}</td>
                                    @if($can_activo==1)
                                        <td>
                                            @foreach($responsables as $key => $responsable)
                                                @if ($responsable->servicio_id == $servicio->servicio_id)
                                                    

                                            <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit_responsable_servicio', [$responsable->id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn btn-primary btn-xs'>{!! $responsable->dni !!}<br/></a>
                                                @endif
                                            @endforeach    
                                        </td>
                                        <td>
                                            @foreach($responsables as $key => $responsable)
                                                @if ($responsable->servicio_id == $servicio->servicio_id)
                                                    {!! $responsable->email !!}<br/>
                                                @endif
                                            @endforeach    
                                        </td>
                                    @endif
                                    <td>
                                        @foreach($responsables as $key => $responsable)
                                            @if ($responsable->servicio_id == $servicio->servicio_id)
                                                @if($can_activo==1)
                                                    {!! $responsable->name !!}<br/>
                                                @else
                                                    {!! $responsable->nombre !!}<br/>
                                                @endif
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
                                    <td> {!! $servicio->nombre_servicio !!} </td>                                    
                                    <td>@if($can_activo==1)
                                            <a data-toggle="tooltip" title="Nuevo Usuario!" href="{!! route('users.create_responsable_servicios',[$can_id,'servicio_id'=>$servicio->servicio_id,'establecimiento_id'=>$establecimiento_id]) !!}" class='btn btn-success btn-xs'><i class="glyphicon glyphicon-file"></i></a>
                                        @endif
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
