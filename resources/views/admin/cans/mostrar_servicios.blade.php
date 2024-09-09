@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h1>
            Listado de Rubros
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.cans.rubros')
                </div>
            </div>
        </div>
        <section class="content-header">
        <h1>
            Listado de Servicios
            <br/>
        </h1>
        </section>
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.cans.mostrar_servicios_fields')
                    <a href="{!! route('cans.show',[$can->id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                </div>
            </div>
        </div>
    </div>
@endsection