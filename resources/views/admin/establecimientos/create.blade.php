@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Establecimientos
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['id'=>'frm_establecimiento','name'=>'frm_establecimiento','route' => 'establecimientos.store']) !!}

                        @include('admin.establecimientos.fields')

                    {!! Form::close() !!}
                    
                </div>
            </div>
        </div>
    </div>
@endsection

