@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Rubros</h1>
        <div class="box-header">
           <a data-toggle="tooltip" title="Nuevo Rubro!" class="btn btn-app pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('rubros.create') !!}"><i class="glyphicon glyphicon-file"></i> Nuevo</a>
        </div>
        
    </section>
    <div class="content">
        <div class="clearfix"></div>


        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.rubros.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection

