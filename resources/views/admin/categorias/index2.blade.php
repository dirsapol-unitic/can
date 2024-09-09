<html>

<head>
 <title>Crear una aplicación crud sin recargar la pagina en laravel </title>
 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

 <br>
 <br>

 <div class="container text-center">
  <div class="jumbotron">
   <h1>Crear una aplicación crud sin recargar la pagina en laravel</h1>
  </div>
 </div>

 <div class="container">
  <div class="panel panel-primary">
   <div class="panel-heading">Crear una aplicación crud sin recargar la pagina en laravel
    
   </div>
   <div class="panel-body">
    <button id="btn_add" name="btn_add" class="btn btn-primary pull-right">Nuevo Producto</button>
    <table class="table">
     <thead>
      <tr>
       <th>ID</th>
       <th>Descripcion</th>
       <th>Accion</th>
      </tr>
     </thead>
     <tbody id="categorias-list" name="categorias-list">
      @foreach ($categorias as $categoria)
      <tr id="categoria{{$categoria->id}}">
       <td>{{$categoria->id}}</td>
       <td>{{$categoria->descripcion}}</td>
       <td>
        <button class="btn btn-warning btn-detail open_modal" value="{{$categoria->id}}">Editar</button>
        <button class="btn btn-danger btn-delete delete-categoria" value="{{$categoria->id}}">Eliminar</button>
       </td>
      </tr>
      @endforeach
     </tbody>
    </table>
   </div>
  </div>
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
    <div class="modal-content">
     <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      <h4 class="modal-title" id="myModalLabel">Categoria</h4>
     </div>
     <div class="modal-body">
      <form id="frmcategorias" name="frmcategorias" class="form-horizontal" novalidate="">
       <div class="form-group">
        <label for="inputDetail" class="col-sm-3 control-label">Descripcion</label>
        <div class="col-sm-9">
         <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="details" value="">
        </div>
       </div>
      </form>
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-primary" id="btn-save" value="add">Guardar</button>
      <input type="hidden" id="categoria_id" name="categ_id" value="0">
     </div>
    </div>
   </div>
  </div>
 </div>
 <meta name="_token" content="{!! csrf_token() !!}" />
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
 <script src="{{asset('js/crud.js')}}"></script>
</body>

</html>
