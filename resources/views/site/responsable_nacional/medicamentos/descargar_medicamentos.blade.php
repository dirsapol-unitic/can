@extends('layouts.app')
@section('css')
    <style type="text/css">
        th, td { font-size: 12px;}
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
        <h1 class="pull-right">
           @if($num_estimaciones>0)
              <div class="box-body">
                <h3 class="pull-left">{!! $descripcion_tipo !!} Registrados</h3>
                <a class="btn btn-app pull-right" target="_blank" href="{!! route('nacional.exportEstimacionDataConsolidada',[$can_id,$establecimiento_id,$tipo,'xlsx']) !!}">Excel <i class="fa fa-file-excel-o"></i></a>                
                </a>
                <a target="_blank" href="{{ route('nacional.pdf_estimacion_nacional',[$can_id,$establecimiento_id,$tipo,$servicio_id]) }}" class="btn btn-app pull-right" >PDF <i class="fa fa-download"></i> </a>
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
              @include('site.responsable_nacional.medicamentos.descarga_table')
              @include('site.responsable_nacional.medicamentos.form_ver')
              @include('site.responsable_nacional.medicamentos.form_ipress')
            </div>
        </div>
        <div class="text-center">
        
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
                    leftColumns: 3
                },                
            });
        })

         function ver_datos(id, descripcion){
          $('#descripcionproducto').text(descripcion);
          $.ajax({
          url: "{{ url('nacional') }}" + '/' + id,
                  async: false,
                  success: function(estimacion){
                  $("#muestra-detalle").html(estimacion);
                  },
                  complete: function () {
                  $("#myModalDatosProductos").modal();
                  }
          });
          }

          function ver_ipress(id, descripcion){
          $('#nombre_establecimiento').text(descripcion);
          $.ajax({
          url: "{{ url('show_ipress') }}" + '/' + id,
                  async: false,
                  success: function(estimacion){
                  $("#muestra-detalle-ipress").html(estimacion);
                  },
                  complete: function () {
                  $("#myModalDatosIpress").modal();
                  }
          });
          }

        
                
    </script>
<!--script src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script-->
<script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>
@stop



    
    
    
    
    
    
    
    

