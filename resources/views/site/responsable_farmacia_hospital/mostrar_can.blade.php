<div class="row">
    <div class="col-xs-12">
        <div>
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            <div class="box-body">
                <?php $x=1; ?>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha del CAN</th>
                            <th>Visualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($cans as $can)
                        <tr>
                            <td>{{$x++}}</td>
                            <td>CAN {!! $can->desc_mes !!} del {!! $can->ano !!}</td>
                            <td>
                                <div class='btn-group'>
                                    <a href="{!! route('farmacia_servicios.listar_servicios', [$can->id]) !!}" class='btn btn-info btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>        
    </div>    
</div>



