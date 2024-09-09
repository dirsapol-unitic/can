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
        <h4 class="pull-left">{!!$nombre_establecimiento!!} - {!!$nombre_division!!}</h4>
        <div class="box-header">
           <a data-toggle="tooltip" title="Nueva Departamento!" class="btn btn-app pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('divisions.asignar_departamentos', [$division_id,$establecimiento_id]) !!}"><i class="fa fa-columns"></i>Nuevo</a>
        </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.divisions.unidads.table')
            </div>
            <a href="{!! route('establecimientos.ver_division',['establecimiento_id'=>$establecimiento_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
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



