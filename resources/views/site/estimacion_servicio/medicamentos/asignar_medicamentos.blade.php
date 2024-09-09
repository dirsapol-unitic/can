@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Elegir Productos - {!!$nombre_servicio!!}
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::model($estimacions, ['route' => ['estimacion_servicio.guardar_medicamentos_asignados', $establecimiento_id,$can_id,$tipo], 'method' => 'patch']) !!}
                        @include('site.estimacion_servicio.medicamentos.fields')
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
  $('#example1').dataTable( {
  "pageLength": 1000,  
    paging: false
} );
</script>
@stop

