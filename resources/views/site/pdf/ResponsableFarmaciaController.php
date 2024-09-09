<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use App\Models\Distribucion;
use App\Models\Estimacion;
use App\Models\Estimacion2;
use App\Models\Estimacion3;
use App\Models\Petitorio;
use App\Models\User;
use DB;
use App\Models\Establecimiento;
use App\Models\Can;
use Illuminate\Support\Facades\Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;      

class ResponsableFarmaciaController extends AppBaseController
{
    /** @var  EstimacionRepository */  
    public function __construct()
    {
        
    }

        public function index(Request $request)
    {
        
        $cans = DB::table('cans')->orderby('cans.id','desc')->get();
     //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;
            
                
        $listacans=DB::table('can_establecimiento')
                    ->join('cans', 'can_establecimiento.can_id','cans.id')                    
                    ->where('establecimiento_id',$establecimiento_id)
                    ->orderby('cans.id', 'desc')
                    ->get();  
        $ano=$cans->get(0)->ano;
        
        if($nivel_id==1){
            return view('site.responsable_farmacia.listar_distribucion')
                    ->with('can_id', $cans->get(0)->id)
                    ->with('cans', $listacans)
                    ->with('ano', $ano)
                    ->with('nivel', $nivel_id)  
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento);
        }
        else
        {
            $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->get();

            $tipo_servicio_id=Auth::user()->rol;
            $responsables= DB::table('users')
                            ->where('rol',2)                            
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id','>',0)
                            ->get();
        
            $medicamento_cerrado=$listacans->get(0)->medicamento_cerrado;
            $dispositivo_cerrado=$listacans->get(0)->dispositivo_cerrado;
            $rubro_pf=$listacans->get(0)->rubro_pf;
            $rubro_mb_iq_pa=$listacans->get(0)->rubro_mb_iq_pa;
            $rubro_mid=$listacans->get(0)->rubro_mid;
            $rubro_mil=$listacans->get(0)->rubro_mil;
            $rubro_mff=$listacans->get(0)->rubro_mff;
            
            return view('site.responsable_farmacia.listar_distribucion')
                        ->with('servicios', $servicios)
                        ->with('tipo_servicio_id', $tipo_servicio_id)
                        ->with('responsables', $responsables)
                        ->with('can_id', $cans->get(0)->id)
                        ->with('medicamento_cerrado', $medicamento_cerrado)
                        ->with('dispositivo_cerrado', $dispositivo_cerrado)
                        ->with('ano', $ano)
                        ->with('establecimiento_id', $establecimiento_id)
                        ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                        ->with('rubro_pf', $rubro_pf)
                        ->with('rubro_mb_iq_pa', $rubro_mb_iq_pa)
                        ->with('rubro_mid', $rubro_mid)
                        ->with('rubro_mil', $rubro_mil)
                        ->with('rubro_mff', $rubro_mff)
                        ->with('cans', $listacans)
                        ->with('nivel', $nivel_id);
        }
    }

    public function listar_distribucion($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;

        $listacans=DB::table('can_establecimiento')
                    ->join('cans', 'can_establecimiento.can_id','cans.id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('cans.id', 'desc')
                    ->get(); 

        $cans = DB::table('cans')->orderby('cans.id','desc')->get();
        $ano=$cans->get(0)->ano;

        $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_id',$can_id)  
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->get();

        

            $tipo_servicio_id=Auth::user()->rol;
            $responsables= DB::table('responsables')
                            ->where('rol',2)      
                            ->where('can_id',$can_id)      
                            ->where('etapa',2)                                                  
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id','>',0)
                            ->get();

        
            $medicamento_cerrado=$listacans->get(0)->medicamento_cerrado;
            $dispositivo_cerrado=$listacans->get(0)->dispositivo_cerrado;
            $rubro_pf=$listacans->get(0)->rubro_pf;
            $rubro_mb_iq_pa=$listacans->get(0)->rubro_mb_iq_pa;
            $rubro_mid=$listacans->get(0)->rubro_mid;
            $rubro_mil=$listacans->get(0)->rubro_mil;
            $rubro_mff=$listacans->get(0)->rubro_mff;

            //return view('site.responsable_farmacia_hospital.listar_servicios')
            return view('site.responsable_farmacia_hospital.table_unificado')
                        ->with('servicios', $servicios)
                        ->with('tipo_servicio_id', $tipo_servicio_id)
                        ->with('responsables', $responsables)
                        ->with('can_id', $can_id)
                        ->with('medicamento_cerrado', $medicamento_cerrado)
                        ->with('dispositivo_cerrado', $dispositivo_cerrado)
                        ->with('ano', $ano)
                        ->with('establecimiento_id', $establecimiento_id)
                        ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                        ->with('rubro_pf', $rubro_pf)
                        ->with('rubro_mb_iq_pa', $rubro_mb_iq_pa)
                        ->with('rubro_mid', $rubro_mid)
                        ->with('rubro_mil', $rubro_mil)
                        ->with('rubro_mff', $rubro_mff)
                        ->with('cans', $listacans)
                        ->with('nivel', $nivel_id);

    }

     public function listar_servicios($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;
        
        $listacans=DB::table('can_establecimiento')
                    ->join('cans', 'can_establecimiento.can_id','cans.id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('cans.id', 'desc')
                    ->get(); 

        $cans = DB::table('cans')->orderby('cans.id','desc')->get();
        $ano=$cans->get(0)->ano;

        $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_id',$can_id)     
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->get();

            $tipo_servicio_id=Auth::user()->rol;
            $responsables= DB::table('responsables')
                            ->where('rol',2)      
                            ->where('can_id',$can_id)      
                            ->where('etapa',1)                            
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id','>',0)
                            ->get();

        
            $medicamento_cerrado=$listacans->get(0)->medicamento_cerrado;
            $dispositivo_cerrado=$listacans->get(0)->dispositivo_cerrado;
            $rubro_pf=$listacans->get(0)->rubro_pf;
            $rubro_mb_iq_pa=$listacans->get(0)->rubro_mb_iq_pa;
            $rubro_mid=$listacans->get(0)->rubro_mid;
            $rubro_mil=$listacans->get(0)->rubro_mil;
            $rubro_mff=$listacans->get(0)->rubro_mff;

            //return view('site.responsable_farmacia_hospital.listar_servicios')
            return view('site.responsable_farmacia_hospital.table_servicios')
                        ->with('servicios', $servicios)
                        ->with('tipo_servicio_id', $tipo_servicio_id)
                        ->with('responsables', $responsables)
                        ->with('can_id', $can_id)
                        ->with('medicamento_cerrado', $medicamento_cerrado)
                        ->with('dispositivo_cerrado', $dispositivo_cerrado)
                        ->with('ano', $ano)
                        ->with('establecimiento_id', $establecimiento_id)
                        ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                        ->with('rubro_pf', $rubro_pf)
                        ->with('rubro_mb_iq_pa', $rubro_mb_iq_pa)
                        ->with('rubro_mid', $rubro_mid)
                        ->with('rubro_mil', $rubro_mil)
                        ->with('rubro_mff', $rubro_mff)
                        ->with('cans', $listacans)
                        ->with('nivel', $nivel_id);

    }

    public function manual($id)
    {
        return view('site.estimacions.manual')
        ->with('id', $id);   ;     
    }
    
    public function show($id)
    {
        //$establecimiento_id=Auth::user()->establecimiento_id;
        $estimacion = Estimacion::findOrFail($id); //estimacion_servicio
        $petitorio_id=($estimacion->petitorio_id);
        $establecimiento_id=($estimacion->establecimiento_id);
                
        $estimaciones = DB::table('estimacions as A')
                    ->select('B.*')
                    ->addselect('C.nombre as nombre')
                    ->join('estimacion_rubro as B', 'A.establecimiento_id','B.establecimiento_id')
                    ->join('responsables as C', 'C.servicio_id','B.rubro_id')
                    ->where('B.establecimiento_id',$establecimiento_id)
                    ->where('B.petitorio_id',$petitorio_id)
                    ->where('C.establecimiento_id',$establecimiento_id)
                    ->where('C.rol',2)
                    ->distinct()
                    ->get();


        return view('site.responsable_farmacia.medicamentos.mostrar_datos')->with('estimaciones', $estimaciones);
    }
   
   public function edit($id)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        $nivel=$establecimiento->nivel_id;

        $contacto = Estimacion::findOrFail($id); //estimacion_servicio
        
        
        return $contacto;        
    }

    //////////////////////////////////////////////////////////////////77
    public function activar_rubro($can_id, $establecimiento_id,$rubro_id )
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('icis.show',$ici_id));
        }
        
        //2/4/4
        $rubro = DB::table('can_rubro')
                ->where('can_rubro.can_id',$can_id)
                ->where('can_rubro.rubro_id',$rubro_id)
                ->where('can_rubro.establecimiento_id',$establecimiento_id)
                ->get();

        
        $cerrado_medicamento=$rubro->get(0)->medicamento_cerrado;
        
        $cerrado_dispositivo=$rubro->get(0)->dispositivo_cerrado;

        return view('site.responsable_farmacia.activar_rubro')->with('cerrado_dispositivo', $cerrado_dispositivo)
                                         ->with('cerrado_medicamento', $cerrado_medicamento)
                                         ->with('can',$can)
                                         ->with('rubro_id',$rubro_id)
                                         ->with('establecimiento_id',$establecimiento_id);
    }

    ///////////////////////////9////////////////////////////////////77
    public function update_activar_rubro(Request $request,$can_id,$establecimiento_id,$rubro_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }
        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');
            return redirect(route('estimacion.index'));
        }

        $rubro=DB::table('establecimiento_rubro')->where('rubro_id',$rubro_id)->where('establecimiento_id',$establecimiento_id);
        if (empty($rubro)) {
            Flash::error('No se ha encontrado');
            return redirect(route('estimacion.index'));
        }
        
        $verifica_rubro=DB::table('can_rubro')
                            ->where('can_id', $can_id)
                            ->where('rubro_id', $rubro_id)
                            ->where('establecimiento_id', $establecimiento_id)
                            ->get();

    
        if($verifica_rubro->get(0)->medicamento_cerrado!=1){
            $medicamento=$request->input('cerrado_medicamento');
            if($medicamento==null)            
            {
                $medicamento=3;
            }
        }   
        else
        {
             $medicamento=1;   
        }
        
        if($verifica_rubro->get(0)->dispositivo_cerrado!=1){
            $dispositivo=$request->input('cerrado_dispositivo');
            if($dispositivo==null)
            {
                $dispositivo=3;   
            }        
        }
        else
        {
             $dispositivo=1;   
        }
        
           //actualizamos los estados de los medicamentos y dispositivos cerrado
        DB::table('can_rubro')
            ->where('can_id', $can_id)
            ->where('rubro_id', $rubro_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->update([
                        'dispositivo_cerrado' => $dispositivo, 
                        'medicamento_cerrado' => $medicamento,  
                        'updated_at'=>Carbon::now()
                    ]);
     
        //calculamos cuantas farmacias hay en el establecimiento
        $total_rubro = DB::table('establecimiento_rubro')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();

        //contamos cuantas farmacias han cerrado en medicamentos
        $rubro_medicamento_cerrado = DB::table('can_rubro')
                                    ->where('medicamento_cerrado',2)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();

        //contamos cuantas farmacias han cerrado en dispositivos
        $rubro_dispositivo_cerrado = DB::table('can_rubro')
                                    ->where('dispositivo_cerrado',2)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();
        
        //si se han cerrado todas las farmacias con medicamentos y dispositivos, cerramos el establecimiento                                    
        if($rubro_medicamento_cerrado == $rubro_dispositivo_cerrado){
            if ($total_rubro == $rubro_medicamento_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado' => 2,
                            'dispositivo_cerrado' => 2,
                            'updated_at'=>Carbon::now()
                ]);                                        
            } 
            else
            {
               DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado' => 1,
                            'dispositivo_cerrado' => 1,
                            'updated_at'=>Carbon::now()
                ]);                                         
            }

        }
        else
        {   
            if ($total_rubro == $rubro_medicamento_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado' => 2,
                            'updated_at'=>Carbon::now()
                ]);                                        
            }
            else
            {
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado' => 1,
                            'updated_at'=>Carbon::now()
                ]); 
            }    
            if ($total_rubro == $rubro_dispositivo_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'dispositivo_cerrado' => 2,
                            'updated_at'=>Carbon::now()
                ]);                                        
            }  
            else
            {
                DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'dispositivo_cerrado' => 1,
                            'updated_at'=>Carbon::now()
                ]); 
            }    
        }       

        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('farmacia.listar_distribucion',[$can_id]));
    }

/*****************4******************/
    public function destroy($id)
    {
        $estimacion = $this->estimacionRepository->findWithoutFail($id);

        if (empty($estimacion)) {
            Flash::error('No encontrado');

            return redirect(route('estimacions.index'));
        }

        $this->estimacionRepository->delete($id);
        Flash::success('Borrado correctamente.');
        return redirect(route('estimacions.index'));
    }

    public function eliminar($id)
    {
        
        $contact = Estimacion::findOrFail($id);

        /*$producto = DB::table('estimacions')
                            ->where('id',$id)
                            ->delete();
        */
        //estado: 1 nuevo 2 eliminado 3 actualizar
        DB::table('estimacions')
        ->where('id', $id)
        ->update([
                    'estado'=> 2,
                    'estado_necesidad'=> 2,
                    'updated_at'=>Carbon::now()
         ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto Eliminado'
        ]);
    }


public function grabar($id,Request $request)
    {
       
       $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        $nivel=$establecimiento->nivel_id;

        if($nivel==1):
            $necesidad_anual = $request->input("necesidad_anual");            
            $cpma = $request->input("cpma");   
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
            $stock = $request->input("stock");

            DB::table('estimacions')
                ->where('id', $id)
                ->update([
                            'necesidad_anual' => $necesidad_anual,                        
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
                            'stock'=>$stock,  
                            'estado_necesidad'=>3,               
                            'updated_at'=>Carbon::now()
                            
                 ]);
        else:

            $cpma = $request->input("cpma");   
            $stock = $request->input("stock");

            DB::table('estimacions')
                ->where('id', $id)
                ->update([
                            'cpma' => $cpma,
                            'stock'=>$stock,  
                            'estado_necesidad'=>3,               
                            'updated_at'=>Carbon::now()
                            
                 ]);

        endif;

        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }

/* Grabar con stock y rectificacion
public function grabar($id,Request $request)
    {
       
       $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        $nivel=$establecimiento->nivel_id;

        if($nivel==1){
            $estado_stock=0;
            $estado_necesidad=0;
            $estimacion = Estimacion::find($id); //servicios
            
            if (empty($estimacion)) {
                Flash::error('Estimación no encontrado');

                return redirect(route('farmacia.index'));
            }
            
            $stock_actual = $request->input("stock_actual");   
            $necesidad_actual = $request->input("necesidad_actual");   

            //estado: 1 nuevo 2 eliminado 3 actualizado
            $data_anterior= DB::table('estimacions')
                            ->where('id', $id)
                            ->get();

            $stock_anterior=$data_anterior->get(0)->stock;
            $necesidad_anterior_rectificacion=$data_anterior->get(0)->necesidad_anual;

            if($stock_anterior!=$stock_actual)
                $estado_stock=3;

            if($necesidad_actual!=$necesidad_anterior_rectificacion)
                $estado_necesidad=3;

            $requerimiento=$necesidad_actual-$stock_actual;

            if($requerimiento<0)
                $requerimiento=0;

            DB::table('estimacions')
            ->where('id', $id)
            ->update([
                        'stock_actual' => $stock_actual,
                        'necesidad_actual' => $necesidad_actual,
                        'estado_stock'=> $estado_stock,
                        'estado_necesidad'=> $estado_necesidad,
                        'requerimiento_usuario'=> $requerimiento,
                        'updated_rectificacion'=>Carbon::now()
             ]);

        }
        else
        {

            $estado_stock=0;
            $estado_necesidad=0;
            
            $stock_actual = $request->input("stock_actual");   
            
            //estado: 1 nuevo 2 eliminado 3 actualizado
            $data_anterior= DB::table('estimacions')
                            ->where('id', $id)
                            ->get();

            $stock_anterior=$data_anterior->get(0)->stock;
            $necesidad_anterior_rectificacion=$data_anterior->get(0)->necesidad_anual;
            $necesidad_actual=$data_anterior->get(0)->necesidad_actual;

            if($stock_anterior!=$stock_actual)
                $estado_stock=3;

            if($necesidad_actual!=$necesidad_anterior_rectificacion)
                $estado_necesidad=3;

            $requerimiento=$necesidad_actual-$stock_actual;

            if($requerimiento<0)
                $requerimiento=0;

            DB::table('estimacions')
            ->where('id', $id)
            ->update([
                        'stock_actual' => $stock_actual,
                        'necesidad_actual' => $necesidad_actual,
                        'estado_stock'=> $estado_stock,
                        'estado_necesidad'=> $estado_necesidad,
                        'requerimiento_usuario'=> $requerimiento,
                        'updated_rectificacion'=>Carbon::now()
             ]);

        }


        
        
        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }

*/

public function grabar_stock($id,Request $request)
    {
       
        $estimacion = Estimacion::find($id); //servicios
        
        if (empty($estimacion)) {
            Flash::error('Estimación no encontrado');

            return redirect(route('farmacia.index'));
        }
        
        $stock = $request->input("stock");   
        DB::table('estimacions')
        ->where('id', $id)
        ->update([
                    'stock' => $stock,
                    'updated_stock'=>Carbon::now()
         ]);

        
        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }

    
    //////////////////  Productos por IPRESS //////////////////////////////////////////
    public function ver_producto_consolidado($establecimiento_id, $can_id,  $tipo)
    {
        //Verificamos si el usuario es el mismo
        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //buscamos los datos del establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            
            $nivel = $establecimiento->nivel_id;
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimiento no existe CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }            

            $can = Can::find($can_id);

            if (empty($can)) {
                Flash::error('CAN no encontrada');
                return redirect(route('estimacion.index'));
            }
            $servicio_id=0;
                
            return view('site.responsable_farmacia.medicamentos.medicamentos')
                            ->with('establecimiento_id', $establecimiento_id)
                            ->with('can_id', $can_id)
                            ->with('nivel', $nivel)
                            ->with('servicio_id', $servicio_id)
                            ->with('tipo', $tipo);        
        }
        else
        {
            Flash::error('No tiene acceso para ver este registro');
            return redirect(route('farmacia.index'));
        }   
        
    }

