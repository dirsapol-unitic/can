@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Restricciones
        </h4>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.restricions.show_fields')
                    <a href="{!! route('restricions.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
