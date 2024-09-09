@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">{!!$descripcion!!} - {!!$nombre_establecimiento!!}</h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <br/>
        <a href="{!! route('cans.mostrar_servicios',[$can_id,$establecimiento_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
        <br/><br/>
        <div class="clearfix"></div>
        
                @include('admin.cans.table_archivo_rubro')
        
        <div>
        <a href="{!! route('cans.mostrar_servicios',[$can_id,$establecimiento_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
        </div>
    </div>
@endsection

