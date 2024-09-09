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
use App\Models\Responsable;
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
        $cans_ultimo= Can::latest('id')->first();
        $can_id=$cans_ultimo->id;
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
                    //->with('can_id', $cans->get(0)->id)
                    ->with('can_id',$can_id)
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
                    'updated_at'=>Carbon::now()
         ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto Eliminado'
        ]);
    }

    public function actualizar_can_2027($can_id,$establecimiento_id)
    {
        
        DB::table('can_establecimiento')
        ->where('can_id', $can_id)
        ->where('establecimiento_id', $establecimiento_id)
        ->update([
                    'estado'=> 1,
                    'updated_at'=>Carbon::now()
         ]);

        Flash::success('Actualizado satisfactoriamente.');
        return redirect(route('farmacia.listar_distribucion',[$can_id]));
    }


public function grabar_multi($id,Request $request)
    {
       
       $model_estimacion = new Estimacion();
       
       if($request->nivel_farma == 1):
            $grabarndo  = $model_estimacion->ActualizaProducto($id, $request->necesidad_anual, $request->necesidad_anual_1, $request->necesidad_anual_2, $request->cpma,$request->cpma_1, $request->cpma_2, $request->mes1,$request->mes1_1, $request->mes1_2, $request->mes2,$request->mes2_1, $request->mes2_2,$request->mes3,$request->mes3_1, $request->mes3_2,$request->mes4,$request->mes4_1, $request->mes4_2,$request->mes5,$request->mes5_1, $request->mes5_2,$request->mes6,$request->mes6_1, $request->mes6_2,$request->mes7,$request->mes7_1, $request->mes7_2,$request->mes8,$request->mes8_1, $request->mes8_2,$request->mes9,$request->mes9_1, $request->mes9_2,$request->mes10,$request->mes10_1, $request->mes10_2,$request->mes11,$request->mes11_1, $request->mes11_2,$request->mes12,$request->mes12_1, $request->mes12_2);
        else:
            $actualiza  =$model_estimacion->ActualizaCPMA($id, $request->cpma,$request->cpma_1, $request->cpma_2);
        endif;
            
        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }

