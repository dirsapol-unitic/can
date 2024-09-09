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
                  <a class="btn btn-app pull-right" target="_blank" href="{!! route('farmacia.exportEstimacionDataConsolidada',[$can_id,$establecimiento_id,$tipo,'xlsx']) !!}">Excel <i class="fa fa-file-excel-o"></i></a>                
                  </a>
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel1',[$can_id,$establecimiento_id,$tipo,$servicio_id]) }}" class="btn btn-app pull-right" >PDF <i class="fa fa-download"></i> </a>
                </div>
            @endif
            @if($cerrado_stock==2)
              @if($nivel==3)          
                @if($tipo==1)            
                <div class="box-body">
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,$tipo,$servicio_id]) }}" class="btn btn-app pull-right" >PDF <i class="fa fa-download"></i> </a>
                </div>
                @else
                <div class="box-body">
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,2,$servicio_id]) }}" class="btn btn-app pull-right" >Biomedico <i class="fa fa-download"></i> </a>
                
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,3,$servicio_id]) }}" class="btn btn-app pull-right" >Quirurgico <i class="fa fa-download"></i> </a>
                
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,7,$servicio_id]) }}" class="btn btn-app pull-right" >Afines <i class="fa fa-download"></i> </a>
                
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,4,$servicio_id]) }}" class="btn btn-app pull-right" >Dental <i class="fa fa-download"></i> </a>
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,6,$servicio_id]) }}" class="btn btn-app pull-right" >Fonotecnico <i class="fa fa-download"></i> </a>
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,10,$servicio_id]) }}" class="btn btn-app pull-right" >Mat. Lab <i class="fa fa-download"></i> </a>
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,5,$servicio_id]) }}" class="btn btn-app pull-right" >Ins. Lab <i class="fa fa-download"></i> </a>
                </div>
                @endif
              @else
                <div class="box-body">
                  <a target="_blank" href="{{ route('farmacia.pdf_estimacion_nivel_1',[$can_id,$establecimiento_id,$tipo,$servicio_id]) }}" class="btn btn-app pull-right" >PDF <i class="fa fa-download"></i> </a>
                </div>
              @endif

            @endif
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box">
            <div class="box-body">
              @include('site.responsable_farmacia.medicamentos.descarga_table')
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
                    leftColumns: 3
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



    
    
    
    
    
    
    
    

