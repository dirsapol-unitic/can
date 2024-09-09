<div class="row">
    <div class="col-xs-12">
        <div class="box box-danger">
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
    </div>    
</div>
