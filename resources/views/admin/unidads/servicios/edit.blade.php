@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4 class="pull-left">{!!$nombre_establecimiento!!} - {!!$nombre_division!!} - {!!$nombre_unidad!!}</h4>
        <br/>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">            
               <div class="row">
                   {!! Form::model($unidad, ['route' => ['unidads.update', $servicio,$unidad_id,$division_id,$establecimiento_id], 'method' => 'patch']) !!}

                        @include('admin.unidads.servicios.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection