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
        @if ($tipo_dispositivo>1)
        <div class="btn-group">
            <a target="_blank" data-toggle="tooltip" title="Material Biomedico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,2]) !!}" class='btn-sm bg-maroon btn-flat margin'><i class="fa fa-fire-extinguisher"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Instrumental Quirurgico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,3]) !!}" class='btn-sm bg-primary btn-flat margin'><i class="fa fa-bed"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material e Insumos Odontológico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,4]) !!}" class='btn-sm bg-aqua btn-flat margin'><i class="fa fa-wrench"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Insumo de Laboratorio por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,5]) !!}" class='btn-sm bg-olive btn-flat margin'><i class="fa fa-hourglass-start"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material de Laboratorio por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,10]) !!}" class='btn-sm bg-purple btn-flat margin'><i class="fa fa-fw fa-flask"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Material Fotográfico y fonotécnico por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,6]) !!}" class='btn-sm bg-navy btn-flat margin'><i class="fa fa-odnoklassniki-square"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Productos Afines por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,7]) !!}" class='btn-sm bg-orange btn-flat margin'><i class="fa fa-hand-lizard-o"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Uso Restringido por Establecimientos!" href="{!! route('cans.consolidado_establecimiento_producto_tipo_dispositivo', [$can->id,9]) !!}" class='btn-sm bg-red btn-flat margin'><i class="fa fa-fw fa-expeditedssl"></i></a>  .
            
        </div>
        @endif
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
                                  <th>#</th>
                                  <th>Codigo</th>
                                  <th>PRODUCTOS</th>
                                  <!--th>Necesidad</th-->
                                  @for($j=0;$j<81;$j++)
                                    @if($descripcion[$j]!="")
                                      <th><p align="center">{!!$descripcion[$j][1]!!}</p></th>
                                    @endif
                                  @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <?php $x=1; ?>
                                @foreach($consulta as $key => $estimacion)
                                <tr>
                                    <td>{!! $x++ !!}</td>
                                    <td>{!! $estimacion->cod_petitorio !!}</td>
                                    <td>{!! $estimacion->descripcion !!}</td>
                                    <!--td></td-->
                                    <td>{!! $estimacion->establecimiento_1 !!}</td>
                                    <td>{!! $estimacion->establecimiento_2 !!}</td>
                                    <td>{!! $estimacion->establecimiento_3 !!}</td>
                                    <td>{!! $estimacion->establecimiento_4 !!}</td>
                                    <td>{!! $estimacion->establecimiento_5 !!}</td>
                                    <td>{!! $estimacion->establecimiento_6 !!}</td>
                                    <td>{!! $estimacion->establecimiento_7 !!}</td>
                                    <td>{!! $estimacion->establecimiento_8 !!}</td>
                                    <td>{!! $estimacion->establecimiento_9 !!}</td>
                                    <td>{!! $estimacion->establecimiento_10 !!}</td>
                                    <td>{!! $estimacion->establecimiento_11 !!}</td>
                                    <td>{!! $estimacion->establecimiento_12 !!}</td>
                                    <td>{!! $estimacion->establecimiento_13 !!}</td>
                                    <td>{!! $estimacion->establecimiento_14 !!}</td>
                                    <td>{!! $estimacion->establecimiento_15 !!}</td>
                                    <td>{!! $estimacion->establecimiento_16 !!}</td>
                                    <td>{!! $estimacion->establecimiento_17 !!}</td>
                                    <td>{!! $estimacion->establecimiento_18 !!}</td>
                                    <td>{!! $estimacion->establecimiento_19 !!}</td>
                                    <td>{!! $estimacion->establecimiento_20 !!}</td>
                                    <td>{!! $estimacion->establecimiento_21 !!}</td>
                                    <td>{!! $estimacion->establecimiento_22 !!}</td>
                                    <td>{!! $estimacion->establecimiento_23 !!}</td>
                                    <td>{!! $estimacion->establecimiento_24 !!}</td>
                                    <td>{!! $estimacion->establecimiento_25 !!}</td>
                                    <td>{!! $estimacion->establecimiento_26 !!}</td>
                                    <td>{!! $estimacion->establecimiento_27 !!}</td>
                                    <td>{!! $estimacion->establecimiento_28 !!}</td>
                                    <td>{!! $estimacion->establecimiento_29 !!}</td>
                                    <td>{!! $estimacion->establecimiento_30 !!}</td>
                                    <td>{!! $estimacion->establecimiento_31 !!}</td>
                                    <td>{!! $estimacion->establecimiento_32 !!}</td>
                                    <td>{!! $estimacion->establecimiento_33 !!}</td>
                                    <td>{!! $estimacion->establecimiento_34 !!}</td>
                                    <td>{!! $estimacion->establecimiento_35 !!}</td>
                                    <td>{!! $estimacion->establecimiento_36 !!}</td>
                                    <td>{!! $estimacion->establecimiento_37 !!}</td>
                                    <td>{!! $estimacion->establecimiento_38 !!}</td>
                                    <td>{!! $estimacion->establecimiento_39 !!}</td>
                                    <td>{!! $estimacion->establecimiento_40 !!}</td>
                                    <td>{!! $estimacion->establecimiento_41 !!}</td>
                                    <td>{!! $estimacion->establecimiento_42 !!}</td>
                                    <td>{!! $estimacion->establecimiento_43 !!}</td>
                                    <td>{!! $estimacion->establecimiento_44 !!}</td>
                                    <td>{!! $estimacion->establecimiento_45 !!}</td>
                                    <td>{!! $estimacion->establecimiento_46 !!}</td>
                                    <td>{!! $estimacion->establecimiento_47 !!}</td>
                                    <td>{!! $estimacion->establecimiento_48 !!}</td>
                                    <td>{!! $estimacion->establecimiento_49 !!}</td>
                                    <td>{!! $estimacion->establecimiento_50 !!}</td>
                                    <td>{!! $estimacion->establecimiento_51 !!}</td>
                                    <td>{!! $estimacion->establecimiento_52 !!}</td>
                                    <td>{!! $estimacion->establecimiento_53 !!}</td>
                                    <td>{!! $estimacion->establecimiento_54 !!}</td>
                                    <td>{!! $estimacion->establecimiento_55 !!}</td>
                                    <td>{!! $estimacion->establecimiento_56 !!}</td>
                                    <td>{!! $estimacion->establecimiento_57 !!}</td>
                                    <td>{!! $estimacion->establecimiento_58 !!}</td>
                                    <td>{!! $estimacion->establecimiento_59 !!}</td>
                                    <td>{!! $estimacion->establecimiento_60 !!}</td>
                                    <td>{!! $estimacion->establecimiento_61 !!}</td>
                                    <td>{!! $estimacion->establecimiento_62 !!}</td>
                                    <td>{!! $estimacion->establecimiento_63 !!}</td>
                                    <td>{!! $estimacion->establecimiento_64 !!}</td>
                                    <td>{!! $estimacion->establecimiento_65 !!}</td>
                                    <td>{!! $estimacion->establecimiento_66 !!}</td>
                                    <td>{!! $estimacion->establecimiento_67 !!}</td>
                                    <td>{!! $estimacion->establecimiento_68 !!}</td>
                                    <td>{!! $estimacion->establecimiento_69 !!}</td>
                                    <td>{!! $estimacion->establecimiento_70 !!}</td>
                                    <td>{!! $estimacion->establecimiento_71 !!}</td>
                                    <td>{!! $estimacion->establecimiento_72 !!}</td>
                                    <td>{!! $estimacion->establecimiento_73 !!}</td>
                                    <td>{!! $estimacion->establecimiento_74 !!}</td>
                                    <td>{!! $estimacion->establecimiento_75 !!}</td>
                                    <td>{!! $estimacion->establecimiento_76 !!}</td>
                                    <td>{!! $estimacion->establecimiento_77 !!}</td>
                                    <td>{!! $estimacion->establecimiento_78 !!}</td>
                                    <td>{!! $estimacion->establecimiento_79!!}</td>
                                    <td>{!! $estimacion->establecimiento_80 !!}</td>
                                    <td>{!! $estimacion->establecimiento_81 !!}</td>
                                </tr>
                                @endforeach
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
        "order": [[ 2, "asc" ]],
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