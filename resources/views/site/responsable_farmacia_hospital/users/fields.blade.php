<div class="form-group col-sm-12">
    <!-- Establecimiento Field -->
    <div class="form-group col-sm-9">
        {!! Form::label('password_actual', 'Contraseña Actual:') !!}
        {!! Form::password('mypassword',['class' => 'form-control','required'=>'required']) !!}
    </div>
</div>
<div class="form-group col-sm-12">
    <!-- Establecimiento Field -->
    <div class="form-group col-sm-9">
        {!! Form::label('nuevo_password', 'Contraseña Nueva:') !!}
        {!! Form::password('password',['class' => 'form-control','required'=>'required']) !!}
    </div>
</div>
<div class="form-group col-sm-12">
    <!-- Establecimiento Field -->
    <div class="form-group col-sm-9">
        {!! Form::label('repetir_password', 'Repetir Contraseña:') !!}
        {!! Form::password('password_confirmation',['class' => 'form-control','required'=>'required']) !!}
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Actualizar', ['class' => 'btn btn-success']) !!}
    <a href="{!! route('home.index') !!}" class="btn btn-default">Cancelar</a>
</div>

