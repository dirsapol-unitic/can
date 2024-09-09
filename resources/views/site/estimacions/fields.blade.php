<div class="form-group col-sm-12">
    <div class="pull-right">
        <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
        <a href="{!! route('estimacions.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    </div>
</div>

<!-- Mes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mes', 'Mes:') !!}
    {!! Form::select('mes',$mes , null, ['class' => 'form-control']) !!}
    
</div>

<!-- Ano Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ano', 'Ano:') !!}
    {!! Form::select('ano', $ano, null, ['class' => 'form-control']) !!}
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box-body">
            <div class="box-body chat" id="chat-box">
                <?php $x=1; ?>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th><input type="checkbox" id="checkTodos" /></th>
                        </tr>
                    </thead>                
                    <tbody>               
                    @foreach($establecimientos as $id => $nombre_establecimiento)
                        <tr>
                            <td>{{$x++}}</td>
                            <td>{{ $nombre_establecimiento }}</td>
                            <td><input 
                                type="checkbox" 
                                value="{{ $id }}" 
                                name="establecimientos[]"
                                
                                @if( $valor==2)
                                {{ 
                                    $estimacion->establecimientos->pluck('id')->contains($id) ? 'checked' : '' 
                                }}
                                @endif
                                >
                            </td>
                        </tr>
                        
                    @endforeach
                    </tbody>
                </table>
            </div>            
        </div>  

    </div>    
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('estimacions.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    
</div>

