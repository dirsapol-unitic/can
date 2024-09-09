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
              <a target="_blank" href="{{ route('estimacion_servicio.pdf_servicio_rectificacion',[$can_id,$establecimiento_id,$tipo]) }}" class="btn btn-app pull-right" >PDF <i class="fa fa-download"></i> </a>
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
              @include('site.estimacion_servicio.medicamentos.descarga_table_rectificacion')
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
                fixedColumns:   {
                    leftColumns: 1
                },                
            });
        })

        
                
    </script>
<!--script src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script-->
<script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>

@stop



    
    
    
    
    
    
    
    

