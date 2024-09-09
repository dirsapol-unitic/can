@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4 class="pull-left">Medicamentos / Dispositivo Médico</h4>
        <h4 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('petitorios.create') !!}">Nuevo</a>
        </h4>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box">
            <div class="box-body">
                    @include('admin.petitorios.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection

