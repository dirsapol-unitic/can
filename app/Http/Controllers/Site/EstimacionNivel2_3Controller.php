<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests\CreateEstimacionRequest;
use App\Http\Requests\UpdateEstimacionRequest;
use App\Repositories\EstimacionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Distribucion;
use App\Models\Estimacion;
use App\Models\Estimacion2;
use App\Models\Estimacion3;
use App\Models\Petitorio;
use App\Models\Servicio;
use App\Models\User;
use DB;
use App\Models\Establecimiento;
use App\Models\Can;
use Illuminate\Support\Facades\Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;      

class EstimacionNivel2_3Controller extends AppBaseController
{
    /** @var  EstimacionRepository */
    private $estimacionRepository;

    public function __construct(EstimacionRepository $estimacionRepo)
    {
        $this->estimacionRepository = $estimacionRepo;
    }

    public function index(Request $request)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Si tiene algun servicio o rubro
        $servicio_id=Auth::user()->servicio_id;

        //busco el dpto que pertenece
        $unidad_id=Auth::user()->unidad_id;

        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $nivel=$establecimiento->nivel_id;

        //Veo el nombre del servicio o del rubro
        $nombre_servicio=Auth::user()->nombre_servicio;

        if ($nivel>1)
        {
            $cans = DB::table('cans')
                ->join('can_servicio', 'can_servicio.can_id', 'cans.id')
                ->join('servicios', 'can_servicio.servicio_id','servicios.id')
                ->where('can_servicio.servicio_id',$servicio_id)
                ->where('can_servicio.establecimiento_id',$establecimiento_id)
                ->orderby('cans.id','desc')
                ->get();



            $items_medicamentos=DB::table('petitorio_servicio')
                                    ->where('servicio_id',$servicio_id)
                                    ->where('tipo_dispositivo_medico_id',1)
                                    ->count();

            $items_dispositivos=DB::table('petitorio_servicio')
                                    ->where('servicio_id',$servicio_id)
                                    ->where('tipo_dispositivo_medico_id','>',1)
                                    ->count();            
        }
        
        $anio=$cans->get(0)->ano;
        $cans_ultimo= Can::latest('id')->first();
        $can_id=$cans_ultimo->id;



        return view('site.estimacion_servicio.index')
            ->with('nombre_servicio', $nombre_servicio)
            ->with('items_medicamentos', $items_medicamentos)
            ->with('items_dispositivos', $items_dispositivos)
            ->with('nivel', $nivel)
            ->with('can_id', $can_id)
            ->with('anio', $anio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('cans', $cans);       
    }

    public function listar_observaciones_nivel2y3($can_id)
    {
       
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        $rol_user=Auth::user()->rol;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id); 

        $cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->get();          
              
        $servicio_id=Auth::user()->servicio_id;
        $cerrado=DB::table('can_servicio')
            ->where('can_id',$can_id)
            ->where('servicio_id',$servicio_id)
            ->where('establecimiento_id',$establecimiento_id)
            ->get();  


        
            $observaciones= DB::table('observaciones')
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('can_id', $can_id)
                        ->where('rubro_id', $servicio_id)
                        ->where('estado',1)
                        ->get();

