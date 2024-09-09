<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
    
        <div><br/>
            Los archivos que se subiran deberan estar firmados y sellados por los responsables que han participado en el CAN, segun el RUBRO asignado por la IPRESS
            <br/><br/>
            <div class="box-header">
                <h3 class="box-title">ARCHIVOS</h3>
            </div>
            <div class="box-body">
                <div class="row col-sm-12">
                     {!! Form::model($archivos, ['route' => ['farmacia_servicios.subir_archivo', $establecimiento_id,$can_id], 'method' => 'patch','enctype'=>"multipart/form-data"]) !!}
                     <div class="form-group col-sm-12">                
                        <div class="form-group col-sm-6">
                            {!! Form::label('descripcion', 'Descripcion del Archivo:') !!}
                            {!! Form::text('descripcion', null, ['id'=>'descripcion','name'=>'descripcion','class' => 'form-control','required'=>'required']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            {!! Form::label('photo', 'Extensiones permitidas(xls,xlsx,doc,docx,jpg,png,jpeg,pdf), archivo Max. 10MB:') !!}
                            <input id="photo" name="photo" accept="tipo_de_archivo|image/*|media_type" name="archivo" type="file" value="" required="required" />
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                            {!! Form::submit('Subir Archivo', ['class' => 'btn btn-success']) !!}
                    </div>
                     {!! Form::close() !!}
                </div>
            </div>
        </div>        
        </div>
        </div>
    </div>    
</div>
<div class="row">
    <div class="col-xs-6">
        <div class="box box-warning">
            <div class="box-body">
            <div class="box-header">
                <h3 class="box-title">Archivos</h3>
            </div>
            <div class="box-body">
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripcion del Archivo</th>
                            <th>Descargar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($archivos as $key => $file)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                {!! $file->descripcion_archivo !!} 
                            </td>
                            <td>
                                <?php 
                                    if($file->extension_archivo=='pdf'){
                                        $imagen='fa fa-file-pdf-o';
                                        $color='bg-red';
                                    }else
                                    {   if($file->extension_archivo=='xls' || $file->extension_archivo=='xlsx'){
                                            $imagen='fa-file-excel-o';
                                            $color='bg-green';
                                        }
                                        else
                                        {
                                            if($file->extension_archivo=='doc' || $file->extension_archivo=='docx'){
                                                $imagen='fa-file-word-o';
                                                $color='bg-blue';
                                            }
                                            else
                                            {
                                                if($file->extension_archivo=='jpg' || $file->extension_archivo=='gif' || $file->extension_archivo=='jpeg' || $file->extension_archivo=='png' || $file->extension_archivo=='svg' || $file->extension_archivo=='eps' || $file->extension_archivo=='psd' ){
                                                    $imagen='fa-file-image-o';
                                                    $color='bg-purple';
                                                }
                                                else
                                                {
                                                    $imagen='fa-archive';
                                                    $color='bg-orange';
                                                }
                                            }
                                        }
                                    }
                                ?>
                                <a target="_blank" href='{{ asset ("$file->descarga_archivo") }}' class='btn <?php echo $color; ?>'><i class="fa <?php echo $imagen; ?>"></i></a>
                            </td>
                            <?php $x=0;?>
                            <td>
                                 @if($x==0)
                                 <a href="{!! route('farmacia_servicios.eliminar_archivo',['id'=>$file->id]) !!}" class='btn bg-red'><i class="fa fa-trash""></i></a>
                                 @else
                                 <a disabled="disabled" href="#" class='btn bg-red'><i class="fa fa-trash""></i></a>
                                 @endif
                            </td>                         
                             
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>            
        </div>        
    </div>   
    <!--div class="col-xs-6">
        <div class="box box-danger">
            <div class="box-body">
                <div class="box-header">
                    <h3 class="box-title">Observaciones</h3>
                </div>
                <div class="box-body">
                    <table id="example" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descripcion de la Observacion</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($observaciones as $key => $observ)
                            <tr>
                                <td>{{$key+1}}</td>                            
                                <td>{!! $observ->descripcion_archivo !!}</td>
                                <td>
                                    <?php 
                                        if($observ->extension_archivo=='pdf'){
                                            $imagen='fa fa-file-pdf-o';
                                            $color='bg-red';
                                        }else
                                        {   if($observ->extension_archivo=='xls' || $observ->extension_archivo=='xlsx'){
                                                $imagen='fa-file-excel-o';
                                                $color='bg-green';
                                            }
                                            else
                                            {
                                                if($observ->extension_archivo=='doc' || $observ->extension_archivo=='docx'){
                                                    $imagen='fa-file-word-o';
                                                    $color='bg-blue';
                                                }
                                                else
                                                {
                                                    if($observ->extension_archivo=='jpg' || $observ->extension_archivo=='gif' || $observ->extension_archivo=='jpeg' || $observ->extension_archivo=='png' || $observ->extension_archivo=='svg' || $observ->extension_archivo=='eps' || $observ->extension_archivo=='psd' ){
                                                        $imagen='fa-file-image-o';
                                                        $color='bg-purple';
                                                    }
                                                    else
                                                    {
                                                        $imagen='fa-archive';
                                                        $color='bg-orange';      
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                    <a data-toggle="tooltip" title="<?php echo $observ->descripcion_archivo?>" target="_blank" href='{{ asset ("$observ->descarga_archivo") }}' class='btn <?php echo $color; ?>'><i class="fa <?php echo $imagen; ?>"></i></a>
                                </td>   
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>            
            </div>        
        </div>    
    </div--> 
</div>
