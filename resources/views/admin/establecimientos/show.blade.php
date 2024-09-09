@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Establecimientos
        </h4>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    <a href="{!! route('establecimientos.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a><br/><br/>
                        @include('admin.establecimientos.show_fields')
                    <a href="{!! route('establecimientos.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                </div>
            </div>
        </div>
    </div>
    
@endsection