            return view('site.estimacion_servicio.listar_observaciones')
                    ->with('can_id', $can_id)
                    ->with('nivel', 2)
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('observaciones', $observaciones);   
            
            
    }

    public function manual($id)
    {
        return view('site.estimacion_servicio.manual')
        ->with('id', $id);   ;     
    }
    
    public function show($id)
    {
    }
    public function edit($id)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        $nivel=$establecimiento->nivel_id;

        if ($nivel!=1)
        {
            $contact = Estimacion3::findOrFail($id); //estimacion_servicio
        }

        return $contact;        
    }

      public function eliminar($id)
    {
        
        //$contact = Estimacion3::findOrFail($id);

        /*$producto = DB::table('estimacion_servicio')
                            ->where('id',$id)
                            ->delete();

        DB::table('estimacion_servicio')
        ->where('id', $id)
        ->update([
                    'estado'=> 2,
                    'estado_necesidad'=> 2,
                    'updated_at'=>Carbon::now()
         ]);

        */
        $model_estimacion = new Estimacion();
        
        $grabando  = $model_estimacion->EliminaProducto($id);

        return response()->json([
            'success' => true,
            'message' => 'Producto Eliminado'
        ]);
    }


    public function apiContact($can_id,$establecimiento_id,$tipo,$cerrado)
    {

        //verificamos la farmacia del usuario
        $servicio_id=Auth::user()->servicio_id;

        //Cargamos los datos a mostrar
        $petitorio_cerrado = DB::table('can_establecimiento')
                                    ->where('establecimiento_id', $establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->get();

        //averiguamos el nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;
        
        if($nivel>1){

            $condicion='servicio_id';

            if($tipo==1){  //medicamento

                    $can=DB::table('estimacion_servicio')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->where($condicion,$servicio_id)
                                    ->where('tipo_dispositivo_id',1)                                    
                                    ->where('estado','<>',2)            
                                    ->get();
                    //$cerrado=$petitorio_cerrado->get(0)->medicamento_cerrado;
            }
            else
            {
                        $can=DB::table('estimacion_servicio')       
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->where($condicion,$servicio_id)
                                        ->where('can_id',$can_id)   
                                        ->where('estado','<>',2)            
                                        ->where('tipo_dispositivo_id','>',1)
                                        ->get();
                    //$cerrado=$petitorio_cerrado->get(0)->dispositivo_cerrado;
            }              
        }    

        
        if($cerrado==2){
                return Datatables::of($can)
                ->addColumn('action', function($can){
                    return '<a href="#" disabled class="btn btn-default btn-xs"><i class="glyphicon glyphicon-edit"></i></a>' ;                
                })
                ->rawColumns(['justificacion', 'action'])->make(true);    
        }
        else
        {
            
            
                return Datatables::of($can)
                ->addColumn('action', function($can){
                    if($can->petitorio==1):
                        return '<a onclick="editForm('. $can->id .')" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i>  </a> '.
                       ' <a onclick="deleteData('. $can->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> </a>';
                    else:
                        if($can->petitorio==0):
                            return "<font color='red'>NO PETITORIO</font>";
                        else:
                            return "<font color='red'>NO CORRESPONDE</font>";
                        endif;
                    endif;
                })
                ->rawColumns(['justificacion', 'action'])->make(true);    
            
        }
    }

    public function apiContactRectificacion($can_id,$establecimiento_id,$tipo,$cerrado)
    {

        //verificamos la farmacia del usuario
        $servicio_id=Auth::user()->servicio_id;

        //Cargamos los datos a mostrar
        $petitorio_cerrado = DB::table('can_establecimiento')
                                    ->where('establecimiento_id', $establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->get();

        //averiguamos el nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;
        
        if($nivel>1){

            $condicion='servicio_id';

            if($tipo==1){  //medicamento

                    $can=DB::table('estimacion_servicio')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->where($condicion,$servicio_id)
                                    ->where('tipo_dispositivo_id',1)
                                    ->where('estado','<>',2)            
                                    ->get();
                    //$cerrado=$petitorio_cerrado->get(0)->medicamento_cerrado;
            }
            else
            {
                        $can=DB::table('estimacion_servicio')       
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->where($condicion,$servicio_id)
                                        ->where('can_id',$can_id)   
                                        ->where('estado','<>',2)            
                                        ->where('tipo_dispositivo_id','>',1)
                                        ->get();
                    //$cerrado=$petitorio_cerrado->get(0)->dispositivo_cerrado;
            }              
        }    

        
            
        if($cerrado==2){
                return Datatables::of($can)
                ->addColumn('action', function($can){
                    return '<a href="#" disabled class="btn btn-default btn-xs"><i class="glyphicon glyphicon-edit"></i></a>' ;                
                })
                ->rawColumns(['justificacion', 'action'])->make(true);    
        }
        else
        {

                return Datatables::of($can)
                ->addColumn('action', function($can){
                  return '<a onclick="editForm('. $can->id .')" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i>  </a> ';
                })
                ->rawColumns(['justificacion', 'action'])->make(true);    
        }
    }

    public function grabar_multi($id, UpdateEstimacionRequest $request)
    {
        $model_estimacion = new Estimacion();
        
        $grabando  = $model_estimacion->ActualizaProductoNivel2y3($id, $request->necesidad_anual, $request->necesidad_anual_1, $request->necesidad_anual_2, $request->mes1,$request->mes1_1, $request->mes1_2, $request->mes2,$request->mes2_1, $request->mes2_2,$request->mes3,$request->mes3_1, $request->mes3_2,$request->mes4,$request->mes4_1, $request->mes4_2,$request->mes5,$request->mes5_1, $request->mes5_2,$request->mes6,$request->mes6_1, $request->mes6_2,$request->mes7,$request->mes7_1, $request->mes7_2,$request->mes8,$request->mes8_1, $request->mes8_2,$request->mes9,$request->mes9_1, $request->mes9_2,$request->mes10,$request->mes10_1, $request->mes10_2,$request->mes11,$request->mes11_1, $request->mes11_2,$request->mes12,$request->mes12_1, $request->mes12_2);
            
        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }

    public function grabar($id, UpdateEstimacionRequest $request)
    {
        $model_estimacion = new Estimacion();
        
        $grabando  = $model_estimacion->ActualizaNewProductoNivel2y3($id, $request->necesidad_anual, $request->mes1,$request->mes2,$request->mes3,$request->mes4,$request->mes5,$request->mes6,$request->mes7,$request->mes8,$request->mes9,$request->mes10,$request->mes11,$request->mes12);
            
        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }

    public function grabar_necesidad($id, UpdateEstimacionRequest $request)
    {
       
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        $nivel=$establecimiento->nivel_id;

        $estado_necesidad=0;

        if ($nivel>1)
        {

            $estimacion = Estimacion3::find($id); //servicios
        }

        if (empty($estimacion)) {
            Flash::error('Estimación no encontrado');

            return redirect(route('estimacion_servicio.index'));
        }

        $data_anterior= DB::table('estimacion_servicio')
                        ->where('id', $id)
                        ->get();

        
        $necesidad_anterior=$data_anterior->get(0)->necesidad_anual;        
        
        $necesidad_actual = $request->input("necesidad_actual");

        if($necesidad_actual!=$necesidad_anterior)
            $estado_necesidad=3;

        
        
        
        if ($nivel>1)
        {
            DB::table('estimacion_servicio')
            ->where('id', $id)
            ->update([
                        'necesidad_actual' => $necesidad_actual,                        
                        'estado_necesidad'=> $estado_necesidad,
                        'updated_rectificacion'=>Carbon::now()
             ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }

    
public function grabar_nuevo_medicamento_dispositivo(Request $request,$establecimiento_id,$can_id,$servicio_id, $destino)
    {

        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            //nombre del servicio o distribuidor
            $nombre_servicio=Auth::user()->nombre_servicio;

            $establecimiento = Establecimiento::find($establecimiento_id);     
            $can = Can::find($can_id);
            $petitorio = Petitorio::find($request->descripcion);

            $nivel=$establecimiento->nivel_id;
            
            $cod_establecimiento=$establecimiento->codigo_establecimiento;
            $nombre_establecimiento=$establecimiento->nombre_establecimiento;
            
            $petitorio_id=$request->descripcion;
            $tipo_dispositivo_id=$petitorio->tipo_dispositivo_medicos_id;
            $descripcion=$petitorio->descripcion;
            $uso_id = $petitorio->tipo_uso_id;
            $cod_petitorio=$petitorio->codigo_petitorio;
            
            $stock = 0;
            //$cpma = $request->input("cpma");
            $necesidad_anual = $request->input("necesidad_anual");
            $mes1 = $request->input("mes1");
            $mes2 = $request->input("mes2");
            $mes3 = $request->input("mes3");
            $mes4 = $request->input("mes4");
            $mes5 = $request->input("mes5");
            $mes6 = $request->input("mes6");
            $mes7 = $request->input("mes7");
            $mes8 = $request->input("mes8");
            $mes9 = $request->input("mes9");
            $mes10 = $request->input("mes10");
            $mes11 = $request->input("mes11");
            $mes12 = $request->input("mes12");
//            $justificacion = $request->input("justificacion");

            
            DB::table('estimacion_servicio')
            ->insert([
                        'can_id' => $can_id,
                        'tipo_dispositivo_id'=>$tipo_dispositivo_id,
                        'descripcion'=>$descripcion,
                        'petitorio_id'=>$petitorio_id,
                        'cod_petitorio'=>$cod_petitorio,
                        'establecimiento_id'=>$establecimiento_id,
                        'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                        'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                        'servicio_id' => $servicio_id,
                        'nombre_servicio' => $nombre_servicio,
                        'necesidad_anual' => $necesidad_anual,
                        'stock' => $stock,
                        //'cpma' => $cpma,
                        'mes1' => $mes1,
                        'mes2' => $mes2,
                        'mes3' => $mes3,
                        'mes4' => $mes4,
                        'mes5' => $mes5,
                        'mes6' => $mes6,
                        'mes7' => $mes7,
                        'mes8' => $mes8,
                        'mes9' => $mes9,
                        'mes10' => $mes10,                        
                        'mes11' => $mes11,
                        'mes12' => $mes12,
                        'estado' => 1,
                        'estado_necesidad' => 1,
  //                      'justificacion' => $justificacion,
                        'created_at'=>Carbon::now(),
                        'uso_id' => $uso_id,
             ]);

        
            if($destino==1)
            {
                    if($necesidad_anual<0){
                        Flash::error('No se ha podido guardar el medicamento, la suma total de ingreso es menor a la suma total de salida');
                    }
                    else
                    {
                        Flash::success('Se ha guardado con exito');
                    }

                    return redirect(route('estimacion_servicio.cargar_medicamentos_servicios',[$can_id,$establecimiento_id,1]));
            }
            else
            {   //dispositivos
                //Flash::error('No se ha podido guardar el medicamento, la suma total de ingreso es menor a la suma total de salida');
                    if($necesidad_anual<0){
                        Flash::error('No se ha podido guardar el medicamento, la suma total de ingreso es menor a la suma total de salida');
                    }
                    else
                    {
                        Flash::success('Se ha guardado con exito');
                    }
                return redirect(route('estimacion_servicio.cargar_medicamentos_servicios',[$can_id,$establecimiento_id,2]));
            }
        }   
        
    }

    
    public function nuevo_medicamento_dispositivo( $can_id, $establecimiento_id, $tipo )
    {
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;
        $nivel_est=$establecimiento->nivel_id;
        $nivel=$nivel_est+1;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $tiempo = $can->tiempo;
        $modeluser = new User;

        $nombre_servicio=Auth::user()->nombre_servicio;
        $estimacions = Estimacion::find($can_id);

        $establecimiento_cerrado=DB::table('can_establecimiento') ->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();  

        $model_estimacion = new Estimacion();
        $model_petitorio = new Petitorio();
        $model_can_servicio = new Can();

        if($tipo==1){  //medicamentos
            $descripcion_tipo='Medicamentos';
            //buscamos si existe medicamentos ya ingresados
            $numero_medicamentos=$model_estimacion->ContarProductosNivel2y3($can_id,$establecimiento_id,1, $servicio_id);
            if($numero_medicamentos->total>0):
                $data=$model_estimacion->ProductosIngresadosNivel2y3($can_id,$establecimiento_id,1,$servicio_id);
            else:
                $data='';
            endif;

            
            $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel2y3(1,$nivel, $servicio_id); //tipo,nivel
            if($total_medicamentos_rubro->total>0):
                $petitorios=$model_petitorio->GetPetitoriosNivel2y3(1,$nivel,$servicio_id);
            else:
                $petitorios='';
            endif;
            
        }
        else
        {
            //buscamos si existe dispositivos ya ingresados
            $descripcion_tipo='Dispositivos Medicos';
            
            $numero_medicamentos=$model_estimacion->ContarProductosNivel2y3($can_id,$establecimiento_id,2, $servicio_id);
            if($numero_medicamentos->total>0):
                $data=$model_estimacion->ProductosIngresadosNivel2y3D2($can_id,$establecimiento_id,1,$servicio_id);
            else:
                $data='';
            endif;

            $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel2y3D(1, $nivel, $servicio_id);
            if($total_medicamentos_rubro->total>0):
                $petitorios=$model_petitorio->GetPetitoriosNivel2y3D(1, $nivel, $servicio_id);
            else:
                $petitorios='';
            endif;
        } 
        
        if($petitorios!=''):
            $petitorios2 = $petitorios->pluck('descripcion','id')->toArray();
            $data2 = $data->pluck('descripcion','petitorio_id')->toArray();
            $descripcion=array_diff($petitorios2,$data2);
            $redireccion=1;

            return view('site.estimacion_servicio.medicamentos.asignar_medicamentos')->with('nombre_servicio', $nombre_servicio)->with('estimacions', $estimacions)->with('can_id', $can_id)->with('tipo', $tipo)->with('establecimiento_id', $establecimiento_id)->with('establecimiento', $establecimiento)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('petitorios', $descripcion)->with('redireccion', $redireccion);

            
        else:
            return redirect(route('estimacion_servicio.index'));
        endif;

        $valor=1;
        
       
        
        
    }

    public function nuevo_medicamento_dispositivo2( $can_id, $establecimiento_id, $tipo_producto )
    {
        
        //Verificamos si el usuario es el mismo
        if (Auth::user()->establecimiento_id == $establecimiento_id ){
            if ($tipo_producto >0 && $tipo_producto <3 )
            {
                //Verificamos si el can es el ultimo
                $cans=DB::table('cans')->orderBy('id', 'desc')->first();
                    $can = Can::find($cans->id);
                    $can_id_ultimo=$cans->id;

                if($can_id_ultimo==$can_id){

                    $servicio_id=Auth::user()->servicio_id;
                
                    //buscamos el establecimiento
                    $establecimiento = Establecimiento::find($establecimiento_id);
                    //si encuentra o no el establecimiento
                    if (empty($establecimiento)) {
                        Flash::error('Establecimientos ICI con esas caracteristicas');
                        return redirect(route('estimacion_servicio.index'));
                    }
                    
                    $nivel_sum=$establecimiento->nivel_id+1; //para la condicion

                    if($tipo_producto==1){ //// 1 si es medicamento

                        //buscamos si existe medicamentos asignados por el administrador
                        $consulta_petitorio = DB::table('servicios')
                                  ->select('petitorios.descripcion as descripcion','petitorios.id as id')
                                  ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                  ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                  ->where('petitorio_servicio.servicio_id',$servicio_id)
                                  ->where('petitorios.estado',1)
                                  ->where('petitorios.nivel_id','<',$nivel_sum)
                                  ->where('petitorio_servicio.tipo_dispositivo_medico_id',1);
                                  //->pluck('petitorios.descripcion','petitorios.id');
                        
                        
                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                        //Buscamos los medicamentos segun el nivel 
                        
                            
                        //Buscamos en la tabla de estimacion_servicios
                        $consulta_medicamentos_nivel = DB::table('estimacion_servicio')
                            ->where('tipo_dispositivo_id',1)
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id',$servicio_id)
                            ->where('estado','<>',2)
                            ->orderby('descripcion','asc')
                            ->orderby ('tipo_dispositivo_id','asc');

                        
                        //pasamos a un arreglo
                        $consulta_medicamento = $consulta_medicamentos_nivel->pluck('descripcion','petitorio_id')->toArray();

                        //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                        $descripcion=array_diff($petitorio,$consulta_medicamento);
                    
                    }
                    else
                    {
                        //Buscamos todos los dispositivos segun el nivel
                        
                        /*$consulta_petitorio = DB::table('petitorios')
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->where('nivel_id','<',$nivel_sum)
                                ->get();
                        */
                        //buscamos si existe medicamentos asignados por el administrador
                        $consulta_petitorio = DB::table('servicios')
                                      ->select('petitorios.descripcion as descripcion','petitorios.id as id')
                                      ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                      ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                      ->where('petitorio_servicio.servicio_id',$servicio_id)
                                      ->where('petitorios.nivel_id','<',$nivel_sum)
                                      ->where('petitorios.estado',1)
                                      ->where('petitorio_servicio.tipo_dispositivo_medico_id','>',1);
                                      //->pluck('petitorios.descripcion','petitorios.id');            
                        
                        //dd($consulta_petitorio);
                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                        
                        //dd($petitorio);

                        //Buscamos los dispositivos segun el nivel 
                        if($establecimiento->nivel_id>1){ 
                            //Buscamos en la tabla de abastecimientos_servicios
                            $consulta_dispositivos_nivel = DB::table('estimacion_servicio')
                                ->where('tipo_dispositivo_id','>',1)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('servicio_id',$servicio_id)
                                ->where('estado','<>',2)
                                ->orderby('descripcion','asc')

                                ->orderby ('tipo_dispositivo_id','asc')
                                ->get();                        

                        }

                        //dd($consulta_dispositivos_nivel);
                        
                        //pasamos a un arreglo
                        $consulta_dispositivo = $consulta_dispositivos_nivel->pluck('descripcion','petitorio_id')->toArray();

                        //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                        $descripcion=array_diff($petitorio,$consulta_dispositivo);

                        //dd($descripcion);
                    
                    }
                
                    //Enviamos al formulario
                    return view('site.estimacion_servicio.nuevo.index')
                            ->with('establecimiento_id', $establecimiento_id)
                            ->with('can_id', $can_id)
                            ->with('servicio_id', $servicio_id)
                            ->with('destino', $tipo_producto)
                            ->with('descripcion', $descripcion);

                }
                else
                {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('estimacion_servicio.index'));
                }    
        
            }
            else
            {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('estimacion_servicio.index'));
            }
        }
        else
        {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('estimacion_servicio.index'));
        }
    }
    
    //////////////////Asignar medicamentos //////////////////////////////////////////
    public function cargar_medicamentos_servicios($can_id, $establecimiento_id, $tipo)
    {
        
        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            //nombre del servicio o distribuidor
            $nombre_servicio=Auth::user()->nombre_servicio;

            //buscamos los datos del establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            
            $nivel = $establecimiento->nivel_id;
            

            $tope_nivel=$nivel+1;

            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimiento no existe CAN con esas caracteristicas');
                return redirect(route('estimacion_servicio.index'));
            }
          
            $can = Can::find($can_id);

            if (empty($can)) {
                Flash::error('CAN no encontrada');
                return redirect(route('estimacion.index'));
            }

            $tiempo = $can->tiempo;
            $modeluser = new User;

            $nombre_servicio=Auth::user()->nombre_servicio;
            $estimacions = Estimacion::find($can_id);

            $servicio_cerrado=DB::table('can_servicio') ->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id',$servicio_id)->get();  
            
            $atenciones = $servicio_cerrado->get(0)->atenciones; 
            
            if($atenciones==0):
                $redireccion = 2;
                return view('site.estimacion_servicio.atenciones.edit')->with('can_id', $can_id)->with('tipo', $tipo)->with('establecimiento_id', $establecimiento_id)->with('establecimiento', $establecimiento)->with('atenciones', $atenciones)->with('redireccion',$redireccion)->with('servicio_id',$servicio_id);
            endif;

            
            if($nivel>1){ 
                
                $table='estimacion_servicio';
                $condicion_1='servicio_id';
                $table2='servicios';
                $condicion_2='servicios.id';
                $condicion_3='petitorio_servicio.servicio_id';
                $condicion_4='petitorio_servicio.petitorio_id';
                $condicion_5='petitorio_servicio.tipo_dispositivo_medico_id';
                $table3='petitorio_servicio';
            }    

            
            
            if($tipo==1){  //medicamentos
                //buscamos si existe medicamentos asignados por el administrador
                $numero_medicamentos=DB::table('estimacion_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id',1)
                                ->where('estado','<>',2)
                                ->where('petitorio','=',1)            
                                ->count(); 

                $total_medicamentos_servicio = DB::table('servicios')
                                  ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                  ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                  ->where('petitorio_servicio.servicio_id',$servicio_id)
                                  ->where('petitorio_servicio.tipo_dispositivo_medico_id',1)
                                  ->where('petitorios.estado',1)
                                  ->where('petitorios.nivel_id','<',$tope_nivel)                                  
                                  ->count(); 

                $diferencia=$total_medicamentos_servicio-$numero_medicamentos;
                $cerrado=$servicio_cerrado->get(0)->medicamento_cerrado;
            }
            else
            {
                //buscamos si existe medicamentos asignados por el administrador
                $numero_medicamentos=DB::table('estimacion_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id','>',1)
                                ->where('petitorio','=',1)     
                                ->where('estado','<>',2)            
                                ->count(); 

                $total_dispositivos_servicio = DB::table('servicios')
                                ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                ->where('petitorio_servicio.servicio_id',$servicio_id)
                                ->where('petitorio_servicio.tipo_dispositivo_medico_id','>',1)
                                ->where('petitorios.nivel_id','<',$tope_nivel)  
                                ->where('petitorios.estado',1)
                                ->count(); 

                $diferencia=$total_dispositivos_servicio-$numero_medicamentos;
                $cerrado=$servicio_cerrado->get(0)->dispositivo_cerrado;
            } 
            
            
            
            //$medicamento_cerrado_stock=$establecimiento->get(0)->medicamento_cerrado_stock;
            //dd($establecimiento_id);
            //Si aun no se ha ingresado los medicamentos/dispositivos al sistema
            if ($numero_medicamentos==0){  
                if($can_id==9){
                    //cargar_can_anterior
                    $model_can_nivel_2y3 = new Can();
                    $producto_asignado = $model_can_nivel_2y3->CopiarProductosCanAnteriorServicioNivel2y3($can_id, $tipo, $establecimiento_id, $servicio_id);
                    
                    if($producto_asignado->sp_copiar_can_anterior_nivel_2y3==1){ //no encontro nada
                        $redireccion = 0; 
                        if($tipo==1){ //medicamento 1
                            $petitorios = DB::table('servicios')
                                      ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                      ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                      ->where('petitorio_servicio.servicio_id',$servicio_id)
                                      ->where('petitorio_servicio.tipo_dispositivo_medico_id',1)
                                      ->where('petitorios.nivel_id','<',$tope_nivel)
                                      ->where('petitorios.estado',1)
                                      ->orderby('descripcion','asc')
                                      ->pluck('petitorios.descripcion','petitorios.id');
                        }
                        else
                        {
                            $petitorios = DB::table('servicios')
                                      ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                      ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                      ->where('petitorio_servicio.servicio_id',$servicio_id)
                                      ->where('petitorio_servicio.tipo_dispositivo_medico_id','>',1)
                                      ->where('petitorios.nivel_id','<',$tope_nivel)
                                      ->where('petitorios.estado',1)
                                      ->orderby('descripcion','asc')
                                      ->pluck('petitorios.descripcion','petitorios.id');
                                      
                        }
                        return view('site.estimacion_servicio.medicamentos.asignar_medicamentos')
                                    ->with('nombre_servicio', $nombre_servicio)
                                    ->with('estimacions', $estimacions)
                                    ->with('can_id', $can_id)
                                    ->with('tipo', $tipo)
                                    ->with('redireccion', $redireccion)
                                    ->with('establecimiento_id', $establecimiento_id)
                                    ->with('establecimiento', $establecimiento)
                                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                                    ->with('petitorios', $petitorios);

                    }
                    else{
                        return redirect(route('estimacion_servicio.cargar_medicamentos_servicios',[$can_id,$establecimiento_id,$tipo])); 
                    }
                    
                }
                else{         
                    $redireccion = 0; 
                    if($tipo==1){ //medicamento 1
                        $petitorios = DB::table('servicios')
                                  ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                  ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                  ->where('petitorio_servicio.servicio_id',$servicio_id)
                                  ->where('petitorio_servicio.tipo_dispositivo_medico_id',1)
                                  ->where('petitorios.nivel_id','<',$tope_nivel)
                                  ->where('petitorios.estado',1)
                                  ->orderby('descripcion','asc')
                                  ->pluck('petitorios.descripcion','petitorios.id');
                    }
                    else
                    {
                        $petitorios = DB::table('servicios')
                                  ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                  ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                  ->where('petitorio_servicio.servicio_id',$servicio_id)
                                  ->where('petitorio_servicio.tipo_dispositivo_medico_id','>',1)
                                  ->where('petitorios.nivel_id','<',$tope_nivel)
                                  ->where('petitorios.estado',1)
                                  ->orderby('descripcion','asc')
                                  ->pluck('petitorios.descripcion','petitorios.id');
                                  
                    }
                    return view('site.estimacion_servicio.medicamentos.asignar_medicamentos')
                                ->with('nombre_servicio', $nombre_servicio)
                                ->with('estimacions', $estimacions)
                                ->with('can_id', $can_id)
                                ->with('tipo', $tipo)
                                ->with('redireccion', $redireccion)
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('establecimiento', $establecimiento)
                                ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                                ->with('petitorios', $petitorios);
                }
            }
            else
            {
                if($can->multianual==0){
                    return view('site.estimacion_servicio.medicamentos.medicamentos_anterior')
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('can_id', $can_id)
                                ->with('nivel', $nivel)
                                ->with('tipo', $tipo)
                                ->with('tiempo', $tiempo)
                                ->with('diferencia', $diferencia)
                                ->with('cerrado', $cerrado)
                                ->with('servicio_id', $servicio_id);
                }
                else{
                    return view('site.estimacion_servicio.medicamentos.medicamentos')
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('can_id', $can_id)
                                ->with('nivel', $nivel)
                                ->with('tipo', $tipo)
                                ->with('tiempo', $tiempo)
                                ->with('diferencia', $diferencia)
                                ->with('cerrado', $cerrado)
                                ->with('servicio_id', $servicio_id);
                }    
                
            }

        }
        else
        {
            Flash::error('No tiene acceso para ver este registro');
            return redirect(route('estimacion_servicio.index'));
        }   
        
    }

    public function cargar_productos_rectificacion($can_id, $establecimiento_id, $tipo,$cerrado)
    {
        
        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            //nombre del servicio o distribuidor
            $nombre_servicio=Auth::user()->nombre_servicio;

            //buscamos los datos del establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            
            $nivel = $establecimiento->nivel_id;
            $tope_nivel=$nivel+1;
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimiento no existe CAN con esas caracteristicas');
                return redirect(route('estimacion_servicio.index'));
            }

            $estimacions = Estimacion::find($can_id);

            if($nivel>1){ 
                
                $table='estimacion_servicio';
                $condicion_1='servicio_id';
                $table2='servicios';
                $condicion_2='servicios.id';
                $condicion_3='petitorio_servicio.servicio_id';
                $condicion_4='petitorio_servicio.petitorio_id';
                $condicion_5='petitorio_servicio.tipo_dispositivo_medico_id';
                $table3='petitorio_servicio';
            }    
            
            if($tipo==1){  //medicamentos
                //buscamos si existe medicamentos asignados por el administrador
                $numero_medicamentos=DB::table('estimacion_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id',1)
                                ->count(); 

                $total_medicamentos_servicio = DB::table('servicios')
                                  ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                  ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                  ->where('petitorio_servicio.servicio_id',$servicio_id)
                                  ->where('petitorio_servicio.tipo_dispositivo_medico_id',1)
                                  ->where('petitorios.estado',1)
                                  ->where('petitorios.nivel_id','<',$tope_nivel)
                                  ->count(); 

                $diferencia=$total_medicamentos_servicio-$numero_medicamentos;
            }
            else
            {
                //buscamos si existe medicamentos asignados por el administrador
                $numero_medicamentos=DB::table('estimacion_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id','>',1)
                                ->count(); 

                $total_dispositivos_servicio = DB::table('servicios')
                                ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                ->where('petitorio_servicio.servicio_id',$servicio_id)
                                ->where('petitorio_servicio.tipo_dispositivo_medico_id','>',1)
                                ->where('petitorios.nivel_id','<',$tope_nivel)  
                                ->where('petitorios.estado',1)
                                ->count(); 

                $diferencia=$total_dispositivos_servicio-$numero_medicamentos;
            } 

            //dd($establecimiento_id);
            //Si aun no se ha ingresado los medicamentos/dispositivos al sistema
            if ($numero_medicamentos==0){           

                if($tipo==1){ //medicamento 1
                    $petitorios = DB::table('servicios')
                              ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                              ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                              ->where('petitorio_servicio.servicio_id',$servicio_id)
                              ->where('petitorio_servicio.tipo_dispositivo_medico_id',1)
                              ->where('petitorios.nivel_id','<',$tope_nivel)
                              ->where('petitorios.estado',1)
                              ->orderby('descripcion','asc')
                              ->pluck('petitorios.descripcion','petitorios.id');
                }
                else
                {
                    $petitorios = DB::table('servicios')
                              ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                              ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                              ->where('petitorio_servicio.servicio_id',$servicio_id)
                              ->where('petitorio_servicio.tipo_dispositivo_medico_id','>',1)
                              ->where('petitorios.nivel_id','<',$tope_nivel)
                              ->where('petitorios.estado',1)
                              ->orderby('descripcion','asc')
                              ->pluck('petitorios.descripcion','petitorios.id');
                }
                return view('site.estimacion_servicio.medicamentos.asignar_medicamentos')
                            ->with('nombre_servicio', $nombre_servicio)
                            ->with('estimacions', $estimacions)
                            ->with('can_id', $can_id)
                            ->with('tipo', $tipo)
                            ->with('establecimiento_id', $establecimiento_id)
                            ->with('establecimiento', $establecimiento)
                            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                            ->with('petitorios', $petitorios);
            }
            else
            {
                return view('site.estimacion_servicio.medicamentos.medicamentos_rectificacion')
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('can_id', $can_id)
                                ->with('nivel', $nivel)
                                ->with('tipo', $tipo)
                                ->with('diferencia', $diferencia)
                                ->with('servicio_id', $servicio_id)
                                ->with('medicamento_cerrado', $cerrado); 
            }

        }
        else
        {
            Flash::error('No tiene acceso para ver este registro');
            return redirect(route('estimacion_servicio.index'));
        }   
        
    }

    
    //public function guardar_medicamentos_asignados(UpdateEstimacionRequest $request, $establecimiento_id, $can_id,$tipo)
    public function guardar_medicamentos_asignados(Request $request, $establecimiento_id, $can_id,$tipo)
    {
        //dd($request);
        
        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            //nombre del servicio o distribuidor
            $nombre_servicio=Auth::user()->nombre_servicio;

            $establecimiento = Establecimiento::find($establecimiento_id);

            if (empty($establecimiento)) {
                Flash::error('Establecimiento no encontrado');

                return redirect(route('estimacion_servicio.index'));
            }

            $nivel=$establecimiento->nivel_id;
            /*
            if (empty($request->petitorios)) {
                Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
                
            }else
            {
                
                $petitorio_total=DB::table('petitorios')
                                ->where('estado',1)
                                ->get();

                
                    foreach($request->petitorios as $key => $petitorio_id){
                    
                        foreach($petitorio_total as $id => $petitorio){     
                            //DB::table('abastecimientos')
                            if($petitorio_id == $petitorio->id){
                                
                                DB::table('estimacion_servicio')
                                     ->insert([
                                        'can_id' => $can_id,
                                        'establecimiento_id' => $establecimiento->id,
                                        'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                                        'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                                        'tipo_dispositivo_id' => $petitorio->tipo_dispositivo_medicos_id,
                                        'petitorio_id' => $petitorio_id,
                                        'cod_petitorio' => $petitorio->codigo_petitorio,
                                        'cod_siga' => $petitorio->codigo_siga,
                                        'descripcion' => $petitorio->descripcion,
                                        'servicio_id' => $servicio_id,
                                        'nombre_servicio' => $nombre_servicio,
                                        'uso_id' => $petitorio->tipo_uso_id,
                                        'created_at'=>Carbon::now(),                           
                                
                                ]);     
                            }         
                        }         
                    }
            } */

            if (empty($request->petitorios)) {
                Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
                
            }else
            {
                $model_can = new Can();
                $productos_elegidos = "";
                $productos_elegidos .= "{";
                foreach ($request->petitorios as $key => $petitorio_id) :
                    if ($request->petitorios[$key] != "") {
                      $productos_elegidos .= "{";
                      $productos_elegidos .= $request->petitorios[$key] ;
                      $productos_elegidos .= "},";
                    }
                endforeach;
                if (strlen($productos_elegidos) > 1) $productos_elegidos = substr($productos_elegidos, 0, -1);
                $productos_elegidos .= "}";


                $producto_asignado = $model_can->RegistrarProductosElegidosNivel2y3($can_id, $nivel, $tipo, $establecimiento_id, $establecimiento->codigo_establecimiento,$establecimiento->nombre_establecimiento,$productos_elegidos,$servicio_id,$nombre_servicio);
                
                       
            }    
   
        }
        
        Flash::success('Estimacion asignado correctamente.');

        return redirect(route('estimacion_servicio.cargar_medicamentos_servicios',[$can_id,$establecimiento_id,$tipo]));
    }
    
    
    //////////////////////////3/////////////////////////////////////
    public function cerrar_medicamento(Request $request,$can_id,$establecimiento_id,$tipo)
    {
        
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN No se ha encontrado');
            return redirect(route('estimacion_servicio.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('estimacion_servicio.index'));
        }
            
        $nivel=$establecimiento->nivel_id;
        
        if ($tipo==1){
            $tipo_cerrado='medicamento_cerrado';
        }
        else
        {
            $tipo_cerrado='dispositivo_cerrado';
        }

        
        $servicio_id=Auth::user()->servicio_id;
        
        DB::table('can_servicio')
            ->where('establecimiento_id', $establecimiento_id)  
            ->where('can_id', $can_id)                
            ->where('servicio_id', $servicio_id)
            ->update([
                    $tipo_cerrado => 2,
                    'updated_at'=>Carbon::now()
        ]);
        
       
        Flash::success('Petitorio Cerrado.');

        return redirect(route('estimacion_servicio.descargar_servicio',[$tipo,$can_id]));

    }

    public function cerrar_medicamento_rectificacion(Request $request,$can_id,$establecimiento_id,$tipo)
    {
        
        $can = Can::find($can_id);
        
        if (empty($can)) {
            Flash::error('CAN No se ha encontrado');
            return redirect(route('estimacion_servicio.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('estimacion_servicio.index'));
        }
            
        $nivel=$establecimiento->nivel_id;
        
        if ($tipo==1){
            $tipo_cerrado='medicamento_cerrado_rectificacion';
        }
        else
        {
            $tipo_cerrado='dispositivo_cerrado_rectificacion';
        }

        $servicio_id=Auth::user()->servicio_id;

        //buscamos todos los medicamentos llenados
        if($tipo==1){
            $data=DB::table('estimacion_servicio')
                    ->select('petitorio_id')
                    ->where('necesidad_actual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('petitorio_id')
                    ->get();     
        }
        else
        {
            $data=DB::table('estimacion_servicio')
                    ->select('petitorio_id')
                    ->where('necesidad_actual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('petitorio_id')
                    ->get();   
        }

        $new_product=$data->pluck('petitorio_id');

        foreach($new_product as $key => $producto){

                /*$cad= "
                        select petitorio_id, sum(necesidad_anual) necesidad_anual, sum(necesidad_actual) necesidad_actual
                        from estimacion_servicio
                        where establecimiento_id = ".$establecimiento_id." and can_id=".$can_id." and estado!=2 and petitorio_id=".$producto."
                        group by petitorio_id";*/


                $data= DB::table('estimacion_servicio')
                        ->select('petitorio_id', 
                                    DB::raw('SUM(necesidad_anual) as necesidad_anual'),
                                    DB::raw('SUM(necesidad_actual) as necesidad_actual')
                                    )
                                ->groupby('petitorio_id')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('can_id',$can_id)                                
                                ->where('petitorio_id',$producto)
                                ->get();

                               /* $data_anterior= DB::table('estimacion_servicio')
                        ->where('id', $id)
                        ->get();*/

        
                $necesidad_anual=$data->get(0)->necesidad_anual;  
                $necesidad_actual=$data->get(0)->necesidad_actual;  
                $petitorio_id=$data->get(0)->petitorio_id;  
                
                /*$data = DB::select($cad);
                if (isset($data[0]))
                    $dato= $data[0];
                else
                    $dato= $data;
                */

                $necesidad1=$necesidad_actual;
                $necesidad2=$necesidad_anual;
                
                
                
                
                if($necesidad1!=$necesidad2){
                    DB::table('estimacions')
                    ->where('petitorio_id', $petitorio_id) 
                    ->where('establecimiento_id',$establecimiento_id)       
                    ->update([
                        'necesidad_actual' => $necesidad1,
                        'estado_necesidad' => 3,
                        'updated_rectificacion'=>Carbon::now()
                    ]);
                }
        }              
            
        $servicio_id=Auth::user()->servicio_id;
        //cerramos medicamento
        DB::table('can_servicio')
            ->where('establecimiento_id', $establecimiento_id)  
            ->where('can_id', $can_id)                
            ->where('servicio_id', $servicio_id)
            ->update([
                    $tipo_cerrado => 2,
                    'updated_rectificacion'=>Carbon::now()
        ]);
        
            //$this->cerrar_establecimiento($can_id,$establecimiento_id,$nivel);
        
        Flash::success('Petitorio Cerrado.');

        return redirect(route('estimacion_servicio.descargar_servicio_rectificacion',[$tipo,$can_id]));

    }
    //////////////////////////3/////////////////////////////////////
    protected function cerrar_establecimiento($can_id,$establecimiento_id,$nivel_id)
    {
        $servicio_id=Auth::user()->servicio_id;        
        
        //calculamos cuantas servicios hay en el departamento
        $total = DB::table('can_servicio')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->count();
        
        //contamos cuantas servicios han cerrado en medicamentos
        $medicamento_cerrado = DB::table('can_servicio')
                                    ->where('medicamento_cerrado','!=',1)
                                    ->where('can_id',$can_id)
                                    ->where('servicio_id',$servicio_id)
                                    ->count();

        //contamos cuantas servicios han cerrado en dispositivos
        $dispositivo_cerrado = DB::table('can_servicio')
                                    ->where('dispositivo_cerrado','!=',1)
                                    ->where('can_id',$can_id)
                                    ->where('servicio_id',$servicio_id)
                                    ->count();
        
        if ($total == $medicamento_cerrado){
            DB::table('can_establecimiento')
                ->where('can_id', $can_id)
                ->where('establecimiento_id', $establecimiento_id)
                ->update([
                        'medicamento_cerrado' => 2,
                        'updated_at'=>Carbon::now()
            ]);                                        
        }
        if ($total == $dispositivo_cerrado){
            DB::table('can_establecimiento')
                ->where('can_id', $can_id)
                ->where('establecimiento_id', $establecimiento_id)
                ->update([
                        'dispositivo_cerrado' => 2,
                        'updated_at'=>Carbon::now()
            ]);                                        
        }  
    }
////////////////////////////////////////////////////////////////////////////////////////
    public function descargar_servicio($tipo,$can_id)
    {       
       
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion_servicio.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion_servicio.index'));
        }
        
        if($tipo==1){
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->get();

            $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacion_servicio')
                    //->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','necesidad_anual','necesidad_anual_1','necesidad_anual_2')
                    //->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','necesidad_anual','necesidad_anual_1','necesidad_anual_2')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    

                    $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion_servicio.index'));  
                }
        }

        $stock_cerrado=DB::table('can_servicio')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('servicio_id',$servicio_id)//cambiar 1
                    ->get(); 
        $actualizado = $stock_cerrado->get(0)->actualizacion;
        
        return view('site.estimacion_servicio.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('actualizado', $actualizado)
            ->with('can_id', $can_id);
    }

    public function descargar_estimacion_servicio_anterior($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion_servicio.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion_servicio.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->get();

            $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacion_servicio')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    

                    $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion_servicio.index'));  
                }
        }
        
        return view('site.estimacion_servicio.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

public function descargar_servicio_rectificacion($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion_servicio.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion_servicio.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();

            $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacion_servicio')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','necesidad_actual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','necesidad_actual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')                  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion_servicio.index'));  
                }
        }

            
        return view('site.estimacion_servicio.medicamentos.descargar_medicamentos_rectificacion')
            ->with('estimacions', $data)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    ///////////////////////////9////////////////////////////////////77
    public function exportEstimacionData($can_id,$establecimiento_id,$opt,$type,$servicio_id=0)
    {

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion_servicio.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;

        if($servicio_id==0):
            $servicio_id=Auth::user()->servicio_id;
            $nombre_servicio=Auth::user()->nombre_servicio;
        else:
            $servicios=DB::table('servicios')
                            ->where('id',$servicio_id)
                            ->get();
            $servicio_id=$servicios->get(0)->id;
            $nombre_servicio=$servicios->get(0)->nombre_servicio;
        endif;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion_servicio.index'));
        }

        if ($nivel==1)
        {
            $table='estimacion_rubro';
            $condicion1='rubro_id';
        }
        else
        {
            $table='estimacion_servicio';
            $condicion1='servicio_id';
        }


        if($opt==1){
            $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc                    
                    ->get();
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    

                    $nombre_producto='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('farmacia_servicio.index'));  
                }
        }

        $archivo='CAN_'.$nombre_producto.'_'.$nombre_establecimiento.'_'.$nombre_servicio.'_'.$can->desc_mes.'_'.$can->ano;
        return Excel::create($archivo, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {   
                
                //6 filas agrupadas con las columnas A,B,C
                $sheet->setMergeColumn(array(
                    'columns' => array('A','R'),
                    'rows' => array(
                        array(1,6)                        
                    )
                ));

                //INSERTAR LOGOS
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo_sanidad.png')); //your image path
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                
                $objDrawing2 = new PHPExcel_Worksheet_Drawing;
                $objDrawing2->setPath(public_path('img/logo_pnp.png')); //your image path
                $objDrawing2->setCoordinates('R1');
                $objDrawing2->setWorksheet($sheet);
    
                $sheet->mergeCells('B1:O1');
                $sheet->cell('B1', function($cell) {$cell->setValue('CAN');  $cell->setFontSize(38); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle');
                });


                $sheet->mergeCells('B2:O2');
                $sheet->mergeCells('B3:O3');
                $sheet->cell('B2', function($cell) {$cell->setValue('CUADRO ANUAL DE NECESIDADES ');  $cell->setFontSize(28); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });
                
                $nombre_servicio=Auth::user()->nombre_servicio;
                $nombre_establecimiento=Auth::user()->nombre_establecimiento;

                
                $sheet->cell('B3', $nombre_establecimiento.' - '.$nombre_servicio);
                $sheet->cell('B3', function($cell) {$cell->setFontSize(18); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });      


                $sheet->setHeight(1, 40);
                $sheet->setHeight(2, 30);
                $sheet->setHeight(3, 20);
                
                //$sheet->mergeCells('R2:Q2');
                $sheet->mergeCells('K4:L5');
                $sheet->cell('K4', function($cell) {$cell->setValue('MES / AÑO');   $cell->setFontSize(18); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });


                $sheet->setMergeColumn(array(
                    'columns' => array('A','B','C','R'),
                    'rows' => array(
                        array(8,10)                        
                    )
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('D','E'),
                    'rows' => array(
                        array(8,9)                        
                    )
                ));

                $sheet->cell('A8', function($cell) {$cell->setValue('N°'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('B8', function($cell) {$cell->setValue('COD MED'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('C8', function($cell) {$cell->setValue('NOMBRE DEL PRODUCTO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('D8', function($cell) {$cell->setValue('STOCK'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('E8', function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('F9', function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                
                $sheet->cell('G9', function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('H9', function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('I9', function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('J9', function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                
                $sheet->cell('K9', function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('L9', function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('M9', function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('N9', function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                
                $sheet->cell('O9', function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('P9', function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('Q9', function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('R8', function($cell) {$cell->setValue('JUSTIFICACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->mergeCells('F8:Q8');
                $sheet->cell('F8', function($cell) {$cell->setValue('PRORRATEO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->setWidth(array(
                    'A'     =>  5,
                    'B'     =>  15,
                    'C'     =>  100,
                    'D'     =>  10,
                    'E'     =>  10,
                    'F'     =>  10,
                    'G'     =>  10,
                    'H'     =>  10,
                    'I'     =>  10,
                    'J'     =>  10,
                    'K'     =>  10,
                    'L'     =>  10,
                    'M'     =>  10,
                    'N'     =>  10,
                    'O'     =>  10,
                    'P'     =>  10,
                    'Q'     =>  10,
                    'R'     =>  100,
                ));

                //fila
                $i=10;
                //ordenar
                $k=1;

                if (!empty($data)) {

                    $total_necesidad_anual = 0;
                    $total_stock = 0;
                    $mes1_total = 0;
                    $mes2_total = 0;
                    $mes3_total = 0;
                    $mes4_total = 0;
                    $mes5_total = 0;
                    $mes6_total = 0;
                    $mes7_total = 0;
                    $mes8_total = 0;
                    $mes9_total = 0;
                    $mes10_total = 0;
                    $mes11_total = 0;
                    $mes12_total = 0;
                    

                    foreach ($data as $key => $value) {
                        $i= $key+11;
                        $sheet->cell('A'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('B'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('C'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('D'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('E'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('F'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('G'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('H'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('I'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('J'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('K'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('L'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('M'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('N'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('O'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('P'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('Q'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('R'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        
                        $sheet->cell('A'.$i, $k); 
                        $sheet->cell('B'.$i, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$i, $value->descripcion); //nivel
                        $sheet->cell('D'.$i, $value->stock); //items 
                        $sheet->cell('E'.$i, $value->necesidad_anual); 
                        $sheet->cell('F'.$i, $value->mes1); 
                        $sheet->cell('G'.$i, $value->mes2); 
                        $sheet->cell('H'.$i, $value->mes3); 
                        $sheet->cell('I'.$i, $value->mes4); 
                        $sheet->cell('J'.$i, $value->mes5); 
                        $sheet->cell('K'.$i, $value->mes6); 
                        $sheet->cell('L'.$i, $value->mes7); 
                        $sheet->cell('M'.$i, $value->mes8); 
                        $sheet->cell('N'.$i, $value->mes9); 
                        $sheet->cell('O'.$i, $value->mes10); 
                        $sheet->cell('P'.$i, $value->mes11); 
                        $sheet->cell('Q'.$i, $value->mes12);
                        $sheet->cell('R'.$i, $value->justificacion);  

                        $k++;

                        $can=DB::table('cans')->orderBy('id', 'desc')->first();
                        $mes=$can->desc_mes;
                        $ano=$can->ano;
                        $can_id=$can->id;
                        //////////////Mes - Año   
                        $sheet->mergeCells('M4:N5');

                        $sheet->cell('M4', $mes.'_'.$ano);
                        $sheet->cell('M4', function($cell) {$cell->setFontSize(18);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });      


                        //Calcular sumatoria
                        $total_necesidad_anual = $value->necesidad_anual + $total_necesidad_anual;
                        $total_stock = $value->stock + $total_stock;
                        $mes1_total= $value->mes1 + $mes1_total;
                        $mes2_total= $value->mes2 + $mes2_total;
                        $mes3_total= $value->mes3 + $mes3_total;
                        $mes4_total= $value->mes4 + $mes4_total;
                        $mes5_total= $value->mes5 + $mes5_total;
                        $mes6_total= $value->mes6 + $mes6_total;
                        $mes7_total= $value->mes7 + $mes7_total;
                        $mes8_total= $value->mes8 + $mes8_total;
                        $mes9_total= $value->mes9 + $mes9_total;
                        $mes10_total= $value->mes10 + $mes10_total;
                        $mes11_total= $value->mes11 + $mes11_total;
                        $mes12_total= $value->mes12 + $mes12_total;
                        
                    }

                    $sheet->cell('D10', $total_stock);
                    $sheet->cell('D10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('E10', $total_necesidad_anual);
                    $sheet->cell('E10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('F10', $mes1_total);
                    $sheet->cell('F10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('G10', $mes2_total);
                    $sheet->cell('G10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('H10', $mes3_total);
                    $sheet->cell('H10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('I10', $mes4_total);
                    $sheet->cell('I10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('J10', $mes5_total);
                    $sheet->cell('J10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('K10', $mes6_total);
                    $sheet->cell('K10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('L10', $mes7_total);
                    $sheet->cell('L10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('M10', $mes8_total);
                    $sheet->cell('M10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('N10', $mes9_total);
                    $sheet->cell('N10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('O10', $mes10_total);
                    $sheet->cell('O10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('P10', $mes11_total);
                    $sheet->cell('P10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    $sheet->cell('Q10', $mes12_total);
                    $sheet->cell('Q10', function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                }
                
                $servicio_id=Auth::user()->servicio_id;
                $establecimiento_id=Auth::user()->establecimiento_id;

                $establecimiento=Establecimiento::find($establecimiento_id);
                $nivel=$establecimiento->nivel_id;

                if ($nivel==1)
                {
                    $table2='can_rubro';
                    $condicion2='rubro_id';
                }
                else
                {
                    $table2='can_servicio';
                    $condicion2='servicio_id';
                }

                $consulta=DB::table($table2)
                                ->where('can_id',$can_id) 
                                ->where($condicion2,$servicio_id) 
                                ->where('establecimiento_id',$establecimiento_id)
                                ->get();

                $fecha_hora=$consulta->get(0)->updated_at;
                $date = date_create($fecha_hora);
                $fecha_hora=date_format($date,'d/m/Y H:i:s');
                
                $j=$i+10;
                $m=$j-1;

                $fh=$j+2;

                $sheet->cell('C6', 'Fecha de Registro: '.$fecha_hora);  

                $now = new \DateTime();

                $sheet->cell('O'.$fh, 'Fecha y Hora de la Descarga: '.$now->format('d/m/Y H:i:s'));  
                

                $sheet->cell('C'.$m, function($cell) {$cell->setValue('____________________________________________________');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->cell('C'.$j, function($cell) {$cell->setValue('RESPONSABLE DEL ESTABLECIMIENTO DE SALUD');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('P2:Q2');
                $sheet->mergeCells('I'.$m.':'.'L'.$m);
                $sheet->cell('I'.$m, function($cell) {$cell->setValue('______________________________________');  $cell->setFontSize(14); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('I'.$j.':'.'L'.$j);
                $sheet->cell('I'.$j, function($cell) {$cell->setValue('RESPONSABLE DE LA FARMACIA');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

            });
        })->download($type);
    }
    
    ///////////////////////////10////////////////////////////////////77
    public function exportEstimacionDataPrevio($can_id,$establecimiento_id,$opt,$type)
    {
        
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion_servicio.index'));
        }

        $nivel=$establecimiento->nivel_id;

        $servicio_id=Auth::user()->servicio_id;
        $nombre_servicio=Auth::user()->nombre_servicio;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion_servicio.index'));
        }

        if ($nivel==1)
        {
            $table='estimacion_rubro';
            $condicion1='rubro_id';
        }
        else
        {
            $table='estimacion_servicio';
            $condicion1='servicio_id';
        }


        if($opt==1){
            $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
        }else
            {   if ($opt==2) {
                    $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion_servicio.index'));  
                }
        }

        $archivo='Borrador_CAN_'.$nombre_servicio.'_'.$can->desc_mes.'_'.$can->ano;
        return Excel::create($archivo, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {   
                
                //6 filas agrupadas con las columnas A,B,C
                $sheet->setMergeColumn(array(
                    'columns' => array('A','R'),
                    'rows' => array(
                        array(1,6)                        
                    )
                ));

                //INSERTAR LOGOS
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo_sanidad.png')); //your image path
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                
                $objDrawing2 = new PHPExcel_Worksheet_Drawing;
                $objDrawing2->setPath(public_path('img/logo_pnp.png')); //your image path
                $objDrawing2->setCoordinates('R1');
                $objDrawing2->setWorksheet($sheet);
    
                $sheet->mergeCells('B1:O1');
                $sheet->cell('B1', function($cell) {$cell->setValue('CAN');  $cell->setFontSize(38); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle');
                });


                $sheet->mergeCells('B2:O2');
                $sheet->mergeCells('B3:O3');
                $sheet->cell('B2', function($cell) {$cell->setValue('CUADRO ANUAL DE NECESIDADES ');  $cell->setFontSize(28); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });
                
                $nombre_servicio=Auth::user()->nombre_servicio;
                $nombre_establecimiento=Auth::user()->nombre_establecimiento;

                
                $sheet->cell('B3', $nombre_establecimiento.' - '.$nombre_servicio);
                $sheet->cell('B3', function($cell) {$cell->setFontSize(18); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });      


                $sheet->setHeight(1, 40);
                $sheet->setHeight(2, 30);
                $sheet->setHeight(3, 20);
                
                //$sheet->mergeCells('R2:Q2');
                $sheet->mergeCells('K4:L5');
                $sheet->cell('K4', function($cell) {$cell->setValue('MES / AÑO');   $cell->setFontSize(18); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });


                $sheet->setMergeColumn(array(
                    'columns' => array('A','B','C','R'),
                    'rows' => array(
                        array(8,10)                        
                    )
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('D','E'),
                    'rows' => array(
                        array(8,9)                        
                    )
                ));

                $sheet->cell('A8', function($cell) {$cell->setValue('N°'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('B8', function($cell) {$cell->setValue('COD MED'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('C8', function($cell) {$cell->setValue('NOMBRE DEL PRODUCTO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                //$sheet->cell('D8', function($cell) {$cell->setValue('STOCK'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('D8', function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('E9', function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                
                $sheet->cell('F9', function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('G9', function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('H9', function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('I9', function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                
                $sheet->cell('J9', function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('K9', function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('L9', function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('M9', function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                
                $sheet->cell('N9', function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('O9', function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('P9', function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                //$sheet->cell('Q8', function($cell) {$cell->setValue('JUSTIFICACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->mergeCells('D8:P8');
                $sheet->cell('D8', function($cell) {$cell->setValue('PRORRATEO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->setWidth(array(
                    'A'     =>  5,
                    'B'     =>  15,
                    'C'     =>  100,
                    'D'     =>  10,
                    'E'     =>  10,
                    'F'     =>  10,
                    'G'     =>  10,
                    'H'     =>  10,
                    'I'     =>  10,
                    'J'     =>  10,
                    'K'     =>  10,
                    'L'     =>  10,
                    'M'     =>  10,
                    'N'     =>  10,
                    'O'     =>  10,
                    'P'     =>  10,
                    'Q'     =>  10,
                    //'R'     =>  100,
                ));

                //fila
                $i=10;
                //ordenar
                $k=1;

                if (!empty($data)) {

                    $total_necesidad_anual = 0;
                    $total_stock = 0;
                    $mes1_total = 0;
                    $mes2_total = 0;
                    $mes3_total = 0;
                    $mes4_total = 0;
                    $mes5_total = 0;
                    $mes6_total = 0;
                    $mes7_total = 0;
                    $mes8_total = 0;
                    $mes9_total = 0;
                    $mes10_total = 0;
                    $mes11_total = 0;
                    $mes12_total = 0;
                    

                    foreach ($data as $key => $value) {
                        $i= $key+11;
                        $sheet->cell('A'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('B'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('C'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('D'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('E'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('F'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('G'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('H'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('I'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('J'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('K'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('L'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('M'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('N'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('O'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('P'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        //$sheet->cell('Q'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        //$sheet->cell('R'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        
                        $sheet->cell('A'.$i, $k); 
                        $sheet->cell('B'.$i, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$i, $value->descripcion); //nivel
                        //$sheet->cell('D'.$i, $value->stock); //items 
                        $sheet->cell('D'.$i, $value->necesidad_anual); 
                        $sheet->cell('E'.$i, $value->mes1); 
                        $sheet->cell('F'.$i, $value->mes2); 
                        $sheet->cell('G'.$i, $value->mes3); 
                        $sheet->cell('H'.$i, $value->mes4); 
                        $sheet->cell('I'.$i, $value->mes5); 
                        $sheet->cell('J'.$i, $value->mes6); 
                        $sheet->cell('K'.$i, $value->mes7); 
                        $sheet->cell('L'.$i, $value->mes8); 
                        $sheet->cell('M'.$i, $value->mes9); 
                        $sheet->cell('N'.$i, $value->mes10); 
                        $sheet->cell('O'.$i, $value->mes11); 
                        $sheet->cell('P'.$i, $value->mes12);
                        //$sheet->cell('R'.$i, $value->justificacion);  

                        $k++;

                        $anomes=DB::table('cans')->orderBy('id', 'desc')->first();
                        $mes=$anomes->desc_mes;
                        $ano=$anomes->ano;
                        //////////////Mes - Año   
                        $sheet->mergeCells('M4:N5');

                        $sheet->cell('M4', $mes.'_'.$ano);
                        $sheet->cell('M4', function($cell) {$cell->setFontSize(18);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });      


                        //Calcular sumatoria
                        $total_necesidad_anual = $value->necesidad_anual + $total_necesidad_anual;
                        $total_stock = $value->stock + $total_stock;
                        $mes1_total= $value->mes1 + $mes1_total;
                        $mes2_total= $value->mes2 + $mes2_total;
                        $mes3_total= $value->mes3 + $mes3_total;
                        $mes4_total= $value->mes4 + $mes4_total;
                        $mes5_total= $value->mes5 + $mes5_total;
                        $mes6_total= $value->mes6 + $mes6_total;
                        $mes7_total= $value->mes7 + $mes7_total;
                        $mes8_total= $value->mes8 + $mes8_total;
                        $mes9_total= $value->mes9 + $mes9_total;
                        $mes10_total= $value->mes10 + $mes10_total;
                        $mes11_total= $value->mes11 + $mes11_total;
                        $mes12_total= $value->mes12 + $mes12_total;
                    }

                    //$sheet->cell('D10', $total_stock);
                    //$sheet->cell('D10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('D10', $total_necesidad_anual);
                    $sheet->cell('D10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('E10', $mes1_total);
                    $sheet->cell('E10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('F10', $mes2_total);
                    $sheet->cell('F10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('G10', $mes3_total);
                    $sheet->cell('G10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('H10', $mes4_total);
                    $sheet->cell('H10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('I10', $mes5_total);
                    $sheet->cell('I10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('J10', $mes6_total);
                    $sheet->cell('J10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('K10', $mes7_total);
                    $sheet->cell('K10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('L10', $mes8_total);
                    $sheet->cell('L10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('M10', $mes9_total);
                    $sheet->cell('M10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('N10', $mes10_total);
                    $sheet->cell('N10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('O10', $mes11_total);
                    $sheet->cell('O10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('P10', $mes12_total);
                    $sheet->cell('P10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                }
                

            });
        })->download($type);
    }

    public function pdf_estimacion_servicio($can_id,$establecimiento_id,$tipo,$id_user=0,$ano)
    {
        

        $establecimiento = Establecimiento::find($establecimiento_id);

        

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($id_user==0):
            $establecimiento_id=Auth::user()->establecimiento_id;
            $name=Auth::user()->name;
            $servicio_id=Auth::user()->servicio_id;
            $user_id=Auth::user()->id;
            $cip=Auth::user()->cip;
            $dni=Auth::user()->dni;
            $nombre_rubro=Auth::user()->nombre_servicio;            
        else:
            $usuario=DB::table('responsables')
            //->join('users', 'users.dni', 'responsables.dni')
            ->where('responsables.id',$id_user)->get();
            $name=$usuario->get(0)->nombre;
            $servicio_id=$usuario->get(0)->servicio_id;
            $user_id=$usuario->get(0)->id;
            $cip=$usuario->get(0)->cip;
            $dni=$usuario->get(0)->dni;
            $nombre_rubro=$usuario->get(0)->nombre_servicio;            
        endif;

        $texto='RUBRO';
        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('petitorio',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->get();
        
            $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('petitorio',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacion_servicio')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','mes1_1','mes2_1','mes3_1','mes4_1','mes5_1','mes6_1','mes7_1','mes8_1','mes9_1','mes10_1','mes11_1','mes12_1','mes1_2','mes2_2','mes3_2','mes4_2','mes5_2','mes6_2','mes7_2','mes8_2','mes9_2','mes10_2','mes11_2','mes12_2','necesidad_anual','necesidad_anual_1','necesidad_anual_2','necesidad_anterior','necesidad_anterior_1','necesidad_anterior_2','estado_necesidad','estado'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','mes1_1','mes2_1','mes3_1','mes4_1','mes5_1','mes6_1','mes7_1','mes8_1','mes9_1','mes10_1','mes11_1','mes12_1','mes1_2','mes2_2','mes3_2','mes4_2','mes5_2','mes6_2','mes7_2','mes8_2','mes9_2','mes10_2','mes11_2','mes12_2','necesidad_anual','necesidad_anual_1','necesidad_anual_2','necesidad_anterior','necesidad_anterior_1','necesidad_anterior_2','estado_necesidad','estado')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('petitorio',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1  
                    ->orderby ('tipo_dispositivo_id','asc')                  
                    ->get();     

                    $num_estimaciones=DB::table('estimacion_servicio')
                        ->where('necesidad_anual','>',0)
                        ->where('servicio_id',$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('estado','<>',2)
                        ->where('petitorio',1)
                        //->where('estado_necesidad',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }


        //$rubro=DB::table('users')->where('id',$user_id)->get();
        

        $nombre_pdf=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_'.$descripcion_tipo;

        $cierre_rubro=DB::table('can_servicio')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id',$servicio_id)->get();
        
        $cierre=$cierre_rubro->get(0)->updated_at;
        
        if($ano==1):
            $pdf = \PDF::loadView('site.pdf.descargar_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]);
            /*return view('site.pdf.descargar_servicio_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('can_id', $can_id)->with('servicio_id', $servicio_id);
            */
        endif;

        if($ano==2):
            $pdf = \PDF::loadView('site.pdf.descargar_servicio_2_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]);
            /*return view('site.pdf.descargar_servicio_2_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('can_id', $can_id)->with('servicio_id', $servicio_id);*/
        endif;

        if($ano==3):
            $pdf = \PDF::loadView('site.pdf.descargar_servicio_3_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]);
            /*return view('site.pdf.descargar_servicio_3_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('can_id', $can_id)->with('servicio_id', $servicio_id);*/
        endif;

        $stock_cerrado=DB::table('can_servicio')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('servicio_id',$servicio_id)//cambiar 1
                    ->get(); 
        $actualizado = $stock_cerrado->get(0)->actualizacion;
        if($actualizado==1):
            $pdf = \PDF::loadView('site.pdf.descargar_servicio_4_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]);
        endif;                            

        
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true); 

        return $pdf->stream($nombre_pdf.'.pdf');
        
     } 

     public function pdf_servicio_rectificacion($can_id,$establecimiento_id,$tipo)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        $name=Auth::user()->name;

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;
        $user_id=Auth::user()->id;
        $cip=Auth::user()->cip;
        $dni=Auth::user()->dni;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->get();
        
            $num_estimaciones=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacion_servicio')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','necesidad_actual','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion','estado','cpma_anterior','necesidad_anterior'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','necesidad_actual','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion','estado','cpma_anterior','necesidad_anterior')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1  
                    ->orderby ('tipo_dispositivo_id','asc')                  
                    ->get();     

                    $num_estimaciones=DB::table('estimacion_servicio')
                        ->where('necesidad_anual','>',0)
                        ->where('servicio_id',$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }


        $rubro=DB::table('users')->where('id',$user_id)->get();
        $nombre_rubro=$rubro->get(0)->nombre_servicio;
        $texto='RUBRO';

        $nombre_pdf=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_'.$descripcion_tipo;

        $cierre_rubro=DB::table('can_servicio')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id',$servicio_id)->get();
        $cierre=$cierre_rubro->get(0)->updated_rectificacion;
    
        $pdf = \PDF::loadView('site.pdf.descargar_servicio_rectificacion_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream($nombre_pdf.'.pdf');
        
    }

    public function update_atenciones($establecimiento_id, Request $request)
        {
            $model_can = new Can();
            $encontrado = $model_can->BuscaCanEstablecimientoServicio($request->can_id,$establecimiento_id, $request->servicio_id);

            if ($encontrado->total>0) {
                DB::table('can_servicio')
                    ->where('can_servicio.can_id',$request->can_id)
                    ->where('can_servicio.establecimiento_id', $establecimiento_id)
                    ->where('can_servicio.servicio_id', $request->servicio_id)
                    ->update([
                        'atenciones' => $request->atenciones
                ]);
            
                if($request->redireccion==2): //
                    Flash::success('Guardado correctamente.');
                    return redirect(route('estimacion_servicio.cargar_medicamentos_servicios',[$request->can_id,$establecimiento_id,$request->tipo]));
                else:
                    Flash::success('Guardado correctamente.');
                    return redirect(route('estimacion_servicio.index'));
                endif;
            }
            else
            {
                Flash::error('Error no se encuentro el registro'); 
                return redirect(route('estimacion_servicio.index'));
            }    

            
        } 

    public function editar_atenciones_c($can_id)
        {
            $establecimiento_id=Auth::user()->establecimiento_id;
            $establecimiento = Establecimiento::find($establecimiento_id);

            $servicio_id=Auth::user()->servicio_id;
            

            $servicio_cerrado=DB::table('can_servicio') ->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id',$servicio_id)->get();  

            $atenciones = $servicio_cerrado->get(0)->atenciones; 
            
            
            $redireccion = 1; $tipo = 1;

            return view('site.estimacion_servicio.atenciones.edit')->with('can_id', $can_id)->with('tipo', $tipo)->with('establecimiento_id', $establecimiento_id)->with('establecimiento', $establecimiento)->with('atenciones', $atenciones)->with('redireccion',$redireccion)->with('servicio_id',$servicio_id);
                

            
        }

    public function exportDataNivel2y3($can_id,$establecimiento_id,$servicio_id,$opt,$type,$valor)
    {

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;

        $servicio_id=Auth::user()->servicio_id;
        $nombre_servicio=Auth::user()->nombre_servicio;
        $rol=Auth::user()->rol;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        $model_estimacion = new Estimacion();

        if($opt==1){
            $data = $model_estimacion->ConsultaEstimacionFarmaceuticosNivel2y3($can_id, $establecimiento_id, $opt, $valor, $servicio_id);
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data = $model_estimacion->ConsultaEstimacionDispositivosNivel2y3($can_id, $establecimiento_id, $opt, $valor, $servicio_id );
                    $nombre_producto='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        } 

        $archivo='CAN_Consolidado_'.$nombre_establecimiento.'_'.$can->desc_mes.'_'.$can->ano;
        return Excel::create($archivo, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {   
                
                //6 filas agrupadas con las columnas A,B,C
                $sheet->setMergeColumn(array(
                    'columns' => array('A','Q'),
                    'rows' => array(
                        array(1,6)                        
                    )
                ));

                //INSERTAR LOGOS
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo_sanidad.png')); //your image path
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                
                $objDrawing2 = new PHPExcel_Worksheet_Drawing;
                $objDrawing2->setPath(public_path('img/logo_pnp.png')); //your image path
                $objDrawing2->setCoordinates('Q1');
                $objDrawing2->setWorksheet($sheet);
    
                $sheet->mergeCells('B1:O1');
                $sheet->cell('B1', function($cell) {$cell->setValue('CAN');  $cell->setFontSize(38); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle');
                });


                $sheet->mergeCells('B2:O2');
                $sheet->mergeCells('B3:O3');
                $sheet->cell('B2', function($cell) {$cell->setValue('CUADRO ANUAL DE NECESIDADES ');  $cell->setFontSize(28); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });
                
                $nombre_servicio=Auth::user()->nombre_servicio;
                $nombre_establecimiento=Auth::user()->nombre_establecimiento;

                
                $sheet->cell('B3', $nombre_establecimiento);
                $sheet->cell('B3', function($cell) {$cell->setFontSize(18); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });      


                $sheet->setHeight(1, 40);
                $sheet->setHeight(2, 30);
                $sheet->setHeight(3, 20);
                
                //$sheet->mergeCells('R2:Q2');
                $sheet->mergeCells('K4:L5');
                $sheet->cell('K4', function($cell) {$cell->setValue('MES / AÑO');   $cell->setFontSize(18); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });


                $sheet->setWidth(array(
                    'A'     =>  5,
                    'B'     =>  15,
                    'C'     =>  100,
                    'D'     =>  10,
                    'E'     =>  10,
                    'F'     =>  10,
                    'G'     =>  10,
                    'H'     =>  10,
                    'I'     =>  10,
                    'J'     =>  10,
                    'K'     =>  10,
                    'L'     =>  10,
                    'M'     =>  10,
                    'N'     =>  10,
                    'O'     =>  10,
                    'P'     =>  10,
                    'Q'     =>  10,                                        
                ));

                //fila
                $i=10;
                //ordenar
                $k=1;

                if (!empty($data)) {

                    $tipo_dispositivo_anterior=0;
                    $m=1; $k=6;

                    foreach ($data as $key => $value) {

                        if($tipo_dispositivo_anterior!=$value->tipo_dispositivo_id){

                            $cpma_total = 0; $total_necesidad_anual = 0; $mes1_total = 0; $mes2_total = 0; $mes3_total = 0; $mes4_total = 0; $mes5_total = 0; $mes6_total = 0; $mes7_total = 0; $mes8_total = 0; $mes9_total = 0; $mes10_total = 0; $mes11_total = 0; $mes12_total = 0;
                            $cpma_total_1 = 0; $total_necesidad_anual_1 = 0; $mes1_total_1 = 0; $mes2_total_1 = 0; $mes3_total_1 = 0; $mes4_total_1 = 0; $mes5_total_1 = 0; $mes6_total_1 = 0; $mes7_total_1 = 0; $mes8_total_1 = 0; $mes9_total_1 = 0; $mes10_total_1 = 0; $mes11_total_1 = 0; $mes12_total_1 = 0;
                            $cpma_total_2 = 0; $total_necesidad_anual_2 = 0; $mes1_total_2 = 0; $mes2_total_2 = 0; $mes3_total_2 = 0; $mes4_total_2 = 0; $mes5_total_2 = 0; $mes6_total_2 = 0; $mes7_total_2 = 0; $mes8_total_2 = 0; $mes9_total_2 = 0; $mes10_total_2 = 0; $mes11_total_2 = 0; $mes12_total_2 = 0;

                            
                            $descripcion_tipo_dispositivo=DB::table('tipo_dispositivo_medicos')->where('id',$value->tipo_dispositivo_id)->get();

                            $nombre_dispositivo=$descripcion_tipo_dispositivo->get(0)->descripcion;

                            $i=$k+2;
                            $j=$i+1;   
                            $d=$i-1;     
                            //construimos cabecera
                            $sheet->cell('A'.$i, function($cell) {$cell->setValue('N°'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('B'.$i, function($cell) {$cell->setValue('COD MED'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('C'.$i, function($cell) {$cell->setValue('NOMBRE DEL PRODUCTO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            

                            $sheet->cell('D'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            //$sheet->cell('F'.$i, function($cell) {$cell->setValue('OBSERVACIÓN'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('E'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('F'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('G'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('H'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('I'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('J'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('K'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('L'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('M'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('N'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('O'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('P'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            

                            $sheet->cell('Q'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('R'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('S'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('T'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('U'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('V'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('W'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('X'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('Y'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('Z'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AA'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AB'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AC'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                           

                            $sheet->cell('AD'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AE'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AF'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AG'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AH'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AI'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AJ'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AK'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AL'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AM'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AN'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AO'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AP'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            

                            $sheet->mergeCells('E'.$i.':P'.$i);
                            $sheet->cell('E'.$i, function($cell) {$cell->setValue('PRORRATEO AÑO 1'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->mergeCells('R'.$i.':AC'.$i);
                            $sheet->cell('R'.$i, function($cell) {$cell->setValue('PRORRATEO AÑO 2'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->mergeCells('AE'.$i.':AP'.$i);
                            $sheet->cell('AE'.$i, function($cell) {$cell->setValue('PRORRATEO AÑO 3'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->mergeCells('A'.$d.':AP'.$d);
                            $sheet->cell('Q'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            //$sheet->mergeCells('R'.$d.':AF'.$d);
                            //$sheet->cell('O'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            //$sheet->mergeCells('AG'.$d.':AT'.$d);
                            //$sheet->cell('O'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->cell('A'.$d, $nombre_dispositivo); //establecimiento 

                            $n=$i+2;
                            
                            $sheet->setMergeColumn(array(
                                'columns' => array('A','B','C'),
                                'rows' => array(
                                    array($i,$n)                        
                                )
                            ));

                            $sheet->setMergeColumn(array(
                                'columns' => array('D','Q','AD'),
                                'rows' => array(
                                    array($i,$j)                        
                                )
                            ));

                            $k=$i+3;
                            $tipo_dispositivo_anterior=$value->tipo_dispositivo_id;
                            $m=1;


                        }


                        
                        $sheet->cell('A'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('B'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('C'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('D'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('E'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('F'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('G'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('H'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('I'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('J'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('K'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('L'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('M'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('N'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('O'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('P'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('Q'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('R'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('S'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('T'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('U'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('V'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('W'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('X'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('Y'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('Z'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AA'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AB'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AC'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AD'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AE'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AF'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AG'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AH'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AI'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('AJ'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('AK'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('AL'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AM'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AN'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AO'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AP'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        
                        
                        
                        /*switch ($value->estado) {                                    
                                    case 0: $descripcion_observacion=' Ratificado '; break;
                                    case 1: $descripcion_observacion=' Nuevo '; break;
                                    case 2: $descripcion_observacion=' Eliminado '; break;
                                    case 3: $descripcion_observacion=' Actualizado, cpma_ant='.$value->cpma_anterior.' nec_ant='.$value->necesidad_anterior; break;   
                                }
                        */
                        $sheet->cell('A'.$k, $m); 
                        $sheet->cell('B'.$k, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$k, $value->descripcion); //nivel
                        if($value->necesidad_anual>0){ $sheet->cell('D'.$k, $value->necesidad_anual);}else{$sheet->cell('D'.$k, number_format($value->necesidad_anual, 2, '.', ','));}
                        if($value->mes1>0){ $sheet->cell('E'.$k, $value->mes1);}else{$sheet->cell('E'.$k, number_format($value->mes1, 2, '.', ','));}
                        if($value->mes2>0){ $sheet->cell('F'.$k, $value->mes2);}else{$sheet->cell('F'.$k, number_format($value->mes2, 2, '.', ','));}
                        if($value->mes3>0){ $sheet->cell('G'.$k, $value->mes3);}else{$sheet->cell('G'.$k, number_format($value->mes3, 2, '.', ','));}
                        if($value->mes4>0){ $sheet->cell('H'.$k, $value->mes4);}else{$sheet->cell('H'.$k, number_format($value->mes4, 2, '.', ','));}
                        if($value->mes5>0){ $sheet->cell('I'.$k, $value->mes5);}else{$sheet->cell('I'.$k, number_format($value->mes5, 2, '.', ','));}
                        if($value->mes6>0){ $sheet->cell('J'.$k, $value->mes6);}else{$sheet->cell('J'.$k, number_format($value->mes6, 2, '.', ','));}
                        if($value->mes7>0){ $sheet->cell('K'.$k, $value->mes7);}else{$sheet->cell('K'.$k, number_format($value->mes7, 2, '.', ','));}
                        if($value->mes8>0){ $sheet->cell('L'.$k, $value->mes8);}else{$sheet->cell('L'.$k, number_format($value->mes8, 2, '.', ','));}
                        if($value->mes9>0){ $sheet->cell('M'.$k, $value->mes9);}else{$sheet->cell('M'.$k, number_format($value->mes9, 2, '.', ','));}
                        if($value->mes10>0){ $sheet->cell('N'.$k, $value->mes10);}else{$sheet->cell('N'.$k, number_format($value->mes10, 2, '.', ','));}
                        if($value->mes11>0){ $sheet->cell('O'.$k, $value->mes11);}else{$sheet->cell('O'.$k, number_format($value->mes11, 2, '.', ','));}
                        if($value->mes12>0){ $sheet->cell('P'.$k, $value->mes12);}else{$sheet->cell('P'.$k, number_format($value->mes12, 2, '.', ','));}

                        
                        if($value->necesidad_anual_1>0){ $sheet->cell('Q'.$k, $value->necesidad_anual_1);}else{$sheet->cell('Q'.$k, number_format($value->necesidad_anual_1, 2, '.', ','));}
                        if($value->mes1_1>0){ $sheet->cell('R'.$k, $value->mes1_1);}else{$sheet->cell('R'.$k, number_format($value->mes1_1, 2, '.', ','));}
                        if($value->mes2_1>0){ $sheet->cell('S'.$k, $value->mes2_1);}else{$sheet->cell('S'.$k, number_format($value->mes2_1, 2, '.', ','));}
                        if($value->mes3_1>0){ $sheet->cell('T'.$k, $value->mes3_1);}else{$sheet->cell('T'.$k, number_format($value->mes3_1, 2, '.', ','));}
                        if($value->mes4_1>0){ $sheet->cell('U'.$k, $value->mes4_1);}else{$sheet->cell('U'.$k, number_format($value->mes4_1, 2, '.', ','));}
                        if($value->mes5_1>0){ $sheet->cell('V'.$k, $value->mes5_1);}else{$sheet->cell('V'.$k, number_format($value->mes5_1, 2, '.', ','));}
                        if($value->mes6_1>0){ $sheet->cell('W'.$k, $value->mes6_1);}else{$sheet->cell('W'.$k, number_format($value->mes6_1, 2, '.', ','));}
                        if($value->mes7_1>0){ $sheet->cell('X'.$k, $value->mes7_1);}else{$sheet->cell('X'.$k, number_format($value->mes7_1, 2, '.', ','));}
                        if($value->mes8_1>0){ $sheet->cell('Y'.$k, $value->mes8_1);}else{$sheet->cell('Y'.$k, number_format($value->mes8_1, 2, '.', ','));}
                        if($value->mes9_1>0){ $sheet->cell('Z'.$k, $value->mes9_1);}else{$sheet->cell('Z'.$k, number_format($value->mes9_1, 2, '.', ','));}
                        if($value->mes10_1>0){ $sheet->cell('AA'.$k, $value->mes10_1);}else{$sheet->cell('AA'.$k, number_format($value->mes10_1, 2, '.', ','));}
                        if($value->mes11_1>0){ $sheet->cell('AB'.$k, $value->mes11_1);}else{$sheet->cell('AB'.$k, number_format($value->mes11_1, 2, '.', ','));}
                        if($value->mes12_1>0){ $sheet->cell('AC'.$k, $value->mes12_1);}else{$sheet->cell('AC'.$k, number_format($value->mes12_1, 2, '.', ','));}

                        
                        if($value->necesidad_anual_2>0){ $sheet->cell('AD'.$k, $value->necesidad_anual_2);}else{$sheet->cell('AD'.$k, number_format($value->necesidad_anual_2, 2, '.', ','));}
                        if($value->mes1_2>0){ $sheet->cell('AE'.$k, $value->mes1_2);}else{$sheet->cell('AE'.$k, number_format($value->mes1_2, 2, '.', ','));}
                        if($value->mes2_2>0){ $sheet->cell('AF'.$k, $value->mes2_2);}else{$sheet->cell('AF'.$k, number_format($value->mes2_2, 2, '.', ','));}
                        if($value->mes3_2>0){ $sheet->cell('AG'.$k, $value->mes3_2);}else{$sheet->cell('AG'.$k, number_format($value->mes3_2, 2, '.', ','));}
                        if($value->mes4_2>0){ $sheet->cell('AH'.$k, $value->mes4_2);}else{$sheet->cell('AH'.$k, number_format($value->mes4_2, 2, '.', ','));}
                        if($value->mes5_2>0){ $sheet->cell('AI'.$k, $value->mes5_2);}else{$sheet->cell('AI'.$k, number_format($value->mes5_2, 2, '.', ','));}
                        if($value->mes6_2>0){ $sheet->cell('AJ'.$k, $value->mes6_2);}else{$sheet->cell('AJ'.$k, number_format($value->mes6_2, 2, '.', ','));}
                        if($value->mes7_2>0){ $sheet->cell('AK'.$k, $value->mes7_2);}else{$sheet->cell('AK'.$k, number_format($value->mes7_2, 2, '.', ','));}
                        if($value->mes8_2>0){ $sheet->cell('AL'.$k, $value->mes8_2);}else{$sheet->cell('AL'.$k, number_format($value->mes8_2, 2, '.', ','));}
                        if($value->mes9_2>0){ $sheet->cell('AM'.$k, $value->mes9_2);}else{$sheet->cell('AM'.$k, number_format($value->mes9_2, 2, '.', ','));}
                        if($value->mes10_2>0){ $sheet->cell('AN'.$k, $value->mes10_2);}else{$sheet->cell('AN'.$k, number_format($value->mes10_2, 2, '.', ','));}
                        if($value->mes11_2>0){ $sheet->cell('AO'.$k, $value->mes11_2);}else{$sheet->cell('AO'.$k, number_format($value->mes11_2, 2, '.', ','));}
                        if($value->mes12_2>0){ $sheet->cell('AP'.$k, $value->mes12_2);}else{$sheet->cell('AP'.$k, number_format($value->mes12_2, 2, '.', ','));}

                        $k++;
                        $m++;
                        
                        $can=DB::table('cans')->orderBy('id', 'desc')->first();
                        $mes=$can->desc_mes;
                        $ano=$can->ano;
                        $can_id=$can->id;
                        //////////////Mes - Año   
                        $sheet->mergeCells('M4:P5');

                        $sheet->cell('M4', $mes.'_'.$ano);
                        $sheet->cell('M4', function($cell) {$cell->setFontSize(18);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });      


                        //Calcular sumatoria
                        $total_necesidad_anual = $value->necesidad_anual + $total_necesidad_anual; $mes1_total= $value->mes1 + $mes1_total; $mes2_total= $value->mes2 + $mes2_total; $mes3_total= $value->mes3 + $mes3_total; $mes4_total= $value->mes4 + $mes4_total; $mes5_total= $value->mes5 + $mes5_total; $mes6_total= $value->mes6 + $mes6_total; $mes7_total= $value->mes7 + $mes7_total; $mes8_total= $value->mes8 + $mes8_total; $mes9_total= $value->mes9 + $mes9_total; $mes10_total= $value->mes10 + $mes10_total; $mes11_total= $value->mes11 + $mes11_total; $mes12_total= $value->mes12 + $mes12_total; $cpma_total= $value->cpma + $cpma_total;

                        $total_necesidad_anual_1 = $value->necesidad_anual_1 + $total_necesidad_anual_1; $mes1_total_1= $value->mes1_1 + $mes1_total_1; $mes2_total_1= $value->mes2_1 + $mes2_total_1; $mes3_total_1= $value->mes3_1 + $mes3_total_1; $mes4_total_1= $value->mes4_1 + $mes4_total_1; $mes5_total_1= $value->mes5_1 + $mes5_total_1; $mes6_total_1= $value->mes6_1 + $mes6_total_1; $mes7_total_1= $value->mes7_1 + $mes7_total_1; $mes8_total_1= $value->mes8_1 + $mes8_total_1; $mes9_total_1= $value->mes9_1 + $mes9_total_1; $mes10_total_1= $value->mes10_1 + $mes10_total_1; $mes11_total_1= $value->mes11_1 + $mes11_total_1; $mes12_total_1= $value->mes12_1 + $mes12_total_1; $cpma_total_1= $value->cpma_1 + $cpma_total_1;

                        $total_necesidad_anual_2 = $value->necesidad_anual_2 + $total_necesidad_anual_2; $mes1_total_2= $value->mes1_2 + $mes1_total_2; $mes2_total_2= $value->mes2_2 + $mes2_total_2; $mes3_total_2= $value->mes3_2 + $mes3_total_2; $mes4_total_2= $value->mes4_2 + $mes4_total_2; $mes5_total_2= $value->mes5_2 + $mes5_total_2; $mes6_total_2= $value->mes6_2 + $mes6_total_2; $mes7_total_2= $value->mes7_2 + $mes7_total_2; $mes8_total_2= $value->mes8_2 + $mes8_total_2; $mes9_total_2= $value->mes9_2 + $mes9_total_2; $mes10_total_2= $value->mes10_2 + $mes10_total_2; $mes11_total_2= $value->mes11_2 + $mes11_total_2; $mes12_total_2= $value->mes12_2 + $mes12_total_2; $cpma_total_2= $value->cpma_2 + $cpma_total_2;
                        
                    
                    if($total_necesidad_anual>0){ $sheet->cell('D'.$n, $total_necesidad_anual);}else{$sheet->cell('D'.$n, number_format($total_necesidad_anual, 2, '.', ','));}
                    $sheet->cell('D'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total>0){ $sheet->cell('E'.$n, $mes1_total);}else{$sheet->cell('E'.$n, number_format($mes1_total, 2, '.', ','));}
                    $sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total>0){ $sheet->cell('F'.$n, $mes2_total);}else{$sheet->cell('F'.$n, number_format($mes2_total, 2, '.', ','));}
                    $sheet->cell('F'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total>0){ $sheet->cell('G'.$n, $mes3_total);}else{$sheet->cell('G'.$n, number_format($mes3_total, 2, '.', ','));}
                    $sheet->cell('G'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total>0){ $sheet->cell('H'.$n, $mes4_total);}else{$sheet->cell('H'.$n, number_format($mes4_total, 2, '.', ','));}
                    $sheet->cell('H'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total>0){ $sheet->cell('I'.$n, $mes5_total);}else{$sheet->cell('I'.$n, number_format($mes5_total, 2, '.', ','));}
                    $sheet->cell('I'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total>0){ $sheet->cell('J'.$n, $mes6_total);}else{$sheet->cell('J'.$n, number_format($mes6_total, 2, '.', ','));}
                    $sheet->cell('J'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total>0){ $sheet->cell('K'.$n, $mes7_total);}else{$sheet->cell('K'.$n, number_format($mes7_total, 2, '.', ','));}
                    $sheet->cell('K'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total>0){ $sheet->cell('L'.$n, $mes8_total);}else{$sheet->cell('L'.$n, number_format($mes8_total, 2, '.', ','));}
                    $sheet->cell('L'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total>0){ $sheet->cell('M'.$n, $mes9_total);}else{$sheet->cell('M'.$n, number_format($mes9_total, 2, '.', ','));}
                    $sheet->cell('M'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total>0){ $sheet->cell('N'.$n, $mes10_total);}else{$sheet->cell('N'.$n, number_format($mes10_total, 2, '.', ','));}
                    $sheet->cell('N'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total>0){ $sheet->cell('O'.$n, $mes11_total);}else{$sheet->cell('O'.$n, number_format($mes11_total, 2, '.', ','));}
                    $sheet->cell('O'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total>0){ $sheet->cell('P'.$n, $mes12_total);}else{$sheet->cell('P'.$n, number_format($mes12_total, 2, '.', ','));}
                    $sheet->cell('P'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });

                    
                    
                    if($total_necesidad_anual_1>0){ $sheet->cell('Q'.$n, $total_necesidad_anual_1);}else{$sheet->cell('Q'.$n, number_format($total_necesidad_anual_1, 2, '.', ','));}
                    $sheet->cell('Q'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total_1>0){ $sheet->cell('R'.$n, $mes1_total_1);}else{$sheet->cell('R'.$n, number_format($mes1_total_1, 2, '.', ','));}
                    $sheet->cell('R'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total_1>0){ $sheet->cell('S'.$n, $mes2_total_1);}else{$sheet->cell('S'.$n, number_format($mes2_total_1, 2, '.', ','));}
                    $sheet->cell('S'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total_1>0){ $sheet->cell('T'.$n, $mes3_total_1);}else{$sheet->cell('T'.$n, number_format($mes3_total_1, 2, '.', ','));}
                    $sheet->cell('T'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total_1>0){ $sheet->cell('U'.$n, $mes4_total_1);}else{$sheet->cell('U'.$n, number_format($mes4_total_1, 2, '.', ','));}
                    $sheet->cell('U'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total_1>0){ $sheet->cell('V'.$n, $mes5_total_1);}else{$sheet->cell('V'.$n, number_format($mes5_total_1, 2, '.', ','));}
                    $sheet->cell('V'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total_1>0){ $sheet->cell('W'.$n, $mes6_total_1);}else{$sheet->cell('W'.$n, number_format($mes6_total_1, 2, '.', ','));}
                    $sheet->cell('W'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total_1>0){ $sheet->cell('X'.$n, $mes7_total_1);}else{$sheet->cell('X'.$n, number_format($mes7_total_1, 2, '.', ','));}
                    $sheet->cell('X'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total_1>0){ $sheet->cell('Y'.$n, $mes8_total_1);}else{$sheet->cell('Y'.$n, number_format($mes8_total_1, 2, '.', ','));}
                    $sheet->cell('Y'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total_1>0){ $sheet->cell('Z'.$n, $mes9_total_1);}else{$sheet->cell('Z'.$n, number_format($mes9_total_1, 2, '.', ','));}
                    $sheet->cell('Z'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total_1>0){ $sheet->cell('AA'.$n, $mes10_total_1);}else{$sheet->cell('AA'.$n, number_format($mes10_total_1, 2, '.', ','));}
                    $sheet->cell('AA'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total_1>0){ $sheet->cell('AB'.$n, $mes11_total_1);}else{$sheet->cell('AB'.$n, number_format($mes11_total_1, 2, '.', ','));}
                    $sheet->cell('AB'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total_1>0){ $sheet->cell('AC'.$n, $mes12_total_1);}else{$sheet->cell('AC'.$n, number_format($mes12_total_1, 2, '.', ','));}
                    $sheet->cell('AC'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });

                    
                    if($total_necesidad_anual_2>0){ $sheet->cell('AD'.$n, $total_necesidad_anual_2);}else{$sheet->cell('AD'.$n, number_format($total_necesidad_anual_2, 2, '.', ','));}
                    $sheet->cell('AD'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total_2>0){ $sheet->cell('AE'.$n, $mes1_total_2);}else{$sheet->cell('AE'.$n, number_format($mes1_total_2, 2, '.', ','));}
                    $sheet->cell('AE'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total_2>0){ $sheet->cell('AF'.$n, $mes2_total_2);}else{$sheet->cell('AF'.$n, number_format($mes2_total_2, 2, '.', ','));}
                    $sheet->cell('AF'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total_2>0){ $sheet->cell('AG'.$n, $mes3_total_2);}else{$sheet->cell('AG'.$n, number_format($mes3_total_2, 2, '.', ','));}
                    $sheet->cell('AG'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total_2>0){ $sheet->cell('AH'.$n, $mes4_total_2);}else{$sheet->cell('AH'.$n, number_format($mes4_total_2, 2, '.', ','));}
                    $sheet->cell('AH'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total_2>0){ $sheet->cell('AI'.$n, $mes5_total_2);}else{$sheet->cell('AI'.$n, number_format($mes5_total_2, 2, '.', ','));}
                    $sheet->cell('AI'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total_2>0){ $sheet->cell('AJ'.$n, $mes6_total_2);}else{$sheet->cell('AJ'.$n, number_format($mes6_total_2, 2, '.', ','));}
                    $sheet->cell('AJ'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total_2>0){ $sheet->cell('AK'.$n, $mes7_total_2);}else{$sheet->cell('AK'.$n, number_format($mes7_total_2, 2, '.', ','));}
                    $sheet->cell('AK'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total_2>0){ $sheet->cell('AL'.$n, $mes8_total_2);}else{$sheet->cell('AL'.$n, number_format($mes8_total_2, 2, '.', ','));}
                    $sheet->cell('AL'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total_2>0){ $sheet->cell('AM'.$n, $mes9_total_2);}else{$sheet->cell('AM'.$n, number_format($mes9_total_2, 2, '.', ','));}
                    $sheet->cell('AM'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total_2>0){ $sheet->cell('AN'.$n, $mes10_total_2);}else{$sheet->cell('AN'.$n, number_format($mes10_total_2, 2, '.', ','));}
                    $sheet->cell('AN'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total_2>0){ $sheet->cell('AO'.$n, $mes11_total_2);}else{$sheet->cell('AO'.$n, number_format($mes11_total_2, 2, '.', ','));}
                    $sheet->cell('AO'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total_2>0){ $sheet->cell('AP'.$n, $mes12_total_2);}else{$sheet->cell('AP'.$n, number_format($mes12_total_2, 2, '.', ','));}
                    $sheet->cell('AP'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    }
                }
                
                $servicio_id=Auth::user()->servicio_id;
                $establecimiento_id=Auth::user()->establecimiento_id;

                $establecimiento=Establecimiento::find($establecimiento_id);
                $nivel=$establecimiento->nivel_id;

                $j=$k+10;
                $m=$j-1;

                $fh=$j+2;

                $now = new \DateTime();

                $sheet->cell('O'.$j, 'Fecha y Hora de la Descarga: '.$now->format('d/m/Y H:i:s'));  
                
            });
        })->download($type);
    }

        
    



}
