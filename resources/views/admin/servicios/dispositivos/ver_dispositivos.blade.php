@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h4 class="pull-left"> Dispositivos - {!!$servicios->nombre_servicio!!}</h4>
        <div class="box-header">
                <a data-toggle="tooltip" title="Asignar Dispositivos!"  class="btn btn-app pull-right" href="{!! route('servicios.asignar_dispositivos', 
           ['servicio_id'=>$servicio_id]) !!}"> <i class="glyphicon glyphicon-check"></i> Asignar</a>
                @if($num_productos>0)
                        <a data-toggle="tooltip" title="Descargar Excel!" class="btn btn-app pull-right"  href="{!! route('servicios.exportServicio',['xlsx','servicio_id'=>$servicio_id,'tipo'=>2]) !!}">  <i class="fa fa-file-excel-o"></i> Excel</a>
                        
                        <a data-toggle="tooltip" target="_blank" title="Descargar PDF!" class="btn btn-app pull-right" href="{!! route('servicios.pdf_servicio',['servicio_id'=>$servicio_id,'tipo'=>2]) !!}">  <i class="fa fa-file-pdf-o"></i> PDF</a>
                @endif
            </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.servicios.dispositivos.table')
            </div>
            <a href="{!! route('servicios.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
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