public function grabar($id,Request $request)
    {
       dd($request);

       $model_estimacion = new Estimacion();
       
       if($request->nivel_farma == 1):
            $grabarndo  = $model_estimacion->ActualizaNewProducto($id, $request->necesidad_anual,$request->cpma, $request->mes1,$request->mes2,$request->mes3,$request->mes4,$request->mes5,$request->mes6,$request->mes7,$request->mes8,$request->mes9,$request->mes10,$request->mes11,$request->mes12);
        else:
            $actualiza  =$model_estimacion->ActualizaCPMA($id, $request->cpma);
            //$actualiza  =$model_estimacion->ActualizaCPMA($id, $request->cpma,$request->cpma_1, $request->cpma_2);
        endif;
            
        return response()->json([
            'success' => true,
            'message' => 'Actualizado'
        ]);
    }


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
        
        $model_responsables = new Responsable();
        $model_estimacion = new Estimacion();

        if($nivel==1):
            $jefe_ipress = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 6, 5); 
            
            if (count($jefe_ipress)>0):

                $jefe_farmacia = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 6, 4);

                if (count($jefe_farmacia)>0): 

                    $responsables = $model_responsables->GetBuscaResponsable($can_id, $establecimiento_id);

                    if (count($responsables)>0):

                        if($establecimiento_id!=79 && $tipo==1):
                            $total_medicamentos = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,1);
                            $responsable_medicamentos = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 1, 3);

                            if (count($total_medicamentos)>0):
                                if (count($responsable_medicamentos)>0):                        
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
                                else:
                                    Flash::error('Falto Ingresar al Personal Responsables de Medicamentos');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            else:
                                Flash::error('Debe Ingresar Productos Farmaceuticos');
                                return redirect(route('users.index_responsable',[$can_id]));
                                
                            endif;
                        else:
                            
                            $total_biomedico_1 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,2);
                            $total_biomedico_2 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,3);
                            $total_biomedico_3 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,7);
                            
                            $total_biomedico = count($total_biomedico_1) + count($total_biomedico_2) + count($total_biomedico_3);
                            if ($total_biomedico>0):
                                
                                $responsable_biomedicos = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 3, 3); //biomedico, rol
                                if (count($responsable_biomedicos)==0):
                                    Flash::error('Tiene Productos de Biomedicos, Falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;

                            $total_dental = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,4);
                            
                            if (count($total_dental)>0):
                                $responsable_dental = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 4, 3);
                                if (count($responsable_dental)==0):
                                    Flash::error('Tiene Productos Odontológico , Falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;

                            $total_fotografico = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,6);
                            if (count($total_fotografico)>0):
                                $responsable_fotografico = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 5, 3);
                                if (count($responsable_fotografico)==0):
                                    Flash::error('Tiene Productos de Fotográficos y Fonotécnicos, falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;

                            $total_laboratorio_1 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,5);
                            $total_laboratorio_2 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,10);
                            $total_laboratorio = count($total_laboratorio_1) + count($total_laboratorio_2);
                            if ($total_laboratorio>0): 
                                $responsable_laboratorio = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 2, 3);
                                if (count($responsable_laboratorio)==0):
                                    Flash::error('Tiene Productos de Laboratorio, falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;

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
                        endif;
                    else:
                        Flash::error('Falta Ingresar Responsables');
                        return redirect(route('users.index_responsable',[$can_id]));    
                    endif;
                else:
                    Flash::error('Falta Ingresar al jefe y/o Responsablde de Farmacia');
                    return redirect(route('users.index_responsable',[$can_id]));    
                endif;
            else:
                Flash::error('Falta Ingresar al jefe de la IPRESS O JEFE');
                    return redirect(route('users.index_responsable',[$can_id]));
            endif;
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
            return redirect(route('farmacia.ver_consolidado_farmacia',[$tipo,$can_id]));
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
        $tiempo = $can->tiempo;

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
            ->with('tiempo', $tiempo)
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
    public function exportEstimacionDataConsolidada($can_id,$establecimiento_id,$opt,$type, $valor)
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
            $data = $model_estimacion->ConsultaEstimacionFarmaceuticos($can_id, $establecimiento_id, $opt, $valor);
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data = $model_estimacion->ConsultaEstimacionDispositivos($can_id, $establecimiento_id, $opt, $valor);
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

                            $sheet->cell('R'.$i, function($cell) {$cell->setValue('CPMA'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('S'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('T'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('U'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('V'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('W'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('X'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('Y'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('Z'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AA'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AB'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AC'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AD'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AE'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->cell('AF'.$i, function($cell) {$cell->setValue('CPMA'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AG'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AH'.$j, function($cell) {$cell->setValue('ENERO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AI'.$j, function($cell) {$cell->setValue('FEBRERO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AJ'.$j, function($cell) {$cell->setValue('MARZO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AK'.$j, function($cell) {$cell->setValue('ABRIL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AL'.$j, function($cell) {$cell->setValue('MAYO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AM'.$j, function($cell) {$cell->setValue('JUNIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AN'.$j, function($cell) {$cell->setValue('JULIO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AO'.$j, function($cell) {$cell->setValue('AGOSTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AP'.$j, function($cell) {$cell->setValue('SETIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                            
                            $sheet->cell('AQ'.$j, function($cell) {$cell->setValue('OCTUBRE'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AR'.$j, function($cell) {$cell->setValue('NOVIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('AS'.$j, function($cell) {$cell->setValue('DICIEMBRE');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            

                            $sheet->mergeCells('F'.$i.':Q'.$i);
                            $sheet->cell('F'.$i, function($cell) {$cell->setValue('PRORRATEO AÑO 1'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->mergeCells('T'.$i.':AE'.$i);
                            $sheet->cell('T'.$i, function($cell) {$cell->setValue('PRORRATEO AÑO 2'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                            $sheet->mergeCells('AH'.$i.':AS'.$i);
                            $sheet->cell('AH'.$i, function($cell) {$cell->setValue('PRORRATEO AÑO 3'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->mergeCells('A'.$d.':AS'.$d);
                            $sheet->cell('O'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
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
                                'columns' => array('E','D','R','S','AF','AG'),
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
                        $sheet->cell('AQ'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AR'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        $sheet->cell('AS'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                        
                        
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
                        if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}
                        if($value->necesidad_anual>0){ $sheet->cell('E'.$k, $value->necesidad_anual);}else{$sheet->cell('E'.$k, number_format($value->necesidad_anual, 2, '.', ','));}
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

                        if($value->cpma_1>0){ $sheet->cell('R'.$k, $value->cpma_1);}else{$sheet->cell('R'.$k,number_format($value->cpma_1, 2, '.', ','));}
                        if($value->necesidad_anual_1>0){ $sheet->cell('S'.$k, $value->necesidad_anual_1);}else{$sheet->cell('E'.$k, number_format($value->necesidad_anual_1, 2, '.', ','));}
                        if($value->mes1_1>0){ $sheet->cell('T'.$k, $value->mes1_1);}else{$sheet->cell('T'.$k, number_format($value->mes1_1, 2, '.', ','));}
                        if($value->mes2_1>0){ $sheet->cell('U'.$k, $value->mes2_1);}else{$sheet->cell('U'.$k, number_format($value->mes2_1, 2, '.', ','));}
                        if($value->mes3_1>0){ $sheet->cell('V'.$k, $value->mes3_1);}else{$sheet->cell('V'.$k, number_format($value->mes3_1, 2, '.', ','));}
                        if($value->mes4_1>0){ $sheet->cell('W'.$k, $value->mes4_1);}else{$sheet->cell('W'.$k, number_format($value->mes4_1, 2, '.', ','));}
                        if($value->mes5_1>0){ $sheet->cell('X'.$k, $value->mes5_1);}else{$sheet->cell('X'.$k, number_format($value->mes5_1, 2, '.', ','));}
                        if($value->mes6_1>0){ $sheet->cell('Y'.$k, $value->mes6_1);}else{$sheet->cell('Y'.$k, number_format($value->mes6_1, 2, '.', ','));}
                        if($value->mes7_1>0){ $sheet->cell('Z'.$k, $value->mes7_1);}else{$sheet->cell('Z'.$k, number_format($value->mes7_1, 2, '.', ','));}
                        if($value->mes8_1>0){ $sheet->cell('AA'.$k, $value->mes8_1);}else{$sheet->cell('AA'.$k, number_format($value->mes8_1, 2, '.', ','));}
                        if($value->mes9_1>0){ $sheet->cell('AB'.$k, $value->mes9_1);}else{$sheet->cell('AB'.$k, number_format($value->mes9_1, 2, '.', ','));}
                        if($value->mes10_1>0){ $sheet->cell('AC'.$k, $value->mes10_1);}else{$sheet->cell('AC'.$k, number_format($value->mes10_1, 2, '.', ','));}
                        if($value->mes11_1>0){ $sheet->cell('AD'.$k, $value->mes11_1);}else{$sheet->cell('AD'.$k, number_format($value->mes11_1, 2, '.', ','));}
                        if($value->mes12_1>0){ $sheet->cell('AE'.$k, $value->mes12_1);}else{$sheet->cell('AE'.$k, number_format($value->mes12_1, 2, '.', ','));}

                        if($value->cpma_2>0){ $sheet->cell('AF'.$k, $value->cpma_2);}else{$sheet->cell('AF'.$k,number_format($value->cpma_2, 2, '.', ','));}
                        if($value->necesidad_anual_2>0){ $sheet->cell('AG'.$k, $value->necesidad_anual_2);}else{$sheet->cell('E'.$k, number_format($value->necesidad_anual_2, 2, '.', ','));}
                        if($value->mes1_2>0){ $sheet->cell('AH'.$k, $value->mes1_2);}else{$sheet->cell('AH'.$k, number_format($value->mes1_2, 2, '.', ','));}
                        if($value->mes2_2>0){ $sheet->cell('AI'.$k, $value->mes2_2);}else{$sheet->cell('AI'.$k, number_format($value->mes2_2, 2, '.', ','));}
                        if($value->mes3_2>0){ $sheet->cell('AJ'.$k, $value->mes3_2);}else{$sheet->cell('AJ'.$k, number_format($value->mes3_2, 2, '.', ','));}
                        if($value->mes4_2>0){ $sheet->cell('AK'.$k, $value->mes4_2);}else{$sheet->cell('AK'.$k, number_format($value->mes4_2, 2, '.', ','));}
                        if($value->mes5_2>0){ $sheet->cell('AL'.$k, $value->mes5_2);}else{$sheet->cell('AL'.$k, number_format($value->mes5_2, 2, '.', ','));}
                        if($value->mes6_2>0){ $sheet->cell('AM'.$k, $value->mes6_2);}else{$sheet->cell('AM'.$k, number_format($value->mes6_2, 2, '.', ','));}
                        if($value->mes7_2>0){ $sheet->cell('AN'.$k, $value->mes7_2);}else{$sheet->cell('AN'.$k, number_format($value->mes7_2, 2, '.', ','));}
                        if($value->mes8_2>0){ $sheet->cell('AO'.$k, $value->mes8_2);}else{$sheet->cell('AO'.$k, number_format($value->mes8_2, 2, '.', ','));}
                        if($value->mes9_2>0){ $sheet->cell('AP'.$k, $value->mes9_2);}else{$sheet->cell('AP'.$k, number_format($value->mes9_2, 2, '.', ','));}
                        if($value->mes10_2>0){ $sheet->cell('AQ'.$k, $value->mes10_2);}else{$sheet->cell('AQ'.$k, number_format($value->mes10_2, 2, '.', ','));}
                        if($value->mes11_2>0){ $sheet->cell('AR'.$k, $value->mes11_2);}else{$sheet->cell('AR'.$k, number_format($value->mes11_2, 2, '.', ','));}
                        if($value->mes12_2>0){ $sheet->cell('AS'.$k, $value->mes12_2);}else{$sheet->cell('AS'.$k, number_format($value->mes12_2, 2, '.', ','));}

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
                        $total_necesidad_anual = $value->necesidad_anual + $total_necesidad_anual; $mes1_total= $value->mes1 + $mes1_total; $mes2_total= $value->mes2 + $mes2_total; $mes3_total= $value->mes3 + $mes3_total; $mes4_total= $value->mes4 + $mes4_total; $mes5_total= $value->mes5 + $mes5_total; $mes6_total= $value->mes6 + $mes6_total; $mes7_total= $value->mes7 + $mes7_total; $mes8_total= $value->mes8 + $mes8_total; $mes9_total= $value->mes9 + $mes9_total; $mes10_total= $value->mes10 + $mes10_total; $mes11_total= $value->mes11 + $mes11_total; $mes12_total= $value->mes12 + $mes12_total; $cpma_total= $value->cpma + $cpma_total;

                        $total_necesidad_anual_1 = $value->necesidad_anual_1 + $total_necesidad_anual_1; $mes1_total_1= $value->mes1_1 + $mes1_total_1; $mes2_total_1= $value->mes2_1 + $mes2_total_1; $mes3_total_1= $value->mes3_1 + $mes3_total_1; $mes4_total_1= $value->mes4_1 + $mes4_total_1; $mes5_total_1= $value->mes5_1 + $mes5_total_1; $mes6_total_1= $value->mes6_1 + $mes6_total_1; $mes7_total_1= $value->mes7_1 + $mes7_total_1; $mes8_total_1= $value->mes8_1 + $mes8_total_1; $mes9_total_1= $value->mes9_1 + $mes9_total_1; $mes10_total_1= $value->mes10_1 + $mes10_total_1; $mes11_total_1= $value->mes11_1 + $mes11_total_1; $mes12_total_1= $value->mes12_1 + $mes12_total_1; $cpma_total_1= $value->cpma_1 + $cpma_total_1;

                        $total_necesidad_anual_2 = $value->necesidad_anual_2 + $total_necesidad_anual_2; $mes1_total_2= $value->mes1_2 + $mes1_total_2; $mes2_total_2= $value->mes2_2 + $mes2_total_2; $mes3_total_2= $value->mes3_2 + $mes3_total_2; $mes4_total_2= $value->mes4_2 + $mes4_total_2; $mes5_total_2= $value->mes5_2 + $mes5_total_2; $mes6_total_2= $value->mes6_2 + $mes6_total_2; $mes7_total_2= $value->mes7_2 + $mes7_total_2; $mes8_total_2= $value->mes8_2 + $mes8_total_2; $mes9_total_2= $value->mes9_2 + $mes9_total_2; $mes10_total_2= $value->mes10_2 + $mes10_total_2; $mes11_total_2= $value->mes11_2 + $mes11_total_2; $mes12_total_2= $value->mes12_2 + $mes12_total_2; $cpma_total_2= $value->cpma_2 + $cpma_total_2;
                        
                    if($cpma_total>0){ $sheet->cell('D'.$n, $cpma_total);}else{$sheet->cell('D'.$n, number_format($cpma_total, 2, '.', ','));}
                    $sheet->cell('D'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($total_necesidad_anual>0){ $sheet->cell('E'.$n, $total_necesidad_anual);}else{$sheet->cell('E'.$n, number_format($total_necesidad_anual, 2, '.', ','));}
                    $sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
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

                    if($cpma_total_1>0){ $sheet->cell('R'.$n, $cpma_total_1);}else{$sheet->cell('R'.$n, number_format($cpma_total_1, 2, '.', ','));}
                    $sheet->cell('R'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($total_necesidad_anual_1>0){ $sheet->cell('S'.$n, $total_necesidad_anual_1);}else{$sheet->cell('S'.$n, number_format($total_necesidad_anual_1, 2, '.', ','));}
                    $sheet->cell('S'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total_1>0){ $sheet->cell('T'.$n, $mes1_total_1);}else{$sheet->cell('T'.$n, number_format($mes1_total_1, 2, '.', ','));}
                    $sheet->cell('T'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total_1>0){ $sheet->cell('U'.$n, $mes2_total_1);}else{$sheet->cell('U'.$n, number_format($mes2_total_1, 2, '.', ','));}
                    $sheet->cell('U'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total_1>0){ $sheet->cell('V'.$n, $mes3_total_1);}else{$sheet->cell('V'.$n, number_format($mes3_total_1, 2, '.', ','));}
                    $sheet->cell('V'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total_1>0){ $sheet->cell('W'.$n, $mes4_total_1);}else{$sheet->cell('W'.$n, number_format($mes4_total_1, 2, '.', ','));}
                    $sheet->cell('W'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total_1>0){ $sheet->cell('X'.$n, $mes5_total_1);}else{$sheet->cell('X'.$n, number_format($mes5_total_1, 2, '.', ','));}
                    $sheet->cell('X'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total_1>0){ $sheet->cell('Y'.$n, $mes6_total_1);}else{$sheet->cell('Y'.$n, number_format($mes6_total_1, 2, '.', ','));}
                    $sheet->cell('Y'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total_1>0){ $sheet->cell('Z'.$n, $mes7_total_1);}else{$sheet->cell('Z'.$n, number_format($mes7_total_1, 2, '.', ','));}
                    $sheet->cell('Z'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total_1>0){ $sheet->cell('AA'.$n, $mes8_total_1);}else{$sheet->cell('AA'.$n, number_format($mes8_total_1, 2, '.', ','));}
                    $sheet->cell('AA'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total_1>0){ $sheet->cell('AB'.$n, $mes9_total_1);}else{$sheet->cell('AB'.$n, number_format($mes9_total_1, 2, '.', ','));}
                    $sheet->cell('AB'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total_1>0){ $sheet->cell('AC'.$n, $mes10_total_1);}else{$sheet->cell('AC'.$n, number_format($mes10_total_1, 2, '.', ','));}
                    $sheet->cell('AC'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total_1>0){ $sheet->cell('AD'.$n, $mes11_total_1);}else{$sheet->cell('AD'.$n, number_format($mes11_total_1, 2, '.', ','));}
                    $sheet->cell('AD'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total_1>0){ $sheet->cell('AE'.$n, $mes12_total_1);}else{$sheet->cell('AE'.$n, number_format($mes12_total_1, 2, '.', ','));}
                    $sheet->cell('AE'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });

                    if($cpma_total_2>0){ $sheet->cell('AF'.$n, $cpma_total_2);}else{$sheet->cell('AF'.$n, number_format($cpma_total_2, 2, '.', ','));}
                    $sheet->cell('AF'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($total_necesidad_anual_2>0){ $sheet->cell('AG'.$n, $total_necesidad_anual_2);}else{$sheet->cell('AG'.$n, number_format($total_necesidad_anual_2, 2, '.', ','));}
                    $sheet->cell('AG'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes1_total_2>0){ $sheet->cell('AH'.$n, $mes1_total_2);}else{$sheet->cell('AH'.$n, number_format($mes1_total_2, 2, '.', ','));}
                    $sheet->cell('AH'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes2_total_2>0){ $sheet->cell('AI'.$n, $mes2_total_2);}else{$sheet->cell('AI'.$n, number_format($mes2_total_2, 2, '.', ','));}
                    $sheet->cell('AI'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes3_total_2>0){ $sheet->cell('AJ'.$n, $mes3_total_2);}else{$sheet->cell('AJ'.$n, number_format($mes3_total_2, 2, '.', ','));}
                    $sheet->cell('AJ'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes4_total_2>0){ $sheet->cell('AK'.$n, $mes4_total_2);}else{$sheet->cell('AK'.$n, number_format($mes4_total_2, 2, '.', ','));}
                    $sheet->cell('AK'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes5_total_2>0){ $sheet->cell('AL'.$n, $mes5_total_2);}else{$sheet->cell('AL'.$n, number_format($mes5_total_2, 2, '.', ','));}
                    $sheet->cell('AL'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes6_total_2>0){ $sheet->cell('AM'.$n, $mes6_total_2);}else{$sheet->cell('AM'.$n, number_format($mes6_total_2, 2, '.', ','));}
                    $sheet->cell('AM'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes7_total_2>0){ $sheet->cell('AN'.$n, $mes7_total_2);}else{$sheet->cell('AN'.$n, number_format($mes7_total_2, 2, '.', ','));}
                    $sheet->cell('AN'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes8_total_2>0){ $sheet->cell('AO'.$n, $mes8_total_2);}else{$sheet->cell('AO'.$n, number_format($mes8_total_2, 2, '.', ','));}
                    $sheet->cell('AO'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes9_total_2>0){ $sheet->cell('AP'.$n, $mes9_total_2);}else{$sheet->cell('AP'.$n, number_format($mes9_total_2, 2, '.', ','));}
                    $sheet->cell('AP'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes10_total_2>0){ $sheet->cell('AQ'.$n, $mes10_total_2);}else{$sheet->cell('AQ'.$n, number_format($mes10_total_2, 2, '.', ','));}
                    $sheet->cell('AQ'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes11_total_2>0){ $sheet->cell('AR'.$n, $mes11_total_2);}else{$sheet->cell('AR'.$n, number_format($mes11_total_2, 2, '.', ','));}
                    $sheet->cell('AR'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    if($mes12_total_2>0){ $sheet->cell('AS'.$n, $mes12_total_2);}else{$sheet->cell('AS'.$n, number_format($mes12_total_2, 2, '.', ','));}
                    $sheet->cell('AS'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
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
        
        $tiempo = $can->tiempo;
        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia.index'));
        }

        if($tipo==1){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)   
                    //->where('estado_necesidad',0)        
                    ->where('estado','<>',2)        
                    ->where('petitorio','=',1)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();
            
            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    //->where('estado','<>',2)   
                    ->where('estado_necesidad',0)                              
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
                    ->where('petitorio','=',1)        
                    //->where('estado_necesidad',0)           
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();  

                    $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2) 
                    ->where('petitorio','=',1)        
                    //->where('estado_necesidad',0)            
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
            $actualizado = $stock_cerrado->get(0)->actualizacion;
        return view('site.responsable_farmacia.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('servicio_id', $servicio_id_cero)
            ->with('estimacions', $data)
            ->with('cerrado', $cerrado)
            ->with('nivel', $nivel)
            ->with('tiempo', $tiempo)
            ->with('cerrado_stock', $cerrado_stock)            
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('actualizado', $actualizado)
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

        $tiempo = $can->tiempo;
        $modeluser = new User;

        $nombre_servicio=Auth::user()->nombre_servicio;
        $estimacions = Estimacion::find($can_id);

        $establecimiento_cerrado=DB::table('can_establecimiento') ->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();  

        $medicina_interna = $establecimiento_cerrado->get(0)->medicina_interna; 
        $odontologia = $establecimiento_cerrado->get(0)->odontologia; 
        $obstetricia = $establecimiento_cerrado->get(0)->obstetricia; 

        if($nivel==1):
            if($medicina_interna==0 and $odontologia ==0 and $obstetricia==0):
                    $redirigir=2;
                    return view('site.responsable_farmacia.atenciones.edit')->with('can_id', $can_id)->with('tipo', $tipo)->with('establecimiento_id', $establecimiento_id)->with('establecimiento', $establecimiento)->with('medicina_interna', $medicina_interna)->with('odontologia', $odontologia)->with('obstetricia', $obstetricia)->with('redirigir', $redirigir);
            endif;
        endif;



        $model_estimacion = new Estimacion();
        $model_petitorio = new Petitorio();
        $model_can_servicio = new Can();

        if($tipo==1){  //medicamentos
            $descripcion_tipo='Medicamentos';
            //buscamos si existe medicamentos ya ingresados
            $numero_medicamentos=$model_estimacion->ContarProductos($can_id,$establecimiento_id,1);
            if($numero_medicamentos->total>0):
                $data=$model_estimacion->ProductosIngresados($can_id,$establecimiento_id,1);
            else:
                $data='';
            endif;
            
            if(Auth::user()->rol==7){
                
                if($nivel==1){
                    $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel(1,1); //tipo,nivel
                    if($total_medicamentos_rubro->total>0):
                        $petitorios=$model_petitorio->PetitoriosPorNivel1(1,1);
                    else:
                        $petitorios='';
                    endif;
                }
                else
                {   
                    $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel(1,$nivel); //tipo,nivel
                    if($total_medicamentos_rubro->total>0):
                        $petitorios=$model_petitorio->PetitoriosPorNivel2y3(1,$nivel);
                    else:
                        $petitorios='';
                    endif;
                        
                }

                $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado;
                $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->medicamento_cerrado_stock;
            }
            else
            {
                if(Auth::user()->rol==1){ return redirect(route('cans.index')); }

            }

            $diferencia=$total_medicamentos_rubro->total-$numero_medicamentos->total;

        }
        else
        {
            //buscamos si existe dispositivos ya ingresados
            $descripcion_tipo='Dispositivos Medicos';
            
            $numero_medicamentos=$model_estimacion->ContarProductos($can_id,$establecimiento_id,2);
            if($numero_medicamentos->total>0):
                $data=$model_estimacion->ProductosIngresados2y3($can_id,$establecimiento_id,1);
            else:
                $data='';
            endif;

            if(Auth::user()->rol==7){
                if($establecimiento_id==4) //Angamos
                {
                    $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel1D();

                    if($total_medicamentos_rubro->total>0):
                        $petitorios=$model_petitorio->PetitoriosPorNivel1D();
                    else:
                        $petitorios='';
                    endif;
                    

                    $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado; 
                    $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->dispositivo_cerrado_stock; 

                }
                else
                {
                    if($nivel==1){

                        $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel1DA();

                        if($total_medicamentos_rubro->total>0):
                            $petitorios=$model_petitorio->PetitoriosPorNivel1DA();
                        else:
                            $petitorios='';
                        endif;

                    }else
                    {
                        
                        $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosN($nivel);

                        if($total_medicamentos_rubro->total>0):
                            $petitorios=$model_petitorio->PetitoriosPorNivel($nivel);
                        else:
                            $petitorios='';
                        endif;
                        
                    }
                    $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado; 
                    $medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->dispositivo_cerrado_stock; 
                }
            }
            $diferencia=$total_medicamentos_rubro->total-$numero_medicamentos->total;
        } 

        $valor=1;
        //dd($numero_medicamentos->total);
        //Si hay medicamentos asignados
        if ($numero_medicamentos->total==0){ //recien ingresa
            if($can_id==9){
                //cargar_can_anterior
                $model_can_nivel_1 = new Can();
                $producto_asignado = $model_can_nivel_1->CopiarProductosCanAnteriorNivel1($can_id, $tipo, $establecimiento_id);
                return redirect(route('farmacia.cargar_medicamentos',[$can_id,$tipo])); 
            }
            else{
                if($nivel==1):
                    $redireccion = 0; 
                    return view('site.responsable_farmacia.medicamentos.asignar_medicamentos')->with('nombre_servicio', $nombre_servicio)->with('estimacions', $estimacions)->with('can_id', $can_id)->with('tipo', $tipo)->with('establecimiento_id', $establecimiento_id)->with('establecimiento', $establecimiento)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('petitorios', $petitorios)->with('redireccion', $redireccion);
                else:
                    Flash::error('Aun no han registrados los servicios');
                    return redirect(route('farmacia.index'));    
                endif;    
            }
            
        }
        else
        {
            
            if($nivel!=1){
                $total_servicio=$model_can_servicio->ContarCanServicio($can_id,$establecimiento_id);
                $total_medicamentos_cerrado=$model_can_servicio->ContarMedicamentoCerrado($can_id,$establecimiento_id);
                $total_dispositivos_cerrado=$model_can_servicio->ContarDispositivoCerrado($can_id,$establecimiento_id);
                
                $total_servicio=$total_servicio->total*2;
                $total_cerrado=$total_medicamentos_cerrado->total+$total_dispositivos_cerrado->total;
                if($total_servicio==$total_cerrado){
                    $valor=1;
                }
                else
                {
                    $valor=1;   
                }
            }

            if($can->multianual==0){
                return view('site.responsable_farmacia.medicamentos.medicamentos_2')->with('estimacions', $data)->with('nivel', $nivel)->with('valor', $valor)->with('servicio_id', $servicio_id)->with('numero_medicamentos', $diferencia)->with('descripcion_tipo', $descripcion_tipo)->with('establecimiento_id', $establecimiento_id)->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)->with('medicamento_cerrado_stock', $medicamento_cerrado_stock)->with('tipo', $tipo)->with('tiempo', $tiempo)->with('can_id', $can_id);
            
            }
            else{
                return view('site.responsable_farmacia.medicamentos.medicamentos')->with('estimacions', $data)->with('nivel', $nivel)->with('valor', $valor)->with('servicio_id', $servicio_id)->with('numero_medicamentos', $diferencia)->with('descripcion_tipo', $descripcion_tipo)->with('establecimiento_id', $establecimiento_id)->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)->with('medicamento_cerrado_stock', $medicamento_cerrado_stock)->with('tipo', $tipo)->with('tiempo', $tiempo)->with('can_id', $can_id);
            }
            
            
        }
        
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


            $producto_asignado = $model_can->RegistrarProductosElegidos($can_id, $nivel, $tipo, $establecimiento_id, $establecimiento->codigo_establecimiento,$establecimiento->nombre_establecimiento,$productos_elegidos);
            
                   
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

    //public function cargar_medicamentos($can_id, $establecimiento_id, $tipo,$cerrado)
    public function nuevo_medicamento_dispositivo( $can_id, $establecimiento_id, $tipo )
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
            $numero_medicamentos=$model_estimacion->ContarProductos($can_id,$establecimiento_id,1);
            if($numero_medicamentos->total>0):
                $data=$model_estimacion->ProductosIngresados($can_id,$establecimiento_id,1);
            else:
                $data='';
            endif;
            
            if(Auth::user()->rol==7){

                if($nivel==1){
                    $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel(1,1); //tipo,nivel
                    if($total_medicamentos_rubro->total>0):
                        $petitorios=$model_petitorio->GetPetitoriosPorNivel1(1,1);
                    else:
                        $petitorios='';
                    endif;
                }
                else
                {   
                    $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel(1,$nivel); //tipo,nivel
                    if($total_medicamentos_rubro->total>0):
                        $petitorios=$model_petitorio->PetitoriosPorNivel2y3(1,$nivel);
                    else:
                        $petitorios='';
                    endif;
                        
                }
            }
            else
            {
                return redirect(route('farmacia.index')); 

            }
        }
        else
        {
            //buscamos si existe dispositivos ya ingresados
            $descripcion_tipo='Dispositivos Medicos';
            
            $numero_medicamentos=$model_estimacion->ContarProductos($can_id,$establecimiento_id,2);
            if($numero_medicamentos->total>0):
                $data=$model_estimacion->ProductosIngresados2y3($can_id,$establecimiento_id,1);
            else:
                $data='';
            endif;

            if(Auth::user()->rol==7){
                if($establecimiento_id==4) //Angamos
                {
                    $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel1D();
                    if($total_medicamentos_rubro->total>0):
                        $petitorios=$model_petitorio->GetPetitoriosPorNivel1D();
                    else:
                        $petitorios='';
                    endif;
                    

                    //$medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado; 
                    //$medicamento_cerrado_stock=$establecimiento_cerrado->get(0)->dispositivo_cerrado_stock; 

                }
                else
                {
                    if($nivel==1){

                        $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosNivel1DA();

                        if($total_medicamentos_rubro->total>0):
                            $petitorios=$model_petitorio->GetPetitoriosPorNivel1DA();
                        else:
                            $petitorios='';
                        endif;
                    }else
                    {
                        $total_medicamentos_rubro=$model_petitorio->ContarPetitoriosN($nivel);

                        if($total_medicamentos_rubro->total>0):
                            $petitorios=$model_petitorio->PetitoriosPorNivel($nivel);
                        else:
                            $petitorios='';
                        endif;
                    }
                }
            }
            else{
                return redirect(route('farmacia.index')); 
            }

        } 

        if($petitorios!=''):
            $petitorios2 = $petitorios->pluck('descripcion','id')->toArray();
            $data2 = $data->pluck('descripcion','petitorio_id')->toArray();
            $descripcion=array_diff($petitorios2,$data2);
            $redireccion=1;

            return view('site.responsable_farmacia.medicamentos.asignar_medicamentos')->with('nombre_servicio', $nombre_servicio)->with('estimacions', $estimacions)->with('can_id', $can_id)->with('tipo', $tipo)->with('establecimiento_id', $establecimiento_id)->with('establecimiento', $establecimiento)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('petitorios', $descripcion)->with('redireccion', $redireccion);

            
        else:
            return redirect(route('farmacia.index'));
        endif;

        $valor=1;
        
       
        
        
    }

    public function nuevo_medicamento_dispositivo2( $can_id, $establecimiento_id, $tipo_producto )
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

            $stock = 0;
            
            $cpma = 0;
            $necesidad_anual = 0;
            $mes1 = 0;
            $mes2 = 0;
            $mes3 = 0;
            $mes4 = 0;
            $mes5 = 0;
            $mes6 = 0;
            $mes7 = 0;
            $mes8 = 0;
            $mes9 = 0;
            $mes10 = 0;
            $mes11 = 0;
            $mes12 = 0;            
            
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
                        'necesidad_anual' => $necesidad_anual,
                        'estado' => 1,
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
                        'created_at'=>Carbon::now(),       
                        'uso_id'=> $uso_id           
            ]);

        
            Flash::success('Se ha guardado con exito');
            
            return redirect(route('farmacia.cargar_medicamentos',[$can_id,$destino]));

            
            
        }   
        
    }

    public function grabar_nuevo_medicamento_dispositivo2(Request $request,$establecimiento_id,$can_id, $destino)
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

            $stock = 0;
            //$stock = $request->input("stock");

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
                if($contact->petitorio==1):
                    return "<a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>
                   <a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='deleteForm(". $contact->id .")' class='btn btn-danger btn-xs'><i class='glyphicon glyphicon-trash'></i></a>";
                else:
                    return "<font color='red'>NO PETITORIO</font>";
                endif;

            })
            ->rawColumns(['justificacion', 'action'])->make(true);        
        else:
            $total_servicio=DB::table('can_servicio')
                                ->where('can_id',$can_id) 
                                ->where('establecimiento_id',$establecimiento_id)
                                ->count();                    
                                
            $total_medicamentos_cerrado = DB::table('can_servicio')  
                                    ->where('can_id',$can_id) 
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where( function ( $query )
                                    {
                                        $query->orWhere('medicamento_cerrado',3)
                                            ->orWhere('medicamento_cerrado',2);
                                    })
                                  ->count();    

            $total_dispositivos_cerrado = DB::table('can_servicio')  
                                        ->where('can_id',$can_id) 
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->where( function ( $query )
                                        {
                                            $query->orWhere('dispositivo_cerrado',3)
                                                ->orWhere('dispositivo_cerrado',2);
                                        })
                                        ->count();    

            $total_servicio=$total_servicio*2;
            //$total_cerrado=$total_medicamentos_cerrado+$total_dispositivos_cerrado;
            $total_cerrado=$total_servicio;
            /*
                if($tipo==1){
            $editar_medicamentos=DB::table('can_servicio')->where('establecimiento_id',$establecimiento_id)->where('medicamento_cerrado',3)->count();    
        }
        else
        {
            $editar_medicamentos=DB::table('can_servicio')->where('establecimiento_id',$establecimiento_id)->where('dispositivo_cerrado',3)->count();    
        }

        if($editar_medicamentos>0){
            return Datatables::of($contact)
            ->addColumn('action', function($contact){
              return "<a data-toggle='tooltip' data-original-title='Ver Producto!' onclick='ver_datos(\"".$contact->petitorio_id."\" ,\"".$contact->descripcion."\" )' class='btn btn-info btn-xs'><i class='glyphicon glyphicon-eye-open'></i></a>";


            })
            ->rawColumns(['justificacion', 'action'])->make(true);    
        }
        else{
                return Datatables::of($contact)
                ->addColumn('action', function($contact){
                  return " <a data-toggle='tooltip' data-original-title='Ver Producto!' onclick='ver_datos(\"".$contact->petitorio_id."\" ,\"".$contact->descripcion."\" )' class='btn btn-info btn-xs'><i class='glyphicon glyphicon-eye-open'></i></a> ";
                })
            ->rawColumns(['justificacion', 'action'])->make(true);        
        }
            */
            if($total_servicio==$total_cerrado){
                return Datatables::of($contact)
                ->addColumn('action', function($contact){
                return "<a data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>
                <a data-toggle='tooltip' data-original-title='Ver Producto!' onclick='ver_datos_avance(\"".$contact->petitorio_id."\" ,\"".$contact->descripcion."\" )' class='btn btn-info btn-xs'><i class='glyphicon glyphicon-eye-open'></i></a>
                ";
                })
            ->rawColumns(['justificacion', 'action'])->make(true);        

            }
            else{
  /*              return Datatables::of($contact)
                ->addColumn('action', function($contact){
                return "<a disabled='disabled' data-toggle='tooltip' data-original-title='Editar Producto!' onclick='editForm(". $contact->id .")' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-edit'></i></a>
                ";
                })
                ->rawColumns(['justificacion', 'action'])->make(true); 
*/
                return Datatables::of($contact)
                ->addColumn('action', function($contact){
                  return "<a data-toggle='tooltip' data-original-title='Ver Producto!' onclick='ver_datos_avance(\"".$contact->petitorio_id."\" ,\"".$contact->descripcion."\" )' class='btn btn-info btn-xs'><i class='glyphicon glyphicon-eye-open'></i></a>
                  <a data-toggle='tooltip' data-original-title='Ver Producto!' onclick='ver_datos(\"".$contact->petitorio_id."\" ,\"".$contact->descripcion."\" )' class='btn btn-info btn-xs'><i class='glyphicon glyphicon-eye-open'></i></a>

                  ";


                })
                ->rawColumns(['justificacion', 'action'])->make(true);       

            }

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
    
    public function pdf_estimacion_nivel1($can_id,$establecimiento_id,$tipo,$servicio_id,$ano)
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
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();

            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                    
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                        
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
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
                    ->where('petitorio','=',1)
                    //->where('estado_necesidad',0)
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('estado','<>',2)
                        ->where('petitorio','=',1)
                        //->where('estado_necesidad',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('estado','<>',2)
                        ->where('petitorio','=',1)
                        //->where('estado_necesidad',0)
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

        $responsable[]="";$j=0;$r=0;
        $model_responsables = new Responsable();
        $model_estimacion = new Estimacion();
                    
                  
        if($nivel==1):
            $jefe_ipress = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 6, 5); 
            
            if (count($jefe_ipress)>0):
                
                $responsable[1]=$jefe_ipress->get(0)->nombre;
                $responsable[4]=$jefe_ipress->get(0)->grado;

                $jefe_farmacia = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 6, 4);

                if (count($jefe_farmacia)>0): 
                    $responsable[0]=$jefe_farmacia->get(0)->nombre;
                    $responsable[3]=$jefe_farmacia->get(0)->grado;
                    
                    $registro_farmacia = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 0, 7);
                    $name=$responsable[2]=Auth::user()->name;
                    $responsable[5]=Auth::user()->grado;
                    $cip=Auth::user()->cip;
                    $dni=Auth::user()->cip;
                    
                    $responsables = $model_responsables->GetBuscaResponsable($can_id, $establecimiento_id);

                    if (count($responsables)>0):
                        if($establecimiento_id!=79 && $tipo==1):
                            $nombre_rubro='MEDICAMENTOS';
                            $total_medicamentos = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,1);
                            $responsable_medicamentos = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 1, 3);
                            
                            if (count($total_medicamentos)>0):
                                if (count($responsable_medicamentos)>0):
                                    $responsable_rubro[0]=$responsable_medicamentos->get(0)->nombre;
                                    $responsable_rubro[6]=$responsable_medicamentos->get(0)->grado;
                                    $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
                                    $cierre=$cierre_rubro->get(0)->updated_at;                  
                                     $nombre_archivo=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_CAN_UGPFDMPS';
                                     
                                    $texto='CONSOLIDADO IPRESS';
                                    
                                    /*
                                    $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf_anterior',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]); 

                                    $pdf->setPaper('A4', 'landscape');
                                    $pdf->getDomPDF()->set_option("enable_php", true);
                                    return $pdf->stream($nombre_archivo.'.pdf');
                                    */
                                    if($ano==1):
                                        
                                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]);

                                        /*return view('site.pdf.descargar_rubro_servicio_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable);*/
                                    endif;
                                    if($ano==2):
                                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_2_pdf',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]);
                                        /*return view('site.pdf.descargar_rubro_servicio_2_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable);*/
                                    endif;
                                    if($ano==3):
                                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_3_pdf',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]);

                                        /*return view('site.pdf.descargar_rubro_servicio_3_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable);*/
                                    endif;
                                    $actualizado=$cierre_rubro->get(0)->actualizacion;
                                    if($actualizado==1):
                                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_4_pdf',['estimaciones'=>$data,
                                  'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                  'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]);

                                        /*return view('site.pdf.descargar_rubro_servicio_3_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable);*/
                                    endif;

                                    $pdf->setPaper('A4', 'landscape');
                                    $pdf->getDomPDF()->set_option("enable_php", true);
                                    return $pdf->stream($nombre_archivo.'.pdf');
                                    
                                    
                                else:
                                    Flash::error('Falto Ingresar al Personal Responsables de Medicamentos');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            else:
                                Flash::error('Debe Ingresar Productos Farmaceuticos');
                                return redirect(route('users.index_responsable',[$can_id]));
                                
                            endif;
                        else:
                            $nombre_rubro='DISPOSITIVOS';
                            $total_biomedico_1 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,2);
                            $total_biomedico_2 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,3);
                            $total_biomedico_3 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,7);

                            $total_biomedico = (count($total_biomedico_1) + count($total_biomedico_2) + count($total_biomedico_3));
                            if ($total_biomedico>0):
                                $responsable_biomedicos = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 3, 3);
                                if (count($responsable_biomedicos)==0):
                                    Flash::error('Tiene Productos de Biomedicos, Falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;

                            $total_dental = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,4);
                            if (count($total_dental)>0):
                                $responsable_dental = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 4, 3);
                                if (count($responsable_dental)==0):
                                    Flash::error('Tiene Productos Odontológico , Falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;

                            $total_fotografico = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,6);
                            if (count($total_fotografico)>0):
                                $responsable_fotografico = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 5, 3);
                                if (count($responsable_fotografico)==0):
                                    Flash::error('Tiene Productos de Fotográficos y Fonotécnicos, falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;

                            $total_laboratorio_1 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,5);
                            $total_laboratorio_2 = $model_estimacion->GetProductosTipoRegistrados($can_id, $establecimiento_id,10);
                            $total_laboratorio = (count($total_laboratorio_1) + count($total_laboratorio_2));
                            if ($total_laboratorio>0):
                                $responsable_laboratorio = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 2, 3);
                                if (count($responsable_laboratorio)==0):
                                    Flash::error('Tiene Productos de Laboratorio, falto Ingresar al Personal Responsable');
                                    return redirect(route('users.index_responsable',[$can_id]));
                                endif;
                            endif;
                            $pf=0; $d=0;
                            $responsables_rub = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<=',9)->where('can_id',$can_id)->where('estado',1)->orderby('rol','asc')->get();
                            foreach ($responsables_rub as $key => $resp_rubro) {
                                switch ($resp_rubro->servicio_id) {
                                    case 1: //productos (1)
                                        if($nivel==1):
                                            $responsable_rubro[0]=$resp_rubro->nombre;
                                            $responsable_rubro[6]=$resp_rubro->grado;
                                            $pf++;                        
                                        else:
                                            if($resp_rubro->rol==7):
                                                $responsable_rubro[0]=$resp_rubro->nombre;
                                                $responsable_rubro[6]=$resp_rubro->grado;
                                                $pf++;
                                            endif;
                                        endif;
                                        break;
                                    case 2: //insumos laboratorio (5,10)
                                        if($nivel==1):
                                            $responsable_rubro[1]=$resp_rubro->nombre;
                                            $responsable_rubro[7]=$resp_rubro->grado;
                                            $d++;
                                        else:
                                            if($resp_rubro->rol==3):
                                                $responsable_rubro[1]=$resp_rubro->nombre;
                                                $responsable_rubro[7]=$resp_rubro->grado;
                                                $d++;
                                            endif;
                                        endif;
                                        break;
                                    case 3://biomedico,quirurgico,afines (2,3,7)
                                        if($nivel==1):
                                            $responsable_rubro[2]=$resp_rubro->nombre;
                                            $responsable_rubro[8]=$resp_rubro->grado;
                                            $d++;
                                        else:
                                            if($resp_rubro->rol==3):
                                                $responsable_rubro[2]=$resp_rubro->nombre;
                                                $responsable_rubro[8]=$resp_rubro->grado;
                                                $d++;   
                                            endif;
                                        endif;
                                        break;
                                    case 4://dentales (4)
                                        if($nivel==1):
                                            $responsable_rubro[3]=$resp_rubro->nombre;
                                            $responsable_rubro[9]=$resp_rubro->grado;
                                            $d++;
                                        else:
                                            if($resp_rubro->rol==3):
                                                $responsable_rubro[3]=$resp_rubro->nombre;
                                                $responsable_rubro[9]=$resp_rubro->grado;
                                                $d++;
                                            endif;
                                        endif;
                                        break;
                                    case 5://fotografico (6)
                                        if($nivel==1):
                                            $responsable_rubro[4]=$resp_rubro->nombre;
                                            $responsable_rubro[10]=$resp_rubro->grado;
                                            $d++;
                                        else:
                                            if($resp_rubro->rol==3):
                                                $responsable_rubro[4]=$resp_rubro->nombre;
                                                $responsable_rubro[10]=$resp_rubro->grado;
                                                $d++;
                                            endif;
                                        endif;
                                        break;
                                }
                            }

                            dd($responsable_rubro);
                            $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
                            $cierre=$cierre_rubro->get(0)->updated_at;                  
                             $nombre_archivo=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_CAN_UGPFDMPS';
                             
                            $texto='CONSOLIDADO IPRESS';
                            
                            //return view('site.pdf.descargar_rubro_servicio_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable);
                            if($ano==1):
                                /*return view('site.pdf.descargar_rubro_servicio_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable); */
                                $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                                'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]); 
                                
                            endif;
                            if($ano==2):
                                /*return view('site.pdf.descargar_rubro_servicio_2_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable); */
                                $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_2_pdf',['estimaciones'=>$data,
                                'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]); 
                                
                            endif;
                            if($ano==3):
                                /*return view('site.pdf.descargar_rubro_servicio_3_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id)->with('responsables', $responsables)->with('responsable_rubro', $responsable_rubro)->with('responsable', $responsable);
                                */
                                $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_3_pdf',['estimaciones'=>$data,
                                'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]); 
                            endif;

                            $actualizado=$cierre_rubro->get(0)->actualizacion;
                                    if($actualizado==1):
                                        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_4_pdf',['estimaciones'=>$data,
                                'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                                'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]); 
                                    endif;

                            $pdf->setPaper('A4', 'landscape');
                            $pdf->getDomPDF()->set_option("enable_php", true);
                            return $pdf->stream($nombre_archivo.'.pdf');

                        endif;
                    else:
                        Flash::error('Falta Ingresar Responsables');
                        return redirect(route('users.index_responsable',[$can_id]));    
                    endif;
                else:
                    Flash::error('Falta Ingresar al jefe y/o Responsablde de Farmacia');
                    return redirect(route('users.index_responsable',[$can_id]));    
                endif;
            else:
                Flash::error('Falta Ingresar al jefe de la IPRESS O JEFE');
                return redirect(route('users.index_responsable',[$can_id]));
            endif;
        else:
            
            if($tipo==1):
                $nombre_rubro='MEDICAMENTOS';
                $texto='CONSOLIDADO IPRESS';
            else:
                $nombre_rubro='DISPOSITIVOS';
                $texto='CONSOLIDADO IPRESS';
            endif;

            $registro_farmacia = $model_responsables->GetJefeResponsable($can_id, $establecimiento_id, 0, 7);
            $name=$responsable[2]=Auth::user()->name;
            $responsable[5]=Auth::user()->grado;
            $cip=Auth::user()->cip;
            $dni=Auth::user()->cip;

            $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
            $cierre=$cierre_rubro->get(0)->updated_at;                  
            $nombre_archivo=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_CAN_UGPFDMPS';
            

                /*
                $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
              'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
              'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]); 

                $pdf->setPaper('A4', 'landscape');
                $pdf->getDomPDF()->set_option("enable_php", true);
                return $pdf->stream($nombre_archivo.'.pdf');
                */
                if($ano==1):
                    $pdf = \PDF::loadView('site.pdf.descargar_consolidado_farmacia_pdf',['estimaciones'=>$data,
              'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
              'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsable'=>$responsable]); 
                    /*return view('site.pdf.descargar_consolidado_farmacia_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id);*/
                endif;

                if($ano==2):
                    $pdf = \PDF::loadView('site.pdf.descargar_consolidado_farmacia_pdf',['estimaciones'=>$data,
              'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
              'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsable'=>$responsable]); 
                    /*return view('site.pdf.descargar_consolidado_farmacia_2_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id);*/
                endif;
                if($ano==3):
                    $pdf = \PDF::loadView('site.pdf.descargar_consolidado_farmacia_3_pdf',['estimaciones'=>$data,
              'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
              'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsable'=>$responsable]);
                    /*return view('site.pdf.descargar_consolidado_farmacia_3_pdf')->with('estimaciones', $data)->with('establecimiento_id', $establecimiento_id)->with('descripcion_tipo', $descripcion_tipo)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('responsable_name', $name)->with('nombre_rubro', $nombre_rubro)->with('cierre', $cierre)->with('cip', $cip)->with('dni', $dni)->with('texto', $texto)->with('nivel', $nivel)->with('tipo', $tipo)->with('can_id', $can_id);*/
                endif;
                
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
        
        $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
        $cierre=$cierre_rubro->get(0)->updated_rectificacion;

        if($tipo==1){
            $nombre_rubro='STOCK MEDICAMENTOS';
        }
        else
        {
            $nombre_rubro='STOCK DISPOSITIVOS';
        }
        
        $nombre_archivo=$establecimiento->nombre_establecimiento.'_'.$nombre_rubro.'_CAN2024';

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

     public function ejecutar_new_insercion($tipo){

        if($tipo==1){
                $data = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9,sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,estimacion_servicio.can_id, estimacion_servicio.establecimiento_id, estimacion_servicio.cod_establecimiento, estimacion_servicio.nombre_establecimiento , estimacion_servicio.petitorio_id,estimacion_servicio.tipo_dispositivo_id, estimacion_servicio.uso_id, estimacion_servicio.cod_petitorio , estimacion_servicio.descripcion'))
                    ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacion_servicio.tipo_dispositivo_id')
                    ->where('estimacion_servicio.can_id',6)
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.tipo_dispositivo_id',1)   
                    ->where('estimacion_servicio.establecimiento_id',1)   
                    ->where('estimacion_servicio.estado','<>',2)
                    ->groupby('estimacion_servicio.can_id', 'estimacion_servicio.establecimiento_id', 'estimacion_servicio.cod_establecimiento', 'estimacion_servicio.nombre_establecimiento' , 'estimacion_servicio.petitorio_id','estimacion_servicio.tipo_dispositivo_id', 'estimacion_servicio.uso_id','estimacion_servicio.cod_petitorio' , 'estimacion_servicio.descripcion', 'tipo_dispositivo_medicos.descripcion')
                    ->orderby('estimacion_servicio.cod_petitorio','asc')
                    ->get();

                foreach($data as $key => $can2022){

                    DB::table('consolidados_2022')
                        ->insert([
                            'can_id' => $can2022->can_id,
                            'establecimiento_id' => $can2022->establecimiento_id,
                            'cod_establecimiento' => $can2022->cod_establecimiento,
                            'nombre_establecimiento' => $can2022->nombre_establecimiento,
                            'tipo_dispositivo_id' => $can2022->tipo_dispositivo_id,
                            'petitorio_id' => $can2022->petitorio_id,
                            'cod_petitorio' => $can2022->cod_petitorio,
                            'descripcion' => $can2022->descripcion,
                            'necesidad_anual' => $can2022->necesidad,
                            'mes1' => $can2022->mes1,
                            'mes2' => $can2022->mes2,
                            'mes3' => $can2022->mes3,
                            'mes4' => $can2022->mes4,
                            'mes5' => $can2022->mes5,
                            'mes6' => $can2022->mes6,
                            'mes7' => $can2022->mes7,
                            'mes8' => $can2022->mes8,
                            'mes9' => $can2022->mes9,
                            'mes10' => $can2022->mes10,
                            'mes11' => $can2022->mes11,
                            'mes12' => $can2022->mes12,
                            'uso_id'=>$can2022->uso_id,
                            'created_at'=>Carbon::now()
                            
                    ]);
                } 
                   
            }else
            {
                $data = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad, sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9,sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,estimacion_servicio.can_id, estimacion_servicio.establecimiento_id, estimacion_servicio.cod_establecimiento, estimacion_servicio.nombre_establecimiento , estimacion_servicio.petitorio_id,estimacion_servicio.tipo_dispositivo_id, estimacion_servicio.uso_id, estimacion_servicio.cod_petitorio , estimacion_servicio.descripcion'))
                    ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacion_servicio.tipo_dispositivo_id')
                    ->where('estimacion_servicio.can_id',6)
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.tipo_dispositivo_id','<>',1)   
                    ->where('estimacion_servicio.estado','<>',2)
                    ->where('estimacion_servicio.establecimiento_id',1)   
                    ->groupby('estimacion_servicio.can_id', 'estimacion_servicio.establecimiento_id', 'estimacion_servicio.cod_establecimiento', 'estimacion_servicio.nombre_establecimiento' , 'estimacion_servicio.petitorio_id','estimacion_servicio.tipo_dispositivo_id', 'estimacion_servicio.uso_id','estimacion_servicio.cod_petitorio' , 'estimacion_servicio.descripcion', 'tipo_dispositivo_medicos.descripcion')
                    ->orderby('estimacion_servicio.cod_petitorio','asc')
                    ->get();

                foreach($data as $key => $can2022){

                    DB::table('consolidados_2022')
                        ->insert([
                            'can_id' => $can2022->can_id,
                            'establecimiento_id' => $can2022->establecimiento_id,
                            'cod_establecimiento' => $can2022->cod_establecimiento,
                            'nombre_establecimiento' => $can2022->nombre_establecimiento,
                            'tipo_dispositivo_id' => $can2022->tipo_dispositivo_id,
                            'petitorio_id' => $can2022->petitorio_id,
                            'cod_petitorio' => $can2022->cod_petitorio,
                            'descripcion' => $can2022->descripcion,
                            'necesidad_anual' => $can2022->necesidad,
                            'mes1' => $can2022->mes1,
                            'mes2' => $can2022->mes2,
                            'mes3' => $can2022->mes3,
                            'mes4' => $can2022->mes4,
                            'mes5' => $can2022->mes5,
                            'mes6' => $can2022->mes6,
                            'mes7' => $can2022->mes7,
                            'mes8' => $can2022->mes8,
                            'mes9' => $can2022->mes9,
                            'mes10' => $can2022->mes10,
                            'mes11' => $can2022->mes11,
                            'mes12' => $can2022->mes12,
                            'uso_id'=>$can2022->uso_id,
                            'created_at'=>Carbon::now()
                            
                    ]);
                } 
            }
        }

        

        /*public function ejecutar_new_insercion($tipo){

        if($tipo==1){
                $data = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9,sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,estimacion_servicio.can_id, estimacion_servicio.establecimiento_id, estimacion_servicio.cod_establecimiento, estimacion_servicio.nombre_establecimiento , estimacion_servicio.petitorio_id,estimacion_servicio.tipo_dispositivo_id, estimacion_servicio.uso_id, estimacion_servicio.cod_petitorio , estimacion_servicio.descripcion, tipo_dispositivo_medicos.descripcion'))
                    ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacion_servicio.tipo_dispositivo_id')
                    ->where('estimacion_servicio.can_id',6)
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.tipo_dispositivo_id',1)   
                    ->where('estimacion_servicio.estado','<>',2)
                    ->groupby('estimacion_servicio.can_id', 'estimacion_servicio.establecimiento_id', 'estimacion_servicio.cod_establecimiento', 'estimacion_servicio.nombre_establecimiento' , 'estimacion_servicio.petitorio_id','estimacion_servicio.tipo_dispositivo_id', 'estimacion_servicio.uso_id','estimacion_servicio.cod_petitorio' , 'estimacion_servicio.descripcion', 'tipo_dispositivo_medicos.descripcion')
                    ->orderby('estimacion_servicio.cod_petitorio','asc')
                    ->get();

                foreach($data as $key => $can2022){

                    DB::table('consolidados_2022')
                        ->insert([
                            'can_id' => $can2022->can_id,
                            'establecimiento_id' => $can2022->establecimiento_id,
                            'cod_establecimiento' => $can2022->cod_establecimiento,
                            'nombre_establecimiento' => $can2022->nombre_establecimiento,
                            'tipo_dispositivo_id' => $can2022->tipo_dispositivo_id,
                            'petitorio_id' => $can2022->petitorio_id,
                            'cod_petitorio' => $can2022->cod_petitorio,
                            'descripcion' => $can2022->descripcion,
                            'necesidad_anual' => $can2022->necesidad,
                            'mes1' => $can2022->mes1,
                            'mes2' => $can2022->mes2,
                            'mes3' => $can2022->mes3,
                            'mes4' => $can2022->mes4,
                            'mes5' => $can2022->mes5,
                            'mes6' => $can2022->mes6,
                            'mes7' => $can2022->mes7,
                            'mes8' => $can2022->mes8,
                            'mes9' => $can2022->mes9,
                            'mes10' => $can2022->mes10,
                            'mes11' => $can2022->mes11,
                            'mes12' => $can2022->mes12,
                            'uso_id'=>$can2022->uso_id,
                            'created_at'=>Carbon::now()
                            
                    ]);
                } 
                   
            }else
            {
                $data = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad, sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9,sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,estimacion_servicio.can_id, estimacion_servicio.establecimiento_id, estimacion_servicio.cod_establecimiento, estimacion_servicio.nombre_establecimiento , estimacion_servicio.petitorio_id,estimacion_servicio.tipo_dispositivo_id, estimacion_servicio.uso_id, estimacion_servicio.cod_petitorio , estimacion_servicio.descripcion, tipo_dispositivo_medicos.descripcion'))
                    ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacion_servicio.tipo_dispositivo_id')
                    ->where('estimacion_servicio.can_id',6)
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.tipo_dispositivo_id','<>',1)   
                    ->where('estimacion_servicio.estado','<>',2)
                    ->groupby('estimacion_servicio.can_id', 'estimacion_servicio.establecimiento_id', 'estimacion_servicio.cod_establecimiento', 'estimacion_servicio.nombre_establecimiento' , 'estimacion_servicio.petitorio_id','estimacion_servicio.tipo_dispositivo_id', 'estimacion_servicio.uso_id','estimacion_servicio.cod_petitorio' , 'estimacion_servicio.descripcion', 'tipo_dispositivo_medicos.descripcion')
                    ->orderby('estimacion_servicio.cod_petitorio','asc')
                    ->get();

                foreach($data as $key => $can2022){

                    DB::table('consolidados_2022')
                        ->insert([
                            'can_id' => $can2022->can_id,
                            'establecimiento_id' => $can2022->establecimiento_id,
                            'cod_establecimiento' => $can2022->cod_establecimiento,
                            'nombre_establecimiento' => $can2022->nombre_establecimiento,
                            'tipo_dispositivo_id' => $can2022->tipo_dispositivo_id,
                            'petitorio_id' => $can2022->petitorio_id,
                            'cod_petitorio' => $can2022->cod_petitorio,
                            'descripcion' => $can2022->descripcion,
                            'necesidad_anual' => $can2022->necesidad,
                            'mes1' => $can2022->mes1,
                            'mes2' => $can2022->mes2,
                            'mes3' => $can2022->mes3,
                            'mes4' => $can2022->mes4,
                            'mes5' => $can2022->mes5,
                            'mes6' => $can2022->mes6,
                            'mes7' => $can2022->mes7,
                            'mes8' => $can2022->mes8,
                            'mes9' => $can2022->mes9,
                            'mes10' => $can2022->mes10,
                            'mes11' => $can2022->mes11,
                            'mes12' => $can2022->mes12,
                            'uso_id'=>$can2022->uso_id,
                            'created_at'=>Carbon::now()
                            
                    ]);
                } 
            }
        }
*/
        public function actualiza_data_nivel_2(){
       
            $data=DB::table('consolidados_2022')
                ->orderby ('descripcion','asc')  
                ->get();

            foreach($data as $key => $can2022){
                
                DB::table('estimacions')
                    ->insert([
                        'can_id' => $can2022->can_id,
                        'establecimiento_id' => $can2022->establecimiento_id,
                        'cod_establecimiento' => $can2022->cod_establecimiento,
                        'nombre_establecimiento' => $can2022->nombre_establecimiento,                        
                        'petitorio_id' => $can2022->petitorio_id,
                        'cod_petitorio' => $can2022->cod_petitorio,
                        'descripcion' => $can2022->descripcion,
                        'tipo_dispositivo_id' => $can2022->tipo_dispositivo_id,
                        'necesidad_anual' => $can2022->necesidad_anual,
                        'uso_id'=>$can2022->uso_id,
                        'cpma' => $can2022->cpma,
                        'mes1' => $can2022->mes1,
                        'mes2' => $can2022->mes2,
                        'mes3' => $can2022->mes3,
                        'mes4' => $can2022->mes4,
                        'mes5' => $can2022->mes5,
                        'mes6' => $can2022->mes6,
                        'mes7' => $can2022->mes7,
                        'mes8' => $can2022->mes8,
                        'mes9' => $can2022->mes9,
                        'mes10' => $can2022->mes10,
                        'mes11' => $can2022->mes11,
                        'mes12' => $can2022->mes12,
                        'estado'=>0,
                        'created_at'=>Carbon::now()
                        
                ]);
            } 
        }

        public function actualiza_data_petitorio($establecimiento_id){
       
            $data=DB::table('petitorios')
                ->orderby ('descripcion','asc')  
                ->get();

            foreach($data as $key => $petitorio){
                
                DB::table('estimacions')
                    ->where('estimacions.petitorio_id',$petitorio->id)
                    ->where('estimacions.establecimiento_id', $establecimiento_id)
                    ->where('can_id',6)
                    ->where('estado',0) 
                    ->update([
                        'descripcion' => $petitorio->descripcion
                ]);
            } 
        }

        public function update_atencion($establecimiento_id, Request $request)
        {
            $model_can = new Can();
            $encontrado = $model_can->BuscaCanEstablecimiento($request->can_id,$establecimiento_id);

            if ($encontrado->total>0) {
                DB::table('can_establecimiento')
                    ->where('can_establecimiento.can_id',$request->can_id)
                    ->where('can_establecimiento.establecimiento_id', $establecimiento_id)
                    ->update([
                        'medicina_interna' => $request->medicina_interna,
                        'odontologia' => $request->odontologia,
                        'obstetricia' => $request->obstetricia,
                ]);
                
                Flash::success('Guardado correctamente.');

                if($request->redirigir == 1):
                    return redirect(route('farmacia.index'));
                else:
                    return redirect(route('farmacia.cargar_medicamentos',[$request->can_id,$request->tipo]));
                endif;

                           
            }
            else
            {
                Flash::error('Error no se encuentro el registro'); 
                return redirect(route('farmacias.index'));
            }
        }

        public function atencion_consultorios($can_id){

            $establecimiento_id = Auth::user()->establecimiento_id;
            $establecimiento = Establecimiento::find($establecimiento_id);

            $establecimiento_cerrado=DB::table('can_establecimiento') ->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();  

            if($establecimiento_id == 69){
                $tipo = 2; 
            }
            else{
                $tipo = 1;     
            }
            

            $medicina_interna = $establecimiento_cerrado->get(0)->medicina_interna; 
            $odontologia = $establecimiento_cerrado->get(0)->odontologia; 
            $obstetricia = $establecimiento_cerrado->get(0)->obstetricia; 
            $redirigir = 1; //home
           return view('site.responsable_farmacia.atenciones.edit')->with('can_id', $can_id)->with('tipo', $tipo)->with('establecimiento_id', $establecimiento_id)->with('establecimiento', $establecimiento)->with('medicina_interna', $medicina_interna)->with('odontologia', $odontologia)->with('obstetricia', $obstetricia)->with('redirigir', $redirigir);
        }

}
