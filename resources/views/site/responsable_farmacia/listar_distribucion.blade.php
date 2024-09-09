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
                @include('site.responsable_farmacia.table')     
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection

