@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Provincia
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($provincia, ['route' => ['provincias.update', $provincia->id], 'method' => 'patch']) !!}

                        @include('admin.provincias.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection