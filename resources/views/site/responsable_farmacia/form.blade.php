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

        //total_necesidad=total*12;
        //redondeo=Math.round(total_necesidad);

        //document.getElementById('sNecesidad').innerHTML = total_necesidad;
        //$("#necesidad_anual").val(redondeo);

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
<div class="modal" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
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
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                <input style="font-size:12px;" type="number" min="0" step="any" id="cpma" placeholder="CPMA"   name="cpma" required class="form-control cpm" onkeyup="calculo();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Stock Actual</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="stock" placeholder="Stock"  name="stock" required class="form-control">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="necesidad_anual" placeholder="Necesidad Anual"  name="necesidad_anual" required class="form-control necesidad" onkeyup="sumar_necesidad();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error"> 
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Enero</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes1" placeholder="Enero"  name="mes1" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>    
                        <div class="col-md-3 form-group has-error">                     
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Febrero</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes2" placeholder="Febrero"  name="mes2" required   class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Marzo</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes3" placeholder="Marzo"  name="mes3" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Abril</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes4" placeholder="Abril"  name="mes4" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>                        
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Mayo</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes5" placeholder="Mayo"  name="mes5" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Junio</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes6" placeholder="Junio"  name="mes6" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Julio</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes7" placeholder="Julio"  name="mes7" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Agosto</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes8" placeholder="Agosto"  name="mes8" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>                        
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Setiembre</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes9" placeholder="Setiembre"  name="mes9" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>                        
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Octubre</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes10" placeholder="Octubre"  name="mes10" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Noviembre</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes11" placeholder="Noviembre"  name="mes11"  required class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Diciembre</b></span>
                                <input style="font-size:12px;" type="number" min="0" id="mes12" placeholder="Diciembre"  name="mes12"  required class="form-control suma_mes" onkeyup="suma_meses();">
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>                        
                    </div>
                    <!--div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-11 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Jusfificación</b></span>
                                <textarea style="font-size:12px;" rows="3" id="justificacion" placeholder="Jusfificación"  name="justificacion" class="form-control">
                                </textarea>
                                <span style="font-size:9px;" class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div-->
                </div>
                <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group">
                            <b><span>Necesidad: </span> <span id="sNecesidad"></span></b>
                        </div>
                        <div class="col-md-3 form-group">
                            <b><span>Total Prorrateo: </span> <span id="sProrroteo"></span></b>
                        </div>
                        <div class="col-md-5 form-group">
                            <b><span>Falta para completar su Prorrateo: </span> <span id="sFalta"></span></b>
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
