@extends('layouts.responsable')

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
                  {!! Form::model($user, ['id'=>'frm_usuario','name'=>'frm_usuario','route' => ['users.update_responsable_servicio', $user->id], 'method' => 'patch']) !!}
                    @include('site.users.fields_responsable_servicio')
                  {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection

