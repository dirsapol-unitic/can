@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3 class="pull-left">Configurar CAN-UGPFDMPS</h3>
        <div class="box-header">
           <a class="btn btn-app pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('cans.create') !!}"><i class="glyphicon glyphicon-file"></i>Nuevo </a>
        </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')        
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.cans.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection


