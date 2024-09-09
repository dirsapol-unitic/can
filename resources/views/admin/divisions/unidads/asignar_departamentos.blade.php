@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            {!!$nombre_establecimiento!!} - {!!$nombre_division!!}</h4>
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::model($divisions, ['route' => ['divisions.guardar_departamentos', $division_id,$establecimiento_id], 'method' => 'patch']) !!}

                        @include('admin.divisions.unidads.fields')

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






