@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Servicio
        </h4>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($servicio, ['route' => ['servicios.update', $servicio->id], 'method' => 'patch']) !!}

                        @include('admin.servicios.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection