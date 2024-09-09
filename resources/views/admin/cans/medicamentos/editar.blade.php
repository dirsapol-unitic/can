@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Editar la Programación del CAN
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($producto, ['route' => ['cans.update_producto', $id, $establecimiento_id, $precio, $can_id,$destino,$servicio_id], 'method' => 'patch']) !!}

                        @include('admin.cans.medicamentos.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection
