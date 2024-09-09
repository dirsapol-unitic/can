@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Responsables
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['id'=>'frm_usuario','name'=>'frm_usuario','route' => 'users.store_responsable']) !!}
                        @include('site.users.fields_responsable')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
