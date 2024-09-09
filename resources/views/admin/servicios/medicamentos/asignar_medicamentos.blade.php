@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h4>
            Asignar Medicamentos - {!!$servicios->nombre_servicio!!}
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::model($servicios, ['route' => ['servicios.guardar_medicamentos',$servicios->id], 'method' => 'patch']) !!}
                        @include('admin.servicios.medicamentos.fields')
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
  "pageLength": 3000  
} );
</script>
<script type="text/javascript">
$(document).ready( function () {
  
  var table = $('#example').DataTable({
        "responsive": true,
        "pageLength": 3000,
        "order": [[ 0, "asc" ]],
    });

  
} );
</script>
@stop

