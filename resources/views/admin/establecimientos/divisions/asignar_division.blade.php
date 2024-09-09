@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            {!!$establecimiento->nombre_establecimiento!!}
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::model($establecimiento, ['route' => ['establecimientos.guardar_divisions', $establecimiento->id], 'method' => 'patch']) !!}

                        @include('admin.establecimientos.divisions.fields')

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






