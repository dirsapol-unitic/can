@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Division
        </h4>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.divisions.show_fields')
                    <a href="{!! route('divisions.listar_division') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
