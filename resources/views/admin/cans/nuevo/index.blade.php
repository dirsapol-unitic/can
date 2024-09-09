@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Registro de Nuevos Productos
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => ['cans.grabar_nuevos_medicamentos_dispositivos',$establecimiento_id,$can_id,$destino], 'method' => 'post']) !!}

                        @include('admin.cans.nuevo.fields')

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
