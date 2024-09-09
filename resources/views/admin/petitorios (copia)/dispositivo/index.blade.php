@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4 class="pull-left">Medicamentos / Dispositivo Médico</h4>        
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box">
            <div class="box-body">
                    @include('admin.petitorios.dispositivo.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset ("/datatable/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset ("/datatable/dataTables.bootstrap.min.js") }}"></script>
    <script>
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>
@stop

