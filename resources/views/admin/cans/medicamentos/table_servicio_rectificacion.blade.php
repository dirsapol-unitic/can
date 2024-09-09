<div class="row">
    <div class="col-xs-12">
        
            <div class="box-body">
                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;" width="80%">Descripción</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad Anterior</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad Actual</th>
                        </tr>
                    </thead>                
                    <tbody>
                    @foreach($estimaciones_servicios as $key => $estimacion_servicio)
                        <tr>
                            <td>{!!$estimacion_servicio->descripcion!!}</td>
                            <td style="text-align:center;">{!! $estimacion_servicio->necesidad_anual !!}</td>                            
                            <td style="text-align:center;">
                                <?php if($estimacion_servicio->necesidad_anual!=$estimacion_servicio->necesidad_actual){ ?>
                                    <span style="color:red;"> {!! $estimacion_servicio->necesidad_actual !!}</span> 
                                <?php }else{ echo $estimacion_servicio->necesidad_actual;}
                                 ?>                                
                            </td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
             
    </div>    
</div>
