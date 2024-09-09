<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<!-- DNI -->
<div class="form-group col-sm-2">
    {!! Form::label('dni', 'DNI:') !!}
    {!! Form::text('dni', null, ['class' => 'form-control','maxlength'=>'8','required'=>'required']) !!}
     
</div>

<!-- NOMBRES -->
<div class="form-group col-sm-4">
    {!! Form::label('nombres', 'Nombres:') !!}
    {!! Form::text('nombres', null, ['class' => 'form-control','required'=>'required']) !!}
</div>

<!-- APELLIDO PATERNO -->
<div class="form-group col-sm-3">
    {!! Form::label('apellido_paterno', 'Apellido Paterno:') !!}
    {!! Form::text('apellido_paterno', null, ['class' => 'form-control','required'=>'required']) !!}
</div>

<!-- APELLIDO MATERNO -->
<div class="form-group col-sm-3">
    {!! Form::label('apellido_materno', 'Apellido Materno:') !!}
    {!! Form::text('apellido_materno', null, ['class' => 'form-control','required'=>'required']) !!}
</div>

<!-- DNI -->
<div class="form-group col-sm-2">
    {!! Form::label('cip', 'CIP:') !!}
    {!! Form::text('cip', null, ['class' => 'form-control','maxlength'=>'6','required'=>'required']) !!}
     
</div>

<!-- EMAIL -->
<div class="form-group col-sm-4">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::email('email', null, ['class' => 'form-control','required'=>'required']) !!}
</div>

<!-- GRADO -->
<div class="form-group col-sm-3">
    {!! Form::label('grado', 'Grado:') !!}
    {!! Form::select('grado_id', [''=>'--- Seleccione ---']+$grado, null, ['class' => 'form-control select2','required'=>'required']) !!}
</div>

<!-- TELEFONO -->
<div class="form-group col-sm-3">
    {!! Form::label('telefono', 'Telefono:') !!}
    {!! Form::text('telefono', null, ['class' => 'form-control','maxlength'=>'9','required'=>'required']) !!}
</div>

<!-- ESTABLECIMIENTO -->
<div class="form-group col-sm-4">
    {!! Form::label('establecimiento', 'Establecimiento:') !!}
    {!! Form::select('establecimiento_id',[''=>'--- Seleccione ---']+$establecimiento,null,['class'=>'form-control','id'=>'establecimiento_id','required'=>'required']) !!}          
</div>

<!-- DIVISIÓN -->
@if ($tipo==1) 
    <div class="form-group col-sm-4">
        {!! Form::label('division', 'División:') !!}
        {!! Form::select('division_id',[''=>'--- Seleccione ---'],null,['class'=>'form-control','id'=>'division_id']) !!}     
    </div>
@else
    @if ($division_id>0)
        <div class="form-group col-sm-4">
            {!! Form::label('division', 'División:') !!}
            {!! Form::select('division_id',[''=>'--- Seleccione ---']+$division,null,['class'=>'form-control','id'=>'division_id']) !!}      
        </div>
    @else
        <div class="form-group col-sm-4">
            {!! Form::label('division', 'División:') !!}
            {!! Form::select('division_id',[''=>'--- Seleccione ---'],null,['class'=>'form-control','id'=>'division_id']) !!}      
        </div>
    @endif
@endif

<!-- DEPARTAMENTO -->
@if ($tipo==1)
    <div class="form-group col-sm-4">
        {!! Form::label('unidad', 'Departamento:') !!}
        {!! Form::select('unidad_id',[''=>'--- Seleccione ---'],null,['class'=>'form-control','id'=>'unidad_id']) !!}     
    </div>
@else
    @if ($unidad_id>0)
        <div class="form-group col-sm-4">
            {!! Form::label('unidad', 'Departamento:') !!}
            {!! Form::select('unidad_id',[''=>'--- Seleccione ---']+$unidad,null,['class'=>'form-control','id'=>'unidad_id']) !!}      
        </div>
    @else
        <div class="form-group col-sm-4">
            {!! Form::label('unidad', 'Departamento:') !!}
            {!! Form::select('unidad_id',[''=>'--- Seleccione ---'],null,['class'=>'form-control','id'=>'unidad_id']) !!}      
        </div>
    @endif
@endif

<!-- SERVICIO -->
@if ($tipo==1)
    <div class="form-group col-sm-4">
        {!! Form::label('servicio', 'Servicio/Distribución:') !!}
        {!! Form::select('servicio_id',[''=>'--- Seleccione ---'],null,['class'=>'form-control','id'=>'servicio_id']) !!}
    </div>
