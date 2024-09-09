<?php

namespace App\Http\Controllers\Site;

set_time_limit(6000); 

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
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

class ResponsableIpressController extends AppBaseController
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
                
        return view('site.responsable_ipress.index')
                ->with('nombre_establecimiento', $nombre_establecimiento)
                ->with('cans', $cans);
    }

    public function listar_servicios($can_id)
    {
        
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;
        
        if($nivel_id>1){
            $servicios = DB::table('servicios')->join('can_servicio', 'can_servicio.servicio_id','servicios.id')->where('can_servicio.establecimiento_id',$establecimiento_id)->get();
        }
        else
        {
            $servicios = DB::table('rubros')->join('can_rubro', 'can_rubro.rubro_id','rubros.id')            ->where('can_rubro.establecimiento_id',$establecimiento_id)->get();
        }
        
        $cans = DB::table('cans')->where('cans.id', $can_id)->get();  

        $ano=$cans->get(0)->ano;

        $responsables= DB::table('responsables')->where('rol',2)->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id','>',0)->where('user_id','!=',335)->get();

        $id_user_responsables= DB::table('responsables')->where('rol',5)->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id','=',0)->where('user_id','!=',335)->get();
        if(count($id_user_responsables)>0){
            $id_user_responsable=$id_user_responsables->get(0)->user_id;
        }else
        {
            $id_user_responsables= DB::table('responsables')->where('rol',5)->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id','>',0)->where('user_id','!=',335)->get();
            $id_user_responsable=$id_user_responsables->get(0)->user_id;
        }
        $responsables_farmacia= DB::table('responsables')->where( function ( $query )
                                            {
                                                $query->orWhere('rol',3)
                                                    ->orWhere('rol',4)
                                                    ->orWhere('rol',7);
                                            })
        ->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
        
        
        $establecimiento_cerrado=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();  

        $medicamento_cerrado=$establecimiento_cerrado->get(0)->medicamento_cerrado;
        $dispositivo_cerrado=$establecimiento_cerrado->get(0)->dispositivo_cerrado;
        $medicamento_cerrado_consolidado=$establecimiento_cerrado->get(0)->medicamento_cerrado_consolidado;
        $dispositivo_cerrado_consolidado=$establecimiento_cerrado->get(0)->dispositivo_cerrado_consolidado;
        $medicamento_cerrado_consolidado_almacen=$establecimiento_cerrado->get(0)->medicamento_cerrado_consolidado_almacen;
        $dispositivo_cerrado_consolidado_almacen=$establecimiento_cerrado->get(0)->dispositivo_cerrado_consolidado_almacen;

        return view('site.responsable_ipress.listar_servicios')->with('servicios', $servicios)->with('responsables', $responsables)->with('responsables_farmacia', $responsables_farmacia)->with('can_id', $can_id)->with('medicamento_cerrado', $medicamento_cerrado)->with('dispositivo_cerrado', $dispositivo_cerrado)->with('medicamento_cerrado_consolidado', $medicamento_cerrado_consolidado)->with('dispositivo_cerrado_consolidado', $dispositivo_cerrado_consolidado)->with('medicamento_cerrado_consolidado_almacen', $medicamento_cerrado_consolidado_almacen)->with('dispositivo_cerrado_consolidado_almacen', $dispositivo_cerrado_consolidado_almacen)->with('ano', $ano)->with('establecimiento_id', $establecimiento_id)->with('id_user_responsable', $id_user_responsable)->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)->with('nivel', $nivel_id);        
    }
    

    public function manual($id)
    {
        return view('site.estimacions.manual')->with('id', $id);
    }
    /*public function show($id)
    {
    }*/
    public function show($id)
    {
        
        //$establecimiento_id=Auth::user()->establecimiento_id;
        $contact = DB::table('consolidados')->where('id',$id)->get(); //estimacion_servicio
        $petitorio_id=($contact->get(0)->petitorio_id);
        $establecimiento_id=($contact->get(0)->establecimiento_id);
        $establecimiento = Establecimiento::find($establecimiento_id);
        
        $nivel=$establecimiento->nivel_id;

        

        if($nivel==1)
        {
            $estimaciones = DB::table('consolidados as A')
                        ->select('B.*')
                        ->addselect('C.nombre as nombre')
                        ->join('estimacions as B', 'A.establecimiento_id','B.establecimiento_id')
                        ->join('responsables as C', 'C.rol','B.consolidado')
                        ->where('B.establecimiento_id',$establecimiento_id)
                        ->where('B.petitorio_id',$petitorio_id)
                        ->where('C.establecimiento_id',$establecimiento_id)
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
        
        return view('site.responsable_ipress.medicamentos.mostrar_datos')->with('estimacions', $estimaciones)->with('nivel', $nivel);
    }
    public function edit($id)
    {
        
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
                
            return view('site.responsable_ipress.medicamentos.medicamentos')
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
            return view('site.responsable_ipress.medicamentos.medicamentos')
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
  /*
    public function descargar_consolidado_farmacia_servicios($tipo,$can_id)
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
        //dd($data);
        return view('site.responsable_ipress.medicamentos.medicamentos')
            ->with('estimacions', $data)
            ->with('servicio_id', $servicio_id)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }
*/
///////////////////////////////////////////////////////////////////////////////////
public function ver_consolidado_farmacia_servicios($tipo,$can_id,$user_id)
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
            $data=DB::table('consolidados')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();

            $num_estimaciones=DB::table('consolidados')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table('consolidados')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->get();    

                    $num_estimaciones=DB::table('consolidados')
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
        
        $nombre_servicio="CONSOLIDADO";
        
        //dd($data);
        $condicion_boton=2; //ver 
        $servicio_id_cero=0; //consolidado
        return view('site.responsable_ipress.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('estimacions', $data)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('id_user', $user_id)
            ->with('servicio_id', $servicio_id_cero)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }

/////////////////////DESCARGA POR SERVICIO - DISTRIBUCION///////////////////////////////////////////
    public function descargar_estimacion_farmacia_servicios($tipo,$can_id,$establecimiento_id,$servicio_id,$dni)
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
            $table='estimacions';
            $condicion1='consolidado';
            $consolidado=$servicio_id;

            /*if($servicio_id==3){
                $consolidado=3;
            }
            else
            {
                $consolidado=4;   
            }*/
        }
        else
        {
            
            if($servicio_id=='F'){
                $consolidado=4;
                $condicion1='consolidado';
                $table='estimacions';
            }
            else{
                if($servicio_id=='A')
                {
                    $consolidado=3;
                    $condicion1='consolidado';
                    $table='estimacions';
                }
                else
                {   $table='estimacion_servicio';
                    $condicion1='servicio_id';
                    $consolidado=$servicio_id;        
                }
            }
        }    

        
        


        if($tipo==1){

            $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->orderby ('descripcion','asc')   
                    ->get();

            $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->orderby ('descripcion','asc')   
                    ->get();                    

                

                    $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
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

        if($nivel==1){
            if($num_estimaciones>0){
               
                if($servicio_id==3 || $servicio_id==4){
                    if($servicio_id==3){
                        $nombre_servicio="ALMACEN";
                    }else
                    {
                        if($servicio_id==4){
                            $nombre_servicio="FARMACIA";
                        }
                        else
                        {
                         $nombre_servicio="CONSOLIDADO";   
                        }
                    }
                }
                else
                {
                    $nombre_servicio=$data->get(0)->nombre_servicio;
                }
            }
            else
            {
                $servicios=DB::table('servicios')
                            ->where('id',$servicio_id)
                            ->get();
                $nombre_servicio=$servicios->get(0)->nombre_servicio;
            }
        }
        else
        {

            if($num_estimaciones>0){
                if($servicio_id=='A' || $servicio_id=='F'){ //Para nivel 2 y 3                
                    if($servicio_id=='A'){                
                        $nombre_servicio="ALMACEN";
                    }else
                    {
                        if($servicio_id=='F'){                  
                            $nombre_servicio="FARMACIA";
                        }
                        else
                        {
                         $nombre_servicio="CONSOLIDADO";   
                        }
                    }
                }
                else
                {
                    $nombre_servicio=$data->get(0)->nombre_servicio;
                }
            }
            else
            {
                $servicios=DB::table('servicios')
                            ->where('id',$servicio_id)
                            ->get();
                $nombre_servicio=$servicios->get(0)->nombre_servicio;
            }
        }

        $condicion_boton=1; //no ver 
        return view('site.responsable_ipress.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('estimacions', $data)
            ->with('id_user', $dni)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('nombre_servicio', $nombre_servicio)
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

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($opt==1){
            $data=DB::table('consolidados')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data=DB::table('consolidados')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
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
    /**********************************************************************************************/
    public function pdf_estimacion_ipress($can_id,$establecimiento_id,$tipo,$servicio_id,$id_user_resp)
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
            $table='estimacions';
            $condicion1='consolidado';
            $consolidado=$servicio_id;

            /*if($servicio_id==3){
                $consolidado=3;
            }
            else
            {
                $consolidado=4;   
            }*/
        }
        else
        {
            
            if($servicio_id=='F'){
                $consolidado=4;
                $condicion1='consolidado';
                $table='estimacions';
            }
            else{
                if($servicio_id=='A')
                {
                    $consolidado=3;
                    $condicion1='consolidado';
                    $table='estimacions';
                }
                else
                {   $table='estimacion_servicio';
                    $condicion1='servicio_id';
                    $consolidado=$servicio_id;        
                }
            }
        }    

        if($tipo==1){

            $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->orderby ('descripcion','asc')   
                    ->get();

            $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')   
                    ->orderby ('descripcion','asc')   
                    ->get();                    

                

                    $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$consolidado)
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

        if($nivel==1){
            if($num_estimaciones>0){
               
                if($servicio_id==3 || $servicio_id==4){
                    if($servicio_id==3){
                        $nombre_servicio="ALMACEN";
                    }else
                    {
                        if($servicio_id==4){
                            $nombre_servicio="FARMACIA";
                        }
                        else
                        {
                         $nombre_servicio="CONSOLIDADO";   
                        }
                    }
                }
                else
                {
                    $nombre_servicio=$data->get(0)->nombre_servicio;
                }
            }
            else
            {
                $servicios=DB::table('servicios')
                            ->where('id',$servicio_id)
                            ->get();
                $nombre_servicio=$servicios->get(0)->nombre_servicio;
            }
        }
        else
        {

            if($num_estimaciones>0){
                if($servicio_id=='A' || $servicio_id=='F'){ //Para nivel 2 y 3                
                    if($servicio_id=='A'){                
                        $nombre_servicio="ALMACEN";
                    }else
                    {
                        if($servicio_id=='F'){                  
                            $nombre_servicio="FARMACIA";
                        }
                        else
                        {
                         $nombre_servicio="CONSOLIDADO";   
                        }
                    }
                }
                else
                {
                    $nombre_servicio=$data->get(0)->nombre_servicio;
                }
            }
            else
            {
                if($servicio_id==0){
                    
                        $nombre_servicio="CONSOLIDADO";  
                    
                    
                 }
                 else
                    {
                        $servicios=DB::table('servicios')
                            ->where('id',$servicio_id)
                            ->get();
                        $nombre_servicio=$servicios->get(0)->nombre_servicio;
                     }

                
            }
        }
        $usuario=DB::table('users')
            ->where('id',$id_user_resp)
            ->get();

        /*if($servicio_id!=0){
            $usuario=DB::table('users')
            ->where('id',$user_id)
            //->where('servicio_id',$servicio_id)
            //->where('rol',2)
            ->get();
        }
        else
        {
            $usuario=DB::table('users')
            ->where('establecimiento_id',$establecimiento_id)
            //->where('servicio_id',$servicio_id)
            //->where('rol',2)
            ->get();

        }
        */
        $name=$usuario->get(0)->name;
        $cip=$usuario->get(0)->cip;
        $dni=$usuario->get(0)->dni;
        $cierre='2019-01-11 17:12:06';
        $texto='SERVICIO';
        
//        dd($name);

        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_servicio,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,
                      'can_id'=>$can_id]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
     }
    /***********************************************************************************************/
    public function pdf_estimacion_ipress2($can_id,$establecimiento_id,$tipo,$servicio_id,$id_user_resp)
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
                            ->orderby ('descripcion','asc')                    
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
                    $data=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->orderby ('tipo_dispositivo_id','asc')  
                            ->orderby ('descripcion','asc')  
                            ->get();

                    $num_estimaciones=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->count();

                    $descripcion_tipo='Medicamentos';
                }else
                    {   if ($tipo==2) {
                            $data=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->orderby ('tipo_dispositivo_id','asc')  
                            ->orderby ('descripcion','asc')  
                            ->get();                    

                            $num_estimaciones=DB::table('consolidados')
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

                    $data=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->orderby ('tipo_dispositivo_id','asc')
                            ->orderby('descripcion','asc')//cambiar desc
                            ->get();
                
                    $num_estimaciones=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->count();

                    $descripcion_tipo='Medicamentos';
                    //dd($establecimiento_id);
                }else
                    {   if ($tipo==2) {
                            $data=DB::table('consolidados')
                            ->select('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion'
                                                    )
                            ->groupby('tipo_dispositivo_id','petitorio_id','cod_petitorio','descripcion','cpma','stock','necesidad_anual','mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','justificacion')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1  
                            ->orderby ('tipo_dispositivo_id','asc')                  
                            ->orderby ('descripcion','asc')  
                            ->get();     

                            $num_estimaciones=DB::table('consolidados')
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

                $usuario=DB::table('users')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where( function ( $query )
                        {
                            $query->orWhere('rol',3)
                                ->orWhere('rol',4)
                                ->orWhere('rol',7);
                        })
                    ->get();

                //dd($usuario);

                $name=$usuario->get(0)->name;
                //$servicio_id=$usuario->get(0)->servicio_id;
                if($servicio_id==3)
                {
                    $nombre_rubro='ALMACEN';

                }
                else
                {
                    if($servicio_id==4)
                    {
                        $nombre_rubro='FARMACIA';
                    }
                    else
                    {
                        $nombre_rubro='FARMACIA/ALMACEN';
                    }                    
                }

                $user_id=$usuario->get(0)->id;
                $cip=$usuario->get(0)->cip;
                $dni=$usuario->get(0)->dni;

                $cierre_rubro=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
                $cierre=$cierre_rubro->get(0)->updated_at;
                
                $texto='RUBRO';

            }
            else
            {

                if($tipo==1){
                    $data=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->orderby ('tipo_dispositivo_id','asc')  
                            ->orderby ('descripcion','asc')  
                            ->get();

                    $num_estimaciones=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->count();

                    $descripcion_tipo='Medicamentos';
                }else
                    {   if ($tipo==2) {
                            $data=DB::table('consolidados')
                            ->where('necesidad_anual','>',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->orderby ('tipo_dispositivo_id','asc')  
                            ->orderby ('descripcion','asc')  
                            ->get();                    

                            $num_estimaciones=DB::table('consolidados')
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
        
        $usuario=DB::table('users')
            ->where('id',$id_user_resp)
            ->get();

        /*if($servicio_id!=0){
            $usuario=DB::table('users')
            ->where('id',$user_id)
            //->where('servicio_id',$servicio_id)
            //->where('rol',2)
            ->get();
        }
        else
        {
            $usuario=DB::table('users')
            ->where('establecimiento_id',$establecimiento_id)
            //->where('servicio_id',$servicio_id)
            //->where('rol',2)
            ->get();

        }
        */
        $name=$usuario->get(0)->name;
//        dd($name);

        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,
                      'can_id'=>$can_id]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
     }
    
}
