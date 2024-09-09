@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3 class="pull-left">Cuadro Anual de Necesidades - CAN </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <br/><br/>
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('site.responsable_red.mostrar_can')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection