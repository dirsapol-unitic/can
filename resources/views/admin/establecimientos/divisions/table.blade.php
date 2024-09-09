<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Departamentos</th>
                        </tr>
                    </thead>                
                    <tbody>                        
                    @foreach($divisions as $key => $division)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{!! $division->nombre_division !!}</td>
                            <td>
                                <div class='btn-group'>
                                    <a data-toggle="tooltip" title="Ver Departamentos!" href="{!! route('divisions.ver_departamentos', [$division->id,$establecimiento_id]) !!}" class='btn bg-purple btn-xs'><i class="fa fa-columns"></i></a>
                                </div>
                            </td>                            
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