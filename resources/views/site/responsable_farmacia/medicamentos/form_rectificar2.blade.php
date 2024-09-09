<script type="text/javascript">
    
    function calculo() {
        var total = 0;
        
        $(".cpm").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total += 0;
            } else {
              total += parseFloat($(this).val());
            }
        });

        total_necesidad=total*12;
        redondeo=Math.round(total_necesidad);

        //document.getElementById('sNecesidad').innerHTML = total_necesidad;
        $("#necesidad_anual").val(redondeo);

        document.getElementById('sNecesidad').innerHTML = redondeo;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    function suma_meses() {
        var total1 = 0;
        
        
        $(".suma_mes").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo').innerHTML = total1;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    function sumar_necesidad() {
        var total1 = 0;
        var total2 = 0;
        
        $(".suma_mes").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        $(".necesidad").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total2 += 0;
            } else {
              total2 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo').innerHTML = total1;
        document.getElementById('sNecesidad').innerHTML = total2;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    
</script>
<div class="modal" id="modal-form-rectificar2" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
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
                                <textarea style="font-size:12px;" rows="2" id="descripcion" placeholder="descripcion"  name="descripcion" disabled class="form-control">                                    
                                </textarea>
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-4 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Necesidad Actual</b></span>
                                <input style="font-size:12px;" type="number" min="0" disabled id="necesidad_actual" placeholder="Nueva Necesidad Anual" name="necesidad_actual" required class="form-control necesidad">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-4 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Stock Actual</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="stock_actual" placeholder="Stock Actual"  name="stock_actual" required class="form-control">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>                        
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-4 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Necesidad Anual</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="necesidad_anual" placeholder="Necesidad Anual" disabled name="necesidad_anual" required class="form-control necesidad" onkeyup="sumar_necesidad();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-4 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Stock Anterior</b></span>
                                <input style="font-size:12px;" type="number" disabled min="0" id="stock" placeholder="Stock"  name="stock" required class="form-control">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
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
