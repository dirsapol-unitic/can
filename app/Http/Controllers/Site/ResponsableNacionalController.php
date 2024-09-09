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
use App\Models\Region;
use DB;
use App\Models\Establecimiento;
use App\Models\Can;
use Illuminate\Support\Facades\Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;      

class ResponsableNacionalController extends AppBaseController
{
    /** @var  EstimacionRepository */  
    public function __construct()
    {
        
    }

        public function index(Request $request)
    {
        
        
        //Verifico de que establecimiento es el usuario
        $nombre_establecimiento=Auth::user()->nombre_establecimiento;

        //listar los cans
        $cans = DB::table('cans')->orderby('cans.id','desc')->get();

        return view('site.responsable_nacional.index')
                ->with('nombre_establecimiento', $nombre_establecimiento)
                ->with('cans', $cans);
    }

    public function listar_nacional($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        //red_region
        $red_region = Region::find($establecimiento->region_id);
        
        $nombre_region_red_salud=$red_region->descripcion;
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;

        $regiones = DB::table('regions')
                                ->get();

        $items_medicamentos = DB::table('establecimientos')
                                    ->select('establecimiento_id', 
                                                DB::raw('COUNT(tipo_dispositivo_id) as cantidad')
                                                )
                                    ->join('estimacions', 'estimacions.establecimiento_id','establecimientos.id')
                                    ->groupby('establecimiento_id')
                                    ->where('establecimientos.region_id',$establecimiento->region_id)
                                    ->where('estimacions.tipo_dispositivo_id',1)
                                    ->get();
        
        $items_dispositivos = DB::table('establecimientos')
                                    ->select('establecimiento_id', 
                                                DB::raw('COUNT(tipo_dispositivo_id) as cantidad')
                                                )
                                    ->join('estimacions', 'estimacions.establecimiento_id','establecimientos.id')
                                    ->groupby('establecimiento_id')
                                    ->where('establecimientos.region_id',$establecimiento->region_id)
                                    ->where('estimacions.tipo_dispositivo_id','>',1)
                                    ->get();
        

        $cans = DB::table('cans')
                ->where('cans.id', $can_id)
                ->get();  

        $ano=$cans->get(0)->ano;

        $responsables= DB::table('responsables')
                            ->where('rol',2)
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id','>',0)
                            ->get();

            return view('site.responsable_nacional.listar_nacional')
                    ->with('regiones', $regiones)
                    ->with('responsables', $responsables)
                    ->with('nombre_region_red_salud', $nombre_region_red_salud)
                    ->with('can_id', $can_id)
                    ->with('ano', $ano)
                    ->with('items_dispositivos', $items_dispositivos)
                    ->with('items_medicamentos', $items_medicamentos)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nivel_id', $nivel_id);        
    }

    public function listar_red($can_id, $region_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        //red_region
        $red_region = Region::find($region_id);
        
        $nombre_region_red_salud=$red_region->descripcion;
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;

        $establecimientos = DB::table('establecimientos')
                                ->where('region_id',$region_id)
                                ->get();

        $items_medicamentos = DB::table('establecimientos')
                                    ->select('establecimiento_id', 
                                                DB::raw('COUNT(tipo_dispositivo_id) as cantidad')
                                                )
                                    ->join('estimacions', 'estimacions.establecimiento_id','establecimientos.id')
                                    ->groupby('establecimiento_id')
                                    ->where('establecimientos.region_id',$establecimiento->region_id)
                                    ->where('estimacions.tipo_dispositivo_id',1)
                                    ->get();
        
        $items_dispositivos = DB::table('establecimientos')
                                    ->select('establecimiento_id', 
                                                DB::raw('COUNT(tipo_dispositivo_id) as cantidad')
                                                )
                                    ->join('estimacions', 'estimacions.establecimiento_id','establecimientos.id')
                                    ->groupby('establecimiento_id')
                                    ->where('establecimientos.region_id',$establecimiento->region_id)
                                    ->where('estimacions.tipo_dispositivo_id','>',1)
                                    ->get();
        

        $cans = DB::table('cans')
                ->join('years', 'years.id','cans.year_id')
                ->where('cans.id', $can_id)
                ->get();  
        $ano=$cans->get(0)->ano;

        /////////Responsables por establecimiento de Farmacia////////////////////////////////////
        $responsables= DB::table('responsables')->where('can_id',$can_id)->where('rol',3)->get();
        

            return view('site.responsable_nacional.listar_red')
                    ->with('establecimientos', $establecimientos)
                    ->with('responsables', $responsables)
                    ->with('nombre_region_red_salud', $nombre_region_red_salud)
                    ->with('can_id', $can_id)
                    ->with('ano', $ano)
                    ->with('region_id', $region_id)
                    ->with('items_dispositivos', $items_dispositivos)
                    ->with('items_medicamentos', $items_medicamentos)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nivel_id', $nivel_id);        
    }

