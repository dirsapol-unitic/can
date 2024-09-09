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
                                @include('site.manual.ingresar_salir')                                
                            @endif
                            @if($id==2)
                                @include('site.manual.entorno_trabajo')                                
                            @endif    
                            @if($id==3)
                                @include('site.manual.asignar_producto')                                
                            @endif    
                            @if($id==4)
                                @include('site.manual.nuevo_producto')                                
                            @endif        
                            @if($id==5)
                                @include('site.manual.descargar_producto') 
                            @endif 
                            @if($id==6)
                                @include('site.manual.cambiar_perfil')                             
                            @endif        
                            @if($id==7)
                                @include('site.manual.petitorio_descarga')
                            @endif        
                            @if($id==8)
                                @include('site.manual.subir_archivo')
                            @endif        
                            @if($id==9)
                                @include('site.manual.entorno_trabajo_servicio')
                            @endif        
                            @if($id==10)
                                @include('site.manual.asignar_producto_servicio')
                            @endif
                            @if($id==11)
                                @include('site.manual.nuevo_producto_servicio')
                            @endif
                            @if($id==12)
                                @include('site.manual.entorno_trabajo_nivel_2')                                
                            @endif
                            @if($id==13)
                                @include('site.manual.activar_servicio_nivel_2')
                            @endif
                            @if($id==14)
                                @include('site.manual.visualizar_producto_nivel_2')
                            @endif
                            @if($id==15)
                                @include('site.manual.registrar_producto_nivel_2')
                            @endif
                            @if($id==16)
                                @include('site.manual.manual_rubro')
                            @endif
                            @if($id==17)
                                @include('site.manual.manual_farmacia_i')
                            @endif
                            @if($id==18)
                                @include('site.manual.manual_farmacia_ii')
                            @endif
                            @if($id==19)
                                @include('site.manual.manual_administrador')
                            @endif
                            @if($id==20)
                                @include('site.manual.soporte_consultas')
                            @endif
                            @if($id==21)
                                @include('site.manual.ingresar_responsables')
                            @endif
                        </div>
                    </div>
                </div>        
                    
            </div>
        </div>
        <div class="text-center"></div>
    </div>  
@endsection