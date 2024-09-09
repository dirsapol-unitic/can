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
        <h3 class="pull-left">{!!$descripcion_tipo!!} POR SERVICIOS</h3>
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
                                  <th>PRODUCTOS</th>
                                  <th>NECESIDAD ANTERIOR</th>     
                                  <th>NECESIDAD ACTUAL</th>                                  
                                  @for($j=0;$j<$col;$j++)
                                    @if($descripcion[$j]!="")
                                      <th><p align="center">{!!$descripcion[$j][1]!!} ANTERIOR</p></th>
                                      <th><p align="center">{!!$descripcion[$j][1]!!} ACTUAL </p></th>
                                    @endif
                                  @endfor
                                </tr>
                            </thead>
                            <tbody>
                              @for($i=0;$i<$fila;$i++)
                                @if($can_productos[$i][299]!="")
                                  <tr>
                                      <td>{!!$can_productos[$i][299]!!}</td>
                                      <td>{!!$can_productos[$i][298]!!}</td>                                    
                                      <td><?php if($can_productos[$i][297]!=$can_productos[$i][298]){ ?>
                                        <span style="color:red;">{!!$can_productos[$i][297]!!}</span>
                                      <?php }else{ ?>
                                        {!!$can_productos[$i][297]!!}
                                      <?php } ?>

                                    </td>                                    
                                      @for($j=0;$j<$y;$j++)
                                        <?php 
                                          $m=$j*2;                        
                                          $n=$m+1;                        
                                        ?>
                                        <td>{!!$can_productos[$i][$m]!!}</td>
                                        <td><?php if($can_productos[$i][$m]!=$can_productos[$i][$n]){?>
                                            <span style="color:red;">{!!$can_productos[$i][$n]!!}</span>
                                        <?php } else {?>
                                            {!!$can_productos[$i][$n]!!}
                                        <?php }?>
                                        </td>
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
            leftColumns: 3
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