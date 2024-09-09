@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Editar la Programación del CAN
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($estimacion, ['route' => ['estimacion_servicio.update_estimacion', $estimacion->id], 'method' => 'patch']) !!}

                        @include('site.estimacion_servicio.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection
@section('scripts')
    <script>
        $('document').ready(function(){
           $("#checkTodos").change(function () {
              $("input:checkbox").prop('checked', $(this).prop("checked"));
          });
        });
</script>
<script type="text/javascript">
$('#example1').dataTable( {
  "pageLength": 100
} );
</script>

@stop
