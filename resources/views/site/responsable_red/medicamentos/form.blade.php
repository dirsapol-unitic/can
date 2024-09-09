<div class="modal" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_contact" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"> &times; </span>
                    </button>
                    <h3 class="modal-title"></h3>
                </div>
                <div class="box-body">
                    <input type="hidden" id="id" name="id">
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-11 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Producto</b></span>
                                <textarea rows="2" id="descripcion" placeholder="descripcion"  name="descripcion" disabled class="form-control">                                    
                                </textarea>
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-5 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Stock Actual</b></span>
                                <input type="number" min="0" id="stock" placeholder="Stock"  name="stock" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-5 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon"><b> Necesidad Anual</b></span>
                                <input type="number" min="0" id="necesidad_anual" placeholder="Necesidad Anual"  name="necesidad_anual" required class="form-control">
                                <span class="help-block with-errors"></span>
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
                                <span class="input-group-addon"><b> Enero</b></span>
                                <input type="number" min="0" id="mes1" placeholder="Enero"  name="mes1" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>    
                        <div class="col-md-3 form-group has-error">                     
                            <div class="input-group">
                                <span class="input-group-addon"><b> Febrero</b></span>
                                <input type="number" min="0" id="mes2" placeholder="Febrero"  name="mes2" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon"><b> Marzo</b></span>
                                <input type="number" min="0" id="mes3" placeholder="Marzo"  name="mes3" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Abril</b></span>
                                <input type="number" min="0" id="mes4" placeholder="Abril"  name="mes4" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>                        
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon"><b> Mayo</b></span>
                                <input type="number" min="0" id="mes5" placeholder="Mayo"  name="mes5" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Junio</b></span>
                                <input type="number" min="0" id="mes6" placeholder="Junio"  name="mes6" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon"><b> Julio</b></span>
                                <input type="number" min="0" id="mes7" placeholder="Julio"  name="mes7" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Agosto</b></span>
                                <input type="number" min="0" id="mes8" placeholder="Agosto"  name="mes8" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>                        
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon"><b> Setiembre</b></span>
                                <input type="number" min="0" id="mes9" placeholder="Setiembre"  name="mes9" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>                        
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Octubre</b></span>
                                <input type="number" min="0" id="mes10" placeholder="Octubre"  name="mes10" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">
                            <div class="input-group">
                                <span class="input-group-addon"><b> Noviembre</b></span>
                                <input type="number" min="0" id="mes11" placeholder="Noviembre"  name="mes11" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Diciembre</b></span>
                                <input type="number" min="0" id="mes12" placeholder="Diciembre"  name="mes12" required class="form-control">
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>                        
                    </div>
                    
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-11 form-group has-error">                        
                            <div class="input-group">
                                <span class="input-group-addon"><b> Jusfificación</b></span>
                                <textarea rows="3" id="justificacion" placeholder="Jusfificación"  name="justificacion" class="form-control">
                                </textarea>
                                <span class="help-block with-errors"></span>
                            </div>                           
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
