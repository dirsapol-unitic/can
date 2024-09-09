<!--table id="example1" class="table table-bordered table-striped"-->
<div class="table-responsive">
    <table id="example" class="table table-bordered table-striped">
        <thead>
            <tr>
                <td>#</td>           
                <td>Establecimiento</td>
                <td>Nombre Rubro</td>
                <td>DNI</td>
                <td>Nombre</td>
                <td>Email</td>                
                <td>Telefono</td>
                <td>Estado</td>                
                <td>Editar</td>               
            </tr>
        </thead>
        <tbody>
        @foreach($users as $key => $user)
            <tr>
                <td>{{$key+1}}</td>
                <td>{!! $user->nombre_establecimiento !!}</td>
                <td>
                    {!! $user->nombre_servicio !!}
                </td>
                <td>{!! $user->dni !!}</td>
                <td>{!! $user->name !!}</td>
                <td>{!! $user->email !!}</td>
                <td>{!! $user->telefono !!}</td>
                <td><?php if($user->estado==1): echo "ACTIVO"; else: echo "DESACTIVADO"; endif; ?></td>
                <td>
                    <div class='btn-group'>
                        <a data-toggle="tooltip" title="Editar Usuario!" href="{!! route('users.edit', [$user->id]) !!}" class='btn btn-primary btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>