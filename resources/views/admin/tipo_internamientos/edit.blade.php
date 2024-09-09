@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tipo Internamiento
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($tipoInternamiento, ['route' => ['tipoInternamientos.update', $tipoInternamiento->id], 'method' => 'patch']) !!}

                        @include('admin.tipo_internamientos.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection