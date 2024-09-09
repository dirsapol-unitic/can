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

class ResponsableRedController extends AppBaseController
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
        $cans = DB::table('cans')
                ->join('mes', 'mes.id','cans.mes_id')
                ->join('years', 'years.id','cans.year_id')
                ->orderby('cans.id','desc')
                ->get();

        return view('site.responsable_red.index')
                ->with('nombre_establecimiento', $nombre_establecimiento)
                ->with('cans', $cans);
    }

    public function listar_red($can_id)
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

        $establecimientos = DB::table('establecimientos')
                                ->where('region_id',$establecimiento->region_id)
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
        

            return view('site.responsable_red.listar_red')
                    ->with('establecimientos', $establecimientos)
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

    public function listar_distribucion($can_id,$establecimiento_id)
    {
        //Verifico de que establecimiento es el usuario
        //$establecimiento_id=Auth::user()->establecimiento_id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;

        if($nivel_id==1){
            $distribucions = DB::table('distribucions')->where('establecimiento_id',$establecimiento_id)->get();
            //Buscar si existen medicamentos asignados
            $items_medicamentos = DB::table('distribucion_petitorio')
                                            ->select('distribucion_id as condicion', 
                                                DB::raw('COUNT(tipo_dispositivo_medico_id) as cantidad')
                                                )
                                            ->groupby('distribucion_id')
                                            ->where('tipo_dispositivo_medico_id',1)
                                            ->get();
            //Buscar si existen dispositivos asignados                                            
            $items_dispositivos = DB::table('distribucion_petitorio' )
                                            ->select('distribucion_id as condicion', 
                                                DB::raw('COUNT(tipo_dispositivo_medico_id) as cantidad')
                                                )
                                            ->groupby('distribucion_id')
                                            ->where('tipo_dispositivo_medico_id','>',1)
                                            ->get();
            
        }
        else
        {
            $distribucions = DB::table('servicios')->where('establecimiento_id',$establecimiento_id)->get();    
            $items_medicamentos = DB::table('petitorio_servicio')
                                            ->select('servicio_id as condicion', 
                                                DB::raw('COUNT(tipo_dispositivo_medico_id) as cantidad')
                                                )
                                            ->groupby('servicio_id')
                                            ->where('tipo_dispositivo_medico_id',1)
                                            ->get();
            $items_dispositivos = DB::table('petitorio_servicio' )
                                            ->select('servicio_id as condicion', 
                                                DB::raw('COUNT(tipo_dispositivo_medico_id) as cantidad')
                                                )
                                            ->groupby('servicio_id')
                                            ->where('tipo_dispositivo_medico_id','>',1)
                                            ->get();
        }    
        
        $cans = DB::table('cans')
                ->join('years', 'years.id','cans.year_id')
                ->where('cans.id', $can_id)
                ->get();  
        $ano=$cans->get(0)->ano;

        $responsables= DB::table('responsables')->where('can_id',$can_id)->where('servicio_id','>',0)->get();
        

            return view('site.responsable_red.listar_distribucion')
                    ->with('distribucions', $distribucions)
                    ->with('responsables', $responsables)
                    ->with('can_id', $can_id)
                    ->with('ano', $ano)
                    ->with('items_dispositivos', $items_dispositivos)
                    ->with('items_medicamentos', $items_medicamentos)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nivel_id', $nivel_id);        
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
                
            return view('site.responsable_red.medicamentos.medicamentos')
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
            return view('site.responsable_red.medicamentos.medicamentos')
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
}
