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
        
        <!--div class="col-md-6 col-sm-6 col-xs-12"-->
          <!--div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-cloud-download-outline"></i></span-->

            <!--div class="info-box-content">
                <span class="info-box-text">Descargar Consolidado</span>
              
                <a target="_blank" data-toggle="tooltip" title="Medicamentos!" href="{{ route('farmacia_servicios.pdf_final_estimacion_nivel2y3',[$can_id,$establecimiento_id,1]) }}" class='btn bg-red btn-md margin'><i class="fa fa-medkit"></i></a>
                <a target="_blank" data-toggle="tooltip" title="Material Biomedico!" href="{{ route('farmacia_servicios.pdf_final_estimacion_nivel3',[$can_id,$establecimiento_id,2]) }}" class='btn bg-maroon btn-md margin'><i class="fa fa-fire-extinguisher"></i></a>
                <a target="_blank" data-toggle="tooltip" title="Instrumental Quirurgico!" href="{{ route('farmacia_servicios.pdf_final_estimacion_nivel3',[$can_id,$establecimiento_id,3]) }}" class='btn bg-primary btn-md margin'><i class="fa fa-bed"></i></a> 
                <a target="_blank" data-toggle="tooltip" title="Material e Insumos Odontológico!" href="{{ route('farmacia_servicios.pdf_final_estimacion_nivel2y3',[$can_id,$establecimiento_id,4]) }}"class='btn bg-aqua btn-md margin'><i class="fa fa-wrench"></i></a> 
                <a target="_blank" data-toggle="tooltip" title="Material e Insumo de Laboratorio!" href="{{ route('farmacia_servicios.pdf_final_estimacion_nivel2y3',[$can_id,$establecimiento_id,5]) }}" class='btn bg-olive btn-md margin'><i class="fa fa-hourglass-start"></i></a>            
                <a target="_blank" data-toggle="tooltip" title="Material Fotográfico y fonotécnico!" href="{{ route('farmacia_servicios.pdf_final_estimacion_nivel2y3',[$can_id,$establecimiento_id,6]) }}" class='btn bg-navy btn-md margin'><i class="fa fa-odnoklassniki-square"></i></a>            
                <a target="_blank" data-toggle="tooltip" title="Productos Afines!" href="{{ route('farmacia_servicios.pdf_final_estimacion_nivel3',[$can_id,$establecimiento_id,7]) }}" class='btn bg-orange btn-md margin'><i class="fa fa-hand-lizard-o"></i></a>
            </div-->
            <!-- /.info-box-content -->
          <!--/div-->
          <!-- /.info-box -->
        <!--/div-->
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="ion ion-ios-eye-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Ver Servicios por Tipo</span>
                <a target="_blank" data-toggle="tooltip" title="Medicamentos!" href="{!! route('farmacia_servicios.productos_servicio_tipo_mof',[$can_id,1]) !!}" class='btn bg-red btn-md margin'><i class="fa fa-medkit"></i></a>
                <a target="_blank" data-toggle="tooltip" title="Material Biomedico!" href="{!! route('farmacia_servicios.productos_servicio_tipo_mof',[$can_id,2]) !!}" class='btn bg-maroon btn-md margin'><i class="fa fa-fire-extinguisher"></i></a>
                <a target="_blank" data-toggle="tooltip" title="Instrumental Quirurgico!" href="{!! route('farmacia_servicios.productos_servicio_tipo_mof',[$can_id,3]) !!}" class='btn bg-primary btn-md margin'><i class="fa fa-bed"></i></a>
                <a target="_blank" data-toggle="tooltip" title="Material e Insumos Odontológico!" href="{!! route('farmacia_servicios.productos_servicio_tipo_mof',[$can_id,4]) !!}" class='btn bg-aqua btn-md margin'><i class="fa fa-wrench"></i></a>
                
                <a target="_blank" data-toggle="tooltip" title="Material e Insumo de Laboratorio!" href="{!! route('farmacia_servicios.productos_servicio_tipo_mof',[$can_id,5]) !!}" class='btn bg-olive btn-md margin'><i class="fa fa-hourglass-start"></i></a>            
                <a target="_blank" data-toggle="tooltip" title="Material Fotográfico y fonotécnico!" href="{!! route('farmacia_servicios.productos_servicio_tipo_mof',[$can_id,6]) !!}" class='btn bg-navy btn-md margin'><i class="fa fa-odnoklassniki-square"></i></a>            
                <a target="_blank" data-toggle="tooltip" title="Productos Afines!" href="{!! route('farmacia_servicios.productos_servicio_tipo_mof',[$can_id,7]) !!}" class='btn bg-orange btn-md margin'><i class="fa fa-hand-lizard-o"></i></a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        
    </div>
<div class="row">
    <br/><br/>
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
                        
                            <th>Medicamentos</th>
                            <th>Dispositivos</th>
                            <th>Activar/Desactivar </th>
                            <th>Ver/Descargar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($servicios as $key => $servicio)
                        <tr>
                            <?php
                            if (Auth::user()->rol==3):
                                $comprueba = $servicio->medicamento_cerrado;
                            else:
                                $comprueba = $servicio->dispositivo_cerrado;
                            endif;
                            ?>

                            @if ($comprueba!=3)                      

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
                                {!! $servicio->nombre_servicio !!}
                                
                                </td>                            
                                <td>
                                    @if ($servicio->medicamento_cerrado ==3)
                                        <small class="label label-default">N/A</small>
                                    @else
                                       @if ($servicio->medicamento_cerrado ==2)
                                            <small class="label label-danger">Cerrado</small>
                                        @else
                                            <small class="label label-success">Abierto</small>
                                        @endif        
                                    @endif                             
                                </td>
                                <td>                            
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
                                <td>
                                    <a href="{!! route('farmacia_servicios.activar_rubro',['can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn bg-navy btn'><i class="fa fa-fw fa-chain"></i></a>
                                </td>
                                <td>
                                    <div class='btn-group'>
                                        <a href="{!! route('farmacia_servicios.descargar_estimacion_farmacia_servicios_ver',['tipo'=>1,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn bg-blue btn'><i class="fa fa-medkit"></i></a>
                                        <a href="{!! route('farmacia_servicios.descargar_estimacion_farmacia_servicios_ver',['tipo'=>2,'can_id'=>$can_id,'establecimiento_id'=>$establecimiento_id,'servicio_id'=>$servicio->servicio_id]) !!}" class='btn bg-maroon btn'><i class="fa fa-stethoscope"></i></a>
                                    </div>
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
</div>
</section>    
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
