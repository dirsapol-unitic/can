@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Consultorios en el Establecimiento 
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($establecimiento,['route' => ['farmacia.update_atencion', $establecimiento_id], 'method' => 'patch']) !!}
                        <!-- Nivel Field -->
                        <div class="form-group col-sm-4">
                            <label>Medicina Interna:</label><br/>
                            <input type="number" tabindex="27"  name="medicina_interna" id="medicina_interna"  min=0 class="form-control" value="<?php echo $medicina_interna; ?>" >
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Odontologia:</label><br/>
                            <input type="number" tabindex="27"  name="odontologia" id="odontologia"  min=0 class="form-control" value="<?php echo $odontologia; ?>" >
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Obstetricia:</label><br/>
                            <input type="number" tabindex="27"  name="obstetricia" id="obstetricia"  min=0 class="form-control" value="<?php echo $obstetricia; ?>" >
                        </div>
                        <input type="hidden" name="establecimiento_id" id="establecimiento_id" value="<?php echo $establecimiento_id?>">
                        <input type="hidden" name="can_id" id="can_id" value="<?php echo $can_id?>">
                        <input type="hidden" name="tipo" id="tipo" value="<?php echo $tipo?>">
                        <input type="hidden" name="redirigir" id="redirigir" value="<?php echo $redirigir?>">
                        <!-- Submit Field -->
                        <div class="form-group col-sm-12">
                            <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
                            <a href="{!! route('farmacia.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
                        </div>
                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection