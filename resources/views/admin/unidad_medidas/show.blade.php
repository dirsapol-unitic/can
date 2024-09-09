@extends('layouts.app')


@section('content')
    <section class="content-header">
        <h1>
            Unidad de Medida
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.unidad_medidas.show_fields')
                    <a href="{!! route('unidadMedidas.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
