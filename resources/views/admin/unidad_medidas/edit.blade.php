@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Unidad de Medida
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($unidadMedida, ['route' => ['unidadMedidas.update', $unidadMedida->id], 'method' => 'patch']) !!}

                        @include('admin.unidad_medidas.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection