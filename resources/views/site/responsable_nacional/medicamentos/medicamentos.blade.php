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
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
      <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@stop
@section('content')
    @if ($establecimiento_id !== Auth::user()->establecimiento_id)
        <div class="content">
            <div class="clearfix"></div>
            @include('flash::message')
            <div class="clearfix"></div>
            <div class="box box">
                <div class="box-body">
                    <h3 class="pull-left">Listado de Productos</h3>
                @if ($nivel==1)
                    <h1 class="pull-right">
                        <a class="btn btn-app" href="{!! route('estimacion.exportEstimacionDataPrevio',[$can_id,$establecimiento_id,$tipo,'xlsx']) !!}">
                            <i class="fa fa-file-excel-o"></i> Descargar Avance
                        </a>
                    </h1>
                @else    
                    <h1 class="pull-right">
                        <a class="btn btn-app" href="{!! route('estimacion.exportEstimacionDataPrevio',[$can_id,$establecimiento_id,$tipo,'xlsx']) !!}">
                            <i class="fa fa-file-excel-o"></i> Descargar Avance
                        </a>
                    </h1>
                @endif 
                    <div class="row">
                        <div class="col-xs-12">        
                            <div class="box-body">
                                <table id="estimacion" class="table table-striped">
                                    <thead>
                                        <tr>
                                            
                                            <th rowspan="2" style="text-align:center;">Editar</th>
                                            <th rowspan="2" style="text-align:center;">Descripción</th>
                                            <th rowspan="2" style="text-align:center;">Stock Actual</th>
                                            <th rowspan="2" style="text-align:center;">Necesidad Anual</th>
                                            <th colspan="12" bgcolor="#D4E6F1" style="text-align:center;">
                                            PRORRATEO</th>
                                            <th rowspan="2" style="text-align:center;">Justificación</th>
                                        </tr>
                                        <tr>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Enero</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Febrero</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Marzo</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Abril</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Mayo</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Junio</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Julio</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Agosto</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Setiembre</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Octubre</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Noviembre</small></th>
                                            <th bgcolor="#EAF2F8" style="text-align:center;"><small>Diciembre</small></th>
                                        </tr>
                                    </thead>  
                                    <tbody></tbody>
                                </table>
                            </div>                                 
                        </div>    
                    </div>
                    @include('site.estimacions.medicamentos.form')
                </div>
            </div>
            <div class="text-center">
            
            </div>
        </div>
    @else
        <div class="content">
            <div class="clearfix"></div>
            <div class="box box">
                <div class="box-body">
                  No tienes autorización para ingresar a esta zona
                </div>
            </div>
            <div class="text-center">
            
            </div>
        </div>
    @endif    
        
