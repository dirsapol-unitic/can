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
        <h4 class="pull-left">{!!$nombre_establecimiento!!}</h4>
        <div class="box-header">
           <a data-toggle="tooltip" title="Nueva División!" class="btn btn-app pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('establecimientos.asignar_divisions', [$establecimiento_id]) !!}"><i class="fa fa-database"></i>Nuevo</a>
        </div>
        
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.establecimientos.divisions.table')
            </div>
            <a href="{!! route('establecimientos.index') !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
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



