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
                    {!! Form::open(['route' => ['estimacion.grabar_nuevo_medicamento_dispositivo',$establecimiento_id,$can_id,$servicio_id,$destino],'method' => 'post']) !!}

                        @include('site.estimacions.nuevo.fields')

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
        
            if (isNaN(parseInt($('#cpma').val())) ||isNaN(parseInt($('#stock').val())) ||
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
                swal("Solo se admiten números enteros."+total);
                return false;
            }

            if (parseInt($('#cpma').val()) < 0 ||parseInt($('#stock').val()) < 0 ||
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
            
            var cpma = parseInt($('#cpma').val());

            if (cpma === 0) {
              swal("El CPMA debe ser mayor a 0.");
              return false;
            }

            if (parseInt($('#stock').val()) < 0 || isNaN(parseInt($('#stock').val()))) {
              swal("El Stock debe ser mayor o igual 0.");
              return false;
            }
            
            if (necesidad === 0) {
              swal('La NECESIDAD ANUAL debe ser mayor a 0.');
              return false;
            }

            

            if (necesidad !== total) {
              swal('La NECESIDAD ANUAL y la suma del prorrateo mensual no pueden ser diferentes.');
              return false;
            }

            total_necesidad=cpma*12; //42
            total_necesidad=total_necesidad+1;
            if (total_necesidad<=necesidad) {
              swal("La Necesidad anual no debe ser mayor 14 veces mas de su cpma.");
              return false;
            }

            if (!e.isDefaultPrevented()){

                $("#form").submit();
            }
                    
            });
        });

    </script>


@stop