//////////////////  Productos por SERVICIO - DISTRIBUCION  //////////////////////////////////////////
public function ver_producto($establecimiento_id, $can_id,  $tipo, $servicio_id)
    {
        //Verificamos si el usuario es el mismo
        if (Auth::user()->establecimiento_id == $establecimiento_id ){


            //buscamos los datos del establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            
            $nivel = $establecimiento->nivel_id;
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimiento no existe CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }

            //Si hay medicamentos asignados
            return view('site.responsable_farmacia.medicamentos.medicamentos')
                            ->with('establecimiento_id', $establecimiento_id)
                            ->with('can_id', $can_id)
                            ->with('nivel', $nivel)
                            ->with('tipo', $tipo)
                            ->with('servicio_id', $servicio_id);        
        }
        else
        {
            Flash::error('No tiene acceso para ver este registro');
            return redirect(route('farmacia.index'));
        }   
        
    }
    //////////////////////////3/////////////////////////////////////
    
    public function cerrar_medicamento_consolidado(Request $request,$can_id,$establecimiento_id,$tipo)
    {
            
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN No se ha encontrado');
            return redirect(route('farmacia.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('farmacia.index'));
        }
            
        $nivel=$establecimiento->nivel_id;

        $servicio_id=Auth::user()->servicio_id;


        if(Auth::user()->rol==7){
            if ($tipo==1){
                $tipo_cerrado='medicamento_cerrado';
            }
            else
            {
                $tipo_cerrado='dispositivo_cerrado';
            }
        }
        
        if($nivel==1):

            $responsables = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<=',7)->where('can_id',$can_id)->where('etapa',1)->orderby('rol','asc')->get();
            $jefes = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',3)->where('rol','<=',5)->where('can_id',$can_id)->where('etapa',1)->count();
            
            
            $pf=0; $d=0; $r=0;
            foreach ($responsables as $key => $resp_rubro) {
                switch ($resp_rubro->servicio_id) {                
                    case 1: $pf++; break; //productos farmaceuticos
                    case 2: $d++; break; //insumos laboratorio (5,10)
                    case 3: $d++; break; //biomedico,quirurgico,afines (2,3,7)
                    case 4: $d++; break; //dentales (4)
                    case 5: $d++; break; //fotografico (6)                
                }
            }

            if($establecimiento_id==79){
                $jefes++; $pf=1;
            }

            
        
            if($jefes==2){
                if($d>=1){
                    if($pf==1){                    
                        if(Auth::user()->rol==7){                
                            DB::table('can_establecimiento')
                                ->where('can_id', $can_id)
                                ->where('establecimiento_id', $establecimiento_id)
                                ->update([
                                        $tipo_cerrado => 2,
                                        'updated_at'=>Carbon::now()
                            ]);
                        }

                        Flash::success('Petitorio Cerrado.');

                        return redirect(route('farmacia.ver_consolidado_farmacia',[$tipo,$can_id]));
                    }
                    else
                    {
                        Flash::error('Falto Ingresar al Personal Responsables y/o jefe de Farmacia');
                        return redirect(route('users.index_responsable',[$can_id]));
                    }
                }
                else
                {
                    Flash::error('Falto Ingresar algun Personal Responsables de los rubros que se encuentra en su listado de productos');
                    return redirect(route('users.index_responsable',[$can_id]));
                }
            }
            else
            {
                Flash::error('Falta Ingresar al jefe de la IPRESS O JEFE Y/O RESPONSABLE DE FARMACIA');
                return redirect(route('users.index_responsable',[$can_id]));
            }
        else:
            if(Auth::user()->rol==7){                
                            DB::table('can_establecimiento')
                                ->where('can_id', $can_id)
                                ->where('establecimiento_id', $establecimiento_id)
                                ->update([
                                        $tipo_cerrado => 2,
                                        'updated_at'=>Carbon::now()
                            ]);
                        }

            Flash::success('Petitorio Cerrado.');
            return redirect(route('farmacia_servicios.ver_consolidado_farmacia_servicios',[$tipo,$can_id]));
        endif;
}

public function cerrar_medicamento_stock(Request $request,$can_id,$establecimiento_id,$tipo)
    {
            
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN No se ha encontrado');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('abastecimiento.show',$ici_id));
        }
            
        $nivel=$establecimiento->nivel_id;

        $servicio_id=Auth::user()->servicio_id;


        if(Auth::user()->rol==7){
            if ($tipo==1){
                $tipo_cerrado='medicamento_cerrado_stock';
            }
            else
            {
                $tipo_cerrado='dispositivo_cerrado_stock';
            }
        }

        DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            $tipo_cerrado => 2,
                            'actualizar_stock'=>Carbon::now()
                ]);
        
        Flash::success('Petitorio Cerrado.');
        return redirect(route('farmacia.ver_stock_farmacia',[$tipo,$can_id]));
        
    
}

