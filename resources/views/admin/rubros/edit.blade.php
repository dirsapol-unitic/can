@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Rubro
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($rubro, ['route' => ['rubros.update', $rubro->id], 'method' => 'patch']) !!}

                        @include('admin.rubros.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection