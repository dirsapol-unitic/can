@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tipo Uso
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($tipoUso, ['route' => ['tipoUsos.update', $tipoUso->id], 'method' => 'patch']) !!}

                        @include('admin.tipo_usos.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection