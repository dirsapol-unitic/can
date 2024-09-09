<div class="row">
    <div class="col-xs-12">
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
    </div> 
</div>
