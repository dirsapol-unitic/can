@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Medicamento/Dispositivo Médico
        </h4>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.petitorios.show_fields')
                    <a href="{!! route('petitorios.index',[$id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i>Regresar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
