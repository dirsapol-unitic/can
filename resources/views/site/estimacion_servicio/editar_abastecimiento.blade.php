@extends('layouts.app')
<!-- bootstrap datepicker -->
@section('css')
  <link rel="stylesheet" href='{{ asset ("/css/bootstrap-datepicker.min.css") }}'>  
@stop  
@section('content')
    <section class="content-header">
        <h1>
            Abastecimiento
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($estimacion, ['route' => ['estimacion_servicio.update_estimacion', $estimacion->id], 'method' => 'patch']) !!}

                        @include('site.estimacion_servicio.fields_estimacion')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection
@section('scripts')
  <script src='{{ asset ("/js/bootstrap-datepicker.min.js") }}'></script>  
@stop