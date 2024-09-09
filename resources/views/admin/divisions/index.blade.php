@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4 class="pull-left">DIVISIÓN</h4>
        <div class="box-header">
           <a title="Nueva División!" class="btn btn-app pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('divisions.create') !!}"><i class="glyphicon glyphicon-file"></i> Nuevo</a>
        </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.divisions.table')
            </div>
        </div>
    </div>
@endsection

