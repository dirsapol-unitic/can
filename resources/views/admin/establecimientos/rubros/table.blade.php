<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($rubros as $key => $rubro)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $rubro->nombre_servicio !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>                  
                            <th>#</th>
                            <th>Descripción</th>
                        </tr>
                    </tfoot>
                </table>
            </div>            
        </div>        
    </div>    
</div>