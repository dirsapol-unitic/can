@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Division
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'divisions.store']) !!}

                        @include('admin.divisions.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
