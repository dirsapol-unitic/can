@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Medicamentos - {!!$nombre_establecimiento!!} </h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('femas.asignar_dispositivos', 
           ['establecimiento_id'=>$establecimiento_id]) !!}">Asignar Dispositivos <i class="glyphicon glyphicon-check"></i></a>
        </h1>
        <br/><br/>
        <a href="{!! route('establecimientos.index')!!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
        <br/><br/>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.femas.dispositivos.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
$('#example1').dataTable( {
  "pageLength": 3000
} );
</script>

@stop


