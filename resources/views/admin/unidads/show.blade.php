@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Departamentos
        </h4>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('unidads.show_fields')
                    <a href="{!! route('unidads.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
