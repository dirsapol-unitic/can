@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Activar Petitorio
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($can, ['route' => ['cans.update_petitorio', $can->id,$establecimiento_id], 'method' => 'patch']) !!}

                        @include('admin.cans.fields_activar')

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
@stop
