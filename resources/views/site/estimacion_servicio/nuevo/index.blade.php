@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Registro de Nuevos Productos
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => ['estimacion_servicio.grabar_nuevo_medicamento_dispositivo',$establecimiento_id,$can_id,$servicio_id,$destino],'method' => 'post']) !!}

                        @include('site.estimacion_servicio.nuevo.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('assets/bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>

    <script type="text/javascript">
        $(function(){
            $('form').validator().on('submit', function (e) {
                
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
            
            if (necesidad !== total) {
              swal('La NECESIDAD ANUAL y la suma del prorrateo mensual no pueden ser diferentes.');
              return false;
            }

            if (necesidad === 0) {
              swal('La NECESIDAD ANUAL debe ser mayor a 0.');
              return false;
            }

            if (!e.isDefaultPrevented()){

                $("#form").submit();
            }
                    
            });
        });

    </script>


@stop
