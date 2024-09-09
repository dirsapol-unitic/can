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
                  {!! Form::model($can, ['route' => ['farmacia.update_activar_rubro', $can->id,$establecimiento_id,$rubro_id], 'method' => 'patch']) !!}
                      @include('site.responsable_farmacia.fields_activar_rubro')
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
