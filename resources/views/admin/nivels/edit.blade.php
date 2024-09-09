@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Nivel
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($nivel, ['route' => ['nivels.update', $nivel->id], 'method' => 'patch']) !!}

                        @include('admin.nivels.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection