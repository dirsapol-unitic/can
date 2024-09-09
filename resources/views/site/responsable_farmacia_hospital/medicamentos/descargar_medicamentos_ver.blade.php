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
    
      @if($num_estimaciones>0)
        <div class="box-body">
          <h3 class="pull-left">{!! $descripcion_tipo !!} Registrados - {!!$nombre_servicio!!} </h3>          
          <a target="_blank" href="{{ route('farmacia_servicios.pdf_servicio_rectificacion_farmacia',[$can_id,$establecimiento_id,$tipo,$servicio_id]) }}" class="btn btn-app pull-right" >PDF <i class="fa fa-download"></i> </a>
        </div>

      @else
          <div class="box-body">
                <h3 class="pull-left">{!! $descripcion_tipo !!} Registrados - {!!$nombre_servicio!!} </h3>
          </div>
      @endif
    
  </section>
  <div class="content">
    <div class="clearfix"></div>
    @include('flash::message')
    <div class="clearfix"></div>
    @if (Auth::user()->rol==7 && (Auth::user()->establecimiento_id==1 || Auth::user()->establecimiento_id==2 || Auth::user()->establecimiento_id==30 || Auth::user()->establecimiento_id==69 || Auth::user()->establecimiento_id==3))
    <a href="{!! route('farmacia.listar_servicios',[$can_id])!!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
    <br/><br/>
    
    @else
    <a href="{!! route('farmacia.index')!!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
    <br/><br/>
    
    @endif
    
    <div class="box box">
        <div class="box-body">
          @if($num_estimaciones>0)
            @include('site.responsable_farmacia_hospital.medicamentos.descarga_table2')
            @include('site.responsable_farmacia_hospital.medicamentos.form_ver')
          @else
            <div class="box-body">
                    <h3 class="pull-left">No se ha registrado producto alguno en este servicio</h3>
            </div>
          @endif
        </div>
    </div>
    <a href="{!! route('farmacia.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
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
                paging: false,  
                fixedColumns:   {
                    leftColumns: 1
                },                
            });
        })

        function ver_datos(id, descripcion){
          $('#descripcionproducto').text(descripcion);
            $.ajax({
            url: "{{ url('farmacia_servicios') }}" + '/' + id,
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



    
    
    
    
    
    
    
    

