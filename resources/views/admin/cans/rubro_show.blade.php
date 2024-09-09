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
        <h3 class="pull-left">{!!$descripcion_tipo!!} POR SERVICIOS EN LOS HOSPITALES DE LA SANIDAD POLICIAL</h3>
        <div class="btn-group">
            <a target="_blank" data-toggle="tooltip" title="Material Biomedico por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,2]) !!}" class='btn-sm bg-maroon btn-flat margin'><i class="fa fa-fire-extinguisher"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Instrumental Quirurgico por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,3]) !!}" class='btn-sm bg-primary btn-flat margin'><i class="fa fa-bed"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material e Insumos Odontológico por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,4]) !!}" class='btn-sm bg-aqua btn-flat margin'><i class="fa fa-wrench"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Insumo de Laboratorio por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,5]) !!}" class='btn-sm bg-olive btn-flat margin'><i class="fa fa-hourglass-start"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material de Laboratorio por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,10]) !!}" class='btn-sm bg-purple btn-flat margin'><i class="fa fa-fw fa-flask"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material Fotográfico y fonotécnico por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,6]) !!}" class='btn-sm bg-navy btn-flat margin'><i class="fa fa-odnoklassniki-square"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Productos Afines por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,7]) !!}" class='btn-sm bg-orange btn-flat margin'><i class="fa fa-hand-lizard-o"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Uso Restringido por Establecimientos!" href="{!! route('cans.establecimientos_servicio_can_tipo', [$can->id,9]) !!}" class='btn-sm bg-red btn-flat margin'><i class="fa fa-fw fa-expeditedssl"></i></a>  .
        </div>
    </section>
    <div class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">
                      @if($fila>0)
                      <div class="table-responsive">
                        <table id="example" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                  <th rowspan="2">PRODUCTOS</th>
                                  <th rowspan="2">NECESIDAD</th>
                                  @for($j=0;$j<$col;$j++)
                                    @if($descripcion[$j]!="")
                                      <th colspan="5"><p align="center">{!!$descripcion[$j][1]!!}</p></th>
                                    @endif
                                  @endfor
                                </tr>
                                <tr>
                                @for($j=0;$j<$col;$j++)
                                    @if($descripcion[$j]!="")
                                  <th>LNS</th>
                                  <th>ABL</th>
                                  <th>GSJ</th>
                                  <th>HCH</th>
                                  <th>HAQ</th>
                                  @endif
                                  @endfor
                                </tr>
                            </thead>
                            <tbody>
                              @for($i=0;$i<$fila;$i++)
                                @if($can_productos[$i][419]!="")
                                  <tr>
                                    <td>{!!$can_productos[$i][419]!!}</td>
                                    <td>{!!$can_productos[$i][418]!!}</td>
                                    @for($j=0;$j<$y;$j++)
                                      <td>{!!$can_productos[$i][$j]!!}</td>
                                    @endfor
                                  </tr>
                                @endif
                              @endfor
                            </tbody>
                        </table>
                      </div>
                    @else
                       No hay registro alguno.
                    @endif
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
        "order": [[ 0, "asc" ]],
        "scrollY":        "400px",
        "scrollX":        true,
        "scrollCollapse": true,
        "pageLength": 1000,  
        fixedColumns:   {
            leftColumns: 2
        },
        dom: 'Bfrtip',
        buttons: [
            
                'excelHtml5',                
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