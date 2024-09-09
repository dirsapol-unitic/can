@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Elegir Productos - 
            <?php if($nombre_servicio=='NO APLICA'){ if($tipo==1) echo "MEDICAMENTOS"; else echo "DISPOSITIVOS"; } else echo $nombre_servicio; ?>             
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::model($estimacions, ['route' => ['farmacia.guardar_medicamentos_asignados', $can_id,$tipo], 'method' => 'patch']) !!}
                        @include('site.responsable_farmacia.medicamentos.fields')
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
  "pageLength": 1000,  
    paging: false
} );
</script>
@stop

