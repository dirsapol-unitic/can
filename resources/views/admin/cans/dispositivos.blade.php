@extends('layouts.app')
@section('css')
    <style type="text/css">
        th, td { white-space: nowrap; font-size: 10px;}
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }      
         
    </style>
    <link rel="stylesheet" href='{{ asset ("/assets/dataTables/css/jquery.dataTables.min.css") }}'>    
    <link rel="stylesheet" href='{{ asset ("/assets/dataTables/css/fixedColumns.dataTables.min.css") }}'>
@stop
@section('content')
    <section class="content-header">
        <h1 class="pull-left">Dispositivos Médicos de {!! $nombre_establecimiento !!}</h1>
        <h1 class="pull-right">
           <a class="btn btn-success pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('abastecimiento.exportAbastecimientoDataPrevio',[$can_id,$establecimiento_id,2,'xlsx']) !!}">Descargar <i class="fa fa-file-excel-o"></i></a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box">
            <div class="box-body">
              <div class="form-group col-sm-12">
                @if ($mostrar_servicios==0)
                <a href="{!! route('cans.show',[$can_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                @else
                <a href="{!! route('cans.mostrar_servicios',[$can_id,$establecimiento_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
                @endif
              </div>
              @include('admin.cans.dispositivos.table')      
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
@section('scripts')
<!--script src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script-->
<script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>
<script>
  
    $(document).ready( function () {  
        var table = $('#example').DataTable({
            "responsive": true,
            "order": [[ 0, "asc" ]], 
            "scrollY":        "400px",
                "scrollX":        true,
                "scrollCollapse": true,
                "pageLength": 1000,  
                fixedColumns:   {
                    leftColumns: 3
                },       
        });
    } );
</script>
@stop