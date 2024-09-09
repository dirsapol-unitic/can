<table class="table table-responsive" id="femas-table">
    <thead>
        <tr>
            <th>Can Id</th>
        <th>Establecimiento Id</th>
        <th>Cod Establecimiento</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($femas as $fema)
        <tr>
            <td>{!! $fema->can_id !!}</td>
            <td>{!! $fema->establecimiento_id !!}</td>
            <td>{!! $fema->cod_establecimiento !!}</td>
            <td>
                {!! Form::open(['route' => ['femas.destroy', $fema->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('femas.show', [$fema->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('femas.edit', [$fema->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>