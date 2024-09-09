@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Atenciones en el Establecimiento 
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['id'=>'frm_atenciones','name'=>'frm_aislamientos','route' => 'farmacias.update_atencion']) !!}

                        <!-- Nivel Field -->
                        <div class="form-group col-sm-4">
                            {!! Form::label('medicina_interna', 'Medicina Interna:') !!}
                            {!! Form::text('medicina_interna', null, ['class' => 'form-control', 'min'=>1]) !!}
                            
                        </div>
                        <div class="form-group col-sm-4">
                            {!! Form::label('odontologia', 'Odontología:') !!}
                            {!! Form::text('odontologia', null, ['class' => 'form-control', 'min'=>0]) !!}
                            
                        </div>
                        <div class="form-group col-sm-4">
                            {!! Form::label('obstetricia', 'Obstetricia:') !!}
                            {!! Form::number('obstetricia', null, ['class' => 'form-control', 'min'=>0]) !!}
                            
                        </div>

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