@else
    @if ($servicio_id>0)
    <div class="form-group col-sm-4">
        {!! Form::label('servicio', 'Servicio/Distribución:') !!}
        {!! Form::select('servicio_id',[''=>'--- Seleccione ---']+$servicio,null,['class'=>'form-control','id'=>'servicio_id']) !!}
    </div>
    @else
        <div class="form-group col-sm-4">
            {!! Form::label('servicio', 'Servicio/Distribución:') !!}
            {!! Form::select('servicio_id',[''=>'--- Seleccione ---'],null,['class'=>'form-control','id'=>'servicio_id']) !!}      
        </div>
    @endif
@endif

<!-- CONTRASEÑA -->
@if ($tipo==1)
<div class="form-group col-sm-4">
    {!! Form::label('password', 'Contraseña:') !!}
    {!! Form::password('password',['class' => 'form-control ','required'=>'required']) !!}
</div>
@else
<div class="form-group col-sm-4">
    {!! Form::label('password', 'Contraseña:') !!}
    {!! Form::password('password',['class' => 'form-control']) !!}
</div>
@endif
<!-- Establecimiento Field -->
<div class="form-group col-sm-6">
    
    
    <input 
        
        type="radio"  
        value="1" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==1 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('administrador', 'Administrador:') !!}
    <br>
    <input 
        
        type="radio" 
        value="2" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==2 ? 'checked' : '' }}
            name="rol">
        @endif    
    {!! Form::label('registrar_can', 'Llenar CAN:') !!}
    <br>
    <input 
        
        type="radio" 
        value="3" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==3 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('registrar_fema', 'Llenar FEMA:') !!}
    <br>
    <input 
        
        type="radio" 
        value="4" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==4 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable', 'Responsable de Farmacia:') !!}
</div>
<div class="form-group col-sm-6">
    <input 
        
        type="radio" 
        value="5" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==5 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('responsable_almacen', 'Responsable de Almacén:') !!}
    <br>
    <input 
        
        type="radio" 
        value="6" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==6 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('visualiza_ipress', 'Visualiza IPRESS:') !!}
    <br>
    <input 
        
        type="radio" 
        value="7" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==7 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('visualiza_red_region', 'Visualiza RED/REGION:') !!}
    <br>
    <input 
        
        type="radio" 
        value="8" 
        @if ($tipo==1)
            name="rol">
        @else
            {{ $user->rol==8 ? 'checked' : '' }}
            name="rol">
        @endif
    {!! Form::label('visualiza_nacional', 'Visualiza NACIONAL:') !!}        
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
    <a href="{!! route('users.index') !!}" class="btn btn-default">Cancelar</a>
</div>
<script type="text/javascript">
  $("#establecimiento_id").change(event => {
  $.get(`midivision/${event.target.value}`, function(res, sta){
    if (event.target.value==1 || event.target.value==2 || event.target.value==3 || event.target.value==30 || event.target.value==69){
        var x=1;
        $("#division_id").empty();
        $("#unidad_id").empty();
        $("#servicio_id").empty();
        res.forEach(element => {
          if(x==1){
            $("#division_id").append(`<option value='0'> --- Seleccione --- </option>`);  
            $("#division_id").append(`<option value=${element.id}> ${element.nombre_division} </option>`);
          }
          else
          {
            $("#division_id").append(`<option value=${element.id}> ${element.nombre_division} </option>`);    
          }  
          x++;

        });

    }
    else
    {
        $("#division_id").empty();
        $("#unidad_id").empty();
        $("#servicio_id").empty();
        var x=1;
        res.forEach(element => {
            $("#servicio_id").append(`<option value=${element.rubro_id}> ${element.descripcion} </option>`);
          x++;

        });

    }    
    
    
  });
});

$("#division_id").change(event => {
  $.get(`miunidad/${event.target.value}`, function(res, sta){
    var x=1;
    $("#unidad_id").empty();
    $("#servicio_id").empty();
    
    if (event.target.value>0){

        res.forEach(element => {
          if(x==1){
            $("#unidad_id").append(`<option value='0'> --- Seleccione --- </option>`);  
            $("#unidad_id").append(`<option value=${element.id}> ${element.nombre_unidad} </option>`);
            
            }
            else
            {
                $("#unidad_id").append(`<option value=${element.id}> ${element.nombre_unidad} </option>`);
            }    
        x++;

        });
    }    
  });
});

$("#unidad_id").change(event => {
  $.get(`miservicio/${event.target.value}`, function(res, sta){
    $("#servicio_id").empty();
    res.forEach(element => {
       $("#servicio_id").append(`<option value=${element.servicio_id}> ${element.nombre_servicio} </option>`); 
    });
  });
});
</script>
