@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Ampliar tiempo al Usuario CAN2020
        </h1>
   </section>
   <div class="content">
      @include('adminlte-templates::common.errors')
      <div class="col-sm-8">
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                  <div class="col-sm-12">
                    {!! Form::model($can, ['route' => ['cans.update_rubro_tiempo', $can->id,$establecimiento_id,$rubro_id], 'method' => 'patch']) !!}
                        @include('admin.cans.fields_ampliar_tiempo')
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
