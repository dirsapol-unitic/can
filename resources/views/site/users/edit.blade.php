@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h4>
            Responsables
        </h4>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($user, ['id'=>'frm_usuario','name'=>'frm_usuario','route' => ['users.update_responsable', $user->id], 'method' => 'patch']) !!}

                        @include('site.users.fields_responsable_edit')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection
