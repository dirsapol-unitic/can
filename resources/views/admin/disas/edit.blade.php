@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h1>
            DISA
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($disa, ['route' => ['disas.update', $disa->id], 'method' => 'patch']) !!}
                        @include('admin.disas.fields')
                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection