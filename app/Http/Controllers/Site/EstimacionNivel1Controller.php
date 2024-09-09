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
use DB;
use App\Models\Establecimiento;
use App\Models\Can;
use Illuminate\Support\Facades\Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;      

class EstimacionNivel1Controller extends AppBaseController
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

        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $nivel=$establecimiento->nivel_id;

        //Veo el nombre del servicio o del rubro
        $nombre_servicio=Auth::user()->nombre_servicio;

        //Si el nivel es 1 (Postas Medicas, Clinicas, Policlinicos)
        if ($nivel==1)
        {
            
            $cans = DB::table('cans')
                ->join('can_rubro', 'can_rubro.can_id','cans.id')
                ->join('rubros', 'can_rubro.rubro_id','rubros.id')
                ->where('can_rubro.rubro_id',$servicio_id)
                ->where('can_rubro.establecimiento_id',$establecimiento_id)
                ->orderby('cans.id','desc')
                ->get();

            $items_medicamentos=DB::table('petitorio_rubro')
                                    ->where('rubro_id',$servicio_id)
                                    ->where('tipo_dispositivo_medico_id',1)
                                    ->count();

            $items_dispositivos=DB::table('petitorio_rubro')
                                    ->where('rubro_id',$servicio_id)
                                    ->where('tipo_dispositivo_medico_id','>',1)
                                    ->count();
        }
        
        //$anio=$cans->get(0)->ano;        
        $anio='2018'; //$cans->get(0)->ano;        
        return view('site.estimacions.index')
            ->with('nombre_servicio', $nombre_servicio)
            ->with('items_medicamentos', $items_medicamentos)
            ->with('items_dispositivos', $items_dispositivos)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nivel', $nivel)
            ->with('anio', $anio)
            ->with('cans', $cans);       
    }
        
        
    public function manual($id)
    {
        return view('site.estimacions.manual')
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

        if ($nivel==1)
        {
            $contact = Estimacion2::findOrFail($id); //estimacion_rubro
        }

        return $contact;        
    }

  public function eliminar($id)
    {
        
        $contact = Estimacion2::findOrFail($id);

        $producto = DB::table('estimacion_rubro')
                            ->where('id',$id)
                            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto Eliminado'
        ]);
    }

    public function cargar_datos_rubro($can_id,$establecimiento_id,$tipo,$cerrado)
    {

        //verificamos la farmacia del usuario
        $servicio_id=Auth::user()->servicio_id;
        
        $rubro=DB::table('rubros')->where('id',$servicio_id)->get();

        $consolidado=$rubro->get(0)->consolidado;

        //Cargamos los datos a mostrar
        $petitorio_cerrado = DB::table('can_establecimiento')
                                    ->where('establecimiento_id', $establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->get();

        //averiguamos el nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;
        
        if($nivel==1){

            $condicion='rubro_id';

            if($tipo==1){  //medicamento

                    //$contact=DB::table('abastecimientos')
                    $can=DB::table('estimacion_rubro')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->where('consolidado',$consolidado)
                                    ->where($condicion,$servicio_id)
                                    ->where('tipo_dispositivo_id',1)
                                    ->get();
                    //dd($contact);

                    //$cerrado=$petitorio_cerrado->get(0)->medicamento_cerrado;
            }
            else
            {
                        $can=DB::table('estimacion_rubro')
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->where('can_id',$can_id)
                                        ->where('consolidado',$consolidado)
                                        ->where($condicion,$servicio_id)
                                        ->where('tipo_dispositivo_id','>',1)
                                        ->get();
                    
            }        

        }
        if($cerrado==2){
                return Datatables::of($can)
                ->addColumn('action', function($contact){
                    return '<a href="#" disabled class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></a>' ;                
                })
                ->rawColumns(['justificacion', 'action'])->make(true);    
        }
        else
        {

                return Datatables::of($can)
                ->addColumn('action', function($can){
                    return '<a onclick="editForm('. $can->id .')" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i>  </a> '.
                       ' <a onclick="deleteData('. $can->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> </a>';
                })
                ->rawColumns(['justificacion', 'action'])->make(true); 

        }
    }

    public function grabar($id, UpdateEstimacionRequest $request)
    {
       
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        $nivel=$establecimiento->nivel_id;

        if ($nivel==1)
        {

            $estimacion = Estimacion2::find($id);  //distribuidor
        }
        

        if (empty($estimacion)) {
            Flash::error('Estimación no encontrado');

            return redirect(route('estimacions.index'));
        }
        
        
        $necesidad_anual = $request->input("necesidad_anual");
        $cpma = $request->input("cpma");        
        $stock = $request->input("stock");        
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
        $justificacion = $request->input("justificacion");

        
        if ($nivel==1)
        {   
            DB::table('estimacion_rubro')
            ->where('id', $id)
            ->update([
                        'necesidad_anual' => $necesidad_anual,
                        'stock' => $stock,
                        'cpma' => $cpma,
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
                        'justificacion' => $justificacion,
                        'updated_at'=>Carbon::now()
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
            $uso_id=$petitorio->tipo_uso_id;
            $descripcion=$petitorio->descripcion;
            $cod_petitorio=$petitorio->codigo_petitorio;
            
            $stock = $request->input("stock");
            $cpma = $request->input("cpma");
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
            $justificacion = $request->input("justificacion");

            
            DB::table('estimacion_rubro')
            ->insert([
                        'can_id' => $can_id,
                        'tipo_dispositivo_id'=>$tipo_dispositivo_id,
                        'descripcion'=>$descripcion,
                        'petitorio_id'=>$petitorio_id,
                        'cod_petitorio'=>$cod_petitorio,
                        'establecimiento_id'=>$establecimiento_id,
                        'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                        'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                        'rubro_id' => $servicio_id,
                        'nombre_rubro' => $nombre_servicio,
                        'necesidad_anual' => $necesidad_anual,
                        'cpma' => $cpma,
                        'stock' => $stock,
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
                        'justificacion' => $justificacion,
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

                    return redirect(route('estimacion.cargar_medicamentos',[$can_id,$establecimiento_id,1,1]));
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
                return redirect(route('estimacion.cargar_medicamentos',[$can_id,$establecimiento_id,2,1]));
            }
        }   
        
    }

    public function nuevo_medicamento_dispositivo( $can_id, $establecimiento_id, $tipo_producto )
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
                        return redirect(route('estimacion.index'));
                    }
                    
                    $nivel_sum=$establecimiento->nivel_id+1;

                    if($tipo_producto==1){ //// 1 si es medicamento

                        //Buscamos todos los medicamentos segun el nivel
                        $consulta_petitorio = DB::table('petitorios')
                                ->join('petitorio_rubro','petitorio_rubro.petitorio_id','petitorios.id')
                                ->where('tipo_dispositivo_medico_id',1)
                                ->where('rubro_id',$servicio_id)
                                ->get();
                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','petitorio_id')->toArray();
                        
                        //Buscamos los medicamentos segun el nivel 
                        $consulta_medicamentos_nivel = DB::table('estimacion_rubro')
                            ->where('tipo_dispositivo_id',1)
                            ->where('can_id',$can_id)
                            ->where('rubro_id',$servicio_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->orderby('descripcion','asc');
                            //->get();  
                        
                        
                        
                        //pasamos a un arreglo
                        $consulta_medicamento = $consulta_medicamentos_nivel->pluck('descripcion','petitorio_id')->toArray();

                        //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                        $descripcion=array_diff($petitorio,$consulta_medicamento);
                        
                    }
                    else
                    {
                        //Buscamos todos los dispositivos segun el nivel
                        $consulta_petitorio = DB::table('petitorios')
                                ->join('petitorio_rubro','petitorio_rubro.petitorio_id','petitorios.id')
                                ->where('tipo_dispositivo_medico_id','>',1)
                                ->where('rubro_id',$servicio_id)
                                ->get();

                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','petitorio_id')->toArray();
                        
                        //Buscamos en la tabla de abastecimientos_copia
                        $consulta_dispositivos_nivel = DB::table('estimacion_rubro')
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('can_id',$can_id)
                            ->where('rubro_id',$servicio_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->orderby('descripcion','asc');                        
                        
                        //pasamos a un arreglo
                        $consulta_dispositivo = $consulta_dispositivos_nivel->pluck('descripcion','petitorio_id')->toArray();

                        //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                        $descripcion=array_diff($petitorio,$consulta_dispositivo);

                        //dd($descripcion);
                    
                    }
                    
                    
                    //Enviamos al formulario
                    return view('site.estimacions.nuevo.index')
                            ->with('establecimiento_id', $establecimiento_id)
                            ->with('can_id', $can_id)
                            ->with('servicio_id', $servicio_id)
                            ->with('destino', $tipo_producto)
                            ->with('descripcion', $descripcion);

                }
                else
                {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('estimacion.index'));
                }    
        
            }
            else
            {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('estimacion.index'));
            }
        }
        else
        {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
        }
    }
    
    //////////////////Asignar medicamentos //////////////////////////////////////////
    public function cargar_medicamentos($can_id, $establecimiento_id, $tipo,$cerrado)
    {
        //Verificamos si el usuario es el mismo
        //dd($establecimiento_id);
        //Auth::user()->establecimiento_id

        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            //nombre del servicio o distribuidor
            $nombre_servicio=Auth::user()->nombre_servicio;

            //buscamos los datos del establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            
            $nivel = $establecimiento->nivel_id;
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimiento no existe CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }

            $estimacions = Estimacion::find($can_id);

            if($tipo==1){  //medicamentos
                //buscamos si existe medicamentos asignados por el administrador
                $numero_medicamentos=DB::table('estimacion_rubro')
                                ->where('rubro_id',$servicio_id)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id',1)
                                ->count(); 

                $total_medicamentos_rubro = DB::table('rubros')
                                  ->join('petitorio_rubro','petitorio_rubro.rubro_id','rubros.id')
                                  ->join('petitorios', 'petitorio_rubro.petitorio_id','petitorios.id')
                                  ->where('petitorio_rubro.rubro_id',$servicio_id)
                                  ->where('petitorio_rubro.tipo_dispositivo_medico_id',1)
                                  ->count(); 

                $diferencia=$total_medicamentos_rubro-$numero_medicamentos;

            }
            else
            {
                //buscamos si existe medicamentos asignados por el administrador
                $numero_medicamentos=DB::table('estimacion_rubro')
                                ->where('rubro_id',$servicio_id)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id','>',1)
                                ->count();

                $total_medicamentos_rubro = DB::table('rubros')
                                  ->join('petitorio_rubro','petitorio_rubro.rubro_id','rubros.id')
                                  ->join('petitorios', 'petitorio_rubro.petitorio_id','petitorios.id')
                                  ->where('petitorio_rubro.rubro_id',$servicio_id)
                                  ->where('petitorio_rubro.tipo_dispositivo_medico_id','>',1)
                                  ->count(); 

                $diferencia=$total_medicamentos_rubro-$numero_medicamentos;
                         
            } 

            //dd($establecimiento_id);
            //Si hay medicamentos asignados
            if ($numero_medicamentos==0){           

                if($tipo==1){ //medicamento 1
                    $petitorios = DB::table('rubros')
                              ->join('petitorio_rubro','petitorio_rubro.rubro_id','rubros.id')
                              ->join('petitorios', 'petitorio_rubro.petitorio_id','petitorios.id')
                              ->where('petitorio_rubro.rubro_id',$servicio_id)
                              ->where('petitorio_rubro.tipo_dispositivo_medico_id',1)
                              ->pluck('petitorios.descripcion','petitorios.id');
                }
                else
                {
                    $petitorios = DB::table('rubros')
                              ->join('petitorio_rubro','petitorio_rubro.rubro_id','rubros.id')
                              ->join('petitorios', 'petitorio_rubro.petitorio_id','petitorios.id')
                              ->where('petitorio_rubro.rubro_id',$servicio_id)
                              ->where('petitorio_rubro.tipo_dispositivo_medico_id','>',1)
                              ->pluck('petitorios.descripcion','petitorios.id');
                }

                return view('site.estimacions.medicamentos.asignar_medicamentos')
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
                return view('site.estimacions.medicamentos.medicamentos')
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('can_id', $can_id)
                                ->with('nivel', $nivel)
                                ->with('numero_medicamentos', $diferencia)
                                ->with('tipo', $tipo)
                                ->with('servicio_id', $servicio_id)
                                ->with('medicamento_cerrado', $cerrado);        
            }

        }
        else
        {
            Flash::error('No tiene acceso para ver este registro');
            return redirect(route('estimacion.index'));
        }   
        
    }

    
    public function guardar_medicamentos_asignados(UpdateEstimacionRequest $request, $establecimiento_id, $can_id,$tipo)
    {
        
        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            $rubro=DB::table('rubros')->where('id',$servicio_id)->get();

            $consolidado=$rubro->get(0)->consolidado;

            //nombre del servicio o distribuidor
            $nombre_servicio=Auth::user()->nombre_servicio;


            $establecimiento = Establecimiento::find($establecimiento_id);

            if (empty($establecimiento)) {
                Flash::error('Establecimiento no encontrado');

                return redirect(route('estimacion.index'));
            }

            if (empty($request->petitorios)) {
                Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
                
            }else
            {
                //petitorios
                
                
                    if($tipo==1)
                    {
                        $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id',$establecimiento->nivel_id)
                                ->where('tipo_dispositivo_medicos_id',1)
                                ->get();    
                    }
                    else
                    {
                        if($establecimiento_id==4)
                        {
                            $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id','<',3)
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->get();        
                        }
                        else
                        {
                            $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id',$establecimiento->nivel_id)
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->get(); 
                        }
                        
                    }
                
                foreach($request->petitorios as $key => $petitorio_id){
                
                    foreach($petitorio_total as $id => $petitorio){     
                        //DB::table('abastecimientos')
                        if($petitorio_id == $petitorio->id){
                            
                            DB::table('estimacion_rubro')
                                 ->insert([
                                    'can_id' => $can_id,
                                    'establecimiento_id' => $establecimiento->id,
                                    'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                                    'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                                    'tipo_dispositivo_id' => $petitorio->tipo_dispositivo_medicos_id,
                                    'uso_id' => $petitorio->tipo_uso_id,
                                    'consolidado' => $consolidado,
                                    'petitorio_id' => $petitorio_id,
                                    'cod_petitorio' => $petitorio->codigo_petitorio,
                                    'descripcion' => $petitorio->descripcion,
                                    'rubro_id' => $servicio_id,
                                    'nombre_rubro' => $nombre_servicio,
                                    'created_at'=>Carbon::now(), 
                            ]);     
                        }         
                    }         
                }
            
            }    
        }
        
        Flash::success('Estimacion asignado correctamente.');

        return redirect(route('estimacion.cargar_medicamentos',[$can_id,$establecimiento_id,$tipo,1]));
    }
    
    
    //////////////////////////3/////////////////////////////////////
    public function cerrar_medicamento(Request $request,$can_id,$establecimiento_id,$tipo)
    {
        
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN No se ha encontrado');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('estimacion.index'));
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

        $rubro=DB::table('rubros')->where('id',$servicio_id)->get();

        $consolidado=$rubro->get(0)->consolidado;


            //buscamos todos los medicamentos llenados
            if($tipo==1){
                $data=DB::table('estimacion_rubro')
                        ->select('petitorio_id')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        ->where('consolidado',$consolidado)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->groupby('petitorio_id')
                        ->get();     

                 DB::table('estimacions')
                    ->where('can_id',$can_id)
                    ->where('tipo_dispositivo_id',1)
                    ->where('consolidado',$consolidado)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->delete();
            }
            else
            {
                $data=DB::table('estimacion_rubro')
                        ->select('petitorio_id')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('consolidado',$consolidado)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->groupby('petitorio_id')
                        ->get();   

                DB::table('estimacions')
                    ->where('can_id',$can_id)
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('consolidado',$consolidado)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->delete();
            }    

            $new_product=$data->pluck('petitorio_id');

            foreach($new_product as $key => $producto){

                    
                $estimacion_insert = DB::table('estimacion_rubro')
                                ->select('petitorio_id', 
                                    DB::raw('SUM(cpma) as cpma'),
                                    DB::raw('SUM(stock) as stock'),
                                    DB::raw('SUM(necesidad_anual) as necesidad_anual'),
                                    DB::raw('SUM(mes1) as mes1'),
                                    DB::raw('SUM(mes2) as mes2'),
                                    DB::raw('SUM(mes3) as mes3'),
                                    DB::raw('SUM(mes4) as mes4'),
                                    DB::raw('SUM(mes5) as mes5'),
                                    DB::raw('SUM(mes6) as mes6'),
                                    DB::raw('SUM(mes7) as mes7'),
                                    DB::raw('SUM(mes8) as mes8'),
                                    DB::raw('SUM(mes9) as mes9'),
                                    DB::raw('SUM(mes10) as mes10'),
                                    DB::raw('SUM(mes11) as mes11'),
                                    DB::raw('SUM(mes12) as mes12')
                                    )
                                ->groupby('petitorio_id')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('can_id',$can_id)
                                ->where('consolidado',$consolidado)
                                ->where('petitorio_id',$producto)
                                ->get();                                        

                $estimacion_insert_datos=DB::table('estimacion_rubro')
                                ->select('petitorio_id',
                                    'can_id',
                                    'establecimiento_id',
                                    'cod_establecimiento',
                                    'nombre_establecimiento',
                                    'tipo_dispositivo_id',
                                    'petitorio_id',
                                    'cod_petitorio',
                                    'uso_id',
                                    'descripcion')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('can_id',$can_id)
                                ->where('petitorio_id',$producto)
                                ->where('consolidado',$consolidado)
                                ->get();

                DB::table('estimacions')
                    ->insert([
                        'can_id' => $estimacion_insert_datos->get(0)->can_id,
                        'establecimiento_id' => $estimacion_insert_datos->get(0)->establecimiento_id,
                        'cod_establecimiento' => $estimacion_insert_datos->get(0)->cod_establecimiento,
                        'nombre_establecimiento' => $estimacion_insert_datos->get(0)->nombre_establecimiento,
                        'tipo_dispositivo_id' => $estimacion_insert_datos->get(0)->tipo_dispositivo_id,
                        'petitorio_id' => $estimacion_insert_datos->get(0)->petitorio_id,
                        'cod_petitorio' => $estimacion_insert_datos->get(0)->cod_petitorio,
                        'descripcion' => $estimacion_insert_datos->get(0)->descripcion,
                        'necesidad_anual' => $estimacion_insert->get(0)->necesidad_anual,
                        'cpma' => $estimacion_insert->get(0)->cpma,
                        'stock' => $estimacion_insert->get(0)->stock,
                        'mes1' => $estimacion_insert->get(0)->mes1,
                        'mes2' => $estimacion_insert->get(0)->mes2,
                        'mes3' => $estimacion_insert->get(0)->mes3,
                        'mes4' => $estimacion_insert->get(0)->mes4,
                        'mes5' => $estimacion_insert->get(0)->mes5,
                        'mes6' => $estimacion_insert->get(0)->mes6,
                        'mes7' => $estimacion_insert->get(0)->mes7,
                        'mes8' => $estimacion_insert->get(0)->mes8,
                        'mes9' => $estimacion_insert->get(0)->mes9,
                        'mes10' => $estimacion_insert->get(0)->mes10,
                        'mes11' => $estimacion_insert->get(0)->mes11,
                        'mes12' => $estimacion_insert->get(0)->mes12,
                        'created_at'=>Carbon::now(),
                        'uso_id' => $estimacion_insert_datos->get(0)->uso_id,
                        'consolidado'=> $consolidado,
                ]);
            }
                
               
            
            //cerramos medicamento
            DB::table('can_rubro')
                ->where('can_id', $can_id)
                ->where('establecimiento_id', $establecimiento_id)
                ->where('rubro_id', $servicio_id)
                ->update([
                        $tipo_cerrado => 2,
                        'updated_at'=>Carbon::now()
            ]);
        
            $this->cerrar_establecimiento($can_id,$establecimiento_id,$nivel);
        
        Flash::success('Petitorio Cerrado.');

        return redirect(route('estimacion.descargar_estimacion',[$tipo,$can_id]));

    }

    //////////////////////////3/////////////////////////////////////
    protected function cerrar_establecimiento($can_id,$establecimiento_id,$nivel_id)
    {
        //calculamos cuantas farmacias hay en el establecimiento
        $total = DB::table('establecimiento_rubro')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->count();
        
        //contamos cuantas farmacias han cerrado en medicamentos
        $medicamento_cerrado = DB::table('can_rubro')
                                    ->where('medicamento_cerrado',2)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();

        //contamos cuantas farmacias han cerrado en dispositivos
        $dispositivo_cerrado = DB::table('can_rubro')
                                    ->where('dispositivo_cerrado',2)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();
        
        //si se han cerrado todas las farmacias con medicamentos y dispositivos, cerramos el establecimiento                                    
        if($medicamento_cerrado == $dispositivo_cerrado){ 
            if ($total == $medicamento_cerrado){  
                DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado' => 2,
                            'dispositivo_cerrado' => 2,
                            'updated_at'=>Carbon::now()
                ]);                                        
            }
        }
        else
        {
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

    }
////////////////////////////////////////////////////////////////////////////////////////
    public function descargar_estimacion($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        if($nivel==1){
            $table='estimacion_rubro';
            $condicion1='rubro_id';
        }
        
        if($tipo==1){
            $data=DB::table('estimacion_rubro')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->get();
        
            $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacion_rubro')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1  
                    ->orderby ('tipo_dispositivo_id','asc')                  
                    ->get();     

                    $num_estimaciones=DB::table($table)
                        ->where('necesidad_anual','>',0)
                        ->where('rubro_id',$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }
    
        return view('site.estimacions.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }
    

    //////////////////////////////////////////////////////////////////
    public function exportEstimacionData($can_id,$establecimiento_id,$opt,$type)
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

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($opt==1){
            $data=DB::table('estimacion_rubro')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data=DB::table('estimacion_rubro')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1                    
                    ->get();                    

                    $nombre_producto='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
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
                    'R'     =>  10,
                    'S'     =>  100,
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

                            $cpma_total = 0;
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

                            
                            $descripcion_tipo_dispositivo=DB::table('tipo_dispositivo_medicos')->where('id',$value->tipo_dispositivo_id)->get();

                            $nombre_dispositivo=$descripcion_tipo_dispositivo->get(0)->descripcion;

                            $i=$k+2;
                            $j=$i+1;   
                            $d=$i-1;     
                            //construimos cabecera
                            $sheet->cell('A'.$i, function($cell) {$cell->setValue('N°'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('B'.$i, function($cell) {$cell->setValue('COD MED'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('C'.$i, function($cell) {$cell->setValue('NOMBRE DEL PRODUCTO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('D'.$i, function($cell) {$cell->setValue('CPMA'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('E'.$i, function($cell) {$cell->setValue('STOCK'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('F'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('G'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('H'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('I'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('J'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('K'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('L'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('M'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('N'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('O'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('P'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('Q'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('R'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->cell('S'.$i, function($cell) {$cell->setValue('JUSTIFICACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->mergeCells('G'.$i.':R'.$i);
                            $sheet->cell('G'.$i, function($cell) {$cell->setValue('PRORRATEO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->mergeCells('A'.$d.':S'.$d);
                            $sheet->cell('A'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->cell('A'.$d, $nombre_dispositivo); //establecimiento 

                            $n=$i+2;
                            
                            $sheet->setMergeColumn(array(
                                'columns' => array('A','B','C','S'),
                                'rows' => array(
                                    array($i,$n)                        
                                )
                            ));

                            $sheet->setMergeColumn(array(
                                'columns' => array('E','F','D'),
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
                        
                        $sheet->cell('A'.$k, $m); 
                        $sheet->cell('B'.$k, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$k, $value->descripcion); //nivel
                        if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}
                        if($value->stock>0){ $sheet->cell('E'.$k, $value->stock);}else{$sheet->cell('E'.$k, number_format($value->stock, 2, '.', ','));}
                        if($value->necesidad_anual>0){ $sheet->cell('F'.$k, $value->necesidad_anual);}else{$sheet->cell('F'.$k, number_format($value->necesidad_anual, 2, '.', ','));}
                        if($value->mes1>0){ $sheet->cell('G'.$k, $value->mes1);}else{$sheet->cell('G'.$k, number_format($value->mes1, 2, '.', ','));}
                        if($value->mes2>0){ $sheet->cell('H'.$k, $value->mes2);}else{$sheet->cell('H'.$k, number_format($value->mes2, 2, '.', ','));}
                        if($value->mes3>0){ $sheet->cell('I'.$k, $value->mes3);}else{$sheet->cell('I'.$k, number_format($value->mes3, 2, '.', ','));}
                        if($value->mes4>0){ $sheet->cell('J'.$k, $value->mes4);}else{$sheet->cell('J'.$k, number_format($value->mes4, 2, '.', ','));}
                        if($value->mes5>0){ $sheet->cell('K'.$k, $value->mes5);}else{$sheet->cell('K'.$k, number_format($value->mes5, 2, '.', ','));}
                        if($value->mes6>0){ $sheet->cell('L'.$k, $value->mes6);}else{$sheet->cell('L'.$k, number_format($value->mes6, 2, '.', ','));}
                        if($value->mes7>0){ $sheet->cell('M'.$k, $value->mes7);}else{$sheet->cell('M'.$k, number_format($value->mes7, 2, '.', ','));}
                        if($value->mes8>0){ $sheet->cell('N'.$k, $value->mes8);}else{$sheet->cell('N'.$k, number_format($value->mes8, 2, '.', ','));}
                        if($value->mes9>0){ $sheet->cell('O'.$k, $value->mes9);}else{$sheet->cell('O'.$k, number_format($value->mes9, 2, '.', ','));}
                        if($value->mes10>0){ $sheet->cell('P'.$k, $value->mes10);}else{$sheet->cell('P'.$k, number_format($value->mes10, 2, '.', ','));}
                        if($value->mes11>0){ $sheet->cell('Q'.$k, $value->mes11);}else{$sheet->cell('Q'.$k, number_format($value->mes11, 2, '.', ','));}
                        if($value->mes12>0){ $sheet->cell('R'.$k, $value->mes12);}else{$sheet->cell('R'.$k, number_format($value->mes12, 2, '.', ','));}

                        $sheet->cell('S'.$k, $value->justificacion);  

                        $k++;
                        $m++;
                        
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
                        $cpma_total= $value->cpma + $cpma_total;
                        
                    
                    
                    if($cpma_total>0){ $sheet->cell('D'.$n, $cpma_total);}else{$sheet->cell('D'.$n, number_format($cpma_total, 2, '.', ','));}
                    $sheet->cell('D'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($total_stock>0){ $sheet->cell('E'.$n, $total_stock);}else{$sheet->cell('E'.$n, number_format($total_stock, 2, '.', ','));}
                    $sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($total_necesidad_anual>0){ $sheet->cell('F'.$n, $total_necesidad_anual);}else{$sheet->cell('F'.$n, number_format($total_necesidad_anual, 2, '.', ','));}
                    $sheet->cell('F'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total>0){ $sheet->cell('G'.$n, $mes1_total);}else{$sheet->cell('G'.$n, number_format($mes1_total, 2, '.', ','));}
                    $sheet->cell('G'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total>0){ $sheet->cell('H'.$n, $mes2_total);}else{$sheet->cell('H'.$n, number_format($mes2_total, 2, '.', ','));}
                    $sheet->cell('H'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total>0){ $sheet->cell('I'.$n, $mes3_total);}else{$sheet->cell('I'.$n, number_format($mes3_total, 2, '.', ','));}
                    $sheet->cell('I'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total>0){ $sheet->cell('J'.$n, $mes4_total);}else{$sheet->cell('J'.$n, number_format($mes4_total, 2, '.', ','));}
                    $sheet->cell('J'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total>0){ $sheet->cell('K'.$n, $mes5_total);}else{$sheet->cell('K'.$n, number_format($mes5_total, 2, '.', ','));}
                    $sheet->cell('K'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total>0){ $sheet->cell('L'.$n, $mes6_total);}else{$sheet->cell('L'.$n, number_format($mes6_total, 2, '.', ','));}
                    $sheet->cell('L'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total>0){ $sheet->cell('M'.$n, $mes7_total);}else{$sheet->cell('M'.$n, number_format($mes7_total, 2, '.', ','));}
                    $sheet->cell('M'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total>0){ $sheet->cell('N'.$n, $mes8_total);}else{$sheet->cell('N'.$n, number_format($mes8_total, 2, '.', ','));}
                    $sheet->cell('N'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total>0){ $sheet->cell('O'.$n, $mes9_total);}else{$sheet->cell('O'.$n, number_format($mes9_total, 2, '.', ','));}
                    $sheet->cell('O'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total>0){ $sheet->cell('P'.$n, $mes10_total);}else{$sheet->cell('P'.$n, number_format($mes10_total, 2, '.', ','));}
                    $sheet->cell('P'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total>0){ $sheet->cell('Q'.$n, $mes11_total);}else{$sheet->cell('Q'.$n, number_format($mes11_total, 2, '.', ','));}
                    $sheet->cell('Q'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total>0){ $sheet->cell('R'.$n, $mes12_total);}else{$sheet->cell('R'.$n, number_format($mes12_total, 2, '.', ','));}
                    $sheet->cell('R'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    }
                }
                
                $servicio_id=Auth::user()->servicio_id;
                $establecimiento_id=Auth::user()->establecimiento_id;

                $establecimiento=Establecimiento::find($establecimiento_id);
                $nivel=$establecimiento->nivel_id;

                $consulta=DB::table('can_rubro')
                                ->where('can_id',$can_id) 
                                ->where('rubro_id',$servicio_id) 
                                ->where('establecimiento_id',$establecimiento_id)
                                ->get();
                
                $fecha_hora=$consulta->get(0)->updated_at;
                $date = date_create($fecha_hora);
                $fecha_hora=date_format($date,'d/m/Y H:i:s');
                
                $j=$k+10;
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
    
    ///////////////////////////////////////////////////////////////////7
    
    ///////////////////////////10////////////////////////////////////77
    public function exportEstimacionDataPrevio($can_id,$establecimiento_id,$opt,$type)
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

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($opt==1){
            $data=DB::table('estimacion_rubro')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data=DB::table('estimacion_rubro')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1                    
                    ->get();                    

                    $nombre_producto='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
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
                    'R'     =>  10,
                    'S'     =>  100,
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

                            $cpma_total = 0;
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

                            
                            $descripcion_tipo_dispositivo=DB::table('tipo_dispositivo_medicos')->where('id',$value->tipo_dispositivo_id)->get();

                            $nombre_dispositivo=$descripcion_tipo_dispositivo->get(0)->descripcion;

                            $i=$k+2;
                            $j=$i+1;   
                            $d=$i-1;     
                            //construimos cabecera
                            $sheet->cell('A'.$i, function($cell) {$cell->setValue('N°'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('B'.$i, function($cell) {$cell->setValue('COD MED'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('C'.$i, function($cell) {$cell->setValue('NOMBRE DEL PRODUCTO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('D'.$i, function($cell) {$cell->setValue('CPMA'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('E'.$i, function($cell) {$cell->setValue('STOCK'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('F'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('G'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('H'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('I'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('J'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('K'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('L'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('M'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('N'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('O'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('P'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('Q'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('R'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->cell('S'.$i, function($cell) {$cell->setValue('JUSTIFICACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->mergeCells('G'.$i.':R'.$i);
                            $sheet->cell('G'.$i, function($cell) {$cell->setValue('PRORRATEO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->mergeCells('A'.$d.':S'.$d);
                            $sheet->cell('A'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->cell('A'.$d, $nombre_dispositivo); //establecimiento 

                            $n=$i+2;
                            
                            $sheet->setMergeColumn(array(
                                'columns' => array('A','B','C','S'),
                                'rows' => array(
                                    array($i,$n)                        
                                )
                            ));

                            $sheet->setMergeColumn(array(
                                'columns' => array('E','F','D'),
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
                        
                        $sheet->cell('A'.$k, $m); 
                        $sheet->cell('B'.$k, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$k, $value->descripcion); //nivel
                        if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}
                        if($value->stock>0){ $sheet->cell('E'.$k, $value->stock);}else{$sheet->cell('E'.$k, number_format($value->stock, 2, '.', ','));}
                        if($value->necesidad_anual>0){ $sheet->cell('F'.$k, $value->necesidad_anual);}else{$sheet->cell('F'.$k, number_format($value->necesidad_anual, 2, '.', ','));}
                        if($value->mes1>0){ $sheet->cell('G'.$k, $value->mes1);}else{$sheet->cell('G'.$k, number_format($value->mes1, 2, '.', ','));}
                        if($value->mes2>0){ $sheet->cell('H'.$k, $value->mes2);}else{$sheet->cell('H'.$k, number_format($value->mes2, 2, '.', ','));}
                        if($value->mes3>0){ $sheet->cell('I'.$k, $value->mes3);}else{$sheet->cell('I'.$k, number_format($value->mes3, 2, '.', ','));}
                        if($value->mes4>0){ $sheet->cell('J'.$k, $value->mes4);}else{$sheet->cell('J'.$k, number_format($value->mes4, 2, '.', ','));}
                        if($value->mes5>0){ $sheet->cell('K'.$k, $value->mes5);}else{$sheet->cell('K'.$k, number_format($value->mes5, 2, '.', ','));}
                        if($value->mes6>0){ $sheet->cell('L'.$k, $value->mes6);}else{$sheet->cell('L'.$k, number_format($value->mes6, 2, '.', ','));}
                        if($value->mes7>0){ $sheet->cell('M'.$k, $value->mes7);}else{$sheet->cell('M'.$k, number_format($value->mes7, 2, '.', ','));}
                        if($value->mes8>0){ $sheet->cell('N'.$k, $value->mes8);}else{$sheet->cell('N'.$k, number_format($value->mes8, 2, '.', ','));}
                        if($value->mes9>0){ $sheet->cell('O'.$k, $value->mes9);}else{$sheet->cell('O'.$k, number_format($value->mes9, 2, '.', ','));}
                        if($value->mes10>0){ $sheet->cell('P'.$k, $value->mes10);}else{$sheet->cell('P'.$k, number_format($value->mes10, 2, '.', ','));}
                        if($value->mes11>0){ $sheet->cell('Q'.$k, $value->mes11);}else{$sheet->cell('Q'.$k, number_format($value->mes11, 2, '.', ','));}
                        if($value->mes12>0){ $sheet->cell('R'.$k, $value->mes12);}else{$sheet->cell('R'.$k, number_format($value->mes12, 2, '.', ','));}

                        $sheet->cell('S'.$k, $value->justificacion);  

                        $k++;
                        $m++;
                        
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
                        $cpma_total= $value->cpma + $cpma_total;
                        
                    
                    
                    if($cpma_total>0){ $sheet->cell('D'.$n, $cpma_total);}else{$sheet->cell('D'.$n, number_format($cpma_total, 2, '.', ','));}
                    $sheet->cell('D'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($total_stock>0){ $sheet->cell('E'.$n, $total_stock);}else{$sheet->cell('E'.$n, number_format($total_stock, 2, '.', ','));}
                    $sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($total_necesidad_anual>0){ $sheet->cell('F'.$n, $total_necesidad_anual);}else{$sheet->cell('F'.$n, number_format($total_necesidad_anual, 2, '.', ','));}
                    $sheet->cell('F'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total>0){ $sheet->cell('G'.$n, $mes1_total);}else{$sheet->cell('G'.$n, number_format($mes1_total, 2, '.', ','));}
                    $sheet->cell('G'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total>0){ $sheet->cell('H'.$n, $mes2_total);}else{$sheet->cell('H'.$n, number_format($mes2_total, 2, '.', ','));}
                    $sheet->cell('H'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total>0){ $sheet->cell('I'.$n, $mes3_total);}else{$sheet->cell('I'.$n, number_format($mes3_total, 2, '.', ','));}
                    $sheet->cell('I'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total>0){ $sheet->cell('J'.$n, $mes4_total);}else{$sheet->cell('J'.$n, number_format($mes4_total, 2, '.', ','));}
                    $sheet->cell('J'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total>0){ $sheet->cell('K'.$n, $mes5_total);}else{$sheet->cell('K'.$n, number_format($mes5_total, 2, '.', ','));}
                    $sheet->cell('K'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total>0){ $sheet->cell('L'.$n, $mes6_total);}else{$sheet->cell('L'.$n, number_format($mes6_total, 2, '.', ','));}
                    $sheet->cell('L'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total>0){ $sheet->cell('M'.$n, $mes7_total);}else{$sheet->cell('M'.$n, number_format($mes7_total, 2, '.', ','));}
                    $sheet->cell('M'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total>0){ $sheet->cell('N'.$n, $mes8_total);}else{$sheet->cell('N'.$n, number_format($mes8_total, 2, '.', ','));}
                    $sheet->cell('N'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total>0){ $sheet->cell('O'.$n, $mes9_total);}else{$sheet->cell('O'.$n, number_format($mes9_total, 2, '.', ','));}
                    $sheet->cell('O'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total>0){ $sheet->cell('P'.$n, $mes10_total);}else{$sheet->cell('P'.$n, number_format($mes10_total, 2, '.', ','));}
                    $sheet->cell('P'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total>0){ $sheet->cell('Q'.$n, $mes11_total);}else{$sheet->cell('Q'.$n, number_format($mes11_total, 2, '.', ','));}
                    $sheet->cell('Q'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total>0){ $sheet->cell('R'.$n, $mes12_total);}else{$sheet->cell('R'.$n, number_format($mes12_total, 2, '.', ','));}
                    $sheet->cell('R'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    }
                }
    
            });
        })->download($type);
    }

    public function pdf_estimacion($can_id,$establecimiento_id,$tipo)
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
            $data=DB::table('estimacion_rubro')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->orderby ('tipo_dispositivo_id','asc')
                    ->get();
        
            $num_estimaciones=DB::table('estimacion_rubro')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacion_rubro')
                    ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion'
                                            )
                    ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1  
                    ->orderby ('tipo_dispositivo_id','asc')                  
                    ->get();     

                    $num_estimaciones=DB::table('estimacion_rubro')
                        ->where('necesidad_anual','>',0)
                        ->where('rubro_id',$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }

        $rubro=DB::table('responsables')->where('user_id',$user_id)->get();
        $nombre_rubro=$rubro->get(0)->nombre_servicio;
        $texto='RUBRO';

        $cierre_rubro=DB::table('can_rubro')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('rubro_id',$servicio_id)->get();
        $cierre=$cierre_rubro->get(0)->updated_at;
    
        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
     } 
}