    public function listar_distribucion($can_id,$id_establecimiento)
    {
        //Verifico de que establecimiento es el usuario
       // $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($id_establecimiento);
            
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;
        
        if($nivel_id>1){
            $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_servicio.establecimiento_id',$id_establecimiento)
            ->get();
        }
        else
        {
            $servicios = DB::table('rubros')
            ->join('can_rubro', 'can_rubro.rubro_id','rubros.id')
            ->where('can_rubro.establecimiento_id',$id_establecimiento)
            ->get();
        }
        
        $cans = DB::table('cans')
                ->where('cans.id', $can_id)
                ->get();  

        $ano=$cans->get(0)->ano;

        $responsables= DB::table('responsables')
                            ->where('rol',2)
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$id_establecimiento)
                            ->where('servicio_id','>',0)
                            ->get();
        
        $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$id_establecimiento)//cambiar 1
                    ->get();  

        $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado_consolidado;
        $dispositivo_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado_consolidado;


            return view('site.responsable_nacional.listar_distribucion')
                    ->with('servicios', $servicios)
                    ->with('responsables', $responsables)
                    ->with('can_id', $can_id)
                    ->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)
                    ->with('dispositivo_cerrado_consolidado', $dispositivo_cerrado_consolidado)
                    ->with('ano', $ano)
                    ->with('establecimiento_id', $id_establecimiento)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
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
        $contact = Estimacion::findOrFail($id); //estimacion_servicio
        $petitorio_id=($contact->petitorio_id);
        $establecimiento_id=($contact->establecimiento_id);
        $establecimiento = Establecimiento::find($establecimiento_id);
        
        $nivel=$establecimiento->nivel_id;

        if($nivel==1)
        {
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
        }
        else
        {
            $estimaciones = DB::table('estimacions as A')
                        ->select('B.*')
                        ->addselect('C.nombre as nombre')
                        ->join('estimacion_servicio as B', 'A.establecimiento_id','B.establecimiento_id')
                        ->join('responsables as C', 'C.servicio_id','B.servicio_id')
                        ->where('B.establecimiento_id',$establecimiento_id)
                        ->where('B.petitorio_id',$petitorio_id)
                        ->where('C.establecimiento_id',$establecimiento_id)
                        ->where('C.rol',2)
                        ->distinct()
                        ->get();

        }

        $data = DB::table('estimacions')
                            ->select('estimacions.tipo_dispositivo_id','estimacions.petitorio_id', 'estimacions.cod_petitorio','estimacions.descripcion','estimacions.id',
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
                            ->join('establecimientos', 'estimacions.establecimiento_id','establecimientos.id')
                            ->join('regions','establecimientos.region_id','regions.id')
                            ->where('regions.id',$region_id)
                            ->groupby('tipo_dispositivo_id','estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','estimacions.id')
                            ->where('can_id',$can_id)
                            ->where('tipo_dispositivo_id','>',1)
                            ->orderby('estimacions.descripcion','asc')//cambiar desc
                            ->get();

        
        //dd($estimaciones);
        return view('site.responsable_nacional.medicamentos.mostrar_ipress')->with('estimacions', $estimaciones)->with('nivel', $nivel);
        //return view('tests.show', compact('test'));
    }
    

    public function show_ipress($id)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;
        //$contact = Estimacion::findOrFail($id); //estimacion_servicio
        $petitorio_id=$id;
        //$establecimiento_id=($contact->establecimiento_id);
        $establecimiento = Establecimiento::find($establecimiento_id);
        
        $nivel=$establecimiento->nivel_id;

        $estimaciones = DB::table('estimacions as A')
                        ->join('establecimientos as E', 'A.establecimiento_id','E.id')
                        ->join('regions as C', 'C.id','E.region_id')
                        ->join('responsables as R', 'R.establecimiento_id','A.establecimiento_id')
                        ->where('A.petitorio_id',$petitorio_id)
                        ->where('C.id',$establecimiento->region_id)
                        ->where('R.rol',3)
                        ->get();
        
        return view('site.responsable_nacional.medicamentos.mostrar_ipress')->with('estimacions', $estimaciones)->with('nivel', $nivel);
        //return view('tests.show', compact('test'));
    }
    public function edit($id)
    {
    
    }

    public function destroy($id)
    {
    
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
                
            return view('site.responsable_nacional.medicamentos.medicamentos')
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
            return view('site.responsable_nacional.medicamentos.medicamentos')
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
    
/////////////////////DESCARGA POR IPRESS ///////////////////////////////////////////////////////////////////
    public function descargar_consolidado_nacional($tipo,$can_id)
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
            $data = DB::table('estimacions')
                ->select('tipo_dispositivo_id','petitorio_id', 'cod_petitorio','descripcion',
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
                ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion')
                ->where('can_id',$can_id)
                ->where('tipo_dispositivo_id',1)
                ->orderby('tipo_dispositivo_id','asc')//cambiar desc
                ->get();

            $num_estimaciones = DB::table('estimacions')
                ->select('tipo_dispositivo_id','petitorio_id', 'cod_petitorio','descripcion',
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
                ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion')
                ->where('can_id',$can_id)
                ->where('tipo_dispositivo_id',1)                
                ->count();
            
            
            $descripcion_tipo='Medicamentos';
        }else            
        {   
            if ($tipo==2) {
                    $data = DB::table('estimacions')
                            ->select('tipo_dispositivo_id','petitorio_id', 'cod_petitorio','descripcion',
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
                            ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion')
                            ->where('can_id',$can_id)
                            ->where('tipo_dispositivo_id','>',1)
                            ->orderby('tipo_dispositivo_id','asc')//cambiar desc
                            ->get();

                    $num_estimaciones = DB::table('estimacions')
                            ->select('tipo_dispositivo_id','petitorio_id', 'cod_petitorio','descripcion',
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
                            ->groupby('petitorio_id','cod_petitorio','descripcion')
                            ->where('can_id',$can_id)
                            ->where('tipo_dispositivo_id','>',1)
                            ->count();

                    $descripcion_tipo='Dispositivos';
            }else
            {
                Flash::error('Datos no son correctos, error al descargar archivo');
                return redirect(route('estimacion.index'));  
            }
        }
        $servicio_id_cero=0;
        $condicion_boton=3;
        return view('site.responsable_nacional.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('condicion_boton', $condicion_boton)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('servicio_id', $servicio_id_cero)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function descargar_consolidado_region($tipo,$can_id,$region_id)
    {       
        
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        if($tipo==1){

            $data = DB::table('estimacions as A')
                ->select('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id',
                    DB::raw('AVG(cpma) as cpma'),
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
                ->join('establecimientos as E', 'A.establecimiento_id','E.id')
                ->join('regions as C','E.region_id','C.id')
                ->where('C.id',$region_id)
                ->where('A.can_id',$can_id)
                ->where('A.tipo_dispositivo_id',1)
                ->groupby('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id')                
                ->orderby('A.tipo_dispositivo_id','asc')//cambiar desc
                ->get();

            $num_estimaciones = DB::table('estimacions as A')
                ->select('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id',
                    DB::raw('AVG(cpma) as cpma'),
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
                ->join('establecimientos as E', 'A.establecimiento_id','E.id')
                ->join('regions as C','E.region_id','C.id')
                ->where('C.id',$region_id)
                ->where('A.can_id',$can_id)
                ->where('A.tipo_dispositivo_id',1)
                ->groupby('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id')
                ->count();
            
            $descripcion_tipo='Medicamentos';
        }else            
        {   
            if ($tipo==2) {
                    
                    $data = DB::table('estimacions as A')
                            ->select('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id',
                                DB::raw('AVG(cpma) as cpma'),
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
                            ->join('establecimientos as E', 'A.establecimiento_id','E.id')
                            ->join('regions as C','E.region_id','C.id')
                            ->where('C.id',$region_id)
                            ->where('A.can_id',$can_id)
                            ->where('A.tipo_dispositivo_id','>',1)
                            ->groupby('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id')
                            ->orderby('A.tipo_dispositivo_id','asc')//cambiar desc
                            ->get();

                    $num_estimaciones = DB::table('estimacions as A')
                            ->select('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id',
                                DB::raw('AVG(cpma) as cpma'),
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
                            ->join('establecimientos as E', 'A.establecimiento_id','E.id')
                            ->join('regions as C','E.region_id','C.id')
                            ->where('C.id',$region_id)
                            ->where('A.can_id',$can_id)
                            ->where('A.tipo_dispositivo_id','>',1)
                            ->groupby('A.tipo_dispositivo_id','A.descripcion','A.petitorio_id')
                            ->count();

                    $descripcion_tipo='Dispositivos';
            }else
            {
                Flash::error('Datos no son correctos, error al descargar archivo');
                return redirect(route('estimacion.index'));  
            }
        }
        
        
        $condicion_boton=3; //ver 
        $establecimiento_id=2; //ver 
        $servicio_id_cero=0;

        return view('site.responsable_nacional.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('servicio_id', $servicio_id_cero)
            ->with('condicion_boton', $condicion_boton)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function descargar_consolidado_ipress($tipo,$can_id,$establecimiento_id)
    {       
        
        //$establecimiento_id=Auth::user()->establecimiento_id;

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
            $data = DB::table('estimacions')
                ->select('estimacions.tipo_dispositivo_id','estimacions.petitorio_id', 'estimacions.cod_petitorio','estimacions.descripcion','estimacions.id',
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
                ->groupby('tipo_dispositivo_id','estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','estimacions.id')
                ->where('can_id',$can_id)
                ->where('tipo_dispositivo_id',1)
                ->where('establecimiento_id',$establecimiento_id)
                ->orderby ('tipo_dispositivo_id','asc')   
                ->get();

            $num_estimaciones = DB::table('estimacions')
                ->select('estimacions.tipo_dispositivo_id','estimacions.petitorio_id', 'estimacions.cod_petitorio','estimacions.descripcion','estimacions.id',
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
                ->groupby('tipo_dispositivo_id','estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','estimacions.id')
                ->where('can_id',$can_id)
                ->where('tipo_dispositivo_id',1)
                ->where('establecimiento_id',$establecimiento_id)
                ->count();
            
            $descripcion_tipo='Medicamentos';
        }else            
        {   
            if ($tipo==2) {
                    
                    $data = DB::table('estimacions')
                            ->select('estimacions.tipo_dispositivo_id','estimacions.petitorio_id', 'estimacions.cod_petitorio','estimacions.descripcion','estimacions.id',
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
                            ->groupby('tipo_dispositivo_id','estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','estimacions.id')
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_id','>',1)
                            ->orderby ('tipo_dispositivo_id','asc')   
                            ->get();

                    $num_estimaciones = DB::table('estimacions')
                            ->select('estimacions.tipo_dispositivo_id','estimacions.petitorio_id', 'estimacions.cod_petitorio','estimacions.descripcion','estimacions.id',
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
                            ->groupby('tipo_dispositivo_id','estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','estimacions.id')
                            ->where('can_id',$can_id)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_id','>',1)
                            ->count();

                    $descripcion_tipo='Dispositivos';
            }else
            {
                Flash::error('Datos no son correctos, error al descargar archivo');
                return redirect(route('estimacion.index'));  
            }
        }
        
        $condicion_boton=2; //ver 
        $servicio_id_cero=0; //consolidado
        return view('site.responsable_nacional.medicamentos.descargar_medicamentos')
            ->with('num_estimaciones', $num_estimaciones)
            ->with('estimacions', $data)
            ->with('servicio_id', $servicio_id_cero)
            ->with('condicion_boton', $condicion_boton)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo) 
            ->with('can_id', $can_id);
    }

    public function ver_rubros_servicios($tipo,$can_id,$establecimiento_id,$servicio_id)
    {       
        
        //$establecimiento_id=Auth::user()->establecimiento_id;

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
        //dd($data);
        $condicion_boton=1; //no ver
        return view('site.responsable_nacional.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('condicion_boton', $condicion_boton)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('servicio_id', $servicio_id)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

    public function ver_servicio_rubro($tipo,$can_id,$establecimiento_id,$petitorio_id)
    {       
        
        //$establecimiento_id=Auth::user()->establecimiento_id;

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
            if($nivel==1){
                $data=DB::table('estimacion_rubro')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('petitorio_id',$petitorio_id)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();    
            }
            else
            {
                $data=DB::table('estimacion_servicio')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('petitorio_id',$petitorio_id)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();       
            }

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    if($nivel==1){
                    $data=DB::table('estimacion_rubro')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('petitorio_id',$petitorio_id)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();    
                }
                else
                {
                    $data=DB::table('estimacion_servicio')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('petitorio_id',$petitorio_id)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();       
                }
                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }
        
        return view('site.responsable_nacional.medicamentos.ver_informacion_medicamentos')
            ->with('estimacions', $data)
            ->with('servicio_id', $servicio_id)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }


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
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $descripcion_tipo='Medicamentos';
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
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }
            
        return view('site.estimacions.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
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
            $table='estimacion_distribucion';
            $condicion1='distribucion_id';
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
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    
                    $descripcion_tipo='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }
            
        return view('site.estimacions.medicamentos.descargar_medicamentos')
            ->with('estimacions', $data)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }
    
    ///////////////////////////9////////////////////////////////////77
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

        if ($nivel==1)
        {
            $table='estimacion_distribucion';
            $condicion1='distribucion_id';
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
                    $table2='can_distribucion';
                    $condicion2='distribucion_id';
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
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;

        $servicio_id=Auth::user()->servicio_id;
        $nombre_servicio=Auth::user()->nombre_servicio;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if ($nivel==1)
        {
            $table='estimacion_distribucion';
            $condicion1='distribucion_id';
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
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
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

                    $sheet->cell('D10', $total_stock);
                    $sheet->cell('D10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('E10', $total_necesidad_anual);
                    $sheet->cell('E10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('F10', $mes1_total);
                    $sheet->cell('F10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('G10', $mes2_total);
                    $sheet->cell('G10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('H10', $mes3_total);
                    $sheet->cell('H10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('I10', $mes4_total);
                    $sheet->cell('I10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('J10', $mes5_total);
                    $sheet->cell('J10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('K10', $mes6_total);
                    $sheet->cell('K10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('L10', $mes7_total);
                    $sheet->cell('L10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('M10', $mes8_total);
                    $sheet->cell('M10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('N10', $mes9_total);
                    $sheet->cell('N10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('O10', $mes10_total);
                    $sheet->cell('O10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('P10', $mes11_total);
                    $sheet->cell('P10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('Q10', $mes12_total);
                    $sheet->cell('Q10', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                }
                

            });
        })->download($type);
    }

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

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($opt==1){
            $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
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

                $j=$k+10;
                $m=$j-1;

                $fh=$j+2;

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

    public function pdf_estimacion_nacional($can_id,$establecimiento_id,$tipo,$servicio_id)
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

        if($nivel>1){
            if($servicio_id!=0){
                if($tipo==1){
                    $data=DB::table('estimacion_servicio')
                            ->where('necesidad_anual','>',0)
                            ->where('servicio_id',$servicio_id)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->orderby('descripcion','asc')//cambiar desc
                            ->orderby ('tipo_dispositivo_id','asc')
                            ->get();
                
                    $num_estimaciones=DB::table('estimacion_servicio')
                            ->where('necesidad_anual','>',0)
                            ->where('servicio_id',$servicio_id)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
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
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1  
                            ->orderby ('tipo_dispositivo_id','asc')                  
                            ->get();     

                            $num_estimaciones=DB::table('estimacion_servicio')
                                ->where('necesidad_anual','>',0)
                                ->where('servicio_id',$servicio_id)
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

                $usuario=DB::table('users')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('servicio_id',$servicio_id)
                    ->where('rol',2)
                    ->get();

                $name=$usuario->get(0)->name;
                $servicio_id=$usuario->get(0)->servicio_id;
                $nombre_rubro=$usuario->get(0)->nombre_servicio;
                $user_id=$usuario->get(0)->id;
                $cip=$usuario->get(0)->cip;
                $dni=$usuario->get(0)->dni;

                $cierre_servicio=DB::table('can_servicio')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id',$servicio_id)->get();
                $cierre=$cierre_servicio->get(0)->updated_at;

                $texto='SERVICIO';
            }
            else
            {

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

                $name=Auth::user()->name;
                $servicio_id=Auth::user()->servicio_id;
                $user_id=Auth::user()->id;
                $cip=Auth::user()->cip;
                $dni=Auth::user()->dni;

                $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
                $cierre=$cierre_rubro->get(0)->updated_at;

                if($tipo==1){
                    $nombre_rubro='MEDICAMENTOS';
                }
                else
                {
                    $nombre_rubro='DISPOSITIVOS';
                }
                
                $texto='CONSOLIDADO IPRESS'; 


            }
        }
        else
        {

            if($servicio_id!=0){
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
                    //dd($establecimiento_id);
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

                $usuario=DB::table('users')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('servicio_id',$servicio_id)
                    ->where('rol',2)
                    ->get();

                $name=$usuario->get(0)->name;
                $servicio_id=$usuario->get(0)->servicio_id;
                $nombre_rubro=$usuario->get(0)->nombre_servicio;
                $user_id=$usuario->get(0)->id;
                $cip=$usuario->get(0)->cip;
                $dni=$usuario->get(0)->dni;

                $cierre_rubro=DB::table('can_rubro')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('rubro_id',$servicio_id)->get();
                $cierre=$cierre_rubro->get(0)->updated_at;
                
                $texto='RUBRO';


            }
            else
            {

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

                $name=Auth::user()->name;
                $servicio_id=Auth::user()->servicio_id;
                $user_id=Auth::user()->id;
                $cip=Auth::user()->cip;
                $dni=Auth::user()->dni;

                $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
                $cierre=$cierre_rubro->get(0)->updated_at;

                if($tipo==1){
                    $nombre_rubro='MEDICAMENTOS';
                }
                else
                {
                    $nombre_rubro='DISPOSITIVOS';
                }
                
                $texto='CONSOLIDADO IPRESS';



            }
        }
        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,
                      'can_id'=>$can_id]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
     }
}