public function cerrar_medicamento_rectificacion(Request $request,$can_id,$establecimiento_id,$tipo)
    {
            
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN No se ha encontrado');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('abastecimiento.show',$ici_id));
        }
            
        $nivel=$establecimiento->nivel_id;

        $servicio_id=Auth::user()->servicio_id;


        if(Auth::user()->rol==7){
            if ($tipo==1){
                $tipo_cerrado='medicamento_cerrado_rectificacion';
            }
            else
            {
                $tipo_cerrado='dispositivo_cerrado_rectificacion';
            }
        }

        DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            $tipo_cerrado => 2,
                            'updated_rectificacion'=>Carbon::now()
                ]);
        
        Flash::success('Petitorio Cerrado.');
        return redirect(route('farmacia.ver_rectificacion_farmacia',[$tipo,$can_id]));
        
    
}
    
    //////////////////////////3/////////////////////////////////////
    protected function cerrar_establecimiento($can_id,$establecimiento_id,$nivel_id)
    {
        if($nivel_id==1){
            $tabla='distribucions';
            $tabla2='can_distribucion';
        }
        else
        {
            $tabla='servicios';
            $tabla2='can_servicio';
        }    

        //calculamos cuantas farmacias hay en el establecimiento
        $total = DB::table($tabla)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->count();

        //contamos cuantas farmacias han cerrado en medicamentos
        $medicamento_cerrado = DB::table($tabla2)
                                    ->where('medicamento_cerrado',2)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();

        //contamos cuantas farmacias han cerrado en dispositivos
        $dispositivo_cerrado = DB::table($tabla2)
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
/////////////////////DESCARGA POR IPRESS ///////////////////////////////////////////////////////////////////
    public function descargar_consolidado_farmacia($tipo,$can_id)
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

        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
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

        $condicion_boton=2; // ver
        
            
        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('nivel', $nivel)
            ->with('condicion_boton', $condicion_boton)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

/////////////////////DESCARGA POR SERVICIO - DISTRIBUCION///////////////////////////////////////////////////////
    public function descargar_estimacion_farmacia($tipo,$can_id,$establecimiento_id,$servicio_id)
    {       
        
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
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
        else
        {
            $table='estimacion_servicio';
            $condicion1='servicio_id';
        }    

        if($tipo==1){
            $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

            $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
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
        
        $condicion_boton=1; //no ver

        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('nivel', $nivel)
            ->with('estimacions', $data)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }
    
    //////////////////////////////////////////////////////////////////
    public function exportEstimacionDataConsolidada($can_id,$establecimiento_id,$opt,$type)
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

        
        if($opt==1){
            $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)                                 
                        ->where('estado','<>',2)                                 
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();
            
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1) 
                        ->where('estado','<>',2)                                                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();

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

                            $sheet->cell('E'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            //$sheet->cell('F'.$i, function($cell) {$cell->setValue('OBSERVACIÓN'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('F'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('G'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('H'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('I'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('J'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('K'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('L'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('M'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('N'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('O'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('P'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('Q'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            

                            $sheet->mergeCells('F'.$i.':Q'.$i);
                            $sheet->cell('F'.$i, function($cell) {$cell->setValue('PRORRATEO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->mergeCells('A'.$d.':Q'.$d);
                            $sheet->cell('O'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->cell('A'.$d, $nombre_dispositivo); //establecimiento 

                            $n=$i+2;
                            
                            $sheet->setMergeColumn(array(
                                'columns' => array('A','B','C'),
                                'rows' => array(
                                    array($i,$n)                        
                                )
                            ));

                            $sheet->setMergeColumn(array(
                                'columns' => array('E','D'),
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
                         
                        
                        
                        switch ($value->estado) {                                    
                                    case 0: $descripcion_observacion=' Ratificado '; break;
                                    case 1: $descripcion_observacion=' Nuevo '; break;
                                    case 2: $descripcion_observacion=' Eliminado '; break;
                                    case 3: $descripcion_observacion=' Actualizado, cpma_ant='.$value->cpma_anterior.' nec_ant='.$value->necesidad_anterior; break;   
                                }

                        $sheet->cell('A'.$k, $m); 
                        $sheet->cell('B'.$k, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$k, $value->descripcion); //nivel
                        if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}
                        //if($value->stock>0){ $sheet->cell('E'.$k, $value->stock);}else{$sheet->cell('E'.$k, number_format($value->stock, 2, '.', ','));}
                        if($value->necesidad_anual>0){ $sheet->cell('E'.$k, $value->necesidad_anual);}else{$sheet->cell('E'.$k, number_format($value->necesidad_anual, 2, '.', ','));}
                        
  //                      $sheet->cell('F'.$k, $descripcion_observacion); 
                        
                        if($value->mes1>0){ $sheet->cell('F'.$k, $value->mes1);}else{$sheet->cell('F'.$k, number_format($value->mes1, 2, '.', ','));}
                        if($value->mes2>0){ $sheet->cell('G'.$k, $value->mes2);}else{$sheet->cell('G'.$k, number_format($value->mes2, 2, '.', ','));}
                        if($value->mes3>0){ $sheet->cell('H'.$k, $value->mes3);}else{$sheet->cell('H'.$k, number_format($value->mes3, 2, '.', ','));}
                        if($value->mes4>0){ $sheet->cell('I'.$k, $value->mes4);}else{$sheet->cell('I'.$k, number_format($value->mes4, 2, '.', ','));}
                        if($value->mes5>0){ $sheet->cell('J'.$k, $value->mes5);}else{$sheet->cell('J'.$k, number_format($value->mes5, 2, '.', ','));}
                        if($value->mes6>0){ $sheet->cell('K'.$k, $value->mes6);}else{$sheet->cell('K'.$k, number_format($value->mes6, 2, '.', ','));}
                        if($value->mes7>0){ $sheet->cell('L'.$k, $value->mes7);}else{$sheet->cell('L'.$k, number_format($value->mes7, 2, '.', ','));}
                        if($value->mes8>0){ $sheet->cell('M'.$k, $value->mes8);}else{$sheet->cell('M'.$k, number_format($value->mes8, 2, '.', ','));}
                        if($value->mes9>0){ $sheet->cell('N'.$k, $value->mes9);}else{$sheet->cell('N'.$k, number_format($value->mes9, 2, '.', ','));}
                        if($value->mes10>0){ $sheet->cell('O'.$k, $value->mes10);}else{$sheet->cell('O'.$k, number_format($value->mes10, 2, '.', ','));}
                        if($value->mes11>0){ $sheet->cell('P'.$k, $value->mes11);}else{$sheet->cell('P'.$k, number_format($value->mes11, 2, '.', ','));}
                        if($value->mes12>0){ $sheet->cell('Q'.$k, $value->mes12);}else{$sheet->cell('Q'.$k, number_format($value->mes12, 2, '.', ','));}

//                        $sheet->cell('S'.$k, $descripcion_observacion);  


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
                    //if($total_stock>0){ $sheet->cell('E'.$n, $total_stock);}else{$sheet->cell('E'.$n, number_format($total_stock, 2, '.', ','));}
                    
                    if($total_necesidad_anual>0){ $sheet->cell('E'.$n, $total_necesidad_anual);}else{$sheet->cell('E'.$n, number_format($total_necesidad_anual, 2, '.', ','));}
                    $sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    
                    //$sheet->cell('F'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total>0){ $sheet->cell('F'.$n, $mes1_total);}else{$sheet->cell('F'.$n, number_format($mes1_total, 2, '.', ','));}
                    $sheet->cell('F'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total>0){ $sheet->cell('G'.$n, $mes2_total);}else{$sheet->cell('G'.$n, number_format($mes2_total, 2, '.', ','));}
                    $sheet->cell('G'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total>0){ $sheet->cell('H'.$n, $mes3_total);}else{$sheet->cell('H'.$n, number_format($mes3_total, 2, '.', ','));}
                    $sheet->cell('H'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total>0){ $sheet->cell('I'.$n, $mes4_total);}else{$sheet->cell('I'.$n, number_format($mes4_total, 2, '.', ','));}
                    $sheet->cell('I'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total>0){ $sheet->cell('J'.$n, $mes5_total);}else{$sheet->cell('J'.$n, number_format($mes5_total, 2, '.', ','));}
                    $sheet->cell('J'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total>0){ $sheet->cell('K'.$n, $mes6_total);}else{$sheet->cell('K'.$n, number_format($mes6_total, 2, '.', ','));}
                    $sheet->cell('K'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total>0){ $sheet->cell('L'.$n, $mes7_total);}else{$sheet->cell('L'.$n, number_format($mes7_total, 2, '.', ','));}
                    $sheet->cell('L'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total>0){ $sheet->cell('M'.$n, $mes8_total);}else{$sheet->cell('M'.$n, number_format($mes8_total, 2, '.', ','));}
                    $sheet->cell('M'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total>0){ $sheet->cell('N'.$n, $mes9_total);}else{$sheet->cell('N'.$n, number_format($mes9_total, 2, '.', ','));}
                    $sheet->cell('N'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total>0){ $sheet->cell('O'.$n, $mes10_total);}else{$sheet->cell('O'.$n, number_format($mes10_total, 2, '.', ','));}
                    $sheet->cell('O'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total>0){ $sheet->cell('P'.$n, $mes11_total);}else{$sheet->cell('P'.$n, number_format($mes11_total, 2, '.', ','));}
                    $sheet->cell('P'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total>0){ $sheet->cell('Q'.$n, $mes12_total);}else{$sheet->cell('Q'.$n, number_format($mes12_total, 2, '.', ','));}
                    $sheet->cell('Q'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
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
                

/*                $sheet->cell('C'.$m, function($cell) {$cell->setValue('____________________________________________________');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->cell('C'.$j, function($cell) {$cell->setValue('RESPONSABLE DEL ESTABLECIMIENTO DE SALUD');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('P2:Q2');
                $sheet->mergeCells('I'.$m.':'.'L'.$m);
                $sheet->cell('I'.$m, function($cell) {$cell->setValue('______________________________________');  $cell->setFontSize(14); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('I'.$j.':'.'L'.$j);
                $sheet->cell('I'.$j, function($cell) {$cell->setValue('RESPONSABLE DE LA FARMACIA');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });
*/
            });
        })->download($type);
    }
    
    public function ver_consolidado_farmacia($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('farmacia.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        $stock_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  


        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)   
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();
            
            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)                            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
            $cerrado=$stock_cerrado->get(0)->medicamento_cerrado;
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();  

                    $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();  

                    $cerrado=$stock_cerrado->get(0)->dispositivo_cerrado;
                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('farmacia.index'));  
                }
        }
        
        //dd($data);
            $condicion_boton=2; //no ver
            $servicio_id_cero=0;
            $cerrado_stock=1;

        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('servicio_id', $servicio_id_cero)
            ->with('estimacions', $data)
            ->with('cerrado', $cerrado)
            ->with('nivel', $nivel)
            ->with('cerrado_stock', $cerrado_stock)            
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function ver_stock_farmacia($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('farmacia.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia.index'));
        }

        $stock_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  


        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)   
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();
            
            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)                            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $cerrado_stock=$stock_cerrado->get(0)->medicamento_cerrado_stock;
            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();  

                    $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();  

                    $descripcion_tipo='Dispositivos';
                    $cerrado_stock=$stock_cerrado->get(0)->dispositivo_cerrado_stock;
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('farmacia.index'));  
                }
        }
        
        //dd($data);
            $condicion_boton=2; //no ver
            $servicio_id_cero=0;
            $cerrado=1;

        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('servicio_id', $servicio_id_cero)
            ->with('estimacions', $data)
            ->with('nivel', $nivel)
            ->with('cerrado', $cerrado)
            ->with('cerrado_stock', $cerrado_stock)            
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }


    public function ver_rectificacion_farmacia($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('farmacia.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia.index'));
        }

        $producto_rectificacion=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  


        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)   
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();
            
            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)                            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

                    $cerrado_rectificacion=$producto_rectificacion->get(0)->medicamento_cerrado_rectificacion;
                    $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();  

                    $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();  

                    $descripcion_tipo='Dispositivos';
                    $cerrado_rectificacion=$producto_rectificacion->get(0)->dispositivo_cerrado_rectificacion;
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('farmacia.index'));  
                }
        }
        
        //dd($data);
            $condicion_boton=2; //no ver
            $servicio_id_cero=0;
            $cerrado=1;

        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos_rectificacion')
            ->with('condicion_boton', $condicion_boton)
            ->with('servicio_id', $servicio_id_cero)
            ->with('estimacions', $data)
            ->with('nivel', $nivel)
            ->with('cerrado', $cerrado)
            ->with('cerrado_rectificacion', $cerrado_rectificacion)            
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function ver_stock_nivel_1($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('farmacia.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)   
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();
            
            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)                            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();  

                    $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();  

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('farmacia.index'));  
                }
        }
        
        //dd($data);
            $condicion_boton=2; //no ver
            $servicio_id_cero=0;
            

        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('servicio_id', $servicio_id_cero)
            ->with('estimacions', $data)
            ->with('nivel', $nivel)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function ver_rectificacion_nivel_1($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('farmacia.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)   
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();
            
            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)                            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();  

                    $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();  

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('farmacia.index'));  
                }
        }
        
        //dd($data);
            $condicion_boton=2; //no ver
            $servicio_id_cero=0;
            

        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('servicio_id', $servicio_id_cero)
            ->with('estimacions', $data)
            ->with('nivel', $nivel)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

     //////////////////Asignar medicamentos //////////////////////////////////////////
    //public function cargar_medicamentos($can_id, $establecimiento_id, $tipo,$cerrado)
    public function cargar_medicamentos($can_id, $tipo)
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

        

        $modeluser = new User;
        //$contar_responsable = $modeluser->buscar_responsable(Auth::user()->establecimiento_id);

        /*if(Auth::user()->establecimiento_id==79)
        {
            
            if($contar_responsable>2)
                $contar_responsable=4;
        }*/

        //if($contar_responsable>3){

            $nombre_servicio=Auth::user()->nombre_servicio;

            $estimacions = Estimacion::find($can_id);

            $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

            if($tipo==1){  //medicamentos
                //buscamos si existe medicamentos ya ingresados
                

                $descripcion_tipo='Medicamentos';
                $numero_medicamentos=DB::table('estimacions')
                                ->where('can_id',$can_id)
                                //->where('necesidad_anual','>',0)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('estado','<>',2)//cambiar 1
                                ->where('tipo_dispositivo_id',1)                                
                                ->count(); 

                //if($establecimiento_id==68)
                  //  $numero_medicamentos=0;


                $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    //->where('necesidad_anual','>',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    
                
                if(Auth::user()->rol==7){
                    if($nivel==1){
                        $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id',1)
                                          ->where('nivel_id',1)
                                          ->where('estado',1)
                                          ->count(); 

                        $petitorios = DB::table('petitorios')
                                            ->where('tipo_dispositivo_medicos_id',1)
                                            ->where('nivel_id',1)
                                            ->where('estado',1)
                                            ->orderby('descripcion','asc')
                                            ->pluck('petitorios.descripcion','petitorios.id');
                    }
                    else
                    {
                        $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id',1)
                                          ->where('nivel_id','<=',$nivel)               
                                         ->where('estado',1)
                                          ->count(); 

                        $petitorios = DB::table('petitorios')
                                            ->where('tipo_dispositivo_medicos_id',1)
                                            ->where('nivel_id','<=',$nivel)
                                            ->where('estado',1)              
                                            ->orderby('descripcion','asc')               
                                            ->pluck('petitorios.descripcion','petitorios.id');
                    }

                    $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado;
                    $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->medicamento_cerrado_stock;
                }
                else
                {
                    if(Auth::user()->rol==1){
                        return redirect(route('cans.index'));
                    }

                }

                $diferencia=$total_medicamentos_rubro-$numero_medicamentos;

            }
            else
            {
                //buscamos si existe medicamentos ya ingresados
                $descripcion_tipo='Dispositivos Medicos';
                $numero_medicamentos=DB::table('estimacions')
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id','>',1)       
                                //->where('necesidad_anual','>',0)
                                ->where('estado','<>',2)//cambiar 1                         
                                ->count(); 

                $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)        
                    //->where('necesidad_anual','>',0)            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    
                

                if(Auth::user()->rol==7){
                    if($establecimiento_id==4)
                        {
                            $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id','<',3)
                                ->where('estado',1)                                
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->get();

                            $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id','>',1)
                                          ->where('nivel_id','<',3)
                                          ->where('estado',1)
                                          ->count(); 

                            $petitorios = DB::table('petitorios')
                                                ->where('tipo_dispositivo_medicos_id','>',1)
                                                ->where('nivel_id','<',3)
                                                ->where('estado',1)
                                                ->orderby('descripcion','asc')
                                                ->pluck('petitorios.descripcion','petitorios.id');
                            $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado; 
                            $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->dispositivo_cerrado_stock; 

                        }
                        else
                        {
                            if($nivel==1){

                            $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id',$establecimiento->nivel_id)
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->where('estado',1)
                                ->get();

                            $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id','>',1)
                                          ->where('nivel_id',1)
                                          ->where('estado',1)
                                          ->count(); 

                            $petitorios = DB::table('petitorios')
                                                ->where('tipo_dispositivo_medicos_id','>',1)
                                                ->where('nivel_id',1)
                                                ->where('estado',1)
                                                ->orderby('descripcion','asc')
                                                ->pluck('petitorios.descripcion','petitorios.id');
                            

                            }else
                            {
                                $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id',$establecimiento->nivel_id)
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->where('estado',1)
                                ->get();

                                $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                              ->where('tipo_dispositivo_medicos_id','>',1)
                                              ->where('nivel_id','<=',$nivel)
                                              ->where('estado',1)
                                              ->count(); 

                                $petitorios = DB::table('petitorios')
                                                    ->where('tipo_dispositivo_medicos_id','>',1)
                                                    ->where('nivel_id','<=',$nivel)
                                                    ->where('estado',1)
                                                    ->orderby('descripcion','asc')
                                                    ->pluck('petitorios.descripcion','petitorios.id');
                            }
                            $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado; 
                            $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->dispositivo_cerrado_stock; 
                        }
                }
                $diferencia=$total_medicamentos_rubro-$numero_medicamentos;
            } 


            //Si hay medicamentos asignados
            if ($numero_medicamentos==0){ //recien ingresa
                return view('site.responsable_farmacia.medicamentos.asignar_medicamentos')
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
                return view('site.responsable_farmacia.medicamentos.medicamentos')
                                ->with('estimacions', $data)
                                ->with('nivel', $nivel)
                                ->with('servicio_id', $servicio_id)
                                ->with('numero_medicamentos', $diferencia)
                                ->with('descripcion_tipo', $descripcion_tipo)
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)
                                ->with('medicamento_cerrado_stock', $medicamento_cerrado_stock)
                                ->with('tipo', $tipo)
                                ->with('can_id', $can_id);
            }
        /*}
        else
        {
            
            return redirect(route('users.index_responsable'));  
        }*/

    }

    public function cargar_medicamentos_rectificacion($can_id, $tipo)
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

        $modeluser = new User;
        $contar_responsable = $modeluser->buscar_responsable(Auth::user()->establecimiento_id);

        if(Auth::user()->establecimiento_id==79)
        {
            if($contar_responsable>2)
                $contar_responsable=4;
        }
        if($contar_responsable>3){

            $nombre_servicio=Auth::user()->nombre_servicio;

            $estimacions = Estimacion::find($can_id);

            $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

            if($tipo==1){  //medicamentos
                //buscamos si existe medicamentos ya ingresados
                $descripcion_tipo='Medicamentos';
                $numero_medicamentos=DB::table('estimacions')
                                ->where('can_id',$can_id)
                                ->where('necesidad_anual','>',0)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('estado','<>',2)//cambiar 1
                                ->where('tipo_dispositivo_id',1)                                
                                ->count(); 

                $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('necesidad_anual','>',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    

                if(Auth::user()->rol==7){
                    if($nivel==1){
                        $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id',1)
                                          ->where('nivel_id',1)
                                          ->where('estado',1)
                                          ->count(); 

                        $petitorios = DB::table('petitorios')
                                            ->where('tipo_dispositivo_medicos_id',1)
                                            ->where('nivel_id',1)
                                            ->where('estado',1)
                                            ->orderby('descripcion','asc')
                                            ->pluck('petitorios.descripcion','petitorios.id');
                    }
                    else
                    {
                        $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id',1)
                                          ->where('nivel_id','<=',$nivel)               
                                         ->where('estado',1)
                                          ->count(); 

                        $petitorios = DB::table('petitorios')
                                            ->where('tipo_dispositivo_medicos_id',1)
                                            ->where('nivel_id','<=',$nivel)
                                            ->where('estado',1)             
                                            ->orderby('descripcion','asc')                
                                            ->pluck('petitorios.descripcion','petitorios.id');
                    }

                    $medicamento_cerrado_rectificacion=$establecimiento_cerrado->get(0)->medicamento_cerrado_rectificacion;
                    $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->medicamento_cerrado_stock;
                    $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado; 
                }


                $diferencia=$total_medicamentos_rubro-$numero_medicamentos;

            }
            else
            {
                //buscamos si existe medicamentos ya ingresados
                $descripcion_tipo='Dispositivos Medicos';
                $numero_medicamentos=DB::table('estimacions')
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('tipo_dispositivo_id','>',1)       
                                ->where('necesidad_anual','>',0)
                                ->where('estado','<>',2)//cambiar 1                         
                                ->count(); 

                $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)        
                    ->where('necesidad_anual','>',0)            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    
                

                if(Auth::user()->rol==7){
                    if($establecimiento_id==4)
                        {
                            $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id','<',3)
                                ->where('estado',1)                                
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->get();

                            $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id','>',1)
                                          ->where('nivel_id','<',3)
                                          ->where('estado',1)
                                          ->count(); 

                            $petitorios = DB::table('petitorios')
                                                ->where('tipo_dispositivo_medicos_id','>',1)
                                                ->where('nivel_id','<',3)
                                                ->where('estado',1)
                                                ->orderby('descripcion','asc')
                                                ->pluck('petitorios.descripcion','petitorios.id');
                            $medicamento_cerrado_rectificacion=$establecimiento_cerrado->get(0)->dispositivo_cerrado_rectificacion; 
                            $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->dispositivo_cerrado_stock; 
                            $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado; 
                        }
                        else
                        {
                            if($nivel==1){

                            $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id',$establecimiento->nivel_id)
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->where('estado',1)
                                ->get();

                            $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id','>',1)
                                          ->where('nivel_id',1)
                                          ->where('estado',1)
                                          ->count(); 

                            $petitorios = DB::table('petitorios')
                                                ->where('tipo_dispositivo_medicos_id','>',1)
                                                ->where('nivel_id',1)
                                                ->where('estado',1)
                                                ->orderby('descripcion','asc')
                                                ->pluck('petitorios.descripcion','petitorios.id');
                            

                            }else
                            {
                                $petitorio_total=DB::table('petitorios')
                                ->where('nivel_id',$establecimiento->nivel_id)
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->where('estado',1)
                                ->get();

                                $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                              ->where('tipo_dispositivo_medicos_id','>',1)
                                              ->where('nivel_id','<=',$nivel)
                                              ->where('estado',1)
                                              ->count(); 

                                $petitorios = DB::table('petitorios')
                                                    ->where('tipo_dispositivo_medicos_id','>',1)
                                                    ->where('nivel_id','<=',$nivel)
                                                    ->where('estado',1)
                                                    ->orderby('descripcion','asc')
                                                    ->pluck('petitorios.descripcion','petitorios.id');
                            }
                            $medicamento_cerrado_rectificacion=$establecimiento_cerrado->get(0)->dispositivo_cerrado_rectificacion; 
                            $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->dispositivo_cerrado_stock; 
                            $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado; 
                        }
                }
                $diferencia=$total_medicamentos_rubro-$numero_medicamentos;
            } 

            //Si hay medicamentos asignados
            if ($numero_medicamentos==0){ //recien ingresa
                return view('site.responsable_farmacia.medicamentos.asignar_medicamentos')
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
                if($nivel==1){
                    return view('site.responsable_farmacia.medicamentos.medicamentos_rectificacion')
                                ->with('estimacions', $data)
                                ->with('nivel', $nivel)
                                ->with('servicio_id', $servicio_id)
                                ->with('numero_medicamentos', $diferencia)
                                ->with('descripcion_tipo', $descripcion_tipo)
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('medicamento_cerrado_rectificacion', $medicamento_cerrado_rectificacion)
                                ->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)
                                ->with('medicamento_cerrado_stock', $medicamento_cerrado_stock)
                                ->with('tipo', $tipo)
                                ->with('can_id', $can_id);
                }
                else
                {
                    return view('site.responsable_farmacia.medicamentos.medicamentos_rectificacion2')
                                ->with('estimacions', $data)
                                ->with('nivel', $nivel)
                                ->with('servicio_id', $servicio_id)
                                ->with('numero_medicamentos', $diferencia)
                                ->with('descripcion_tipo', $descripcion_tipo)
                                ->with('establecimiento_id', $establecimiento_id)
                                ->with('medicamento_cerrado_rectificacion', $medicamento_cerrado_rectificacion)
                                ->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)
                                ->with('medicamento_cerrado_stock', $medicamento_cerrado_stock)
                                ->with('tipo', $tipo)
                                ->with('can_id', $can_id);

                }
                
            }
        }
        else
        {
            
            return redirect(route('users.index_responsable',[$can_id]));
        }

    }

    public function guardar_medicamentos_asignados(Request $request, $can_id,$tipo)
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

        //nombre del servicio o distribuidor
        $nombre_servicio=Auth::user()->nombre_servicio;
    
        if (empty($request->petitorios)) {
            Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
            
        }else
        {
            if($tipo==1)
            {
                $petitorio_total=DB::table('petitorios')
                        ->where('nivel_id',$establecimiento->nivel_id)
                        ->where('tipo_dispositivo_medicos_id',1)
                        ->where('estado',1)
                        ->get();    
            }
            else
            {
                if($establecimiento_id==4)
                {
                    $petitorio_total=DB::table('petitorios')
                        ->where('nivel_id','<',3)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('estado',1)
                        ->get();        
                }
                else
                {
                    $petitorio_total=DB::table('petitorios')
                        ->where('nivel_id',$establecimiento->nivel_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('estado',1)
                        ->get(); 
                }
            }
            
            foreach($request->petitorios as $key => $petitorio_id){
                foreach($petitorio_total as $id => $petitorio){     
                    //DB::table('abastecimientos')
                    if($petitorio_id == $petitorio->id){
                        
                        DB::table('estimacions')
                             ->insert([
                                'can_id' => $can_id,
                                'establecimiento_id' => $establecimiento->id,
                                'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                                'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                                'tipo_dispositivo_id' => $petitorio->tipo_dispositivo_medicos_id,
                                'uso_id' => $petitorio->tipo_uso_id,    
                                'petitorio_id' => $petitorio_id,
                                'cod_petitorio' => $petitorio->codigo_petitorio,
                                'cod_siga' => $petitorio->codigo_siga,
                                'descripcion' => $petitorio->descripcion,
                                'created_at'=>Carbon::now(), 
                        ]);
                    }         
                }         
            }        
        }    
        
        Flash::success('Productos asignados correctamente.');

        return redirect(route('farmacia.cargar_medicamentos',[$can_id,$tipo]));
    }

    public function editar_consolidado_farmacia($tipo,$can_id)
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

        if(Auth::user()->rol==3){
            $consolidado=3; //almacen
        }
        else
        {
            $consolidado=4; //farmacia   
        }

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('consolidado',$consolidado)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();

            $descripcion_tipo='Medicamentos';

            $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

            if(Auth::user()->rol==3){
                $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado_consolidado_almacen;
            }else
             {
                $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado_consolidado;
             }   


            $numero_medicamentos=DB::table('estimacions')
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_id',1)
                            ->where('consolidado',$consolidado)
                            ->count(); 

            if(Auth::user()->rol==3){
                $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                  ->where('tipo_dispositivo_medicos_id',1)
                                  ->where('nivel_id',1)
                                  ->where('estado',1)
                                  ->where('tipo_uso_id','<>',5)
                                  ->count(); 
            }
            else
            {
                $total_medicamentos_rubro = DB::table('petitorios')  //farmacia
                                  ->where('tipo_dispositivo_medicos_id',1)
                                  ->where('nivel_id',1)
                                  ->where('estado',1)
                                  ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_uso_id',5)
                                            ->orWhere('tipo_uso_id',2);
                                    })
                                  ->count();    
            }
            $diferencia=$total_medicamentos_rubro-$numero_medicamentos;

        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('consolidado',$consolidado)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    
                    $descripcion_tipo='Dispositivos';

                    $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

                    if(Auth::user()->rol==3){
                        $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado_consolidado_almacen;
                    }
                    else
                    {
                        $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado_consolidado;   
                    }

                    $numero_medicamentos=DB::table('estimacions')
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('consolidado',$consolidado)
                            ->where('tipo_dispositivo_id','>',1)
                            ->count(); 

                    if(Auth::user()->rol==3){
                        $total_medicamentos_rubro = DB::table('petitorios') //almacen
                                          ->where('tipo_dispositivo_medicos_id','>',1)
                                          ->where('nivel_id',1)
                                          ->where('estado',1)
                                          ->where('tipo_uso_id','<>',2) //
                                          ->count(); 
                    }
                    else
                    {
                        $total_medicamentos_rubro = DB::table('petitorios') //farmacia
                                          ->where('tipo_dispositivo_medicos_id','>',1)
                                          ->where('nivel_id',1)
                                          ->where('estado',1)
                                          ->where( function ( $query )
                                            {
                                                $query->orWhere('tipo_uso_id',5)
                                                    ->orWhere('tipo_uso_id',2);
                                            })
                                          ->count();    
                    }

                    $diferencia=$total_medicamentos_rubro-$numero_medicamentos;


                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }
     
        return view('site.responsable_farmacia.medicamentos.medicamentos')
            ->with('estimacions', $data)
            ->with('numero_medicamentos', $diferencia)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function nuevo_medicamento_dispositivo( $can_id, $establecimiento_id, $tipo_producto )
    {
        
        //Verificamos si el usuario es el mismo
        if (Auth::user()->establecimiento_id == $establecimiento_id ){
            if ($tipo_producto >0 && $tipo_producto <3 )
            {
                $rol_id=Auth::user()->rol;
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

                        
                        if($rol_id==3){

                            //Buscamos todos los medicamentos segun el nivel
                            $consulta_petitorio = DB::table('petitorios') //almacen
                                    ->where('tipo_dispositivo_medicos_id',1)
                                    ->where('nivel_id',1)
                                    ->where('estado',1)
                                    ->where('tipo_uso_id','<>',5)
                                    ->get();    

                            $consolidado=3;
                        }
                        else
                        {
                            if($rol_id==4){
                            //Buscamos todos los medicamentos segun el nivel
                            $consulta_petitorio = DB::table('petitorios') //farmacia
                                    ->where('tipo_dispositivo_medicos_id',1)
                                    ->where('nivel_id',1)
                                    ->where('estado',1)
                                    ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_uso_id',5)
                                            ->orWhere('tipo_uso_id',2);
                                    })
                                    ->get();
                            $consolidado=4;
                            }
                            else
                            {
                                if($rol_id==7){
                                    //Buscamos todos los medicamentos segun el nivel
                                    $consulta_petitorio = DB::table('petitorios') //farmacia
                                            ->where('tipo_dispositivo_medicos_id',1)
                                            ->where('nivel_id',1)
                                            ->where('estado',1)
                                            ->get();
                                }

                            }

                        }
                        
                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                        
                        if($rol_id==7){
                            //Buscamos los medicamentos segun el nivel 
                            $consulta_medicamentos_nivel = DB::table('estimacions')
                                ->where('tipo_dispositivo_id',1)
                                ->where('can_id',$can_id)
                                ->where('estado','<>',2)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->orderby('descripcion','asc');           
                        }
                        else
                        {

                            //Buscamos los medicamentos segun el nivel 
                            $consulta_medicamentos_nivel = DB::table('estimacions')
                                ->where('tipo_dispositivo_id',1)
                                ->where('can_id',$can_id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('estado','<>',2)                                
                                ->orderby('descripcion','asc');              
                        }

                        //pasamos a un arreglo
                        $consulta_medicamento = $consulta_medicamentos_nivel->pluck('descripcion','petitorio_id')->toArray();

                        //dd($consulta_medicamento);                        
                        //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                        $descripcion=array_diff($petitorio,$consulta_medicamento);
                        
                    }
                    else
                    {
                        if($rol_id==3){
                            //Buscamos todos los medicamentos segun el nivel
                            if($establecimiento_id==4){
                                $consulta_petitorio = DB::table('petitorios') //almacen
                                    ->where('tipo_dispositivo_medicos_id','>',1)
                                    ->where('nivel_id','<',3)
                                    ->where('estado',1)
                                    ->where('tipo_uso_id','<>',5)
                                    ->get();
                            }
                            else
                            {
                                $consulta_petitorio = DB::table('petitorios') //almacen
                                    ->where('tipo_dispositivo_medicos_id','>',1)
                                    ->where('nivel_id',1)
                                    ->where('estado',1)
                                    ->where('tipo_uso_id','<>',5)
                                    ->get();    
                            }
                            

                            $consolidado=3;





                        }
                        else
                        {
                            if($rol_id==4){
                                if($establecimiento_id==4){
                                    //Buscamos todos los medicamentos segun el nivel
                                    $consulta_petitorio = DB::table('petitorios') //farmacia
                                        ->where('tipo_dispositivo_medicos_id','>',1)
                                        ->where('nivel_id','<',3)
                                        ->where('estado',1)
                                        ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_uso_id',5)
                                                ->orWhere('tipo_uso_id',2);
                                        })
                                        ->get();   
                                }
                                else
                                {
                                    //Buscamos todos los medicamentos segun el nivel
                                    $consulta_petitorio = DB::table('petitorios') //farmacia
                                        ->where('tipo_dispositivo_medicos_id','>',1)
                                        ->where('nivel_id',1)
                                        ->where('estado',1)
                                        ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_uso_id',5)
                                                ->orWhere('tipo_uso_id',2);
                                        })
                                        ->get();       
                                }
                                

                                $consolidado=4;

                            }
                            else
                            {
                                if($rol_id==7){

                                    if($establecimiento_id==4){
                                        $consulta_petitorio = DB::table('petitorios') //farmacia
                                        ->where('tipo_dispositivo_medicos_id',4)                                        
                                        ->where('estado',1)
                                        ->orwhere( function ( $query )
                                        {
                                            $query->Where('tipo_dispositivo_medicos_id','>',1)
                                                ->Where('nivel_id',1);
                                        })
                                        ->get();  
                                    }
                                    else
                                    {
                                            //Buscamos todos los medicamentos segun el nivel
                                        $consulta_petitorio = DB::table('petitorios') //farmacia
                                                ->where('tipo_dispositivo_medicos_id','>',1)
                                                ->where('nivel_id',1)
                                                ->where('estado',1)
                                                ->get();   
                                    }
                                }
                                
                            }
                        }
                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                        
                        if($rol_id==7){
                        //Buscamos los medicamentos segun el nivel 
                        $consulta_medicamentos_nivel = DB::table('estimacions')
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)                            
                            ->where('estado','<>',2)
                            ->orderby('descripcion','asc');                            
                        }
                        else
                        {
                         //Buscamos los medicamentos segun el nivel 
                        $consulta_medicamentos_nivel = DB::table('estimacions')
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('estado','<>',2)                            
                            ->orderby('descripcion','asc');                               
                        }
                        //pasamos a un arreglo
                        $consulta_medicamento = $consulta_medicamentos_nivel->pluck('descripcion','petitorio_id')->toArray();

                        //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                        $descripcion=array_diff($petitorio,$consulta_medicamento);
                        
                    }

                    //Enviamos al formulario
                    return view('site.responsable_farmacia.nuevo.index')
                            ->with('establecimiento_id', $establecimiento_id)
                            ->with('can_id', $can_id)
                            ->with('destino', $tipo_producto)
                            ->with('descripcion', $descripcion);
                }
                else
                {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('farmacia.index'));
                }
            }
            else
            {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('farmacia.index'));
            }
        }
        else
        {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('farmacia.index'));
        }
    }

    public function grabar_nuevo_medicamento_dispositivo(Request $request,$establecimiento_id,$can_id, $destino)
    {

        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            $rol_id=Auth::user()->rol;            
            
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
            $cod_petitorio=$petitorio->codigo_petitorio;
            $codigo_siga=$petitorio->codigo_siga;
            $uso_id=$petitorio->tipo_uso_id;

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

            DB::table('estimacions')
            ->insert([
                        'can_id' => $can_id,
                        'establecimiento_id'=>$establecimiento_id,
                        'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                        'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                        'petitorio_id'=>$petitorio_id,
                        'cod_petitorio'=>$cod_petitorio,
                        'cod_siga'=>$codigo_siga,
                        'descripcion'=>$descripcion,
                        'tipo_dispositivo_id'=>$tipo_dispositivo_id,
                        'cpma' => $cpma,
                        'stock' => $stock,
                        'necesidad_anual' => $necesidad_anual,
                        'estado' => 1,
                        'estado_necesidad' => 1,
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
                //        'consolidado'=>$consolidado,  
                        'uso_id'=> $uso_id,              
            ]);

        
            if($necesidad_anual<0){
                Flash::error('No se ha podido guardar el medicamento, la suma total de ingreso es menor a la suma total de salida');
            }
            else
            {
                Flash::success('Se ha guardado con exito');
            }

            return redirect(route('farmacia.cargar_medicamentos',[$can_id,$destino]));

            
            
        }   
        
    }


    public function ver_datos_consolidado_farmacia($tipo,$can_id)
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

        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $descripcion_tipo='Medicamentos';

            $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

            $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado_consolidado;
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    
                    $descripcion_tipo='Dispositivos';

                    $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

                    $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado_consolidado;

                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }
        
        //dd($data);
        return view('site.responsable_farmacia.medicamentos.medicamentos')
            ->with('estimacions', $data)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function cargar_datos_consolidado($can_id, $establecimiento_id, $tipo)
    {
        //averiguamos el nivel del establecimiento

        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;

        if(Auth::user()->rol==3){
                $consolidado=3; //almacen
            }
            else
            {
                $consolidado=4; //farmacia   
            }    
        
        
        if(Auth::user()->rol==7){

            if($tipo==1){
                $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        //->where('necesidad_anual','>',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                $descripcion_tipo='Medicamentos';
            }
            else
            {   if ($tipo==2) {
                        $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        //->where('tipo_dispositivo_id',4)
                        //->where('necesidad_anual','>',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();                    
                        $descripcion_tipo='Dispositivos';
                }
                else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
            }
        }
        else
        {
            if($tipo==1){
                $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22                        
                        ->where('tipo_dispositivo_id',1)
                        
                        //->where('necesidad_anual','>',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                $descripcion_tipo='Medicamentos';
            }
            else
            {   if ($tipo==2) {
                        $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        //->where('tipo_dispositivo_id','>',1)
                        ->where('tipo_dispositivo_id',4)
                        //->where('necesidad_anual','>',0)
                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();                    
                        $descripcion_tipo='Dispositivos';
                }
                else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
            }
        }


        if($nivel==1):
        return Datatables::of($contact)
        ->addColumn('action', function($contact){
          return "<a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>
           <a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='deleteForm(". $contact->id .")' class='btn btn-danger btn-xs'><i class='glyphicon glyphicon-trash'></i></a>";

        })
        ->rawColumns(['justificacion', 'action'])->make(true);        
        else:
            return Datatables::of($contact)
        ->addColumn('action', function($contact){
          return "<a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>
          ";

        })
        ->rawColumns(['justificacion', 'action'])->make(true);        
        endif;
        
        /*
        return Datatables::of($contact)
        ->addColumn('action', function($contact){
          return "<a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>";

        })
        ->rawColumns(['justificacion', 'action'])->make(true);        
        */
    }

    public function cargar_datos_rectificacion($can_id, $establecimiento_id, $tipo)
    {
        //averiguamos el nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;

        if(Auth::user()->rol==3){
                $consolidado=3; //almacen
            }
            else
            {
                $consolidado=4; //farmacia   
            }    
        

        if(Auth::user()->rol==7){

            if($tipo==1){
                
                $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        ->where('necesidad_anual','>',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc                        
                        ->get();
                $descripcion_tipo='Medicamentos';                
            }
            else
            {   if ($tipo==2) {
                        $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('necesidad_anual','>',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc                        
                        ->get();                    
                        $descripcion_tipo='Dispositivos';
                }
                else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
            }
        }
        /*else
        {
            if($tipo==1){
                $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        ->where('consolidado',$consolidado)
                        ->where('necesidad_anual','>',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                $descripcion_tipo='Medicamentos';
            }
            else
            {   if ($tipo==2) {
                        $contact=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('necesidad_anual','>',0)
                        ->where('consolidado',$consolidado)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();                    
                        $descripcion_tipo='Dispositivos';
                }
                else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
            }
        }
        */
        /*  
        return Datatables::of($contact)
        ->addColumn('action', function($contact){
          return "<a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>
           <a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='deleteForm(". $contact->id .")' class='btn btn-danger btn-xs'><i class='glyphicon glyphicon-trash'></i></a>";

        })
        ->rawColumns(['justificacion', 'action'])->make(true);        
        */
        
        return Datatables::of($contact)
        ->addColumn('action', function($contact){
          return "<a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>";

        })
        ->rawColumns(['justificacion', 'action'])->make(true);        
    }
    
    public function cerrar_no_aplica($can_id,$tipo)
    {

        $establecimiento_id=Auth::user()->establecimiento_id;
        $rol=Auth::user()->rol;
        
        if ($tipo==1){
            if($rol==7){
                $tipo_cerrado='medicamento_cerrado_consolidado_almacen';
            }
            else
            {
                if($rol==3){

                }
                else
                {

                }
            }

            $tipo_cerrado='medicamento_cerrado_consolidado_almacen';
            $tipo_cerrado2='medicamento_cerrado_consolidado';
            $tipo_cerrado3='medicamento_cerrado';
        }
        else
        {
            $tipo_cerrado='dispositivo_cerrado_consolidado_almacen';
            $tipo_cerrado2='dispositivo_cerrado_consolidado';
            $tipo_cerrado3='dispositivo_cerrado';
        }
        

        DB::table('can_establecimiento')
                ->where('can_id', $can_id)
                ->where('establecimiento_id', $establecimiento_id)
                ->update([
                        $tipo_cerrado => 2,
                        'updated_at'=>Carbon::now()
            ]);
    }
    
    public function pdf_estimacion_nivel1($can_id,$establecimiento_id,$tipo,$servicio_id)
    {
        
        $establecimiento = Establecimiento::find($establecimiento_id);        

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('farmacia.index'));
        }
        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia.index'));
        }

        
        if($tipo==1){
            
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();

            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                    
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                        
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('tipo_dispositivo_id')
                    ->get();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $total_tipo_productos=DB::table('consolidados')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')                           
                        ->get();                    

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('farmacia.index'));  
                }
        }

        
        $name=Auth::user()->name;
        $servicio_id=Auth::user()->servicio_id;
        $user_id=Auth::user()->id;
        $cip=Auth::user()->cip;
        $dni=Auth::user()->dni;
        $grado=Auth::user()->grado;

        $responsables = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<=',7)->where('can_id',$can_id)->where('etapa',1)->orderby('rol','asc')->get();
        $jefes = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',3)->where('rol','<=',5)->where('can_id',$can_id)->where('etapa',1)->count();
        
        $responsable[]="";$j=0;$r=0;
        foreach ($responsables as $key => $resp) {
            switch ($resp->rol) {
                case 4: // responsable farmacia
                    $responsable[0]=$resp->nombre;
                    $responsable[3]=$resp->grado;
                    $j++;
                    break;

                case 5: //jefe ipress
                    $responsable[1]=$resp->nombre;
                    $responsable[4]=$resp->grado;
                    $j++;
                    break;

                case 7: //responsable registrador
                    //$responsable[2]=$resp->nombre;
                    //$responsable[5]=$resp->grado;
                    $responsable[2]=$name;
                    $responsable[5]=$grado;
                    $r++;
                    break;
            }
        }

        $responsable_rubro[0]="";
        $responsable_rubro[1]="";
        $responsable_rubro[2]="";
        $responsable_rubro[3]="";
        $responsable_rubro[4]="";
        $responsable_rubro[5]="";
        $responsable_rubro[6]="";
        $responsable_rubro[7]="";
        $responsable_rubro[8]="";
        $responsable_rubro[9]="";
        $responsable_rubro[10]="";

        /*$responsable_rubro[1]=0;
        $responsable_rubro[2]=0;
        $responsable_rubro[3]=0;
        $responsable_rubro[4]=0;
        $responsable_rubro[5]=0;
        $responsable_rubro[6]=0;
        $responsable_rubro[7]=0;
        $responsable_rubro[8]=0;
        $responsable_rubro[9]=0;
        $responsable_rubro[10]=0;*/
        $pf=0; $d=0;

        foreach ($responsables as $key => $resp_rubro) {
            switch ($resp_rubro->servicio_id) {
                case 1: //productos (1)
                    $responsable_rubro[0]=$resp_rubro->nombre;
                    $responsable_rubro[6]=$resp_rubro->grado;
                    $pf++;
                    break;
                case 2: //insumos laboratorio (5,10)
                    $responsable_rubro[1]=$resp_rubro->nombre;
                    $responsable_rubro[7]=$resp_rubro->grado;
                    $d++;
                    break;
                case 3://biomedico,quirurgico,afines (2,3,7)
                    $responsable_rubro[2]=$resp_rubro->nombre;
                    $responsable_rubro[8]=$resp_rubro->grado;
                    $d++;
                    break;
                case 4://dentales (4)
                    $responsable_rubro[3]=$resp_rubro->nombre;
                    $responsable_rubro[9]=$resp_rubro->grado;
                    $d++;
                    break;
                case 5://fotografico (6)
                    $responsable_rubro[4]=$resp_rubro->nombre;
                    $responsable_rubro[10]=$resp_rubro->grado;
                    $d++;
                    break;
            }
        }

        //dd($responsable_rubro);
        if($establecimiento_id==79){
            $j=2; $pf=1;
        }


        if($nivel==1):

            if($j==2){
                if($pf==1 and $d>=1){
                    $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
                    $cierre=$cierre_rubro->get(0)->updated_at;

                    if($tipo==1){
                        $nombre_rubro='MEDICAMENTOS';
                    }
                    else
                    {
                        $nombre_rubro='DISPOSITIVOS';
                    }
                
                    $nombre_archivo=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_CAN_UGPFDMPS';
                    if($establecimiento_id==79){
                        $texto='CONSOLIDADO UNIDAD';
                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_unidad_pdf',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]);
                    }
                    else
                    {
                        $texto='CONSOLIDADO IPRESS';
                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]);    
                        
                        
                    }
                
                    $pdf->setPaper('A4', 'landscape');
                    $pdf->getDomPDF()->set_option("enable_php", true);

                    return $pdf->stream($nombre_archivo.'.pdf');

                }
                else
                {
                    Flash::error('Falto Ingresar algun Personal Responsables de los rubros que se encuentra en su listado de productos');
                    return redirect(route('users.index_responsable',[$can_id]));
                }
            }
            else
            {
                Flash::error('Falta Ingresar al jefe de la IPRESS O JEFE Y/O RESPONSABLE DE FARMACIA');
                return redirect(route('users.index_responsable',[$can_id]));
            }

        else:
            $texto='CONSOLIDADO IPRESS';
                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]); 
             $pdf->setPaper('A4', 'landscape');
                    $pdf->getDomPDF()->set_option("enable_php", true);

                    return $pdf->stream($nombre_archivo.'.pdf');   
        endif;

        
     }

    public function pdf_estimacion_nivel_1($can_id,$establecimiento_id,$tipo,$servicio_id)
    {
        
        $establecimiento = Establecimiento::find($establecimiento_id);        

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        
        if($tipo==1){
            
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();

            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                    
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                        
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('tipo_dispositivo_id')
                    ->get();


            $descripcion_tipo='Medicamentos';
        }else
        {   
            if ($nivel<3) {
                if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')                           
                        ->get();                    

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
            }else
            {
                
                $data=DB::table('estimacions')
                ->where('necesidad_anual','>',0)
                ->where('can_id',$can_id) //cambiar 22
                ->where('tipo_dispositivo_id',$tipo)                    
                ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                ->where('estado','<>',2)
                ->orderby ('tipo_dispositivo_id','asc')  
                ->orderby ('descripcion','asc')  
                ->get();                    

                $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$tipo)     
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

                $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$tipo)                        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)
                    ->groupby('tipo_dispositivo_id')
                    ->orderby ('tipo_dispositivo_id','asc')                           
                    ->get();                    

                $descripcion_tipo='Dispositivos';
                
            }

        }

        $name=Auth::user()->name;
        $servicio_id=Auth::user()->servicio_id;
        $user_id=Auth::user()->id;
        $cip=Auth::user()->cip;
        $dni=Auth::user()->dni;

        if($nivel==1){
            $responsables = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<=',7)->where('estado',1)->orderby('rol','asc')->get();

            //dd($responsables);
            $responsable[]="";
            foreach ($responsables as $key => $resp) {
                switch ($resp->rol) {
                    case 4: // responsable farmacia
                        $responsable[0]=$resp->name;
                        $responsable[3]=$resp->grado;
                        break;

                    case 5: //jefe ipress
                        $responsable[1]=$resp->name;
                        $responsable[4]=$resp->grado;
                        break;

                    case 7: //responsable registrador
                        $responsable[2]=$resp->name;
                        $responsable[5]=$resp->grado;
                        break;
                }
            }

        }
        else
        {
            $responsables = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>=',7)->where('rol','<=',9)->where('estado',1)->orderby('rol','asc')->get();
            //dd($responsables);
            $responsable[]="";
            foreach ($responsables as $key => $resp) {
                switch ($resp->rol) {
                    case 7: // responsable farmacia
                        $responsable[0]=$resp->name;
                        $responsable[3]=$resp->grado;
                        $responsable[2]=$resp->name;
                        $responsable[5]=$resp->grado;
                        break;

                    case 9: //jefe ipress
                        $responsable[1]=$resp->name_rectificacion;
                        $responsable[4]=$resp->grado_rectificacion;
                        break;
                }
            }

        }
        

        

        
        
            //dd($responsable_rubro);
            $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
            $cierre=$cierre_rubro->get(0)->updated_rectificacion;

            if($tipo==1){
                $nombre_rubro='STOCK MEDICAMENTOS';
            }
            else
            {
                $nombre_rubro='STOCK DISPOSITIVOS';
            }
            
            $nombre_archivo=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_CAN2020';

            if($establecimiento_id==79){
                $texto='CONSOLIDADO UNIDAD';
                $pdf = \PDF::loadView('site.pdf.descargar_rubro_unidad_2pdf',['estimaciones'=>$data,
                          'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                          'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable'=>$responsable]);
            }
            else
            {
                $texto='CONSOLIDADO IPRESS';
                $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_2pdf',['estimaciones'=>$data,
                          'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                          'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable'=>$responsable]);    
            }
            
            //$pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream($nombre_archivo.'.pdf');

        
        
     } 

     public function pdf_rectificacion_nivel_1($can_id,$establecimiento_id,$tipo,$servicio_id)
    {
        
        $establecimiento = Establecimiento::find($establecimiento_id);        

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        
        if($tipo==1){
            
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();

            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                    
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                        
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('tipo_dispositivo_id')
                    ->get();


            $descripcion_tipo='Medicamentos';
        }else
        {   
            if ($nivel<3) {
                if ($tipo==2) {
                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')                           
                        ->get();                    

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
            }else
            {
                
                $data=DB::table('estimacions')
                ->where('necesidad_anual','>',0)
                ->where('can_id',$can_id) //cambiar 22
                ->where('tipo_dispositivo_id',$tipo)                    
                ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                ->where('estado','<>',2)
                ->orderby ('tipo_dispositivo_id','asc')  
                ->orderby ('descripcion','asc')  
                ->get();                    

                $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$tipo)     
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

                $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$tipo)                        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('estado','<>',2)
                    ->groupby('tipo_dispositivo_id')
                    ->orderby ('tipo_dispositivo_id','asc')                           
                    ->get();                    

                $descripcion_tipo='Dispositivos';
                
            }

        }

        $name=Auth::user()->name;
        $servicio_id=Auth::user()->servicio_id;
        $user_id=Auth::user()->id;
        $cip=Auth::user()->cip;
        $dni=Auth::user()->dni;

        if($nivel==1){
            $responsables = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<=',7)->where('estado',1)->orderby('rol','asc')->get();

            //dd($responsables);
            $responsable[]="";
            foreach ($responsables as $key => $resp) {
                switch ($resp->rol) {
                    case 4: // responsable farmacia
                        $responsable[0]=$resp->name_rectificacion;
                        $responsable[3]=$resp->grado_rectificacion;
                        break;

                    case 5: //jefe ipress
                        $responsable[1]=$resp->name_rectificacion;
                        $responsable[4]=$resp->grado_rectificacion;
                        break;

                    case 7: //responsable registrador
                        $responsable[2]=$resp->name_rectificacion;
                        $responsable[5]=$resp->grado_rectificacion;
                        break;
                }
            }

        }
        else
        {
            $responsables = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>=',7)->where('rol','<=',9)->where('estado',1)->orderby('rol','asc')->get();
            //dd($responsables);
            $responsable[]="";
            foreach ($responsables as $key => $resp) {
                switch ($resp->rol) {
                    case 7: // responsable farmacia
                        $responsable[0]=$resp->name_rectificacion;
                        $responsable[3]=$resp->grado_rectificacion;
                        $responsable[2]=$resp->name_rectificacion;
                        $responsable[5]=$resp->grado_rectificacion;
                        break;

                    case 9: //jefe ipress
                        $responsable[1]=$resp->name_rectificacion;
                        $responsable[4]=$resp->grado_rectificacion;
                        break;
                }
            }
        }
        
        //dd($responsable_rubro);
        $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
        $cierre=$cierre_rubro->get(0)->updated_rectificacion;

        if($tipo==1){
            $nombre_rubro='MEDICAMENTOS';
        }
        else
        {
            $nombre_rubro='DISPOSITIVOS';
        }
        
        $nombre_archivo=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_CAN2020';

        if($establecimiento_id==79){
            $texto='CONSOLIDADO UNIDAD';
            $pdf = \PDF::loadView('site.pdf.descargar_rubro_unidad_rectificacion_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable'=>$responsable]);
        }
        else
        {
            $texto='CONSOLIDADO IPRESS';
            $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_rectificacion_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable'=>$responsable]);    
        }
        
        //$pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream($nombre_archivo.'.pdf');

        
        
     } 
    
}
