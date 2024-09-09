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
        <h1 class="pull-right">
           
                @if($cerrado==2)
                    <div class="box-body">
                      <h3 class="pull-left">{!! $descripcion !!} Registrados</h3>
                      <a class="btn btn-app pull-right" target="_blank" href="{!! route('farmacia.exportEstimacionDataConsolidada',[$can_id,$establecimiento_id,$tipo,'xlsx',2]) !!}">Excel <i class="fa fa-file-excel-o"></i></a>                
                      </a>
                      @if(actualizado==1)
                      <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel1',[$can_id,$establecimiento_id,$tipo,$servicio_id,4]) }}" class="btn btn-app pull-right" >PDF AÑO 4<i class="fa fa-download"></i> </a>
                      @endif
                      <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel1',[$can_id,$establecimiento_id,$tipo,$servicio_id,3]) }}" class="btn btn-app pull-right" >PDF AÑO 3<i class="fa fa-download"></i> </a>
                      <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel1',[$can_id,$establecimiento_id,$tipo,$servicio_id,2]) }}" class="btn btn-app pull-right" >PDF AÑO 2<i class="fa fa-download"></i> </a>
                      <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel1',[$can_id,$establecimiento_id,$tipo,$servicio_id,1]) }}" class="btn btn-app pull-right" >PDF AÑO 1<i class="fa fa-download"></i> </a>
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
              @if($tiempo==1)
                @include('site.responsable_farmacia.medicamentos.descarga_table')
              @else
                @if($tiempo==2)
                    @include('site.responsable_farmacia.medicamentos.descarga_table_2')
                @else
                    @include('site.responsable_farmacia.medicamentos.descarga_table_3')
                @endif
              @endif
              
              @include('site.responsable_farmacia.medicamentos.form_ver')
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
                "pageLength": 15,  
                fixedColumns:   {
                    leftColumns: 1
                },                
            });
        })

        function ver_datos(id, descripcion){
          $('#descripcionproducto').text(descripcion);
          $.ajax({
          url: "{{ url('farmacia') }}" + '/' + id,
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



    
    
    
    
    
    
    
    

