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
        <h4 class="pull-left">Dispositivos Médicos de {!!$nombre_servicio!!} - {!! $nombre_establecimiento !!}</h4>
        <h1 class="pull-right">
            <a class="btn btn-app" href="{!! route('cans.pdf_servicio_rectificacion_administrador',[$can_id,$establecimiento_id,2,$servicio_id]) !!}">
            PDF <i class="fa fa-download"></i> </a>
        </h1>
        

    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box">
            <div class="box-body">
              <div class="form-group col-sm-12">
                <a href="{!! route('cans.mostrar_servicios',[$can_id,$establecimiento_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
              </div>
              @include('admin.cans.dispositivos.table_servicios_rectificacion')      
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
