<div class="row">
    <div class="col-xs-12">
            <!-- Mes Field -->
        <div class="col-xs-3 form-group">
            {!! Form::label('mes', 'Mes:') !!}
            {!! $can->mes !!}
        </div>
        <!-- Ano Field -->
        <div class="col-xs-3 form-group">
            {!! Form::label('ano', 'Año:') !!}
            {!! $can->ano !!}
        </div> 
        <div class="col-xs-6 form-group">
            {!! Form::label('establecimiento', 'Establecimiento:') !!}
            {!! $establecimientos->nombre_establecimiento !!}
        </div>        
    </div>    
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box-body">
            <div class="box-body chat" id="chat-box">
                <?php $x=1; ?>
                <table id="example1" class="table table-responsive table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Rubros</th>
                            <th>Medicamento</th>
                            <th>Dispositivo</th>                            
                        </tr>                        
                    </thead>                
                    <tbody>               
                    @foreach($consulta as $id => $consulta)                        
                        <tr>
                            <td>{{$x++}}</td>
                            
                            <td>{{ $consulta->descripcion }}</td>
                            
                            @if ($consulta->medicamento_cerrado == 3)         
                                <td> <a href="{!! route('cans.medicamentos_rubros',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'rubro_id'=>$consulta->rubro_id]) !!}" class='btn btn-success btn-xs'> Abierto </a></td>
                            @else
                                @if ($consulta->medicamento_cerrado == 2)
                                    <td> <a href="{!! route('cans.medicamentos_rubros',['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'rubro_id'=>$consulta->rubro_id]) !!}" class='btn btn-danger btn-xs'> Cerrado </a></td>
                                @else
                                    <td><small class="label label-default">No Habilitado</small></td>
                                @endif
                            @endif
                            @if ($consulta->dispositivo_cerrado == 3)         
                                <td> <a href="{!! route('cans.dispositivos_rubros', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'rubro_id'=>$consulta->rubro_id]) !!}" class='btn btn-success btn-xs'> Abierto </a></td>
                            @else
                                @if ($consulta->dispositivo_cerrado == 2)         
                                    <td> <a href="{!! route('cans.dispositivos_rubros', ['can_id'=>$consulta->can_id,'establecimiento_id'=>$consulta->establecimiento_id,'rubro_id'=>$consulta->rubro_id]) !!}" class='btn btn-danger btn-xs'> Cerrado </a></td>
                                @else
                                    <td><small class="label label-default">No Habilitado</small></td>
                                @endif
                            @endif
                        </tr>
                        
                        
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>  

    </div>    
</div>


