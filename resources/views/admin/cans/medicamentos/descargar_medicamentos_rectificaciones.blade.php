@extends('layouts.app')
@section('css')
    <style type="text/css">
        th, td { font-size: 14px;}
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
        <h1 class="pull-left">Medicamentos de {!! $nombre_establecimiento !!}</h1>
        <h1 class="pull-right">
            @if($num_estimaciones>0)
                <div class="row no-print">
                    <div class="col-xs-12">
                        <a target="_blank" href="{{ route('cans.pdf_rectificacion',[$can_id,$establecimiento_id,$tipo]) }}" class="btn btn-app">
                            <i class="fa fa-download"></i> PDF
                        </a>
                    </div>
                </div>                
            @endif
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')
 
        <div class="clearfix"></div>
        <div class="box box">
            <div class="box-body">
                <div class="form-group col-sm-12">
                    <a href="{!! route('cans.show',[$can_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
              </div>
              @include('admin.cans.medicamentos.descarga_table_rectificaciones')
              @include('admin.cans.medicamentos.form_ver')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
@section('scripts')
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
                    leftColumns: 1
                },
            });
        })
        function ver_datos(id, descripcion){
          $('#descripcionproducto').text(descripcion);
            $.ajax({
            url: "{{ url('servicios_mostrar_med') }}" + '/' + id,
                    async: false,
                    success: function(estimacion){
                    $("#muestra-detalle").html(estimacion);
                    },
                    complete: function () {
                    $("#myModalDatosProductos").modal();
                    }
            });
          }
    </script>
<!--script src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script-->
<script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>
@stop



    
    
    
    
    
    
    
    

