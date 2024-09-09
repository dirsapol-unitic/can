@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h3 class="pull-left">Manual  </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div>
                            @if($id==1)
                                @include('site.estimacion_servicio.ingresar_salir')                                
                            @endif
                            @if($id==2)
                                @include('site.estimacion_servicio.listar_estimacion')                                
                            @endif    
                            @if($id==3)
                                @include('site.estimacion_servicio.atenciones')                                
                            @endif    
                            @if($id==4)
                                @include('site.estimacion_servicio.nuevo_producto')                                
                            @endif        
                            @if($id==5)
                                @include('site.estimacion_servicio.editar_producto')                                                                
                            @endif        
                            @if($id==6)
                                @include('site.estimacion_servicio.buscar_producto')                                
                            @endif        
                        </div>
                    </div>
                </div>        
                    
            </div>
        </div>
        <div class="text-center"></div>
    </div>  
@endsection