<div class="form-group col-sm-12"><br/>
<table id="example" class="table table-responsive table-striped">
    <tbody>                        
        <tr>
            <td>{!! Form::label('codigo', 'Codigo:') !!}</td>
            <td><p>{!! $petitorio->codigo_petitorio !!}</p></td>
            <td>{!! Form::label('descripcion', 'Descripcion:') !!}</td>
            <td><p>{!! $petitorio->descripcion !!}</p></td>
        </tr>
        <tr>    
            <td>{!! Form::label('principio_activo', 'Principio Activo:') !!}</td>
            <td><p>{!! $petitorio->principio_activo !!}</p></td>
            <td>{!! Form::label('concentracion', 'Concentracion:') !!}</td>
            <td><p>{!! $petitorio->concentracion !!}</p></td>
        </tr>
        <tr>    
            <td>{!! Form::label('form_farm', 'Form Farm:') !!}</td>
            <td><p>{!! $petitorio->form_farm !!}</p></td>
            <td>{!! Form::label('presentacion', 'Presentacion:') !!}</td>
            <td><p>{!! $petitorio->presentacion !!}</p></td>
        </tr>
        <tr>    
            <td>{!! Form::label('unidad_medida', 'Unidad Medida:') !!}</td>
            <td><p>{!! $petitorio->pet_unidad_medida->descripcion !!}</p></td>
            <td>{!! Form::label('id_nivel', 'Nivel:') !!}</td>
            <td><p>{!! $petitorio->pet_nivel->descripcion !!}</p></td>
        </tr>
        <tr>    
            <td>{!! Form::label('id_tipo_dispositivo', 'Tipo Dispositivo:') !!}</td>
            <td><p>{!! $petitorio->descripcion_tipo_dispositivo !!}</p></td>
            <td>{!! Form::label('id_tipo_uso', 'Tipo Uso:') !!}</td>
            <td><p>{!! $petitorio->pet_tipo_uso->descripcion !!}</p></td>
        </tr>        
    </tbody>
</table>
</div>