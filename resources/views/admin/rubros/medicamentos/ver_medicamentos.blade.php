@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4 class="pull-left"> Medicamentos - {!!$rubros->descripcion!!}</h4>
        <div class="box-header">
                <a data-toggle="tooltip" title="Asignar Dispositivos!"  class="btn btn-app pull-right" href="{!! route('rubros.asignar_medicamentos', 
           ['rubro_id'=>$rubro_id]) !!}"> <i class="glyphicon glyphicon-check"></i> Asignar</a>
                @if($num_productos>0)
                        <a data-toggle="tooltip" title="Descargar Excel!" class="btn btn-app pull-right"  href="{!! route('rubros.exportRubro',['xlsx','rubro_id'=>$rubro_id,'tipo'=>1]) !!}">  <i class="fa fa-file-excel-o"></i> Excel</a>
                        
                        <a target="_blank" data-toggle="tooltip" target="_blank" title="Descargar PDF!" class="btn btn-app pull-right" href="{!! route('rubros.pdf_rubro',['rubro_id'=>$rubro_id,'tipo'=>1]) !!}">  <i class="fa fa-file-pdf-o"></i> PDF</a>
                    
                @endif
            </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.rubros.medicamentos.table')
            </div>
            <a href="{!! route('rubros.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
        </div>        
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
$('#example1').dataTable( {
  "pageLength": 1000
} );
</script>

@stop



