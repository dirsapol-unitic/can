@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Usuarios
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="row col-sm-5">
          <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">CAMBIAR CONTRASEÑA</h3>
                </div>

               <div class="box-body">
                   <div class="row col-sm-12">
                       {!! Form::model($user, ['route' => ['users.update_clave', $user->id], 'method' => 'patch']) !!}
                            @include('site.users.fields')
                       {!! Form::close() !!}
                   </div>
               </div>
          </div>
        </div>
        <div class="row col-sm-1">
        </div>
        <div class="row col-sm-5">
          <div class="box box-primary">
              <div class="box-header">
                    <h3 class="box-title">CAMBIAR FOTO</h3>
              </div>
             <div class="box-body">
                 <div class="row col-sm-12">
                     {!! Form::model($user, ['route' => ['users.subir_foto', $user->id], 'method' => 'patch','enctype'=>"multipart/form-data"]) !!}
                          @include('site.users.fields_foto')
                     {!! Form::close() !!}

                 </div>
             </div>
         </div>
        </div>

   </div>
@endsection
