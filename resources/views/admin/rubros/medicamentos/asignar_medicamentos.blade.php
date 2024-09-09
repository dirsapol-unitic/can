@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Asignar Medicamentos - {!!$rubros->descripcion!!}
        </h4>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::model($rubros, ['route' => ['rubros.guardar_medicamentos',$rubros->id], 'method' => 'patch']) !!}

                        @include('admin.rubros.medicamentos.fields')

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

