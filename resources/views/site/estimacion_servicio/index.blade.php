@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h3 class="pull-left">CAN - 2020  </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <br/><br/>
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('site.estimacion_servicio.table')
            </div>
        </div>
        <div class="text-center"></div>
    </div>

    

@endsection
@section('scripts')
<script>
  
  $(document).ready( function () {
  
  var table = $('#example').DataTable({
        "responsive": true,
        "order": [[ 0, "asc" ]]        
    });

    
} );


  </script>
@stop

