@extends('layouts.app')

@section('content')
  <section class="content-header">
      <h1>
          Activar/Desactivar Petitorio
      </h1>
  </section>
  <div class="content">
     @include('adminlte-templates::common.errors')
     <div class="col-sm-4">
     <div class="box box-primary">
         <div class="box-body">
             <div class="row">
                <div class="col-sm-12">
                  {!! Form::model($can, ['route' => ['farmacia_servicios.update_petitorio_rubro', $can->id,$establecimiento_id,$servicio_id], 'method' => 'patch']) !!}
                      @include('site.responsable_farmacia_hospital.fields_activar_servicio')
                  {!! Form::close() !!}
                </div>
             </div>
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
@stop
