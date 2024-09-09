
@extends('layouts.app')
@section('css')
    <style type="text/css">
        th, td { font-size: 14px;}
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }        
    </style>
    <link rel="stylesheet" href='{{ asset ("/assets/dataTables/css/bootstrap-datepicker.min.css") }}'>
    <link rel="stylesheet" href='{{ asset ("/assets/dataTables/css/jquery.dataTables.min.css") }}'>    
    <link rel="stylesheet" href='{{ asset ("/assets/dataTables/css/fixedColumns.dataTables.min.css") }}'>
    
@stop
@section('content')
    <section class="content-header">
        @if($num_estimaciones>0)
            <div class="box-body">
              <h3 class="pull-left">{!! $descripcion_tipo !!} Registrados</h3>
              <a class="btn btn-app pull-right" target="_blank" href="{!! route('estimacion_servicio.exportDataNivel2y3',[$can_id,$establecimiento_id,$servicio_id,$tipo,'xlsx',2]) !!}">Excel <i class="fa fa-file-excel-o"></i>           
              </a>
              @if(actualizado==1)
                <a target="_blank" href="{{ route('estimacion_servicio.pdf_estimacion_servicio',[$can_id,$establecimiento_id,$tipo,0,4]) }}" class="btn btn-app pull-right" >PDF AÑO 4<i class="fa fa-download"></i> </a>
              @endif
              <a target="_blank" href="{{ route('estimacion_servicio.pdf_estimacion_servicio',[$can_id,$establecimiento_id,$tipo,0,3]) }}" class="btn btn-app pull-right" >PDF AÑO 3<i class="fa fa-download"></i> </a>
              <a target="_blank" href="{{ route('estimacion_servicio.pdf_estimacion_servicio',[$can_id,$establecimiento_id,$tipo,0,2]) }}" class="btn btn-app pull-right" >PDF AÑO 2<i class="fa fa-download"></i> </a>
              <a target="_blank" href="{{ route('estimacion_servicio.pdf_estimacion_servicio',[$can_id,$establecimiento_id,$tipo,0,1]) }}" class="btn btn-app pull-right" >PDF AÑO 1<i class="fa fa-download"></i> </a>
            </div>
        @endif
       
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')
        <a href="{!! route('estimacion_servicio.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
        <br/><br/>
        <div class="clearfix"></div>
        <div class="box box">
            <div class="box-body">
              @include('site.estimacion_servicio.medicamentos.descarga_table_anterior')
            </div>
        </div>
        <div>
        <a href="{!! route('estimacion_servicio.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>

        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready( function () {
            var table = $('table.display').DataTable({
                "responsive": true,
                "order": [[ 0, "asc" ]],                       
                "scrollY":        "400px",
                "scrollX":        true,
                "scrollCollapse": true,
                "pageLength": 1000,  
                /*fixedColumns:   {
                    leftColumns: 2
                },*/                
            });
        })

        
                
    </script>
<!--script src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script-->
<script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>

@stop