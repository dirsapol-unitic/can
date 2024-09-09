@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">{!!$nombre_establecimiento!!}</h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <br/>
        <a href="{!! route('cans.show',[$can_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a><br/><br/>
        <div class="clearfix"></div>
        @if($nivel_id==1)
            @include('admin.cans.table_archivo')
        @else
            @include('admin.cans.table_archivo_lista_rubro')
        @endif
        <div>
        <a href="{!! route('cans.show',[$can_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
        </div>
    </div>
@endsection

