<div class="row">
    <div class="col-xs-6">
        <div class="box box-warning">
            <div class="box-body">
                <div class="box-header">
                    <h3 class="box-title">Archivos</h3>
                </div>
                <br/><br/><br/>
                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripcion del Archivo</th>
                            <th>Fecha Subida</th>
                            <th>Descargar</th>
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
                                {!! $file->created_at !!} 
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
                                <a data-toggle="tooltip" title="clic para descargar" target="_blank" href='{{ asset ("$file->descarga_archivo") }}' class='btn <?php echo $color; ?>'><i class="fa <?php echo $imagen; ?>"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>        
        </div>
    </div>    
    <div class="col-xs-6">
        <div class="box box-danger">
            <div class="box-body">
                <div class="box-header">
                    <h3 class="box-title">Observaciones</h3>
                    <h1 class="pull-right"><a data-toggle="tooltip" title="clic para subir archivo" href="{!! route('cans.subiendo_archivos_can',['establecimiento_id'=>$establecimiento_id,'can_id'=>$can_id]) !!}" class='btn bg-blue'><i class="fa fa-upload"></i> Subir Observaciones </a></h1>
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
    </div>    
</div>
