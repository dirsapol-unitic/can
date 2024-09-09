@extends('layouts.app')
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
        th, td { font-size: 14px;}
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
            <a href="{!! route('estimacion_servicio.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
            <br/><br/>
            <div class="clearfix"></div>
            <div class="box box">
                <div class="box-body">
                    <h3 class="pull-left">Listado de Productos</h3>
                    @if($nivel>1)
                        @if($cerrado==1)                    
                        <h1 class="pull-right">
                            @if ($diferencia>0)
                                <a class="btn btn-app" href="{!! route('estimacion_servicio.nuevo_medicamento_dispositivo',[$can_id,$establecimiento_id,$tipo]) !!}">
                                    <span class="badge bg-yellow">{!!$diferencia!!}</span>
                                <i class="fa fa-file"></i> Nuevo
                            </a>                        
                            @endif
                            <!--a class="btn btn-app" href="{!! route('estimacion_servicio.exportEstimacionDataPrevio',[$can_id,$establecimiento_id,$tipo,'xlsx']) !!}">
                                <i class="fa fa-file-excel-o"></i> Descargar Avance
                            </a-->
                            
                            <a class="btn btn-app" onclick="CerrarPetitorio({!!$can_id!!},{!!$establecimiento_id!!},{!!$tipo!!})"> <i class="fa fa-lock"></i>Cerrar Petitorio</a>       
                        </h1>
                        @endif
                    @endif 
                    <div class="row">
                        <div class="col-xs-12">        
                            <div class="box-body">
                                <table id="estimacion" class="table table-striped">
                                    <thead>
                                        <tr>
                                            
                                            <th rowspan="2" style="text-align:center;">Editar/Borrar</th>
                                            <th rowspan="2" style="text-align:center;">Descripción del Producto</th>
                                            <!--th rowspan="2" style="text-align:center;">CPMA</th-->
                                            <th rowspan="2" style="text-align:center;">Necesidad Anual</th>
                                            <th colspan="12" bgcolor="#D4E6F1" style="text-align:center;">
                                            PRORRATEO</th>
                                            <!--th rowspan="2" style="text-align:center;">Justificación</th-->
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
                    @include('site.estimacion_servicio.medicamentos.form_anterior')
                </div>
            </div>
            <div>
            <a href="{!! route('estimacion_servicio.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
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
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('assets/bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>
    <script type="text/javascript">
      var table = $('#estimacion').DataTable({
                      "order": [[ 1, "asc" ]],
                      processing: true,                      
                        "responsive": true,
                        paging: false,
                        "scrollY":        "400px",
                        "scrollX":        true,
                        "scrollCollapse": true,
                        
                        fixedColumns:   {
                                leftColumns: 3
                        }, //$estimacions                      
                      
                      ajax: "{{ route('api.contact',['estimacion'=>$can_id, 'establecimiento_id'=>$establecimiento_id,'tipo'=>$tipo,'cerrado'=>$cerrado]) }}",

                      columns: [
                        {data: 'action', name: 'Editar', orderable: false, searchable: false},
                        {data: 'descripcion'},
                        //{data: 'cpma'},         
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
                        
                      ],
                    });                          
                            
      function editForm(id) {
        save_method = 'edit';
        $('input[name=_method]').val('PATCH');
        $('#modal-form form')[0].reset();
        $.ajax({
          url: "{{ url('estimacion_servicio') }}" + '/' + id + "/edit",
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            $('#modal-form').modal('show');
            $('.modal-title').text('Editar Estimacion');
            $('#id').val(data.id);
            $('#descripcion').val(data.descripcion);            
            //$('#cpma').val(data.cpma);
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
            suma=data.mes1+data.mes2+data.mes3+data.mes4+data.mes5+data.mes6+data.mes7+data.mes8+data.mes9+data.mes10+data.mes11+data.mes12;   
            $('#sProrroteo').html(suma);
            necesidad=data.necesidad_anual-suma;
            $('#sNecesidad').html(data.necesidad_anual);
            $('#sFalta').html(necesidad);

          },
          error : function() {
              alert("No hay Datos");
          }
        });
      }

      function deleteData(id){
          var csrf_token = $('meta[name="csrf-token"]').attr('content');
          swal({
              title: 'Esta usted seguro que desea hacer esto?',
              text: "Ya no podrá revertir esta acción!",
              type: 'warning',
              showCancelButton: true,
              cancelButtonColor: '#d33',
              confirmButtonColor: '#3085d6',
              confirmButtonText: 'Si, eliminar!'
          }).then(function () {
              $.ajax({
                  url : "{{ url('estimacion_servicio/eliminar')}}" + '/' + id,
                  type : "POST",
                  data : {'_method' : 'DELETE', '_token' : csrf_token},
                  success : function(data) {
                      table.ajax.reload();
                      swal({
                          title: 'Excelente!',
                          text: data.message,
                          type: 'success',
                          timer: '1500'
                      })
                  },
                  error : function () {
                      swal({
                          title: 'Oops...',
                          text: data.message,
                          type: 'error',
                          timer: '1500'
                      })
                  }
              });
          });
        }

      function CerrarPetitorio(can_id,establecimiento_id,tipo){
          
          swal({
              title: 'Esta usted seguro de cerrar su petitorio?',
              text: "Ya no podrá editar/eliminar productos!",
              type: 'warning',
              showCancelButton: true,
              cancelButtonColor: '#d33',
              confirmButtonColor: '#3085d6',
              confirmButtonText: 'Si, cerrar!'
          }).then(function () {
               window.location.href = '/servicio/estimacion/cerrar_medicamento/' + can_id + '/' + establecimiento_id + '/'+ tipo;
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
            //var cpma = $('#cpma').val();
        
            if (isNaN(parseInt($('#necesidad_anual').val())) ||
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

            if (parseInt($('#necesidad_anual').val()) < 0 ||
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
            
            /*if (necesidad>cpma*12) {
              swal('La NECESIDAD No debe de ser mayor al CPMA * 12.');
              return false;
            }
            */
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
                url = "{{ url('servicio/grabar') . '/' }}" + id;

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
    <script src='{{ asset ("/js/dataTables.fixedColumns.min.js") }}'></script>
@stop