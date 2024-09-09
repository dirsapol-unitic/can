<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>@if ($medicamento_cerrado==1)
                            <th rowspan="3" style="text-align:center;">Editar</th>
                            @endif
                            <th rowspan="3" style="text-align:center;">Descripción</th>
                            <th rowspan="3" style="text-align:center;">Precio</th>
                            <th rowspan="3" style="text-align:center;">CPMA</th>
                            <th colspan="7" bgcolor="#D4E6F1" style="text-align:center;">INGRESOS</th>
                            <th colspan="5" bgcolor="#D4EFDF" style="text-align:center;">SALIDAS</th>
                            <th colspan="3" bgcolor="#FEF5E7" style="text-align:center;">STOCK</th>
                            <th rowspan="2" colspan="2" bgcolor="#FDEDEC" style="text-align:center;">SOBRESTOCK</th>
                                          
                        </tr>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;" ><small>Stock Inicial</small></th>
                            <th bgcolor="#D4E6F1" style="text-align:center;"><small>Almacen Central</small></th>
                            <th bgcolor="#D4E6F1" style="text-align:center;"><small>Directo Proveedor</small></th>
                            <th bgcolor="#D4E6F1" style="text-align:center;"><small>Transferencia</small></th>
                            <th colspan="2" bgcolor="#D4E6F1" style="text-align:center;">TOTAL INGRESOS</th>
                            <th colspan="2" bgcolor="#D4EFDF" style="text-align:center;">CONSUMO</th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Transferencia</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Pérdida/Merma</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Total Salidas</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Final</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Fecha Vencimiento</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Disponibilidad</small></th>         
                        </tr>
                        <tr>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Valor</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Valor</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#D4EFDF" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Fecha</small></th>
                            <th bgcolor="#FEF5E7" style="text-align:center;"><small>Meses</small></th>
                            <th bgcolor="#FDEDEC" style="text-align:center;"><small>Unidad</small></th>
                            <th bgcolor="#FDEDEC" style="text-align:center;"><small>Valor</small></th>
                        </tr>
                    </thead>                
                    <tbody>
                    @foreach($abastecimientos as $key => $abastecimiento)
                        <tr>
                            @if ($medicamento_cerrado==1)
                            <td>
                                <div class='btn-group'>
                                    <!--a href="{//!! route('abastecimiento.editar_abastecimiento', [$abastecimiento->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a-->
                                    <?php echo '<a onclick="editForm('. $abastecimiento->id .')" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-edit"></i></a>';
                                    ?>

                                    
                                </div>
                            </td>
                            @endif
                            <td><small>{!! $abastecimiento->descripcion !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->precio !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->cpma !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->stock_incanal !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->almacen_central !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->ingreso_proveedor !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->ingreso_transferencia !!}</small></td>
                            <td bgcolor="#EAF2F8" style="text-align:center;"><small>{!! $abastecimiento->unidad_ingreso !!}</small></td>
                            <td bgcolor="#D4E6F1" style="text-align:center;"><small>{!! number_format($abastecimiento->valor_ingreso,2) !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->unidad_consumo !!}</small></td>
                            <td bgcolor="#D4E6F1" style="text-align:center;"><small>{!! number_format($abastecimiento->valor_consumo,2) !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->salida_transferencia !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->merma !!}</small></td>
                            <td bgcolor="#D4EFDF" style="text-align:center;"><small>{!! $abastecimiento->total_salidas !!}</small></td>
                            <?php $color = ($abastecimiento->stock_final < 0) ? 'bgcolor="#FF0000"' : '#bgcolor="#FEF5E7"'; ?>
                            <td <?php echo $color; ?> style="text-align:center;"><small>{!! $abastecimiento->stock_final !!}</small></td>
                            <td style="text-align:center;"><small>{!! $abastecimiento->fecha_vencimiento !!}</small></td>
                            <?php $color = ($abastecimiento->disponibilidad < 0) ? 'bgcolor="#FF0000"' : '#bgcolor="#FEF5E7"'; ?>
                            <td <?php echo $color; ?> style="text-align:center;"><small>{!! number_format($abastecimiento->disponibilidad,2) !!}</small></td>
                            <td bgcolor="#FDEDEC" style="text-align:center;"><small>{!! $abastecimiento->unidades_sobrestock !!}</small></td>
                            <td bgcolor="#FDEDEC" style="text-align:center;"><small>{!! number_format($abastecimiento->valor_sobrestock,2) !!}</small></td>                            
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
        @include('site.cans.medicamentos.form')          
    </div>    
</div>

@section('scripts')
    
<!--script src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script-->
<script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>
<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--script src="{{ asset('assets/jquery/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script-->

    {{-- dataTables --}}
    <!--script src="{{ asset('assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/dataTables/js/dataTables.bootstrap.min.js') }}"></script-->

    {{-- Validator --}}
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('assets/bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>

    <script type="text/javascript">
      function editForm(id) {
        save_method = 'edit';
        $('input[name=_method]').val('PATCH');
        $('#modal-form form')[0].reset();
        $.ajax({
          url: "{{ url('abastecimiento') }}" + '/' + id + "/edit",
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            $('#modal-form').modal('show');
            $('.modal-title').text('Editar Estimacion');

            $('#id').val(data.id);
            $('#descripcion').val(data.descripcion);
            $('#cpma').val(data.cpma);
            $('#stock_incanal').val(data.stock_incanal);
            $('#almacen_central').val(data.almacen_central);
            $('#ingreso_proveedor').val(data.ingreso_proveedor);
            $('#ingreso_transferencia').val(data.ingreso_transferencia);
            $('#nombre_establecimiento').val(data.nombre_establecimiento);
            $('#unidad_consumo').val(data.unidad_consumo);
            $('#salida_transferencia').val(data.salida_transferencia);
            $('#merma').val(data.merma);
            $('#fecha_vencimiento').val(data.fecha_vencimiento);            
          },
          error : function() {
              alert("No hay Datos");
          }
        });
      }

      $(function(){
            $('#modal-form form').validator().on('submit', function (e) {
                if (!e.isDefaultPrevented()){
                    var id = $('#id').val();
                    url = "{{ url('abastecimiento') . '/' }}" + id;

                    $.ajax({
                        url : url,
                        type : "POST",
//                        data : $('#modal-form form').serialize(),
                        data: new FormData($("#modal-form form")[0]),
                        contentType: false,
                        processData: false,
                        success : function(data) {
                            $('#modal-form').modal('hide');
                            table.ajax.reload();
                            swal({
                                title: 'Excelente!',
                                text: data.message,
                                type: 'success',
                                timer: '1500'
                            })
                        },
                        error : function(data){
                            swal({
                                title: 'Oops...',
                                text: data.message,
                                type: 'error',
                                timer: '1500'
                            })
                        }
                    });
                    return false;
                }
            });
        });
    </script>

@stop