@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Departamentos
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'unidads.store']) !!}

                        @include('admin.unidads.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
