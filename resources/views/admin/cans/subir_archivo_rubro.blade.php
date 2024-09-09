
<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                    <h3 class="box-title">ARCHIVOS</h3>
            </div>
            <div class="box-body">
                <div class="row col-sm-12">
                     {!! Form::model($archivos, ['route' => ['cans.subir_archivo_rubro', $establecimiento_id,$can_id,$establecimiento_id, $servicio_id], 'method' => 'patch','enctype'=>"multipart/form-data"]) !!}
                     <div class="form-group col-sm-12">
                        <!-- Establecimiento Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('descripcion', 'Descripcion del Archivo:') !!}
                            {!! Form::text('descripcion', null, ['id'=>'descripcion','name'=>'descripcion','class' => 'form-control','required'=>'required']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            {!! Form::label('photo', 'Extensiones permitidas(xls,xlsx,doc,docx,jpg,png,jpeg,pdf), archivo Max. 10MB:') !!}                            <input id="photo" name="photo" accept="tipo_de_archivo|image/*|media_type" name="archivo" type="file" value="" required="required" />
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    
                    <div class="form-group col-sm-12">
                            {!! Form::submit('Subir Archivo', ['class' => 'btn btn-success ']) !!}<i class="fa fa-upload"></i>
                    </div>
                     {!! Form::close() !!}
                </div>
            </div>
        </div>        
    </div>    
</div>
