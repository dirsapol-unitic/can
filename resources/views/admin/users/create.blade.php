@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Usuarios
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['id'=>'frm_usuario','name'=>'frm_usuario','route' => 'users.store']) !!}


                        @include('admin.users.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
