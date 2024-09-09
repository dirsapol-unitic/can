@extends('layouts.app')
@section('css')
    <style type="text/css">
        th, td { white-space: nowrap; font-size: 11px;}
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }        
    </style>
@stop
@section('content')
    <section class="content-header">
        <h4 class="pull-left">Asignar Medicamentos - {!!$nombre_establecimiento!!}</h4>
        <h4 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('establecimientos.asignar_medicamentos', [$establecimiento_id]) !!}">Asignar Medicamentos <i class="glyphicon glyphicon-check"></i></a>
        </h4>
        <br/><br/>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.establecimientos.medicamentos.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
$('#example1').dataTable( {
  "pageLength": 1000
} );
</script>

@stop



