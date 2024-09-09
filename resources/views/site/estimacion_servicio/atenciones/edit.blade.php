@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Atenciones en el Servicio 
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($establecimiento,['route' => ['estimacion_servicio.update_atenciones', $establecimiento_id], 'method' => 'patch']) !!}
                        <!-- Nivel Field -->
                        <div class="form-group col-sm-4">
                            <label>Numero de Atenciones:</label><br/>
                            <input type="number" tabindex="27"  name="atenciones" id="atenciones"  min=1 class="form-control" value="<?php echo $atenciones; ?>" >
                        </div>
                        <input type="hidden" name="establecimiento_id" id="establecimiento_id" value="<?php echo $establecimiento_id?>">
                        <input type="hidden" name="can_id" id="can_id" value="<?php echo $can_id?>">
                        <input type="hidden" name="tipo" id="tipo" value="<?php echo $tipo?>">
                        <input type="hidden" name="redireccion" id="redireccion" value="<?php echo $redireccion?>">
                        <input type="hidden" name="servicio_id" id="servicio_id" value="<?php echo $servicio_id?>">
                        <!-- Submit Field -->
                        <div class="form-group col-sm-12">
                            <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
                            <a href="{!! route('estimacion_servicio.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
                        </div>
                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection