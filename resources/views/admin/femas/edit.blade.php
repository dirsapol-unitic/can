@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fema
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fema, ['route' => ['femas.update', $fema->id], 'method' => 'patch']) !!}

                        @include('femas.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection