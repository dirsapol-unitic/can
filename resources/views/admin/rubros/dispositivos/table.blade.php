<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Nivel</th>
                            <th>Uso</th>
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($petitorios as $key => $petitorio)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $petitorio->codigo_petitorio !!}</td>
                            <td>{!! $petitorio->descripcion !!}</td>
                            <td>{!! $petitorio->descripcion_tipo_dispositivo !!}</td>
                            <td>{!! $petitorio->descripcion_nivel !!}</td>
                            <td>{!! $petitorio->descripcion_tipo_uso !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>                  
                            <th>#</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Tipo Uso</th>
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>