@endsection
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
      var table = $('#estimacion').DataTable({
                      "order": [[ 1, "asc" ]],
                      processing: true,                      
                        "responsive": true,
                        "scrollY":        "400px",
                        "scrollX":        true,
                        "scrollCollapse": true,
                        "pageLength": 3000,  
                        fixedColumns:   {
                                leftColumns: 2
                        }, //$estimacions                      
                      
                      ajax: "{{ route('api.contacto',['estimacion'=>$can_id, 'establecimiento_id'=>$establecimiento_id,'tipo'=>$tipo,'servicio_id'=>$servicio_id]) }}",

                      columns: [
                        {data: 'action', name: 'Editar', orderable: false, searchable: false},
                        {data: 'descripcion'},
                        {data: 'stock'},
                        {data: 'necesidad_anual'},
                        {data: 'mes1'},
                        {data: 'mes2'},
                        {data: 'mes3'},
                        {data: 'mes4'},
                        {data: 'mes5'},
                        {data: 'mes6'},
                        {data: 'mes7'},
                        {data: 'mes8'},
                        {data: 'mes9'},
                        {data: 'mes10'},
                        {data: 'mes11'},
                        {data: 'mes12'},
                        {data: 'justificacion'},
                      ],
                    });                          
                            
      function editForm(id) {
        save_method = 'edit';
        $('input[name=_method]').val('PATCH');
        $('#modal-form form')[0].reset();
        $.ajax({
          url: "{{ url('estimacion') }}" + '/' + id + "/edit",
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            $('#modal-form').modal('show');
            $('.modal-title').text('Editar Estimacion');

            $('#id').val(data.id);
            $('#descripcion').val(data.descripcion);
            $('#stock').val(data.stock);
            $('#necesidad_anual').val(data.necesidad_anual);
            $('#mes1').val(data.mes1);
            $('#mes2').val(data.mes2);
            $('#mes3').val(data.mes3);
            $('#mes4').val(data.mes4);
            $('#mes5').val(data.mes5);
            $('#mes6').val(data.mes6);
            $('#mes7').val(data.mes7);
            $('#mes8').val(data.mes8);
            $('#mes9').val(data.mes9);
            $('#mes10').val(data.mes10);
            $('#mes11').val(data.mes11);
            $('#mes12').val(data.mes12);            
          },
          error : function() {
              alert("No hay Datos");
          }
        });
      }

      $(function(){
            $('#modal-form form').validator().on('submit', function (e) {
                
            var total = parseInt($('#mes1').val()) +
            parseInt($('#mes2').val()) +
            parseInt($('#mes3').val()) +
            parseInt($('#mes4').val()) +
            parseInt($('#mes5').val()) +
            parseInt($('#mes6').val()) +
            parseInt($('#mes7').val()) +
            parseInt($('#mes8').val()) +
            parseInt($('#mes9').val()) +
            parseInt($('#mes10').val()) +
            parseInt($('#mes11').val()) +
            parseInt($('#mes12').val());

            var necesidad = parseInt($('#necesidad_anual').val());
        
            if (isNaN(parseInt($('#stock').val())) ||
              isNaN(parseInt($('#necesidad_anual').val())) ||
              isNaN(parseInt($('#mes1').val())) ||
              isNaN(parseInt($('#mes2').val())) ||
              isNaN(parseInt($('#mes3').val())) ||
              isNaN(parseInt($('#mes4').val())) ||
              isNaN(parseInt($('#mes5').val())) ||
              isNaN(parseInt($('#mes6').val())) ||
              isNaN(parseInt($('#mes7').val())) ||
              isNaN(parseInt($('#mes8').val())) ||
              isNaN(parseInt($('#mes9').val())) ||
              isNaN(parseInt($('#mes10').val())) ||
              isNaN(parseInt($('#mes11').val())) ||
              isNaN(parseInt($('#mes12').val())) ||
              isNaN(total)) {
                swal("Solo se admiten números enteros.");
                return false;
            }

            if (parseInt($('#stock').val()) < 0 ||
              parseInt($('#necesidad_anual').val()) < 0 ||
              parseInt($('#mes1').val()) < 0 ||
              parseInt($('#mes2').val()) < 0 ||
              parseInt($('#mes3').val()) < 0 ||
              parseInt($('#mes4').val()) < 0 ||
              parseInt($('#mes5').val()) < 0 ||
              parseInt($('#mes6').val()) < 0 ||
              parseInt($('#mes7').val()) < 0 ||
              parseInt($('#mes8').val()) < 0 ||
              parseInt($('#mes9').val()) < 0 ||
              parseInt($('#mes10').val()) < 0 ||
              parseInt($('#mes11').val()) < 0 ||
              parseInt($('#mes12').val()) < 0) {
              swal("No se admiten menores a 0.");
              return false;
            }
            
            if (parseInt($('#stock').val()) < 0 || isNaN(parseInt($('#stock').val()))) {
              swal("El Stock debe ser mayor o igual 0.");
              return false;
            }
            if (necesidad !== total) {
              swal('La NECESIDAD ANUAL y la suma del prorrateo mensual no pueden ser diferentes.');
              return false;
            }

            if (necesidad === 0) {
              swal('La NECESIDAD ANUAL debe ser mayor a 0.');
              return false;
            }


            if (!e.isDefaultPrevented()){
                // Check if there is an entered value
                var id = $('#id').val();
                url = "{{ url('grabar') . '/' }}" + id;

                $.ajax({
                    url : url,
                    type : "POST",
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

    
    
    
    
    
    
    
    

