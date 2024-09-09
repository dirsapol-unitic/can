@extends('layouts.app')
@section('css')
    <style type="text/css">
        th, td { font-size: 10px;}
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
        <h1 class="pull-left">{!!$descripcion_tipo!!} POR ESTABLECIMIENTO DE SALUD</h1>
        <br/><br/>
        
        <div class="btn-group">
            <a target="_blank" data-toggle="tooltip" title="Productos Farmaceuticos por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,1]) !!}" class='btn bg-red btn-flat margin'><i class="fa fa-expeditedssl"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material Biomedico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,2]) !!}" class='btn bg-maroon btn-flat margin'><i class="fa fa-fire-extinguisher"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Instrumental Quirurgico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,3]) !!}" class='btn bg-primary btn-flat margin'><i class="fa fa-bed"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material e Insumos Odontológico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,4]) !!}" class='btn bg-aqua btn-flat margin'><i class="fa fa-wrench"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Insumo de Laboratorio por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,5]) !!}" class='btn bg-olive btn-flat margin'><i class="fa fa-hourglass-start"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material de Laboratorio por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,10]) !!}" class='btn bg-purple btn-flat margin'><i class="fa fa-fw fa-flask"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material Fotográfico y fonotécnico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,6]) !!}" class='btn bg-navy btn-flat margin'><i class="fa fa-odnoklassniki-square"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Productos Afines por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,7]) !!}" class='btn bg-orange btn-flat margin'><i class="fa fa-hand-lizard-o"></i></a>            
        </div>
        
    </section>
    <div class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">
                      <div class="table-responsive">
                        <table id="example" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                  <th>Codigo</th>
                                  <th>PRODUCTOS</th>
                                  <th>Necesidad</th>
                                  @for($j=0;$j<$col;$j++)
                                    @if($descripcion[$j]!="")
                                      <th><p align="center">{!!$descripcion[$j][1]!!}</p></th>
                                    @endif
                                  @endfor
                                  
                                </tr>
                                
                            </thead>
                            <tbody>
                              @for($i=0;$i<$fila;$i++)
                                @if($can_productos[$i][87]!="")
                                  <tr>
                                    <td>{!!$can_productos[$i][87]!!}</td>
                                    <td>{!!$can_productos[$i][85]!!}</td>
                                    <td>{!!$can_productos[$i][84]!!}</td>
                                    @for($j=0;$j<$col;$j++)
                                      <td>{!!$can_productos[$i][$j]!!}</td>
                                    @endfor
                                  </tr>
                                @endif
                              @endfor
                            </tbody>
                        </table>
                    </div>
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
        fixedColumns:   {
            leftColumns: 3
        },
        dom: 'Bfrtip',
        buttons: [
            
                'excel',                
        ]
    });
})

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