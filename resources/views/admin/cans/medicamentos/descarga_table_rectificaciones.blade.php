<div class="row">
    <div class="col-xs-12">        
            <div class="box-body">                
                <!--table id="example" class="table table-responsive table-striped"-->
                <table id="example" class="stripe row-border order-column" cellspacing="0" >  
                    <thead>
                        <tr>
                            <th bgcolor="#D4E6F1" style="text-align:center;" width="50%">Descripción</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad Anterior</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Necesidad Actual</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Stock Anterior</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Stock Actual</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Estado</th>
                            <th bgcolor="#D4E6F1" style="text-align:center;">Requerimiento</th>                            
                        </tr>
                    </thead>                
                    <tbody>
                    @foreach($estimaciones as $key => $estimacion)
                        <tr>
                            <td>{!!$estimacion->descripcion!!}</td>
                            <td style="text-align:center;">{!! $estimacion->necesidad_anual !!}</td>                            
                            <td style="text-align:center;">
                                <?php if($estimacion->necesidad_anual!=$estimacion->necesidad_actual){ ?>
                                    <span style="color:red;"> {!! $estimacion->necesidad_actual !!}</span> 
                                <?php }else{ echo $estimacion->necesidad_actual;}
                                 ?>                                
                            </td>
                            <td style="text-align:center;">{!! $estimacion->stock !!}</td>
                            <td style="text-align:center;">{!! $estimacion->stock_actual !!}</td>
                            <td style="text-align:center;">
                                <?php if($estimacion->estado == 2)
                                            echo "Eliminado";
                                ?>                                
                            </td>
                            <!--td style="text-align:center;"><b><span style="color:blue;">{!! $estimacion->requerimiento_usuario !!}</b></span></td-->
                            <?php $requerimiento_usuario= $estimacion->necesidad_actual - $estimacion->stock_actual; 
                                if($requerimiento_usuario<0)
                                    $requerimiento_usuario=0;
                            ?>
                            <td style="text-align:center;"><b><span style="color:blue;"><?php echo $requerimiento_usuario; ?></b></span></td>                            
                        </tr>
                    @endforeach
                    </tbody>
                    
                </table>
            
            </div>            
             
    </div>    
</div>