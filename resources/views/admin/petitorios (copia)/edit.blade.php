@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Medicamento / Dispositivo Médico
        </h4>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($petitorio, ['route' => ['petitorios.update', $petitorio->id], 'method' => 'patch']) !!}

                        @include('admin.petitorios.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection