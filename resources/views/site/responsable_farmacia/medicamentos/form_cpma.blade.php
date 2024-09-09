<script type="text/javascript">
    
    function calculacpm() {
        document.getElementById('cpma_1').value = document.getElementById('cpma').value;
        document.getElementById('cpma_2').value = document.getElementById('cpma').value;
    }

     
</script>
<div class="modal" id="modal-form-cpma" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_contact" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"> &times; </span>
                    </button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="box-body">
                    <input type="hidden" id="id" name="id">
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-11 form-group has-error"> 
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Producto</b></span>
                                <textarea style="font-size:12px;" rows="2" id="descripcion" placeholder="descripcion"  name="descripcion"  class="form-control">                                    
                                </textarea>
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table id="estimacion" class="table table-striped">
                            <thead>
                                <tr><th colspan="5" bgcolor="#D4E6F1" style="text-align:center;">PRORRATEO AÑO 1</th><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" type="number" min="0" step="any" id="cpma" placeholder="CPMA"   name="cpma" required class="form-control cpm" onkeyup="calculacpm();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" type="number" min="0" id="necesidad_anual" placeholder="Necesidad Anual"  name="necesidad_anual" required  class="form-control necesidad">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td colspan="5" bgcolor="#00a65a" style="color:#FCFBFB; text-align:center;"><b>PRORRATEO AÑO 2</b></td><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" type="number" min="0" step="any" id="cpma_1" placeholder="CPMA"   name="cpma_1" required class="form-control">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" type="number" min="0" id="necesidad_anual_1" placeholder="Necesidad Anual"  name="necesidad_anual_1" required class="form-control necesidad1">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td colspan="5" bgcolor="#AC1C51" style="color:#FCFBFB; text-align:center;"><b> PRORRATEO AÑO 3</b></td><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" type="number" min="0" step="any" id="cpma_2" placeholder="CPMA"   name="cpma_2" required class="form-control">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" type="number" min="0" id="necesidad_anual_2" placeholder="Necesidad Anual"  name="necesidad_anual_2" required class="form-control necesidad2" >
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    
                    
                        

                   
                </div>
                <div class="modal-footer">
                    <button id="guardar" class="btn btn-primary btn-save">Guardar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    

</script>
