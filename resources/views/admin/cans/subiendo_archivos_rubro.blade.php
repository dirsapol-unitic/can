@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">{!!$descripcion!!} - {!!$nombre_establecimiento!!}</h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <br/>
        <a href="{!! route('cans.listar_archivos_can_rubro',[$can_id,$establecimiento_id,$servicio_id]) !!}" class='btn btn-info'><i class="glyphicon glyphicon-hand-left"></i> Regresar</a>
        <br/><br/>
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.cans.subir_archivo_rubro')
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).on('change','input[type="file"]',function(){
    // this.files[0].size recupera el tamaño del archivo
    // alert(this.files[0].size);
    
    var fileName = this.files[0].name;
    var fileSize = this.files[0].size;

    if(fileSize > 10000000){
        alert('El archivo no debe superar los 10MB');
        this.value = '';
        this.files[0].name = '';
    }else{
        // recuperamos la extensión del archivo
        var ext = fileName.split('.').pop();

        // console.log(ext);
        switch (ext) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
            case 'pdf': break;
            default:
                alert('El archivo no tiene la extensión adecuada');
                this.value = ''; // reset del valor
                this.files[0].name = '';
        }
    }
});
</script>
@stop