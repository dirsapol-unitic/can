@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4 class="pull-left">{!!$nombre_establecimiento!!} - {!!$nombre_division!!} - {!!$nombre_unidad!!}</h4>
        <br/>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::model($unidads, ['route' => ['unidads.guardar_servicios', $dpto_id,$division_id,$establecimiento_id], 'method' => 'patch']) !!}

                        @include('admin.unidads.servicios.fields')

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
  "pageLength": 1000  
} );
</script>

@stop






