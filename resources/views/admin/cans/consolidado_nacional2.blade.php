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
        <h1>
            Consolidado - {!!$descripcion_tipo!!}
        </h1>
        <br/>
        <div class="btn-group">
            <a target="_blank" data-toggle="tooltip" title="Productos Farmaceuticos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,1]) !!}" class='btn bg-red btn-flat margin'><i class="fa fa-plus"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material Biomedico por Establecimientos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,2]) !!}" class='btn bg-maroon btn-flat margin'><i class="fa fa-fire-extinguisher"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Instrumental Quirurgico por Establecimientos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,3]) !!}" class='btn bg-primary btn-flat margin'><i class="fa fa-bed"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material e Insumos Odontológico por Establecimientos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,4]) !!}" class='btn bg-aqua btn-flat margin'><i class="fa fa-wrench"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Insumo de Laboratorio por Establecimientos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,5]) !!}" class='btn bg-olive btn-flat margin'><i class="fa fa-hourglass-start"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material de Laboratorio por Establecimientos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,10]) !!}" class='btn bg-purple btn-flat margin'><i class="fa fa-fw fa-flask"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material Fotográfico y fonotécnico por Establecimientos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,6]) !!}" class='btn bg-navy btn-flat margin'><i class="fa fa-odnoklassniki-square"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Productos Afines por Establecimientos!" href="{!! route('cans.consolidado_nacional_tipo', [$can->id,7]) !!}" class='btn bg-orange btn-flat margin'><i class="fa fa-hand-lizard-o"></i></a>
        </div>
        
    </section>
    <div class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">
                      <div class="table-responsive">
                        <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                            <thead>
                                <tr>
                                    <th bgcolor="#D4E6F1" style="text-align:center;">Código</th>
                                    <th bgcolor="#D4E6F1" style="text-align:center;">Descripción Producto</th>
                                    <th bgcolor="#D4E6F1" style="text-align:center;" >Necesidad - NIVEL I</th>
                                    <th bgcolor="#D4E6F1" style="text-align:center;" >Necesidad - NIVEL II</th>
                                    <th bgcolor="#D4E6F1" style="text-align:center;" >Necesidad - NIVEL III</th>
                                    <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad NACIONAL</th>
                                </tr>
                            </thead>                
                            <tbody>
                                
                            @foreach($estimacions as $key => $estimacion)
                                <tr>
                                    <td>
                                        {!! $estimacion->cod_petitorio !!}
                                    </td>
                                    <td>
                                        {!! $estimacion->descripcion !!}
                                    </td>
                                    <td>
                                        @if ($estimacion->necesidad_anual_nivel_1>0)
                                            <a onclick="ver_datos_nivel_1({!!$estimacion->petitorio_id!!},'{!!$estimacion->cod_petitorio.' - '.$estimacion->descripcion!!}')">{!! $estimacion->necesidad_anual_nivel_1 !!}</a>
                                        @else
                                            {!! $estimacion->necesidad_anual_nivel_1 !!}
                                        @endif
                                        
                                    </td>
                                    <td>
                                        @if ($estimacion->necesidad_anual_nivel_2>0)
                                            <a onclick="ver_datos_nivel_2({!!$estimacion->petitorio_id!!},'{!!$estimacion->cod_petitorio.' - '.$estimacion->descripcion!!}')">{!! $estimacion->necesidad_anual_nivel_2 !!}</a>
                                        @else
                                            {!!$estimacion->necesidad_anual_nivel_2!!}
                                        @endif
                                        
                                    </td>
                                    <td>
                                        @if ($estimacion->necesidad_anual_nivel_3>0)
                                            <a onclick="ver_datos_nivel_3({!!$estimacion->petitorio_id!!},'{!!$estimacion->cod_petitorio.' - '.$estimacion->descripcion!!}')">{!! $estimacion->necesidad_anual_nivel_3 !!}</a>
                                        @else
                                            {!!$estimacion->necesidad_anual_nivel_3!!}
                                        @endif
                                        
                                    </td>
                                    <td>
                                        {!!$estimacion->necesidad_anual_nivel_1+$estimacion->necesidad_anual_nivel_2+$estimacion->necesidad_anual_nivel_3!!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            
                        </table>
                    </div>
                    @include('admin.cans.form_ver_datos_nivel_1')
                    @include('admin.cans.form_ver_datos_nivel_2')
                    @include('admin.cans.form_ver_datos_ajuste_nivel_2')
                </div>
            </div>
        </div>
      </div>  
    </div>    

@endsection
@section('scripts')
<script>
$(document).ready( function () {
    var table = $('#example').DataTable({
        "searching": true,
        "paging":   false,
        "order": [[ 1, "asc" ]],
        "scrollY":        "400px",
        "scrollX":        true,
        "scrollCollapse": true,
        "pageLength": 1000,  
        dom: 'Bfrtip',
        buttons: [
            
                'excelHtml5',                
        ]
    });
})

function ver_datos_nivel_1(id, descripcion){
  $('#descripcionproducto').text(descripcion);
    $.ajax({
    url: "{{ url('mostrar_medicamento_nivel_1') }}" + '/' + id,
            async: false,
            success: function(estimacion){
            $("#muestra-detalle").html(estimacion);
            },
            complete: function () {
            $("#myModalDatosProductos").modal();
            }
    });
  }

function ver_datos_nivel_2(id, descripcion){
  $('#descripcionproducto').text(descripcion);
    $.ajax({
    url: "{{ url('mostrar_medicamento_nivel_2') }}" + '/' + id,
            async: false,
            success: function(estimacion){
            $("#muestra-detalle").html(estimacion);
            },
            complete: function () {
            $("#myModalDatosProductos").modal();
            }
    });
  }

function ver_datos_nivel_3(id, descripcion){
  $('#descripcionproducto').text(descripcion);
    $.ajax({
    url: "{{ url('mostrar_medicamento_nivel_3') }}" + '/' + id,
            async: false,
            success: function(estimacion){
            $("#muestra-detalle").html(estimacion);
            },
            complete: function () {
            $("#myModalDatosProductos").modal();
            }
    });
  }
function ver_datos_ajuste_nivel_2(id, descripcion){
  $('#descripcionproducto').text(descripcion);
    $.ajax({
    url: "{{ url('mostrar_medicamento_ajuste_nivel_2') }}" + '/' + id,
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
<script src="{{ asset('assets/dataTables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/jszip.min.js')}}"></script>
<!--script src="{{ asset('js/pdfmake.min.js')}}"></script>
<script src="{{ asset('js/vfs_fonts.js')}}"></script-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="{{ asset('js/buttons.html5.min.js')}}"></script>
<script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>

@stop