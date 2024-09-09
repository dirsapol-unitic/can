<?php
namespace App\Http\Controllers\Admin;
use App\Http\Requests\CreateCanRequest;
use App\Http\Requests\UpdateCanRequest;
use App\Repositories\CanRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use DB;
use Carbon\Carbon;
use App\Models\Establecimiento;
use App\Models\Indicador;
use App\Models\Estimacion;
use App\Models\Petitorio;
use App\Models\Can;
use App\Models\Servicio;
use App\Models\Rubro;
use App\Models\Year;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Auth;
use Response;
use Excel;
use PHPExcel_Worksheet_Drawing;

class CanController extends AppBaseController
{
    private $canRepository;
    public function __construct(CanRepository $canRepo)
    {
        $this->canRepository = $canRepo;
    }

    public function index(Request $request)
    {
        if (Auth::user()->rol == 1 || Auth::user()->rol == 11){
            $this->canRepository->pushCriteria(new RequestCriteria($request));
            $cans = $this->canRepository->orderby('id','desc')->all();        
            //dd($cans);
            return view('admin.cans.index')
                ->with('cans', $cans);
        }
        else
        {
            return redirect(route('estimacion.index'));
        }
    }

    public function create()
    {   
        $valor=1; //creacion 1 edcanon 2
        $mes = DB::table('mes')->pluck('descripcion','id');
        $ano = DB::table('years')->where('estado','=',1)->pluck('descripcion','id');
        $establecimientos=Establecimiento::pluck('nombre_establecimiento','id');
        return view('admin.cans.create',compact(["mes"],["ano"],["establecimientos"],["valor"]));
    }

    public function store(CreateCanRequest $request)
    {
        $year=Year::find($request->year_id);
        $ano=$year->descripcion;
        $model_can = new Can();
        $repetido = $model_can->BuscaCanRepetido($request->year_id,$request->mes_id);
        $id_user = Auth::user()->id;

        if ($repetido->total>0) {
            Flash::error('Ya se encuentra registrado');            
        }
        else
        {
            //Escogemos el mes
            switch ($request->mes_id) {
                case '1':$meses='Enero';break; case '2':$meses='Febrero';break; case '3':$meses='Marzo';break;
                case '4':$meses='Abril';break; case '5':$meses='Mayo';break; case '6':$meses='Junio';break;
                case '7':$meses='Julio';break; case '8':$meses='Agosto';break; case '9':$meses='Setiembre';break;
                case '10':$meses='Octubre';break; case '11':$meses='Noviembre';break; case '12':$meses='Dcanembre';break;
            }

            
            if($request->multianual==null):
                $multianual = 0;
            else:
                $multianual = 1;
            endif;

            $id_can = $model_can->RegistrarCan($request->mes_id, $meses, $ano, $request->nombre_can, $multianual, $request->tiempo, 0, $id_user, $request->year_id);
            
            Flash::success('Guardado correctamente.');
        }    

        return redirect(route('cans.index'));
    }

    public function show($id)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        $consulta = DB::table('can_establecimiento')                
                ->join('establecimientos', 'establecimientos.id', 'can_establecimiento.establecimiento_id')
                ->join('cans', 'cans.id', 'can_establecimiento.can_id')          
                ->where('cans.id', $id) 
                ->orderby('establecimientos.id','asc')
                ->get();

        $login=DB::table('users')
                ->where('rol',7)
                ->where('estado',1)
                ->orderby('establecimiento_id','asc')
                ->get();

        $rol=Auth::user()->rol;
        

        $consulta2 = DB::table('archivos')                
                ->select(DB::raw('count(*) as contar, establecimiento_id'))
                ->join('cans', 'cans.id', 'archivos.can_id')
                ->where('cans.id', $id)
                ->groupby('establecimiento_id')
                ->get();
        
        $establecimientos=Establecimiento::pluck('nombre_establecimiento','id');
        //dd($consulta);
        return view('admin.cans.show')->with('can', $can)
                                      ->with('establecimientos', $establecimientos)
                                      ->with('consulta', $consulta)
                                      ->with('login', $login)
                                      ->with('rol', $rol)
                                      ->with('consulta2', $consulta2);

    }

    public function show_medicamentos($id)
    {
        $estimacion = Estimacion::findOrFail($id); //estimacion_servicio
        
        $petitorio_id=($estimacion->petitorio_id);
        $establecimiento_id=($estimacion->establecimiento_id);
        
        $estimaciones = DB::table('consolidados as A')
                    ->select('B.*')
                    ->addselect('C.name as nombre')
                    ->join('estimacion_servicio as B', 'A.establecimiento_id','B.establecimiento_id')
                    ->join('users as C', 'C.servicio_id','B.servicio_id')
                    ->where('B.establecimiento_id',$establecimiento_id)
                    ->where('B.petitorio_id',$petitorio_id)
                    ->where('C.establecimiento_id',$establecimiento_id)
                    ->where('C.rol',2)
                    ->distinct()
                    ->get();
        
        $descripcionproducto=($estimacion->descripcion);
        return view('admin.cans.medicamentos.mostrar_datos')->with('estimaciones', $estimaciones)->with('descripcionproducto',$descripcionproducto);     
    }

    public function show_medicamentos_nivel_1($id,$opcion,$can_id)
    {
        $estimaciones = DB::table('estimacions as A')
                    ->select('A.*')
                    ->join('establecimientos as B', 'A.establecimiento_id','B.id')
                    ->where('A.petitorio_id',$id)
                    ->where('A.can_id',$can_id)
                    ->where('A.necesidad_anual','>',0)
                    ->where('A.estado','<>',2)
                    ->where('B.nivel_id',1)
                    ->distinct()
                    ->get();
        
        //dd($estimaciones);

        $descripcionproducto=($estimaciones->get(0)->descripcion);
        return view('admin.cans.mostrar_datos_nivel_1')->with('estimaciones', $estimaciones)->with('descripcionproducto',$descripcionproducto)->with('opcion',$opcion);   
    }

    public function show_medicamentos_nivel_2($id,$opcion,$can_id)
    {
        /*
        si va
        $estimaciones = DB::table('estimacions as A')
                    ->select('B.*')
                    ->join('establecimientos as D', 'A.establecimiento_id','D.id')
                    ->join('estimacion_servicio as B', 'A.establecimiento_id','B.establecimiento_id')
                    ->join('users as C', 'C.servicio_id','B.servicio_id')
                    ->where('B.petitorio_id',$id)
                    ->where('A.necesidad_anual','>',0)
                    ->where('D.nivel_id',2)
                    ->distinct()
                    ->orderby('B.nombre_establecimiento')
                    ->orderby('B.nombre_servicio')
                    ->get();
        */

        $estimaciones = DB::table('estimacion_servicio as B')
                    ->select('B.*')
                    ->join('establecimientos as D', 'B.establecimiento_id','D.id')
                    ->where('B.petitorio_id',$id)
                    ->where('B.can_id',$can_id)
                    ->where('B.necesidad_anual','>',0)
                    ->where('D.nivel_id',2)
                    ->where('B.estado','<>',2)
                    ->distinct()
                    ->orderby('B.nombre_establecimiento')
                    ->orderby('B.nombre_servicio')
                    ->get();

        
        $descripcionproducto=($estimaciones->get(0)->descripcion);
        return view('admin.cans.mostrar_datos_nivel_2')->with('estimaciones', $estimaciones)->with('descripcionproducto',$descripcionproducto)->with('opcion',$opcion);        
    }

    public function show_medicamentos_ajuste_nivel_2($id)
    {
        $estimaciones = DB::table('estimacions')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad_anual,estimacions.descripcion,estimacions.establecimiento_id, establecimientos.nombre_establecimiento'))
                    ->join('establecimientos', 'estimacions.establecimiento_id','establecimientos.id')
                    ->where('establecimientos.nivel_id',2)
                    ->where('estimacions.petitorio_id',$id)
                    ->where('estimacions.necesidad_anual','>',0)
                    ->orderby('estimacions.establecimiento_id')
                    ->groupby('estimacions.descripcion','estimacions.establecimiento_id','establecimientos.nombre_establecimiento')
                    ->get();

        $estimaciones1 = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad_anual,estimacion_servicio.descripcion,estimacion_servicio.establecimiento_id, establecimientos.nombre_establecimiento'))
                    ->join('establecimientos', 'estimacion_servicio.establecimiento_id','establecimientos.id')
                    ->where('establecimientos.nivel_id',2)
                    ->where('estimacion_servicio.petitorio_id',$id)
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->orderby('estimacion_servicio.establecimiento_id')
                    ->groupby('estimacion_servicio.descripcion','estimacion_servicio.establecimiento_id','establecimientos.nombre_establecimiento')
                    ->get();

            for($i=0;$i<5;$i++){
                for($j=0;$j<4;$j++){
                    $productos[$i][$j]=0;
                }
            }        

        $m=0;

        foreach ($estimaciones as $key => $value) {
            switch ($value->establecimiento_id) {
                            case 2:
                                    $productos[0][0]=$value->necesidad_anual;
                                    $productos[0][1]=$value->descripcion;
                                    $productos[0][2]=$value->nombre_establecimiento;    
                                break;
                            case 3:
                                    $productos[1][0]=$value->necesidad_anual;
                                    $productos[1][1]=$value->descripcion;
                                    $productos[1][2]=$value->nombre_establecimiento;    
                                
                                break;
                            case 30:
                                    $productos[2][0]=$value->necesidad_anual;
                                    $productos[2][1]=$value->descripcion;
                                    $productos[2][2]=$value->nombre_establecimiento;    
                                
                                break;
                            case 69:
                                    $productos[3][0]=$value->necesidad_anual;
                                    $productos[3][1]=$value->descripcion;
                                    $productos[3][2]=$value->nombre_establecimiento;    
                                
                                break;
                            
                }            
            $m++;

        }

        $k=0;
        foreach ($estimaciones1 as $key => $value) {
            switch ($value->establecimiento_id) {
                            case 2:
                                    $productos[0][3]=$value->necesidad_anual;
                                    $productos[0][2]=$value->nombre_establecimiento;  
                                    
                                break;
                            case 3:
                                    $productos[1][3]=$value->necesidad_anual;
                                    $productos[1][2]=$value->nombre_establecimiento;  
                                
                                break;
                            case 30:
                                    $productos[2][3]=$value->necesidad_anual;
                                    $productos[2][2]=$value->nombre_establecimiento;  
                                
                                break;
                            case 69:
                                    $productos[3][3]=$value->necesidad_anual;
                                    $productos[3][2]=$value->nombre_establecimiento;  
                                
                                break;
                            
                }            
            $k++;
        }

        if($k>$m){
            $k=$m;
        }
        
        //dd($productos);
      

        $descripcionproducto=($estimaciones->get(0)->descripcion);
        return view('admin.cans.mostrar_datos_ajuste_nivel_2')->with('estimaciones', $productos)->with('descripcionproducto',$descripcionproducto)->with('k',$k);     
        
    }

    public function show_medicamentos_nivel_3($id, $opcion,$can_id)
    {
        /*$estimacion_encontrado = DB::table('estimacions as A')
                    ->select('B.*')
                    ->join('establecimientos as D', 'A.establecimiento_id','D.id')
                    ->join('estimacion_servicio as B', 'A.establecimiento_id','B.establecimiento_id')
                    ->join('users as C', 'C.servicio_id','B.servicio_id')
                    ->where('B.petitorio_id',$id)
                    ->where('A.necesidad_anual','>',0)
                    ->where('D.nivel_id',3)
                    ->distinct()
                    ->orderby('B.nombre_establecimiento')
                    ->orderby('B.nombre_servicio')
                    ->count();
        */
        $estimaciones = DB::table('estimacion_servicio as B')
                    ->select('B.*')
                    ->join('establecimientos as D', 'B.establecimiento_id','D.id')
                    ->where('B.petitorio_id',$id)
                    ->where('B.can_id',$can_id)
                    ->where('B.necesidad_anual','>',0)
                    ->where('D.nivel_id',3)
                    ->where('B.estado','<>',2)
                    ->distinct()
                    ->orderby('B.nombre_establecimiento')
                    ->orderby('B.nombre_servicio')
                    ->get();
        if(count($estimaciones)>0)
            $descripcionproducto=($estimaciones->get(0)->descripcion);
        else
            $descripcionproducto='NO ENCONTRADO';

        return view('admin.cans.mostrar_datos_nivel_3')->with('estimaciones', $estimaciones)->with('descripcionproducto',$descripcionproducto)->with('opcion',$opcion);       
    }

    public function establecimientos_can_2020($id)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<81;$i++){
            for($j=0;$j<20;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        $contar_nivel = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,establecimientos.id, establecimientos.nombre_establecimiento, count(*) as cantidad,estimacions.tipo_dispositivo_id'))
                ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacions.tipo_dispositivo_id')
                ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id')
                ->where('estimacions.can_id', $id)
                ->where('estimacions.necesidad_anual', '>',0)
                ->where('estimacions.estado', '<>',2)
                ->groupby('establecimientos.id','establecimientos.nombre_establecimiento','estimacions.tipo_dispositivo_id')
                ->count();

        
        if($contar_nivel>0){
            $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,establecimientos.id, establecimientos.nombre_establecimiento, count(*) as cantidad,estimacions.tipo_dispositivo_id,tipo_dispositivo_medicos.descripcion'))
                ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacions.tipo_dispositivo_id')
                ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id')
                ->where('estimacions.can_id', $id)
                ->where('estimacions.necesidad_anual', '>',0)
                ->where('estimacions.estado', '<>',2)
                ->groupby('establecimientos.id','establecimientos.nombre_establecimiento','estimacions.tipo_dispositivo_id','tipo_dispositivo_medicos.descripcion')
                ->orderby('establecimientos.id')
                ->orderby('estimacions.tipo_dispositivo_id')
                ->get();

            //dd($consulta);

            $tipo_dispositivo_x = DB::table('tipo_dispositivo_medicos')
                                      ->orderby('id','asc')
                                      ->get();
            $i=0;

            foreach ($tipo_dispositivo_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->descripcion;   
                $i++;
            }
            //$fila_anterior=0; $x=-1; $y=0;
            $fila_anterior=0; $x=0; $y=0;
            
            foreach ($consulta as $key => $value) {
                # code...
                $fila=$value->id-1;
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }
                //$m=$i*2;
                $k=0; $n=0;
                while ($k<$i){
                    if($value->tipo_dispositivo_id==$descripcion[$k][0]){
                        $n=$k*2;
                        $can_productos[$x][$n]=$value->necesidad;
                        $p=$n+1;
                        $can_productos[$x][$p]=$value->cantidad;
                        $can_productos[$x][20]=$value->nombre_establecimiento;
                    }
                    $k++;
                }
            }
        }
        else
        {
            $x=0;
        }
        
        $x=$x+1;

        return view('admin.cans.establecimientos_cans')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('descripcion', $descripcion);        
    }
    
    public function establecimientos_servicio_can($id,$tipo)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<3205;$i++){
            for($j=0;$j<420;$j++){ //total servicios
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        if($tipo==1){
            $contar_nivel = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio'))
                ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                ->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                ->join('servicios', function($join)
                    {
                        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                    })
                ->join('petitorio_servicio', function($join)
                    {
                        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                    })
                ->join('petitorios', function($join)
                    {
                        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                    })
                ->where('estimacion_servicio.can_id',$id)
                ->where('estimacion_servicio.estado','<>',2)
                ->where('estimacion_servicio.tipo_dispositivo_id',1)
                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio')
                ->orderby('estimacion_servicio.petitorio_id')
                ->count();
        }
        else
        {
            $contar_nivel = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio'))
                ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                ->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                ->join('servicios', function($join)
                    {
                        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                    })
                ->join('petitorio_servicio', function($join)
                    {
                        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                    })
                ->join('petitorios', function($join)
                    {
                        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                    })
                ->where('estimacion_servicio.can_id',$id)
                ->where('estimacion_servicio.estado','<>',2)
                ->where('estimacion_servicio.tipo_dispositivo_id','>',1)
                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio')
                ->orderby('estimacion_servicio.petitorio_id')
                ->count();
        }
        
        if($contar_nivel>0){
            if($tipo==1){
                /*
                $consulta = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id'))
                ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                ->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                ->join('servicios', function($join)
                    {
                        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                    })
                ->join('petitorio_servicio', function($join)
                    {
                        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                    })
                ->join('petitorios', function($join)
                    {
                        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                    })
                ->where('estimacion_servicio.can_id',$id)
                ->where('estimacion_servicio.estado','<>',2)
                ->where('estimacion_servicio.tipo_dispositivo_id',1)
                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', 'servicios.id')
                ->orderby('estimacion_servicio.petitorio_id','asc')
                ->orderby('servicios.id','asc')
                ->get();

                */

                $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  petitorio_id,descripcion, nombre_servicio,servicio_id,establecimiento_id, cod_petitorio'))
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)                                      
                    ->where('estimacion_servicio.estado','<>',2)
                    ->where('estimacion_servicio.tipo_dispositivo_id',1)
                    
                    ->groupby('petitorio_id','descripcion','nombre_servicio', 'servicio_id', 'establecimiento_id', 'cod_petitorio')
                    ->orderby('petitorio_id','asc')
                    ->orderby('servicio_id','asc')
                    ->get();

                $descripcion_tipo="MEDICAMENTOS";
                
            }
            else
            {
                /*
                $consulta = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id'))
                ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                ->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                ->join('servicios', function($join)
                    {
                        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                    })
                ->join('petitorio_servicio', function($join)
                    {
                        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                    })
                ->join('petitorios', function($join)
                    {
                        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                    })
                ->where('estimacion_servicio.can_id',$id)
                ->where('estimacion_servicio.estado','<>',2)
                //->where('estimacion_servicio.petitorio_id',284)
                ->where('estimacion_servicio.tipo_dispositivo_id','>',1)
                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', 'servicios.id')
                ->orderby('estimacion_servicio.petitorio_id','asc')
                ->orderby('servicios.id','asc')
                ->get();

                */

                $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  petitorio_id,descripcion, nombre_servicio,servicio_id,establecimiento_id, cod_petitorio'))
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)                                      
                    ->where('estimacion_servicio.estado','<>',2)
                    ->where('estimacion_servicio.tipo_dispositivo_id','>',1)
                    
                    ->groupby('petitorio_id','descripcion','nombre_servicio', 'servicio_id', 'establecimiento_id', 'cod_petitorio')
                    ->orderby('petitorio_id','asc')
                    ->orderby('servicio_id','asc')
                    ->get();

                $descripcion_tipo="DISPOSITIVOS";
            }
            
            $servicios_x = DB::table('servicios')
                               ->orderby('servicios.id','asc')
                               ->get();
            $i=0;

            foreach ($servicios_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_servicio;                      
                $i++;
            }
            

            $fila_anterior=5000; $x=-1; $y=0; $z=0;
            
            foreach ($consulta as $key => $value) {
                
                $fila=$value->petitorio_id;
                //dd($x);
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }
                
                for($k=0;$k<$i;$k++){
                    if($value->servicio_id==$descripcion[$k][0]){
                        switch ($value->establecimiento_id) {
                            case 1: $valor=0;break; //lns
                            case 2: $valor=1;break; //abl
                            case 3: $valor=2;break; //sj
                            case 30: $valor=3;break; //cix
                            case 69: $valor=4;break; //aqa
                        }                        
                        //$calculo=($value->servicio_id-2)*5;  //175
                        $calculo=$k*5;  //175
                        $m=($calculo+$valor);                        
                        $can_productos[$x][$m]=$value->necesidad;
                        $can_productos[$x][419]=$value->descripcion;
                        $can_productos[$x][418]=$can_productos[$x][418]+$can_productos[$x][$m];
                    }
                }
                $y++;
            }
            $x++;
        }
        else
        {
            $x=0;
            $descripcion_tipo="MEDICAMENTOS";
        }
        
        

        $total_servicios=$i*5;
        //dd($total_servicios);

        return view('admin.cans.rubro_show')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('y', $total_servicios)
                                      ->with('descripcion_tipo', $descripcion_tipo)
                                      ->with('descripcion', $descripcion);
    }

    public function establecimientos_servicio_can_tipo($id,$tipo)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<3205;$i++){
            for($j=0;$j<420;$j++){ //total servicios
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        
       /* $consulta = DB::table('estimacion_servicio')
        ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id'))
        ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
        ->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
        ->join('servicios', function($join)
            {
                $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                     ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
            })
        ->join('petitorio_servicio', function($join)
            {
                $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                     ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
            })
        ->join('petitorios', function($join)
            {
                $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                     ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
            })
        ->where('estimacion_servicio.can_id',$id)
        ->where('estimacion_servicio.estado','<>',2)
        ->where('estimacion_servicio.tipo_dispositivo_id',$tipo)
        ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', 'servicios.id')
        ->orderby('estimacion_servicio.petitorio_id','asc')
        ->orderby('servicios.id','asc')
        ->get();*/

        $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  petitorio_id,descripcion, nombre_servicio,servicio_id,establecimiento_id, cod_petitorio'))
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)                                      
                    ->where('estimacion_servicio.estado','<>',2)
                    ->where('estimacion_servicio.tipo_dispositivo_id',$tipo)
                    
                    ->groupby('petitorio_id','descripcion','nombre_servicio', 'servicio_id', 'establecimiento_id', 'cod_petitorio')
                    ->orderby('petitorio_id','asc')
                    ->orderby('servicio_id','asc')
                    ->get();
        
        switch ($tipo) {
            case 2: $descripcion_tipo="MATERIAL BIOMEDICO"; break;
            case 3: $descripcion_tipo="INSTRUMENTAL QUIRURGICO"; break;
            case 4: $descripcion_tipo="MATERIAL E INSUMO ODONTOLOGICO"; break;
            case 5: $descripcion_tipo="INSUMO DE LABORATORIO"; break;
            case 6: $descripcion_tipo="MATERIAL FOTOGRAFICO Y FONOTECNICO"; break;
            case 7: $descripcion_tipo="PRODUCTOS AFINES"; break;
            case 9: $descripcion_tipo="DISPOSITIVO MEDICO DE USO RESTRINGIDO"; break;
            case 10: $descripcion_tipo="MATERIAL DE LABORATORIO"; break;
        }
                
        
            
            $servicios_x = DB::table('servicios')
                               ->orderby('servicios.id','asc')
                               ->get();
            $i=0;

            foreach ($servicios_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_servicio;                   
                $i++;
            }
            //dd($consulta->get(0)); //47
            $fila_anterior=5000; $x=-1; $y=0; $z=0;
            
            foreach ($consulta as $key => $value) {
                
                $fila=$value->petitorio_id;
                //dd($x);
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }
                
                for($k=0;$k<$i;$k++){
                    if($value->servicio_id==$descripcion[$k][0]){
                        switch ($value->establecimiento_id) {
                            case 1: $valor=0;break; //lns
                            case 2: $valor=1;break; //abl
                            case 3: $valor=2;break; //sj
                            case 30: $valor=3;break; //cix
                            case 69: $valor=4;break; //aqa
                        }                        
                        //$calculo=($value->servicio_id-2)*5;  //175
                        $calculo=$k*5;  //175
                        $m=($calculo+$valor);                        
                        $can_productos[$x][$m]=$value->necesidad;
                        $can_productos[$x][419]=$value->descripcion;
                        $can_productos[$x][418]=$can_productos[$x][418]+$can_productos[$x][$m];
                    }
                }
                $y++;
            }
            $x++;
        
        
        $total_servicios=$i*5;
        
        return view('admin.cans.rubro_show')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('y', $total_servicios)
                                      ->with('descripcion_tipo', $descripcion_tipo)
                                      ->with('descripcion', $descripcion);
    }

    public function consolidado_nacional($id,$tipo)
    {
        
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<3205;$i++){
            for($j=0;$j<49;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        if($tipo==1){
            $contar_nivel = DB::table('consolidados')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  sum(cpma) as cpma, sum(stock) as stock,sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9, sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,consolidados.petitorio_id,consolidados.descripcion'))
                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                ->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                ->where('consolidados.can_id',$id)
                ->where('consolidados.tipo_dispositivo_id',1)
                ->where('necesidad_anual','>',0)
                ->groupby('consolidados.petitorio_id','consolidados.descripcion')
                ->orderby('consolidados.petitorio_id')
                ->count();
        }
        else
        {
            $contar_nivel = DB::table('consolidados')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  sum(cpma) as cpma, sum(stock) as stock,sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9, sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,consolidados.petitorio_id,consolidados.descripcion'))
                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                ->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                ->where('consolidados.can_id',$id)
                ->where('consolidados.tipo_dispositivo_id','>',1)
                ->where('necesidad_anual','>',0)
                ->groupby('consolidados.petitorio_id','consolidados.descripcion')
                ->orderby('consolidados.petitorio_id')
                ->count();
        }
        
        if($contar_nivel>0){
            if($tipo==1){
                $consulta = DB::table('consolidados')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  sum(cpma) as cpma, sum(stock) as stock,sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9, sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,consolidados.petitorio_id,consolidados.descripcion'))
                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                ->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                ->where('consolidados.can_id',$id)
                ->where('consolidados.tipo_dispositivo_id',1)
                ->where('necesidad_anual','>',0)
                ->groupby('consolidados.petitorio_id','consolidados.descripcion')
                ->orderby('consolidados.petitorio_id')
                ->get();
                $descripcion_tipo="MEDICAMENTOS";
            }
            else
            {
                $consulta = DB::table('consolidados')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  sum(cpma) as cpma, sum(stock) as stock,sum(mes1) as mes1,sum(mes2) as mes2,sum(mes3) as mes3,sum(mes4) as mes4,sum(mes5) as mes5,sum(mes6) as mes6,sum(mes7) as mes7,sum(mes8) as mes8,sum(mes9) as mes9, sum(mes10) as mes10,sum(mes11) as mes11,sum(mes12) as mes12,consolidados.petitorio_id,consolidados.descripcion'))
                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                ->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                ->where('consolidados.can_id',$id)
                ->where('consolidados.tipo_dispositivo_id','>',1)
                ->where('necesidad_anual','>',0)
                ->groupby('consolidados.petitorio_id','consolidados.descripcion')
                ->orderby('consolidados.petitorio_id')
                ->get();
                $descripcion_tipo="DISPOSITIVOS";
            }
        }
        
        return view('admin.cans.consolidado_nacional')->with('can', $can)->with('descripcion_tipo', $descripcion_tipo)->with('estimaciones', $consulta);
                                      
    }

    

public function consolidado_nacional_tipo($id,$tipo)
    {
       
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
    
    //    SI VA - SOLO LO QUITE PARA HACER MAS RAPIDA LA CARGA
        for($i=0;$i<3205;$i++){
            for($j=0;$j<15;$j++){
                $productos[$i][$j]=0;
            }
        }        
        
        if($id<5){
            $consulta = DB::table('petitorio_antes')
            ->where('tipo_dispositivo_medicos_id',$tipo)
            ->orderby('id')
            ->get();    
        }
        else
        {
            $consulta = DB::table('petitorios')
            ->where('tipo_dispositivo_medicos_id',$tipo)
            ->orderby('id')
            ->get();   
        }
        

        $k=0;

        foreach ($consulta as $key => $value) {

            $contar_nivel_1 = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('necesidad_anual','>',0)
                ->where('estado','<>',2)
                ->where('petitorio_id',$value->id)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [4, 29])
                                ->orwhereBetween('establecimiento_id', [31, 68])
                                ->orWhere('establecimiento_id','>',69);
                        })
                ->groupby('petitorio_id')
                ->count();

            //dd($contar_nivel_1);

            if($contar_nivel_1>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1, sum(necesidad_actual) as necesidad2,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('estado','<>',2)
                ->where('necesidad_anual','>',0)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [4, 29])
                                ->orwhereBetween('establecimiento_id', [31, 68])
                                ->orWhere('establecimiento_id','>',69);
                        })
                ->groupby('petitorio_id')
                ->get();

                $productos[$k][3]=$consulta->get(0)->necesidad1;
                $productos[$k][11]=$consulta->get(0)->necesidad2;
                
            }


            $contar_nivel_2 = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)                
                ->where('estado','<>',2)
                ->where('establecimiento_id','<>',1)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_2>0){
                $consulta = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1, sum(necesidad_actual) as necesidad2,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id','<>',1)
                ->where('necesidad_anual','>',0)                
                ->where('estado','<>',2)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][4]=$consulta->get(0)->necesidad1;
                $productos[$k][9]=$consulta->get(0)->necesidad2;
            }
            
            /*$contar_ajuste_nivel_2 = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('estado','<>',2)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [2, 3])
                                  ->orWhere('establecimiento_id',30)
                                  ->orWhere('establecimiento_id',69);
                        })
                ->groupby('petitorio_id')
                ->count();

            //dd($contar_nivel_1);

            if($contar_ajuste_nivel_2>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('estado','<>',2)
                ->where('petitorio_id',$value->id)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [2, 3])
                                  ->orWhere('establecimiento_id',30)
                                  ->orWhere('establecimiento_id',69);
                        })
                ->groupby('petitorio_id')
                ->get();

                $productos[$k][9]=$consulta->get(0)->necesidad1;
            }
            */
            $contar_nivel_3 = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('necesidad_anual','>',0)
                ->where('estado','<>',2)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_3>0){
                $consulta = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1, sum(necesidad_actual) as necesidad2,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('necesidad_anual','>',0)
                ->where('estado','<>',2)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][5]=$consulta->get(0)->necesidad1;
                $productos[$k][10]=$consulta->get(0)->necesidad2;
            }

            /*$contar_ajuste_nivel_3 = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('estado','<>',2)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->count();

            //dd($contar_nivel_1);

            if($contar_ajuste_nivel_3>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,sum(cpma) as cpma,sum(stock) as stock,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('necesidad_anual','>',0)
                ->where('estado','<>',2)
                ->groupby('petitorio_id')
                ->get();

                $productos[$k][10]=$consulta->get(0)->necesidad1;
            }
            */
            $contar_nivel_total = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)
                ->where('estado','<>',2)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_total>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('necesidad_anual','>',0)
                ->where('petitorio_id',$value->id)
                ->where('estado','<>',2)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][7]=$consulta->get(0)->necesidad1;
            }

            $contar_nivel_consolidado = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)
                ->where('estado','<>',2)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_consolidado>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('estado','<>',2)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][8]=$consulta->get(0)->necesidad1;
            }

            if($contar_nivel_1>0 || $contar_nivel_2>0 || $contar_nivel_3>0 || $contar_nivel_total>0 || $contar_nivel_consolidado>0){
                $productos[$k][0]=$value->id;
                $productos[$k][1]=$value->codigo_petitorio;
                $productos[$k][2]=$value->descripcion;
                $productos[$k][6]=$productos[$k][3]+$productos[$k][4]+$productos[$k][5];
                $productos[$k][13]=$value->precio;
                $k++;                
            }
        }

        $m=$k;
        
        switch ($tipo) {
            case 1: $descripcion_tipo="MEDICAMENTOS"; break;
            case 2: $descripcion_tipo="MATERIAL BIOMEDICO"; break;
            case 3: $descripcion_tipo="INSTRUMENTAL QUIRURGICO"; break;
            case 4: $descripcion_tipo="MATERIAL E INSUMO ODONTOLOGICO"; break;
            case 5: $descripcion_tipo="INSUMO DE LABORATORIO"; break;
            case 6: $descripcion_tipo="MATERIAL FOTOGRAFICO Y FONOTECNICO"; break;
            case 7: $descripcion_tipo="PRODUCTOS AFINES"; break;
            case 8: $descripcion_tipo="NN"; break;
            case 9: $descripcion_tipo="DISPOSITIVO MEDICO DE USO RESTRINGIDO"; break;
            case 10: $descripcion_tipo="MATERIAL DE LABORATORIO"; break;
            case 11: if($id>5){ $descripcion_tipo="INSTRUMENTAL TRAUMATOLOGIA";} break;
            case 12: if($id>5){ $descripcion_tipo="MATERIAL TRAUMATOLOGIA";} break;
        }
    
        
    return view('admin.cans.consolidado_nacional')->with('can', $can)->with('descripcion_tipo', $descripcion_tipo)->with('estimaciones', $consulta)->with('productos', $productos)->with('k', $k)->with('can_id',$id);
    
    //return view('admin.cans.consolidado_nacional2')->with('can', $can)->with('descripcion_tipo', $descripcion_tipo)->with('estimacions', $consulta);

        //return view('admin.cans.index')->with('cans', $cans);
        
                                      
    }

public function consolidado_nacional_tipo2($id,$tipo)
    {
       
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
    
    //    SI VA - SOLO LO QUITE PARA HACER MAS RAPIDA LA CARGA
        for($i=0;$i<3205;$i++){
            for($j=0;$j<15;$j++){
                $productos[$i][$j]=0;
            }
        }        
        if($id<5){
            $consulta = DB::table('petitorio_antes')
            ->where('tipo_dispositivo_medicos_id',$tipo)
            ->orderby('id')
            ->get();
        }
        else{
            $consulta = DB::table('petitorios')
            ->where('tipo_dispositivo_medicos_id',$tipo)
            ->orderby('id')
            ->get();
        }

        $k=0;

        foreach ($consulta as $key => $value) {

            $contar_nivel_1 = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('necesidad_anual','>',0)
                ->where('petitorio_id',$value->id)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [4, 29])
                                ->orwhereBetween('establecimiento_id', [31, 68])
                                ->orWhere('establecimiento_id','>',69);
                        })
                ->groupby('petitorio_id')
                ->count();

            //dd($contar_nivel_1);

            if($contar_nivel_1>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [4, 29])
                                ->orwhereBetween('establecimiento_id', [31, 68])
                                ->orWhere('establecimiento_id','>',69);
                        })
                ->groupby('petitorio_id')
                ->get();

                $productos[$k][3]=$consulta->get(0)->necesidad1;
                
            }


            $contar_nivel_2 = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)
                ->where('establecimiento_id','<>',1)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_2>0){
                $consulta = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id','<>',1)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][4]=$consulta->get(0)->necesidad;
            }
            
            $contar_ajuste_nivel_2 = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [2, 3])
                                  ->orWhere('establecimiento_id',30)
                                  ->orWhere('establecimiento_id',69);
                        })
                ->groupby('petitorio_id')
                ->count();

            //dd($contar_nivel_1);

            if($contar_ajuste_nivel_2>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where( function ( $query )
                        {
                            $query->orwhereBetween('establecimiento_id', [2, 3])
                                  ->orWhere('establecimiento_id',30)
                                  ->orWhere('establecimiento_id',69);
                        })
                ->groupby('petitorio_id')
                ->get();

                $productos[$k][9]=$consulta->get(0)->necesidad1;
            }

            $contar_nivel_3 = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_3>0){
                $consulta = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][5]=$consulta->get(0)->necesidad;
            }

            $contar_ajuste_nivel_3 = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->count();

            //dd($contar_nivel_1);

            if($contar_ajuste_nivel_3>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,sum(cpma) as cpma,sum(stock) as stock,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('establecimiento_id',1)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->get();

                $productos[$k][10]=$consulta->get(0)->necesidad1;
            }

            $contar_nivel_total = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_total>0){
                $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('necesidad_anual','>',0)
                ->where('petitorio_id',$value->id)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][7]=$consulta->get(0)->necesidad1;
            }

            $contar_nivel_consolidado = DB::table('consolidados')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->count();

            if($contar_nivel_consolidado>0){
                $consulta = DB::table('consolidados')
                ->select(DB::raw('sum(necesidad_anual) as necesidad1,petitorio_id'))
                ->where('can_id',$id)
                ->where('petitorio_id',$value->id)
                ->where('necesidad_anual','>',0)
                ->groupby('petitorio_id')
                ->get();
                $productos[$k][8]=$consulta->get(0)->necesidad1;
            }

            if($contar_nivel_1>0 || $contar_nivel_2>0 || $contar_nivel_3>0 || $contar_nivel_total>0 || $contar_nivel_consolidado>0||$contar_ajuste_nivel_3>0 || $contar_ajuste_nivel_2>0){
                $productos[$k][0]=$value->id;
                $productos[$k][1]=$value->codigo_petitorio;
                $productos[$k][2]=$value->descripcion;
                $productos[$k][6]=$productos[$k][3]+$productos[$k][4]+$productos[$k][5];
                $productos[$k][13]=$value->precio;
                $k++;                
            }
        }

        $m=$k;
        //dd($productos);

    for($k=0;$k<$m;$k++){
        DB::table('vista_total')
                    ->insert([
                                'can_id' => 3,
                                'tipo_dispositivo_id'=>$tipo,
                                'descripcion'=>$productos[$k][2],
                                'petitorio_id'=>$productos[$k][0],
                                'cod_petitorio'=>$productos[$k][1],
                                'cod_siga'=>$productos[$k][1],
                                'precio'=>$productos[$k][13],
                                'necesidad_anual_nivel_1' => $productos[$k][3],
                                'necesidad_anual_nivel_2' => $productos[$k][4],
                                'necesidad_anual_nivel_3' => $productos[$k][5],
                                'ajuste_necesidad_anual_nivel_2' => $productos[$k][9],
                                'ajuste_necesidad_anual_nivel_3' => $productos[$k][10],
                                'necesidad_total' => $productos[$k][6],
                                'necesidad_total_ajuste' => $productos[$k][7],
                                'necesidad_consolidado' => $productos[$k][8],
                                'created_at'=>Carbon::now(),
                     ]);
    }
    

        
    
        
  
        

        $consulta = DB::table('vista_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo)
                ->orderby('descripcion','asc')
                ->get();
       
        switch ($tipo) {
            case 1: $descripcion_tipo="MEDICAMENTOS"; break;
            case 2: $descripcion_tipo="MATERIAL BIOMEDICO"; break;
            case 3: $descripcion_tipo="INSTRUMENTAL QUIRURGICO"; break;
            case 4: $descripcion_tipo="MATERIAL E INSUMO ODONTOLOGICO"; break;
            case 5: $descripcion_tipo="INSUMO DE LABORATORIO"; break;
            case 6: $descripcion_tipo="MATERIAL FOTOGRAFICO Y FONOTECNICO"; break;
            case 7: $descripcion_tipo="PRODUCTOS AFINES"; break;
            case 8: $descripcion_tipo="NN"; break;
            case 9: $descripcion_tipo="DISPOSITIVO MEDICO DE USO RESTRINGIDO"; break;
            case 10: $descripcion_tipo="MATERIAL DE LABORATORIO"; break;
            case 11: if($id>5){ $descripcion_tipo="INSTRUMENTAL TRAUMATOLOGIA";} break;
            case 12: if($id>5){ $descripcion_tipo="MATERIAL TRAUMATOLOGIA";} break;
        }
        
        //return view('admin.cans.consolidado_nacional')->with('can', $can)->with('descripcion_tipo', $descripcion_tipo)->with('estimaciones', $consulta)->with('productos', $productos)->with('k', $k);
    
    return view('admin.cans.consolidado_nacional2')->with('can', $can)->with('descripcion_tipo', $descripcion_tipo)->with('estimacions', $consulta);

        //return view('admin.cans.index')->with('cans', $cans);
        
                                      
    }

    public function consolidado_region_producto($id,$tipo)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<3205;$i++){
            for($j=0;$j<49;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        if($id<5){
            $tabla='petitorio_antes';
        }
        else{
            $tabla='petitorios';
        }
        if($tipo==1){
            $contar_nivel = DB::table('consolidados')
                                ->select(DB::raw('sum(necesidad_anual) as necesidad,  consolidados.petitorio_id,consolidados.descripcion, regions.descripcion as region, regions.id as region_id'))
                                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                                ->join('regions', 'regions.id', 'establecimientos.region_id')
                                ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                                //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->where('consolidados.can_id',$id)
                                ->where('consolidados.tipo_dispositivo_id',1)
                                ->where('necesidad_anual','>',0)
                                ->groupby('consolidados.petitorio_id','consolidados.descripcion','regions.descripcion','regions.id')
                                ->orderby('consolidados.petitorio_id')
                                ->count();
        }
        else
        {
            $contar_nivel = DB::table('consolidados')
                                ->select(DB::raw('sum(necesidad_anual) as necesidad,  consolidados.petitorio_id,consolidados.descripcion, regions.descripcion as region, regions.id as region_id'))
                                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                                ->join('regions', 'regions.id', 'establecimientos.region_id')
                                //->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                                ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                                //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->where('consolidados.can_id',$id)
                                ->where('consolidados.tipo_dispositivo_id','>',1)
                                ->where('necesidad_anual','>',0)
                                ->groupby('consolidados.petitorio_id','consolidados.descripcion','regions.descripcion','regions.id')
                                ->orderby('consolidados.petitorio_id')
                                ->count();
        }
        
        if($contar_nivel>0){
            if($tipo==1){
                $consulta = DB::table('consolidados')
                                ->select(DB::raw('sum(necesidad_anual) as necesidad,  consolidados.petitorio_id,consolidados.descripcion, regions.descripcion as region, regions.id as region_id'))
                                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                                ->join('regions', 'regions.id', 'establecimientos.region_id')
                                //->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                                ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                                //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->where('consolidados.can_id',$id)
         
                                ->where('consolidados.tipo_dispositivo_id',1)
                                ->where('necesidad_anual','>',0)
                                ->groupby('consolidados.petitorio_id','consolidados.descripcion','regions.descripcion','regions.id')
                                ->orderby('consolidados.petitorio_id')
                                ->get();
                
                $descripcion_tipo="MEDICAMENTOS";
            }
            else
            {
                $consulta = DB::table('consolidados')
                                ->select(DB::raw('sum(necesidad_anual) as necesidad,  consolidados.petitorio_id,consolidados.descripcion, regions.descripcion as region, regions.id as region_id'))
                                ->join('establecimientos', 'establecimientos.id', 'consolidados.establecimiento_id')
                                ->join('regions', 'regions.id', 'establecimientos.region_id')
                                //->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                                ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                                //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->where('consolidados.can_id',$id)
                                ->where('consolidados.tipo_dispositivo_id','>',1)
                                ->where('necesidad_anual','>',0)
                                ->groupby('consolidados.petitorio_id','consolidados.descripcion','regions.descripcion','regions.id')
                                ->orderby('consolidados.petitorio_id')
                                ->get();
                $descripcion_tipo="DISPOSITIVOS";
            }

            $regions_x = DB::table('regions')
                                ->orderby('regions.id','asc')
                                ->get();
            $i=0;

            foreach ($regions_x as $key => $value) {
                //$descripcion[$i]=$value->servicio_id;   
                $descripcion[$i][0]=$value->id;   
                //$descripcion[$i][1]=$value->codigo;   
                $descripcion[$i][1]=$value->descripcion;   
                $i++;
            }

            $fila_anterior=5000; $x=0; $y=0; $z=0;
            
            foreach ($consulta as $key => $value) {
                
                $fila=$value->petitorio_id;

                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }

                # code...
                for($k=0;$k<$i;$k++){
                    if($value->region_id==$descripcion[$k][0]){
                        $can_productos[$x][$k]=$value->necesidad;
                        $can_productos[$x][47]=$can_productos[$x][$k]+$can_productos[$x][47];
                        $can_productos[$x][48]=$value->descripcion;
                        //$can_productos[$x][49]=$value->region;
                    }
                }
                $y++;
                
            }
            $x++;
            
        }

        return view('admin.cans.region_show')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('y', $y)
                                      ->with('descripcion_tipo', $descripcion_tipo)
                                      ->with('descripcion', $descripcion);
    }

    public function consolidado_establecimiento_producto($id,$tipo)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        
  
        for($i=0;$i<9000;$i++){
            for($j=0;$j<86;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        if($id<5){
            $tabla='petitorio_antes';
        }
        else{
            $tabla='petitorios';
        }

        if($tipo==1){
            
            $contar_nivel = DB::table('estimacions')
                                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id'))
                                ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id')                                
                                //->join('petitorios', 'estimacions.petitorio_id', 'petitorios.id')
                                //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                                ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->where('estimacions.can_id',$id)
                                ->where('estimacions.tipo_dispositivo_id',1)
                                ->where('estimacions.necesidad_anual','>',0)
                                ->where('estimacions.estado','<>',2)
                                ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id')
                                ->orderby('estimacions.petitorio_id')
                                ->count();
            
        }
        else
        {
            $contar_nivel = DB::table('estimacions')
                        ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id,,'.$tabla.'.precio'))
                        ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id',$tabla.'.precio')
                        //->join('petitorios', 'estimacions.petitorio_id', 'petitorios.id')
                        //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                        ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                        ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                        ->where('estimacions.can_id',$id)
                        ->where('estimacions.tipo_dispositivo_id',$tipo)
                        ->where('necesidad_anual','>',0)
                        ->where('estimacions.estado','<>',2)
                        ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id',$tabla.'.precio')
                        ->orderby('estimacions.petitorio_id')
                        ->count();

        }

        if($contar_nivel>0){
            if($tipo==1){
                $consulta = DB::table('estimacions')
                                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id,'.$tabla.'.precio'))
                                ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id')
                                //->join('petitorios', 'consolidados.petitorio_id', 'petitorios.id')
                                //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                                ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                                ->where('estimacions.can_id',$id)
                                ->where('estimacions.tipo_dispositivo_id',1)
                                ->where('estimacions.necesidad_anual','>',0)
                                ->where('estimacions.estado','<>',2)
                                ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id',$tabla.'.precio')
                                ->orderby('estimacions.petitorio_id')
                                ->get();
                $descripcion_tipo="MEDICAMENTOS";
            }
            else
            {
                $consulta = DB::table('estimacions')
                        ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id,'.$tabla.'.precio'))
                        ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id',$tabla.'.precio')
                        //->join('petitorios', 'estimacions.petitorio_id', 'petitorios.id')
                        //->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                        ->join($tabla, 'consolidados.petitorio_id', $tabla.'.id')
                        ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                        ->where('estimacions.can_id',$id)
                        ->where('estimacions.tipo_dispositivo_id',$tipo)
                        ->where('estimacions.necesidad_anual','>',0)
                        ->where('estimacions.estado','<>',2)
                        ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id',$tabla.'.precio')
                        ->orderby('estimacions.petitorio_id')
                        ->get();
                $descripcion_tipo="DISPOSITIVOS";

        
            }
            
            
            $regions_x = DB::table('establecimientos')
                                ->orderby('establecimientos.id','asc')
                                ->get();
            $i=0;

            foreach ($regions_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_establecimiento;   
                $i++;
            }
        
        

            $fila_anterior=6000; $x=0; $y=0;

            foreach ($consulta as $key => $value) {
                
                $fila=$value->petitorio_id;

                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }

                for($k=0;$k<$i;$k++){
                    if($value->establecimiento_id==$descripcion[$k][0]){
                        $can_productos[$x][$k]=$value->necesidad;
                        $can_productos[$x][80]=$can_productos[$x][$k]+$can_productos[$x][80];
                        $can_productos[$x][81]=$value->descripcion;
                        $can_productos[$x][82]=$value->precio;
                        $can_productos[$x][83]=$value->cod_petitorio;
                        $can_productos[$x][84]=$value->petitorio_id;
                    }
                }
                $y++;
            }
            $x++;
        }
        
        //dd($fila);
       /*
        for($i=0;$i<$fila;$i++){
            if($can_productos[$i][80]!=""){
                DB::table('vista_establecimiento_total')
                    ->insert([
                                'can_id' => 3,
                                'tipo_dispositivo_id'=>$tipo,
                                'descripcion'=>$can_productos[$i][81],
                                'petitorio_id'=>$can_productos[$i][84],
                                'cod_petitorio'=>$can_productos[$i][83],
                                'precio'=>$can_productos[$i][82],
                                'necesidad' => $can_productos[$i][80],
                                'valorizado' => $can_productos[$i][82]*$can_productos[$i][80],
                                'establecimiento_1' => $can_productos[$i][0],
                                'establecimiento_2' => $can_productos[$i][1],
                                'establecimiento_3' => $can_productos[$i][2],
                                'establecimiento_4' => $can_productos[$i][3],
                                'establecimiento_5' => $can_productos[$i][4],
                                'establecimiento_6' => $can_productos[$i][5],
                                'establecimiento_7' => $can_productos[$i][6],
                                'establecimiento_8' => $can_productos[$i][7],
                                'establecimiento_9' => $can_productos[$i][8],
                                'establecimiento_10' => $can_productos[$i][9],
                                'establecimiento_11' => $can_productos[$i][10],
                                'establecimiento_12' => $can_productos[$i][11],
                                'establecimiento_13' => $can_productos[$i][12],
                                'establecimiento_14' => $can_productos[$i][13],
                                'establecimiento_15' => $can_productos[$i][14],
                                'establecimiento_16' => $can_productos[$i][15],
                                'establecimiento_17' => $can_productos[$i][16],
                                'establecimiento_18' => $can_productos[$i][17],
                                'establecimiento_19' => $can_productos[$i][18],
                                'establecimiento_20' => $can_productos[$i][19],
                                'establecimiento_21' => $can_productos[$i][20],
                                'establecimiento_22' => $can_productos[$i][21],
                                'establecimiento_23' => $can_productos[$i][22],
                                'establecimiento_24' => $can_productos[$i][23],
                                'establecimiento_25' => $can_productos[$i][24],
                                'establecimiento_26' => $can_productos[$i][25],
                                'establecimiento_27' => $can_productos[$i][26],
                                'establecimiento_28' => $can_productos[$i][27],
                                'establecimiento_29' => $can_productos[$i][28],
                                'establecimiento_30' => $can_productos[$i][29],
                                'establecimiento_31' => $can_productos[$i][30],
                                'establecimiento_32' => $can_productos[$i][31],
                                'establecimiento_33' => $can_productos[$i][32],
                                'establecimiento_34' => $can_productos[$i][33],
                                'establecimiento_35' => $can_productos[$i][34],
                                'establecimiento_36' => $can_productos[$i][35],
                                'establecimiento_37' => $can_productos[$i][36],
                                'establecimiento_38' => $can_productos[$i][37],
                                'establecimiento_39' => $can_productos[$i][38],
                                'establecimiento_40' => $can_productos[$i][39],
                                'establecimiento_41' => $can_productos[$i][40],
                                'establecimiento_42' => $can_productos[$i][41],
                                'establecimiento_43' => $can_productos[$i][42],
                                'establecimiento_44' => $can_productos[$i][43],
                                'establecimiento_45' => $can_productos[$i][44],
                                'establecimiento_46' => $can_productos[$i][45],
                                'establecimiento_47' => $can_productos[$i][46],
                                'establecimiento_48' => $can_productos[$i][47],
                                'establecimiento_49' => $can_productos[$i][48],
                                'establecimiento_50' => $can_productos[$i][49],
                                'establecimiento_51' => $can_productos[$i][50],
                                'establecimiento_52' => $can_productos[$i][51],
                                'establecimiento_53' => $can_productos[$i][52],
                                'establecimiento_54' => $can_productos[$i][53],
                                'establecimiento_55' => $can_productos[$i][54],
                                'establecimiento_56' => $can_productos[$i][55],
                                'establecimiento_57' => $can_productos[$i][56],
                                'establecimiento_58' => $can_productos[$i][57],
                                'establecimiento_59' => $can_productos[$i][58],
                                'establecimiento_60' => $can_productos[$i][59],
                                'establecimiento_61' => $can_productos[$i][60],
                                'establecimiento_62' => $can_productos[$i][61],
                                'establecimiento_63' => $can_productos[$i][62],
                                'establecimiento_64' => $can_productos[$i][63],
                                'establecimiento_65' => $can_productos[$i][64],
                                'establecimiento_66' => $can_productos[$i][65],
                                'establecimiento_67' => $can_productos[$i][66],
                                'establecimiento_68' => $can_productos[$i][67],
                                'establecimiento_69' => $can_productos[$i][68],
                                'establecimiento_70' => $can_productos[$i][69],
                                'establecimiento_71' => $can_productos[$i][70],
                                'establecimiento_72' => $can_productos[$i][71],
                                'establecimiento_73' => $can_productos[$i][72],
                                'establecimiento_74' => $can_productos[$i][73],
                                'establecimiento_75' => $can_productos[$i][74],
                                'establecimiento_76' => $can_productos[$i][75],
                                'establecimiento_77' => $can_productos[$i][76],
                                'establecimiento_78' => $can_productos[$i][77],
                                'establecimiento_79' => $can_productos[$i][78],
                                'establecimiento_80' => $can_productos[$i][79],
                                'establecimiento_81' => $can_productos[$i][80],
                                'created_at'=>Carbon::now(),
                    ]);
            }
        }                      
        */
        $descripcion_tipo="MEDICAMENTOS";

        $regions_x = DB::table('establecimientos')
                                ->orderby('establecimientos.id','asc')
                                ->get();
            $i=0;

            foreach ($regions_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_establecimiento;   
                $i++;
            }
   
    /*$consulta = DB::table('vista_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo)
                ->orderby('descripcion','asc');
                
    $consulta1 = $consulta->pluck('descripcion','petitorio_id')->toArray();

    $consulta2 = DB::table('vista_establecimiento_total')
            ->where('can_id',$id)
            ->where('tipo_dispositivo_id',$tipo)
            ->orderby('descripcion','asc');
            
    $consulta3 = $consulta2->pluck('descripcion','petitorio_id')->toArray();

    $descripciones=array_diff($consulta1,$consulta3);
    */
    /*
        foreach ($descripciones as $key => $value) {
            $consulta2 = DB::table('vista_total')
                ->where('descripcion',$value)
                ->orderby('descripcion','asc')
                ->get();

            DB::table('vista_establecimiento_total')
                    ->insert([
                                'can_id' => 3,
                                'tipo_dispositivo_id'=>$tipo,
                                'descripcion'=>$consulta2->get(0)->descripcion,
                                'petitorio_id'=>$consulta2->get(0)->petitorio_id,
                                'cod_petitorio'=>$consulta2->get(0)->cod_petitorio,
                                'precio'=>$consulta2->get(0)->precio,
                                'necesidad' => $consulta2->get(0)->necesidad_anual_nivel_3,
                                'valorizado' => $consulta2->get(0)->precio*$consulta2->get(0)->necesidad_anual_nivel_3,
                                'establecimiento_82' => $consulta2->get(0)->necesidad_anual_nivel_3,
                            ]);
        }
        
      
        $consulta = DB::table('vista_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo)
                ->orderby('descripcion','asc')
                ->get();

        foreach ($consulta as $key => $consulta2) {
            DB::table('vista_establecimiento_total')
                ->where('petitorio_id', $consulta2->petitorio_id)
                ->update([
                            'establecimiento_81' => $consulta2->necesidad_anual_nivel_3,
                        ]);
        }    
    */
        $consulta = DB::table('vista_establecimiento_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo)
                ->orderby('descripcion','asc')
                ->get();

  

        return view('admin.cans.establecimiento_total_show')
                ->with('consulta', $consulta)
                ->with('tipo_dispositivo', $tipo)
                ->with('descripcion_tipo', $descripcion_tipo)
                ->with('can', $can)
                ->with('descripcion', $descripcion);
                
    }

    public function consolidado_establecimiento_producto2($id,$tipo)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        
  
        for($i=0;$i<9000;$i++){
            for($j=0;$j<87;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        if($id<5){
            $tabla='petitorio_antes';
        }
        else{
            $tabla='petitorios';
        }

        $contar_nivel = DB::table('estimacions')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id,'.$tabla.'.precio'))
                    ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id',$tabla.'.precio')
                    ->join($tabla, 'estimacions.petitorio_id', $tabla.'.id')
                    ->join('tipo_dispositivo_medicos', $tabla.'.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                    ->where('estimacions.can_id',$id)
                    ->where('estimacions.tipo_dispositivo_id',$tipo)
                    ->where('necesidad_anual','>',0)
                    ->where($tabla.'.nivel_id',1)
                    ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id',$tabla.'.precio')
                    ->orderby('estimacions.petitorio_id')
                    ->count();

        

        if($contar_nivel>0){
                /*$consulta = DB::table('estimacions')
                        ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id,petitorios.precio'))
                        ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id','petitorios.precio')
                        ->join('petitorios', 'estimacions.petitorio_id', 'petitorios.id')
                        ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                        ->where('estimacions.can_id',$id)
                        ->where('estimacions.tipo_dispositivo_id',$tipo)
                        ->where('petitorios.nivel_id',1)
                        ->where('necesidad_anual','>',0)
                        ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id','petitorios.precio')
                        ->orderby('estimacions.petitorio_id')
                        ->get();

                $consulta1 = DB::table('estimacion_servicio')
                        ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.cod_petitorio,estimacion_servicio.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id,petitorios.precio'))
                        ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id','petitorios.precio')
                        ->join('petitorios', 'estimacion_servicio.petitorio_id', 'petitorios.id')
                        ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                        ->where('estimacion_servicio.can_id',$id)
                        ->where('estimacion_servicio.tipo_dispositivo_id',$tipo)                        
                        ->where('necesidad_anual','>',0)
                        ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.cod_petitorio','estimacion_servicio.descripcion','establecimientos.nombre_establecimiento','establecimientos.id','petitorios.precio')
                        ->orderby('estimacion_servicio.petitorio_id')
                        ->get();
                */
                $consultar="select sum(necesidad_anual) as necesidad,  e.petitorio_id as petitorio_id,e.cod_petitorio,e.descripcion, est.nombre_establecimiento, est.id as establecimiento_id,p.precio
                    from estimacions e                       
                    inner join establecimientos est on est.id=e.establecimiento_id
                    inner join ".$tabla." p on e.petitorio_id=p.id
                    inner join tipo_dispositivo_medicos tdm on p.tipo_dispositivo_medicos_id=tdm.id
                    where e.can_id = 3 and e.tipo_dispositivo_id=".$tipo." and p.nivel_id = 1 and necesidad_anual>0
                    group by e.petitorio_id,e.cod_petitorio,e.descripcion,est.nombre_establecimiento,est.id,p.precio
                    union all
                    select sum(necesidad_anual) as necesidad,  e.petitorio_id as petitorio_id,e.cod_petitorio,e.descripcion, est.nombre_establecimiento, est.id as establecimiento_id,p.precio
                    from estimacion_servicio e                       
                    inner join establecimientos est on est.id=e.establecimiento_id
                    inner join ".$tabla." p on e.petitorio_id=p.id
                    inner join tipo_dispositivo_medicos tdm on p.tipo_dispositivo_medicos_id=tdm.id
                    where e.can_id = 3 and e.tipo_dispositivo_id=".$tipo."  and necesidad_anual>0
                    group by e.petitorio_id,e.cod_petitorio,e.descripcion,est.nombre_establecimiento,est.id,p.precio
                    order by petitorio_id desc";
                $data = DB::select($consultar);

                if($tipo==1)
                    $descripcion_tipo="MEDICAMENTOS";
                else
                    $descripcion_tipo="DISPOSITIVOS";        
        }

        $regions_x = DB::table('establecimientos')
                                ->orderby('establecimientos.id','asc')
                                ->get();
            $i=0;

            foreach ($regions_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_establecimiento;   
                $i++;
            }            
            
            //dd($regions);

            $fila_anterior=6000; $x=0; $y=0;
          
            foreach ($data as $key => $value) {                
                $fila=$value->petitorio_id;
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }

                # code...
                for($k=0;$k<$i;$k++){
                    if($value->establecimiento_id==$descripcion[$k][0]){
                        $can_productos[$x][$k]=$value->necesidad;
                        $can_productos[$x][81]=$can_productos[$x][$k]+$can_productos[$x][81];
                        $can_productos[$x][82]=$value->descripcion;
                        $can_productos[$x][83]=$value->precio;
                        $can_productos[$x][84]=$value->cod_petitorio;
                        $can_productos[$x][85]=$value->petitorio_id;
                    }
                }
                $y++;
            }

            $x++;

            
            count($can_productos);

            /*$fila_anterior=6000; $x=0; $y=0;
          
            foreach ($consulta1 as $key => $value) {                
                $fila=$value->petitorio_id;
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }

                # code...
                for($k=0;$k<$i;$k++){
                    if($value->establecimiento_id==$descripcion[$k][0]){
                        $can_productos[$x][$k]=$value->necesidad;
                        $can_productos[$x][81]=$can_productos[$x][$k]+$can_productos[$x][81];
                        $can_productos[$x][82]=$value->descripcion;
                        $can_productos[$x][83]=$value->precio;
                        $can_productos[$x][84]=$value->cod_petitorio;
                        $can_productos[$x][85]=$value->petitorio_id;
                    }
                }
                $y++;
            }

            $x++;
              */  
        
       
        for($i=0;$i<$fila;$i++){
            if($can_productos[$i][82]!=""){
                DB::table('vista_establecimiento_total')
                    ->insert([
                                'can_id' => 3,
                                'tipo_dispositivo_id'=>$tipo,
                                'descripcion'=>$can_productos[$i][82],
                                'petitorio_id'=>$can_productos[$i][85],
                                'cod_petitorio'=>$can_productos[$i][84],
                                'precio'=>$can_productos[$i][83],
                                'necesidad' => $can_productos[$i][81],
                                'valorizado' => $can_productos[$i][83]*$can_productos[$i][81],
                                'establecimiento_1' => $can_productos[$i][0],
                                'establecimiento_2' => $can_productos[$i][1],
                                'establecimiento_3' => $can_productos[$i][2],
                                'establecimiento_4' => $can_productos[$i][3],
                                'establecimiento_5' => $can_productos[$i][4],
                                'establecimiento_6' => $can_productos[$i][5],
                                'establecimiento_7' => $can_productos[$i][6],
                                'establecimiento_8' => $can_productos[$i][7],
                                'establecimiento_9' => $can_productos[$i][8],
                                'establecimiento_10' => $can_productos[$i][9],
                                'establecimiento_11' => $can_productos[$i][10],
                                'establecimiento_12' => $can_productos[$i][11],
                                'establecimiento_13' => $can_productos[$i][12],
                                'establecimiento_14' => $can_productos[$i][13],
                                'establecimiento_15' => $can_productos[$i][14],
                                'establecimiento_16' => $can_productos[$i][15],
                                'establecimiento_17' => $can_productos[$i][16],
                                'establecimiento_18' => $can_productos[$i][17],
                                'establecimiento_19' => $can_productos[$i][18],
                                'establecimiento_20' => $can_productos[$i][19],
                                'establecimiento_21' => $can_productos[$i][20],
                                'establecimiento_22' => $can_productos[$i][21],
                                'establecimiento_23' => $can_productos[$i][22],
                                'establecimiento_24' => $can_productos[$i][23],
                                'establecimiento_25' => $can_productos[$i][24],
                                'establecimiento_26' => $can_productos[$i][25],
                                'establecimiento_27' => $can_productos[$i][26],
                                'establecimiento_28' => $can_productos[$i][27],
                                'establecimiento_29' => $can_productos[$i][28],
                                'establecimiento_30' => $can_productos[$i][29],
                                'establecimiento_31' => $can_productos[$i][30],
                                'establecimiento_32' => $can_productos[$i][31],
                                'establecimiento_33' => $can_productos[$i][32],
                                'establecimiento_34' => $can_productos[$i][33],
                                'establecimiento_35' => $can_productos[$i][34],
                                'establecimiento_36' => $can_productos[$i][35],
                                'establecimiento_37' => $can_productos[$i][36],
                                'establecimiento_38' => $can_productos[$i][37],
                                'establecimiento_39' => $can_productos[$i][38],
                                'establecimiento_40' => $can_productos[$i][39],
                                'establecimiento_41' => $can_productos[$i][40],
                                'establecimiento_42' => $can_productos[$i][41],
                                'establecimiento_43' => $can_productos[$i][42],
                                'establecimiento_44' => $can_productos[$i][43],
                                'establecimiento_45' => $can_productos[$i][44],
                                'establecimiento_46' => $can_productos[$i][45],
                                'establecimiento_47' => $can_productos[$i][46],
                                'establecimiento_48' => $can_productos[$i][47],
                                'establecimiento_49' => $can_productos[$i][48],
                                'establecimiento_50' => $can_productos[$i][49],
                                'establecimiento_51' => $can_productos[$i][50],
                                'establecimiento_52' => $can_productos[$i][51],
                                'establecimiento_53' => $can_productos[$i][52],
                                'establecimiento_54' => $can_productos[$i][53],
                                'establecimiento_55' => $can_productos[$i][54],
                                'establecimiento_56' => $can_productos[$i][55],
                                'establecimiento_57' => $can_productos[$i][56],
                                'establecimiento_58' => $can_productos[$i][57],
                                'establecimiento_59' => $can_productos[$i][58],
                                'establecimiento_60' => $can_productos[$i][59],
                                'establecimiento_61' => $can_productos[$i][60],
                                'establecimiento_62' => $can_productos[$i][61],
                                'establecimiento_63' => $can_productos[$i][62],
                                'establecimiento_64' => $can_productos[$i][63],
                                'establecimiento_65' => $can_productos[$i][64],
                                'establecimiento_66' => $can_productos[$i][65],
                                'establecimiento_67' => $can_productos[$i][66],
                                'establecimiento_68' => $can_productos[$i][67],
                                'establecimiento_69' => $can_productos[$i][68],
                                'establecimiento_70' => $can_productos[$i][69],
                                'establecimiento_71' => $can_productos[$i][70],
                                'establecimiento_72' => $can_productos[$i][71],
                                'establecimiento_73' => $can_productos[$i][72],
                                'establecimiento_74' => $can_productos[$i][73],
                                'establecimiento_75' => $can_productos[$i][74],
                                'establecimiento_76' => $can_productos[$i][75],
                                'establecimiento_77' => $can_productos[$i][76],
                                'establecimiento_78' => $can_productos[$i][77],
                                'establecimiento_79' => $can_productos[$i][78],
                                'establecimiento_80' => $can_productos[$i][79],
                                'establecimiento_81' => $can_productos[$i][80],
                                'created_at'=>Carbon::now(),
                    ]);
            }
        }                      
        
        $descripcion_tipo="MEDICAMENTOS";

        $regions_x = DB::table('establecimientos')
                                ->orderby('establecimientos.id','asc')
                                ->get();
            $i=0;

            foreach ($regions_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_establecimiento;   
                $i++;
            }
   /*
        $consulta = DB::table('vista_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo)
                ->orderby('descripcion','asc');
                
        $consulta1 = $consulta->pluck('descripcion','petitorio_id')->toArray();

        $consulta2 = DB::table('vista_establecimiento_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo)
                ->orderby('descripcion','asc');
                
        $consulta3 = $consulta2->pluck('descripcion','petitorio_id')->toArray();

        $descripciones=array_diff($consulta1,$consulta3);

        */
    
        $consulta = DB::table('vista_establecimiento_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo)
                ->orderby('descripcion','asc')
                ->get();

  

        return view('admin.cans.establecimiento_total_show')
                ->with('consulta', $consulta)
                ->with('tipo_dispositivo', $tipo)
                ->with('descripcion_tipo', $descripcion_tipo)
                ->with('can', $can)
                ->with('descripcion', $descripcion);
                
    }

    public function consolidado_establecimiento_producto_tipo_dispositivo($id,$tipo_dispositivo)
    {
        
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        for($i=0;$i<3205;$i++){
            for($j=0;$j<91;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }
            
        $contar_nivel = DB::table('estimacions')
                            ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id'))
                            ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id')                            
                            ->join('petitorios', 'estimacions.petitorio_id', 'petitorios.id')
                            ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                            ->where('estimacions.can_id',$id)
                            ->where('estimacions.tipo_dispositivo_id',$tipo_dispositivo)
                            ->where('necesidad_anual','>',0)
                            ->where('estimacions.estado','<>',2)
                            ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id')
                            ->orderby('estimacions.petitorio_id')
                            ->count();
        
        $consulta = DB::table('estimacions')
                        ->select(DB::raw('sum(necesidad_anual) as necesidad,sum(necesidad_actual) as necesidad_actual,  estimacions.petitorio_id,estimacions.cod_petitorio,estimacions.descripcion, establecimientos.nombre_establecimiento, establecimientos.id as establecimiento_id,petitorios.precio'))
                        ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id','petitorios.precio')                        
                        ->join('petitorios', 'estimacions.petitorio_id', 'petitorios.id')
                        ->join('tipo_dispositivo_medicos', 'petitorios.tipo_dispositivo_medicos_id', 'tipo_dispositivo_medicos.id')
                        ->where('estimacions.can_id',$id)
                        ->where('estimacions.tipo_dispositivo_id',$tipo_dispositivo)
                        ->where('estimacions.necesidad_anual','>',0)
                        ->where('estimacions.estado','<>',2)
                        ->groupby('estimacions.petitorio_id','estimacions.cod_petitorio','estimacions.descripcion','establecimientos.nombre_establecimiento','establecimientos.id','petitorios.precio')
                        ->orderby('estimacions.petitorio_id')
                        ->get();
        
                        
        switch ($tipo_dispositivo) {
            case 1: $descripcion_tipo="PRODUCTOS FARMACEUTICOS"; break;
            case 2: $descripcion_tipo="MATERIAL BIOMEDICO"; break;
            case 3: $descripcion_tipo="INSTRUMENTAL QUIRURGICO"; break;
            case 4: $descripcion_tipo="MATERIAL E INSUMO ODONTOLOGICO"; break;
            case 5: $descripcion_tipo="INSUMO DE LABORATORIO"; break;
            case 6: $descripcion_tipo="MATERIAL FOTOGRAFICO Y FONOTECNICO"; break;
            case 7: $descripcion_tipo="PRODUCTOS AFINES"; break;
            case 9: $descripcion_tipo="DISPOSITIVO MEDICO DE USO RESTRINGIDO"; break;
            case 10: $descripcion_tipo="MATERIAL DE LABORATORIO"; break;
        }
            
        $regions_x = DB::table('establecimientos')
                            ->orderby('establecimientos.id','asc')
                            ->get();
        $i=0;

        foreach ($regions_x as $key => $value) {
            //$descripcion[$i]=$value->servicio_id;   
            $descripcion[$i][0]=$value->id;   
            //$descripcion[$i][1]=$value->codigo;   
            $descripcion[$i][1]=$value->nombre_establecimiento;   
            $i++;
        }
    
        $fila_anterior=5000; $x=0; $y=0;
        
        foreach ($consulta as $key => $value) {
            
            $fila=$value->petitorio_id;

            if($fila_anterior!=$fila){
                $fila_anterior=$fila;
                $x++;
            }
/*
            //# code...
            for($k=0;$k<$i;$k++){
                if($value->establecimiento_id==$descripcion[$k][0]){
                    $can_productos[$x][$k]=$value->necesidad;                    
                    $can_productos[$x][81]=$can_productos[$x][$k]+$can_productos[$x][81];
                    $can_productos[$x][82]=$value->descripcion;
                    $can_productos[$x][83]=$value->precio;
                    $can_productos[$x][84]=$value->cod_petitorio;
                    $can_productos[$x][86]=$value->petitorio_id;
                    $can_productos[$x][87]=$value->necesidad_actual;
                }
            }
            $y++;

*/
            
            for($k=0;$k<$i;$k++){
                if($value->establecimiento_id==$descripcion[$k][0]){
                    $can_productos[$x][$k]=$value->necesidad;                    
                    $can_productos[$x][84]=$can_productos[$x][$k]+$can_productos[$x][84];
                    $can_productos[$x][85]=$value->descripcion;
                    $can_productos[$x][86]=$value->precio;
                    $can_productos[$x][87]=$value->cod_petitorio;
                    $can_productos[$x][88]=$value->petitorio_id;
                    $can_productos[$x][89]=$value->necesidad_actual;
                }
            }
            $y++;
        }
        $x++;
    /*    
        for($i=0;$i<$fila;$i++){
            if($can_productos[$i][78]!=""){
                DB::table('vista_establecimiento_total')
                    ->insert([
                                'can_id' => 3,
                                'tipo_dispositivo_id'=>$tipo_dispositivo,
                                'descripcion'=>$can_productos[$i][79],
                                'petitorio_id'=>$can_productos[$i][82],
                                'cod_petitorio'=>$can_productos[$i][81],
                                'precio'=>$can_productos[$i][80],
                                'necesidad' => $can_productos[$i][78],
                                'valorizado' => $can_productos[$i][80]*$can_productos[$i][78],
                                'establecimiento_1' => $can_productos[$i][0],
                                'establecimiento_2' => $can_productos[$i][1],
                                'establecimiento_3' => $can_productos[$i][2],
                                'establecimiento_4' => $can_productos[$i][3],
                                'establecimiento_5' => $can_productos[$i][4],
                                'establecimiento_6' => $can_productos[$i][5],
                                'establecimiento_7' => $can_productos[$i][6],
                                'establecimiento_8' => $can_productos[$i][7],
                                'establecimiento_9' => $can_productos[$i][8],
                                'establecimiento_10' => $can_productos[$i][9],
                                'establecimiento_11' => $can_productos[$i][10],
                                'establecimiento_12' => $can_productos[$i][11],
                                'establecimiento_13' => $can_productos[$i][12],
                                'establecimiento_14' => $can_productos[$i][13],
                                'establecimiento_15' => $can_productos[$i][14],
                                'establecimiento_16' => $can_productos[$i][15],
                                'establecimiento_17' => $can_productos[$i][16],
                                'establecimiento_18' => $can_productos[$i][17],
                                'establecimiento_19' => $can_productos[$i][18],
                                'establecimiento_20' => $can_productos[$i][19],
                                'establecimiento_21' => $can_productos[$i][20],
                                'establecimiento_22' => $can_productos[$i][21],
                                'establecimiento_23' => $can_productos[$i][22],
                                'establecimiento_24' => $can_productos[$i][23],
                                'establecimiento_25' => $can_productos[$i][24],
                                'establecimiento_26' => $can_productos[$i][25],
                                'establecimiento_27' => $can_productos[$i][26],
                                'establecimiento_28' => $can_productos[$i][27],
                                'establecimiento_29' => $can_productos[$i][28],
                                'establecimiento_30' => $can_productos[$i][29],
                                'establecimiento_31' => $can_productos[$i][30],
                                'establecimiento_32' => $can_productos[$i][31],
                                'establecimiento_33' => $can_productos[$i][32],
                                'establecimiento_34' => $can_productos[$i][33],
                                'establecimiento_35' => $can_productos[$i][34],
                                'establecimiento_36' => $can_productos[$i][35],
                                'establecimiento_37' => $can_productos[$i][36],
                                'establecimiento_38' => $can_productos[$i][37],
                                'establecimiento_39' => $can_productos[$i][38],
                                'establecimiento_40' => $can_productos[$i][39],
                                'establecimiento_41' => $can_productos[$i][40],
                                'establecimiento_42' => $can_productos[$i][41],
                                'establecimiento_43' => $can_productos[$i][42],
                                'establecimiento_44' => $can_productos[$i][43],
                                'establecimiento_45' => $can_productos[$i][44],
                                'establecimiento_46' => $can_productos[$i][45],
                                'establecimiento_47' => $can_productos[$i][46],
                                'establecimiento_48' => $can_productos[$i][47],
                                'establecimiento_49' => $can_productos[$i][48],
                                'establecimiento_50' => $can_productos[$i][49],
                                'establecimiento_51' => $can_productos[$i][50],
                                'establecimiento_52' => $can_productos[$i][51],
                                'establecimiento_53' => $can_productos[$i][52],
                                'establecimiento_54' => $can_productos[$i][53],
                                'establecimiento_55' => $can_productos[$i][54],
                                'establecimiento_56' => $can_productos[$i][55],
                                'establecimiento_57' => $can_productos[$i][56],
                                'establecimiento_58' => $can_productos[$i][57],
                                'establecimiento_59' => $can_productos[$i][58],
                                'establecimiento_60' => $can_productos[$i][59],
                                'establecimiento_61' => $can_productos[$i][60],
                                'establecimiento_62' => $can_productos[$i][61],
                                'establecimiento_63' => $can_productos[$i][62],
                                'establecimiento_64' => $can_productos[$i][63],
                                'establecimiento_65' => $can_productos[$i][64],
                                'establecimiento_66' => $can_productos[$i][65],
                                'establecimiento_67' => $can_productos[$i][66],
                                'establecimiento_68' => $can_productos[$i][67],
                                'establecimiento_69' => $can_productos[$i][68],
                                'establecimiento_70' => $can_productos[$i][69],
                                'establecimiento_71' => $can_productos[$i][70],
                                'establecimiento_72' => $can_productos[$i][71],
                                'establecimiento_73' => $can_productos[$i][72],
                                'establecimiento_74' => $can_productos[$i][73],
                                'establecimiento_75' => $can_productos[$i][74],
                                'establecimiento_76' => $can_productos[$i][75],
                                'establecimiento_77' => $can_productos[$i][76],
                                'establecimiento_78' => $can_productos[$i][77],
                                'establecimiento_79' => $can_productos[$i][83],
                                'created_at'=>Carbon::now(),
                    ]);
            }
        }

        $regions_x = DB::table('establecimientos')
                                ->orderby('establecimientos.id','asc')
                                ->get();
            $i=0;

            foreach ($regions_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_establecimiento;   
                $i++;
            }
  
    $consulta = DB::table('vista_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo_dispositivo)
                ->orderby('descripcion','asc');
                
    $consulta1 = $consulta->pluck('descripcion','petitorio_id')->toArray();

    $consulta2 = DB::table('vista_establecimiento_total')
            ->where('can_id',$id)
            ->where('tipo_dispositivo_id',$tipo_dispositivo)
            ->orderby('descripcion','asc');
            
    $consulta3 = $consulta2->pluck('descripcion','petitorio_id')->toArray();

    $descripcion=array_diff($consulta1,$consulta3);
  
        foreach ($descripcion as $key => $value) {
            $consulta2 = DB::table('vista_total')
                ->where('descripcion',$value)
                ->orderby('descripcion','asc')
                ->get();

            DB::table('vista_establecimiento_total')
                    ->insert([
                                'can_id' => 3,
                                'tipo_dispositivo_id'=>$tipo_dispositivo,
                                'descripcion'=>$consulta2->get(0)->descripcion,
                                'petitorio_id'=>$consulta2->get(0)->petitorio_id,
                                'cod_petitorio'=>$consulta2->get(0)->cod_petitorio,
                                'precio'=>$consulta2->get(0)->precio,
                                'necesidad' => $consulta2->get(0)->necesidad_anual_nivel_3,
                                'valorizado' => $consulta2->get(0)->precio*$consulta2->get(0)->necesidad_anual_nivel_3,
                                'establecimiento_79' => $consulta2->get(0)->necesidad_anual_nivel_3,
                            ]);
        }

  
    switch ($tipo_dispositivo) {
            case 2: $descripcion_tipo="MATERIAL BIOMEDICO"; break;
            case 3: $descripcion_tipo="INSTRUMENTAL QUIRURGICO"; break;
            case 4: $descripcion_tipo="MATERIAL E INSUMO ODONTOLOGICO"; break;
            case 5: $descripcion_tipo="INSUMO DE LABORATORIO"; break;
            case 6: $descripcion_tipo="MATERIAL FOTOGRAFICO Y FONOTECNICO"; break;
            case 7: $descripcion_tipo="PRODUCTOS AFINES"; break;
            case 9: $descripcion_tipo="DISPOSITIVO MEDICO DE USO RESTRINGIDO"; break;
            case 10: $descripcion_tipo="MATERIAL DE LABORATORIO"; break;
        }

        $consulta = DB::table('vista_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo_dispositivo)
                ->orderby('descripcion','asc')
                ->get();

        foreach ($consulta as $key => $consulta2) {
            DB::table('vista_establecimiento_total')
                ->where('petitorio_id', $consulta2->petitorio_id)
                ->update([
                            'establecimiento_79' => $consulta2->necesidad_anual_nivel_3,
                        ]);
        }    

        $consulta = DB::table('vista_establecimiento_total')
                ->where('can_id',$id)
                ->where('tipo_dispositivo_id',$tipo_dispositivo)
                ->orderby('descripcion','asc')
                ->get();
  
        return view('admin.cans.establecimiento_total_show')
                ->with('consulta', $consulta)
                ->with('tipo_dispositivo', $tipo_dispositivo)
                ->with('descripcion_tipo', $descripcion_tipo)
                ->with('can', $can)
                ->with('descripcion', $descripcion);

*/
        return view('admin.cans.establecimiento_show')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('y', $y)
                                      ->with('descripcion_tipo', $descripcion_tipo)
                                      ->with('tipo_dispositivo', $tipo_dispositivo)
                                      ->with('descripcion', $descripcion);   
        
  
    }

    
    public function nivel_total($id)
    {
        $can = $this->canRepository->findWithoutFail($id);
        
        if (empty($can)) {
            Flash::error('No se ha encontrado');
            return redirect(route('cans.index'));
        }

        for($i=0;$i<3;$i++){
            for($j=0;$j<11;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j]="";
            }
        }

        $contar_nivel = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,tipo_dispositivo_medicos.id'))
                ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacions.tipo_dispositivo_id')
                ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id')
                ->where('estimacions.can_id', $id)
                ->where('estimacions.estado','<>',2)
                ->where('estimacions.necesidad_anual','>',0)
                ->groupby('tipo_dispositivo_medicos.id')
                ->count();

        if($contar_nivel>0){
            $consulta = DB::table('estimacions')
                ->select(DB::raw('sum(necesidad_anual) as necesidad, tipo_dispositivo_medicos.id,tipo_dispositivo_medicos.descripcion, establecimientos.nivel_id'))
                ->join('tipo_dispositivo_medicos', 'tipo_dispositivo_medicos.id', 'estimacions.tipo_dispositivo_id')
                ->join('establecimientos', 'establecimientos.id', 'estimacions.establecimiento_id')
                ->where('estimacions.can_id', $id)
                ->where('estimacions.estado','<>',2)
                ->where('estimacions.necesidad_anual','>',0)
                ->groupby('tipo_dispositivo_medicos','tipo_dispositivo_medicos.id','establecimientos.nivel_id')
                ->orderby('tipo_dispositivo_medicos.id')
                ->get();
            
            foreach ($consulta as $key => $value) {
                # code...
                $nivel=$value->nivel_id-1;
                $id=$value->id-1;
                $can_productos[$nivel][$id]=$value->necesidad;
                $can_productos[$nivel][10]=$can_productos[$nivel][10]+$value->necesidad; //total
                $descripcion[$id]=$value->descripcion;
            }

        }

        return view('admin.cans.rubro_nivel')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('descripcion', $descripcion);
                                      
    }

    public function establecimientos_cans($id)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<3205;$i++){
            for($j=0;$j<49;$j++){
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }


        $contar_nivel = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio'))
                ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                ->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                ->join('servicios', function($join)
                    {
                        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                    })
                ->join('petitorio_servicio', function($join)
                    {
                        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                    })
                ->join('petitorios', function($join)
                    {
                        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                    })
                ->where('estimacion_servicio.can_id',$id)
                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio')
                ->orderby('estimacion_servicio.petitorio_id')
                ->count();
        
        if($contar_nivel>0){
            $consulta = DB::table('estimacion_servicio')
                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id'))
                ->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                ->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                ->join('servicios', function($join)
                    {
                        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                    })
                ->join('petitorio_servicio', function($join)
                    {
                        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                    })
                ->join('petitorios', function($join)
                    {
                        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                    })
                ->where('estimacion_servicio.can_id',$id)
                ->where('estimacion_servicio.tipo_dispositivo_id',1)
                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id')
                ->orderby('estimacion_servicio.petitorio_id','asc')
                ->orderby('estimacion_servicio.servicio_id','asc')
                ->get();
            
            
            $establecimientos_x = DB::table('establecimientos')
                               ->get();
            
            $i=0;

            foreach ($establecimientos_x as $key => $value) {
                $descripcion[$i][0]=$value->codigo_establecimiento;   
                $descripcion[$i][1]=$value->nombre_establecimiento;   
                $i++;
            }


            $fila_anterior=0; $x=-1; $y=0;

            foreach ($consulta as $key => $value) {
                # code...
                $fila=$value->petitorio_id-1; //petitorio
                
                for($k=0;$k<$i;$k++){
                    if($value->servicio_id==$descripcion[$k][0]){
                        $can_productos[$x][$k]=$value->necesidad;
                        $can_productos[$x][46]=$value->descripcion;
                    }
                }
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }
            }

        }
        
        return view('admin.cans.establecimientos_cans')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('descripcion', $descripcion);
    }
    
    public function edit($id)
    {
        $valor=2; //creacion 1 edcanon 2
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        $mes = DB::table('mes')->pluck('descripcion','id');
        $ano = DB::table('years')->pluck('descripcion','id');
        $establecimientos=Establecimiento::pluck('nombre_establecimiento','id');
        return view('admin.cans.edit')->with('can', $can)
                                      ->with('mes', $mes)
                                      ->with('ano', $ano)
                                      ->with('valor', $valor)
                                      ->with('establecimientos', $establecimientos);        
    }

    public function update($id, UpdateCanRequest $request)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');
            return redirect(route('cans.index'));
        }

        $year=Year::find($request->year_id);
        $ano=$year->descripcion;

        $stock = $request->input("stock");
        if($stock!="")$stock = 1;
        else $stock=0;

        $extraordinario = $request->input("extraordinario");
        if($extraordinario!="")$extraordinario = 1;
        else $extraordinario=0;

        $repetido=DB::table('cans')
                    ->where('ano',$request->year_id)
                    ->where('mes_id',$request->mes_id)
                    ->count();

        if ($repetido>0) {
            Flash::error('Ya se encuentra registrado');
        }
        else
        {
            //Escogemos el mes
            switch ($request->mes_id) {
                case '1':$meses='Enero';break;
                case '2':$meses='Febrero';break;
                case '3':$meses='Marzo';break;
                case '4':$meses='Abril';break;
                case '5':$meses='Mayo';break;
                case '6':$meses='Junio';break;
                case '7':$meses='Julio';break;
                case '8':$meses='Agosto';break;
                case '9':$meses='Setiembre';break;
                case '10':$meses='Octubre';break;
                case '11':$meses='Noviembre';break;
                case '12':$meses='Dcanembre';break;
            }

            //insertamos datos
            DB::table('cans')
                ->where('id', $id)
                ->update([
                            'mes_id' => $request->mes_id,
                            'desc_mes' => $meses,
                            'year_id' => $request->year_id,
                            'nombre_can' => $request->nombre_can,
                            'ano'=>$ano,
                            'stock'=>$stock,
                            'extraordinario'=>$extraordinario,
                        ]);
            //ordenamos descendentemente para saber cual es el primer can
                        
            Flash::success('Actualizado satisfactoriamente.');
        }    
        return redirect(route('cans.index'));
    }

    public function destroy($id)
    {
        $can = $this->canRepository->findWithoutFail($id);

        if (empty($can)) {
            Flash::error('No encontrado');

            return redirect(route('cans.index'));
        }
        $this->canRepository->delete($id);
        Flash::success('Borrado correctamente.');
        return redirect(route('cans.index'));
    }

/****************1*****************/
    public function mostrar_rubros($id,$establecimiento_id)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('CAN No se ha encontrado');

            return redirect(route('cans.index'));
        }
        $consulta = DB::table('can_rubro')
                ->join('rubros', 'rubros.id','can_rubro.rubro_id')
                ->join('cans', 'cans.id','can_rubro.can_id')
                ->where('cans.id', $id)
                ->where('can_rubro.establecimiento_id', $establecimiento_id)
                ->get();
        
        $establecimientos=Establecimiento::find($establecimiento_id);
        
        return view('admin.cans.mostrar_rubros')->with('can', $can)
                                      ->with('establecimientos', $establecimientos)
                                      ->with('consulta', $consulta);
    }

    public function mostrar_servicios($id,$establecimiento_id)
    {
        $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('CAN no se ha encontrado');

            return redirect(route('cans.index'));
        }
        
        $rubros = DB::table('can_establecimiento')
                //->join('users', 'users.establecimiento_id','can_establecimiento.establecimiento_id')
                ->where('can_establecimiento.can_id', $id)
                ->where('can_establecimiento.establecimiento_id', $establecimiento_id)
//                ->where('users.rol','>',2)
                //->where('users.rol','<>',7)
                ->get();


        $rubro_pf=$rubros->get(0)->rubro_pf;
        $rubro_mb_iq_pa=$rubros->get(0)->rubro_mb_iq_pa;
        $rubro_mid=$rubros->get(0)->rubro_mid;
        $rubro_mil=$rubros->get(0)->rubro_mil;
        $rubro_mff=$rubros->get(0)->rubro_mff;

        $consulta = DB::table('can_servicio')
                ->join('servicios', 'servicios.id','can_servicio.servicio_id')
                ->join('cans', 'cans.id','can_servicio.can_id')
                ->where('cans.id', $id)
                ->where('can_servicio.establecimiento_id', $establecimiento_id)
                ->orderby('servicios.id','asc')
                ->get();

        $establecimientos=Establecimiento::find($establecimiento_id);
        
        /***************************************************************/
        $users = DB::table('users')->where('establecimiento_id',$establecimiento_id)->where('rol','>=',2)->where('rol','<',6)->where('estado',1)->orderby('rol','asc')->get();
        for($i=0;$i<7;$i++){
            for($j=0;$j<3;$j++){
                $user[$i][$j]="";    
            }
        }
        
        

        foreach ($users as $key => $usuario) {
            switch ($usuario->rol) {                
                case 3: 
                        $user[0][0]=$usuario->name;
                        $user[0][1]=$usuario->estado;
                        $user[0][2]=$usuario->id;
                    break;

                case 4:
                        $user[1][0]=$usuario->name;
                        $user[1][1]=$usuario->estado;
                        $user[1][2]=$usuario->id;
                    break;
                case 5: 
                        $user[2][0]=$usuario->name;
                        $user[2][1]=$usuario->estado;
                        $user[2][2]=$usuario->id;
                    break;

                case 6:
                        $user[3][0]=$usuario->name;
                        $user[3][1]=$usuario->estado;
                        $user[3][2]=$usuario->id;
                    break;

                case 8: 
                        $user[4][0]=$usuario->name;
                        $user[4][1]=$usuario->estado;
                        $user[4][2]=$usuario->id;
                    break;
                
            }
            
        }



        $login=DB::table('users')
                ->where('rol',2)
                ->where('estado',1)
                ->where('establecimiento_id',$establecimiento_id)
                ->orderby('establecimiento_id','asc')
                ->get();

        $login_comite=DB::table('users')
                ->where('rol','>',2)
                ->where('rol','<',9)
                ->where('estado',1)
                ->where('establecimiento_id',$establecimiento_id)
                ->orderby('establecimiento_id','asc')
                ->get();

        

        $consulta2 = DB::table('archivos')                
                ->select(DB::raw('count(*) as contar, establecimiento_id'))
                ->join('cans', 'cans.id', 'archivos.can_id')
                ->where('cans.id', $id)
                ->groupby('establecimiento_id')
                ->get();
        
        $rol=Auth::user()->rol;



        /***********************************************************/
        return view('admin.cans.mostrar_servicios')->with('can', $can)
                                      ->with('establecimientos', $establecimientos)
                                      ->with('consultas', $consulta)
                                      ->with('rubro_pf', $rubro_pf)
                                      ->with('can_id', $id)
                                      ->with('login', $login)
                                      ->with('rol', $rol)
                                      ->with('login_comite', $login_comite)
                                      ->with('consulta2', $consulta2)
                                      ->with('establecimiento_id',$establecimiento_id)
                                      ->with('rubro_mb_iq_pa', $rubro_mb_iq_pa)
                                      ->with('rubro_mil', $rubro_mil)
                                      ->with('rubro_mid', $rubro_mid)
                                      ->with('rubro_mff', $rubro_mff)
                                      ->with('user', $user);
    }

/*******************2************************/
public function activar_can_establecimiento($can_id, $establecimiento_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        //2/4/4
        $establecimiento_can = DB::table('can_establecimiento')
                ->where('can_id',$can_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();

        $cerrado_medicamento=$establecimiento_can->get(0)->medicamento_cerrado;
        
        $cerrado_dispositivo=$establecimiento_can->get(0)->dispositivo_cerrado;

        return view('admin.cans.activar_servicio')
                    ->with('cerrado_dispositivo', $cerrado_dispositivo)
                    ->with('cerrado_medicamento', $cerrado_medicamento)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function activar_can_establecimiento_stock($can_id, $establecimiento_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        //2/4/4
        $establecimiento_can = DB::table('can_establecimiento')
                ->where('can_id',$can_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();

        $cerrado_medicamento=$establecimiento_can->get(0)->medicamento_cerrado_stock;
        
        $cerrado_dispositivo=$establecimiento_can->get(0)->dispositivo_cerrado_stock;

        return view('admin.cans.activar_servicio_stock')
                    ->with('cerrado_dispositivo', $cerrado_dispositivo)
                    ->with('cerrado_medicamento', $cerrado_medicamento)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function activar_can_establecimiento_rectificacion($can_id, $establecimiento_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        //2/4/4
        $establecimiento_can = DB::table('can_establecimiento')
                ->where('can_id',$can_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();

        $cerrado_medicamento=$establecimiento_can->get(0)->medicamento_cerrado_rectificacion;
        
        $cerrado_dispositivo=$establecimiento_can->get(0)->dispositivo_cerrado_rectificacion;

        return view('admin.cans.activar_servicio_rectificacion')
                    ->with('cerrado_dispositivo', $cerrado_dispositivo)
                    ->with('cerrado_medicamento', $cerrado_medicamento)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function ampliacion_ipress($can_id, $establecimiento_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        //2/4/4
        /*$establecimiento_can = DB::table('can_establecimiento')
                ->where('can_id',$can_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();
        */
        
        return view('admin.cans.ampliacion_ipress')
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function ampliacion_servicio($can_id, $establecimiento_id, $servicio_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        
        return view('admin.cans.ampliacion_servicio')
                    ->with('can',$can)
                    ->with('servicio_id',$servicio_id)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function ampliacion_rubro($can_id, $establecimiento_id, $rubro_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        
        return view('admin.cans.ampliacion_rubro')
                    ->with('can',$can)
                    ->with('rubro_id',$rubro_id)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function update_can_establecimiento(Request $request,$can_id,$establecimiento_id)
    {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }

        $nivel=$establecimiento->nivel_id;
        
        $medicamento=$request->input('cerrado_medicamento');
        if($medicamento==null)            
        {
            $medicamento=1;
        }
        else
        {
            $medicamento=2;
        }
        
        $dispositivo=$request->input('cerrado_dispositivo');

        if($dispositivo==null)
        {
            $dispositivo=1;   
        }
        else
        {
            $dispositivo=2;
        }



        /*******************************/
                //actualizamos los estados de los medicamentos y dispositivos cerrado
        if($nivel>1){
            $total_servicio = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->count();

        //contamos cuantas farmacias han cerrado en medicamentos
        $servicio_medicamento_cerrado = DB::table('can_servicio')
                                    ->where('medicamento_cerrado','!=',1)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();

        //contamos cuantas farmacias han cerrado en dispositivos
        $servicio_dispositivo_cerrado = DB::table('can_servicio')
                                    ->where('dispositivo_cerrado','!=',1)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();
        

        //si se han cerrado todas las farmacias con medicamentos y dispositivos, cerramos el establecimiento                                    
        if($servicio_medicamento_cerrado == $servicio_dispositivo_cerrado){
            if ($total_servicio == $servicio_medicamento_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
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
            if ($total_servicio == $servicio_medicamento_cerrado){
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
            if ($total_servicio == $servicio_dispositivo_cerrado){
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
    
        }
        else
        {
            DB::table('can_establecimiento')
            ->where('can_id', $can_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->update([
                        'medicamento_cerrado' => $medicamento,
                        'dispositivo_cerrado' => $dispositivo,   
                        'updated_at'=>Carbon::now()
                    ]);
        }
        
        /*******************************/
        
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.show',[$can_id]));
    }


public function update_can_establecimiento_stock(Request $request,$can_id,$establecimiento_id)
    {
        

        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }

        $nivel=$establecimiento->nivel_id;
        
        $medicamento=$request->input('cerrado_medicamento');
        if($medicamento==null)            
        {
            $medicamento=1;
        }
        else
        {
            $medicamento=2;
        }
        
        $dispositivo=$request->input('cerrado_dispositivo');

        if($dispositivo==null)
        {
            $dispositivo=1;   
        }
        else
        {
            $dispositivo=2;
        }

        /*******************************/
                //actualizamos los estados de los medicamentos y dispositivos cerrado
        DB::table('can_establecimiento')
            ->where('can_id', $can_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->update([
                        'medicamento_cerrado_stock' => $medicamento,
                        'dispositivo_cerrado_stock' => $dispositivo,   
                        'updated_at'=>Carbon::now()
                    ]);
        
        /*******************************/
        Flash::success('Se Actualizado satisfactoriamente.');

        return redirect(route('cans.show',[$can_id]));
    }

public function update_can_establecimiento_rectificacion(Request $request,$can_id,$establecimiento_id)
    {
        

        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }

        $nivel=$establecimiento->nivel_id;
        
        $medicamento=$request->input('cerrado_medicamento');
        if($medicamento==null)            
        {
            $medicamento=1;
        }
        else
        {
            $medicamento=2;
        }
        
        $dispositivo=$request->input('cerrado_dispositivo');

        if($dispositivo==null)
        {
            $dispositivo=1;   
        }
        else
        {
            $dispositivo=2;
        }

        /*******************************/
                //actualizamos los estados de los medicamentos y dispositivos cerrado
        DB::table('can_establecimiento')
            ->where('can_id', $can_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->update([
                        'medicamento_cerrado_rectificacion' => $medicamento,
                        'dispositivo_cerrado_rectificacion' => $dispositivo,   
                        'updated_at'=>Carbon::now()
                    ]);
        
        /*******************************/
        Flash::success('Se Actualizado satisfactoriamente.');

        return redirect(route('cans.show',[$can_id]));
    }

public function update_can_rubro_establecimiento(Request $request,$can_id,$establecimiento_id,$servicio_id)   {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }

        $nivel=$establecimiento->nivel_id;
        
        switch ($servicio_id) {
            case 1: $texto='rubro_pf';break;
            case 2: $texto='rubro_mb_iq_pa';break;
            case 3: $texto='rubro_mid';break;
            case 4: $texto='rubro_mil';break;
            case 5: $texto='rubro_mff';break;
        }

        if($servicio_id==1){
            $medicamento=$request->input('cerrado_medicamento');
            if($medicamento==null)            
            {
                $medicamento=1;
                DB::table('can_establecimiento')
                        ->where('can_id','=',$can_id)
                        ->where('establecimiento_id', $establecimiento_id)
                        ->update([
                                'rubro_pf' => 1,                            
                                'medicamento_cerrado' => 1,                            
                                'updated_at'=>Carbon::now()
                    ]);                                        
            }
            else
            {
                $medicamento=2;
                DB::table('can_establecimiento')
                        ->where('can_id','=',$can_id)
                        ->where('establecimiento_id', $establecimiento_id)
                        ->update([
                                'rubro_pf' => 2,           
                                'medicamento_cerrado' => 2,                                             
                                'updated_at'=>Carbon::now()
                    ]);                                        
            }
    
        }
        else
        {
            $dispositivo=$request->input('cerrado_dispositivo');

            if($dispositivo==null)
            {
                $dispositivo=1;   

                DB::table('can_establecimiento')
                        ->where('can_id','=',$can_id)
                        ->where('establecimiento_id', $establecimiento_id)
                        ->update([
                                $texto => 1,           
                                'dispositivo_cerrado' => 1,                                             
                                'updated_at'=>Carbon::now()
                    ]);                                        
            }
            else
            {
                $dispositivo=2;
                DB::table('can_establecimiento')
                        ->where('can_id','=',$can_id)
                        ->where('establecimiento_id', $establecimiento_id)
                        ->update([
                                $texto => 2,                            
                                'updated_at'=>Carbon::now()
                    ]);         

                $contar=DB::table('can_establecimiento')
                        ->where('can_id','=',$can_id)
                        ->where('establecimiento_id', $establecimiento_id)
                        ->where('rubro_mb_iq_pa',2)
                        ->where('rubro_mid',2)
                        ->where('rubro_mil',2)
                        ->where('rubro_mff',2)
                        ->count();

                //dd($contar);

                if($contar==1)
                {
                    DB::table('can_establecimiento')
                        ->where('can_id','=',$can_id)
                        ->where('establecimiento_id', $establecimiento_id)
                        ->update([
                                'dispositivo_cerrado' => 2,                                             
                                'updated_at'=>Carbon::now()
                    ]);         
                }
            }            
        }
        
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.mostrar_servicios',[$can_id,$establecimiento_id]));
    }

public function update_user_tiempo(Request $request,$can_id,$establecimiento_id)
    {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }

        $tiempo_porroga = new Carbon;

        $fin_first_login=$tiempo_porroga->addDays($request->tiempo_id);
        $fin_first_login->modify('+'.$request->hora_id.'hour');

        DB::table('users')
            ->where('establecimiento_id', $establecimiento_id)
            ->where('estado',1)
            ->update([
                    'fin_first_login' => $fin_first_login,
                    'updated_at'=>Carbon::now()
        ]);                                        
        
        /*******************************/       
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.show',[$can_id]));
    }
public function update_servicio_tiempo(Request $request,$can_id,$establecimiento_id,$servicio_id)
    {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }

        $tiempo_porroga = new Carbon;

        $fin_first_login=$tiempo_porroga->addDays($request->tiempo_id);
        $fin_first_login->modify('+'.$request->hora_id.'hour');

        DB::table('users')
            ->where('establecimiento_id', $establecimiento_id)
            ->where('rol', 2)
            ->where('servicio_id', $servicio_id)
            ->where('estado',1)
            ->update([
                    'fin_first_login' => $fin_first_login,
                    'updated_at'=>Carbon::now()
        ]);                                        
        
        /*******************************/       
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.mostrar_servicios',[$can_id,$establecimiento_id]));
    }

public function update_rubro_tiempo(Request $request,$can_id,$establecimiento_id,$rubro_id)
    {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }


        $tiempo_porroga = new Carbon;

        $fin_first_login=$tiempo_porroga->addDays($request->tiempo_id);
        $fin_first_login->modify('+'.$request->hora_id.'hour');

        DB::table('users')
            ->where('establecimiento_id', $establecimiento_id)
            ->where('rol', $rubro_id)
            ->where('password','<>', '$password')
            ->where('estado',1)
            ->update([
                    'fin_first_login' => $fin_first_login,
                    'updated_at'=>Carbon::now()
        ]);                                        
        
        /*******************************/       
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.mostrar_servicios',[$can_id,$establecimiento_id]));
    }

public function activar_rubro_establecimiento($can_id, $establecimiento_id, $servicio_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        
        //2/4/4
        $establecimiento_can_servicio = DB::table('can_establecimiento')
                ->where('can_id',$can_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();
        $cerrado_medicamento=0;
        $cerrado_dispositivo=0;

        switch ($servicio_id) {
            case 1:$cerrado_medicamento=$establecimiento_can_servicio->get(0)->rubro_pf;break;
            case 2:$cerrado_dispositivo=$establecimiento_can_servicio->get(0)->rubro_mb_iq_pa;break;
            case 3:$cerrado_dispositivo=$establecimiento_can_servicio->get(0)->rubro_mid;break;
            case 4:$cerrado_dispositivo=$establecimiento_can_servicio->get(0)->rubro_mil;break;
            case 5:$cerrado_dispositivo=$establecimiento_can_servicio->get(0)->rubro_mff;break;           
            
        }

        return view('admin.cans.activar_rubro_servicio')
                    ->with('cerrado_medicamento', $cerrado_medicamento)
                    ->with('cerrado_dispositivo', $cerrado_dispositivo)
                    ->with('servicio_id', $servicio_id)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }
public function activar_servicio_establecimiento($can_id, $establecimiento_id, $servicio_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        //2/4/4
        $establecimiento_can_servicio = DB::table('can_servicio')
                ->where('can_id',$can_id)
                ->where('servicio_id',$servicio_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();

        //dd($establecimiento_can_servicio);
        $cerrado_dispositivo=$establecimiento_can_servicio->get(0)->dispositivo_cerrado;        
        $cerrado_medicamento=$establecimiento_can_servicio->get(0)->medicamento_cerrado;        
        
        return view('admin.cans.activar_can_servicio')
                    ->with('cerrado_medicamento', $cerrado_medicamento)
                    ->with('cerrado_dispositivo', $cerrado_dispositivo)
                    ->with('servicio_id', $servicio_id)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function activar_servicio_rectificacion_establecimiento($can_id, $establecimiento_id, $servicio_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        //2/4/4
        $establecimiento_can_servicio = DB::table('can_servicio')
                ->where('can_id',$can_id)
                ->where('servicio_id',$servicio_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();

        //dd($establecimiento_can_servicio);
        $cerrado_dispositivo_rectificacion=$establecimiento_can_servicio->get(0)->dispositivo_cerrado_rectificacion;        
        $cerrado_medicamento_rectificacion=$establecimiento_can_servicio->get(0)->medicamento_cerrado_rectificacion;        
        
        return view('admin.cans.activar_can_servicio_rectificacion')
                    ->with('cerrado_dispositivo_rectificacion', $cerrado_dispositivo_rectificacion)
                    ->with('cerrado_medicamento_rectificacion', $cerrado_medicamento_rectificacion)
                    ->with('servicio_id', $servicio_id)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }

public function update_servicio_establecimiento(Request $request,$can_id,$establecimiento_id,$servicio_id)
    {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }
        
        $medicamento=$request->input('cerrado_medicamento');

        if($medicamento==null)            
        {
            $medicamento=1;
        }
        else
        {   if($medicamento==1)            
                $medicamento=2;
        }
        
        
        $dispositivo=$request->input('cerrado_dispositivo');

        if($dispositivo==null)
        {
            $dispositivo=1;
        }
        else
        {   if($dispositivo==1)            
                $dispositivo=2;
        }
        
        
        /*switch ($servicio_id) {
            case 'value':
                # code...
                break;
            
            default:
                # code...
                break;
        }*/
        
        DB::table('can_servicio')
            ->where('can_id', $can_id)
            ->where('servicio_id', $servicio_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->update([
                        'medicamento_cerrado' => $medicamento,   
                        'dispositivo_cerrado' => $dispositivo,     
                        'updated_at'=>Carbon::now()
                    ]);

        /******************************/
        //actualizamos los estados de los medicamentos y dispositivos cerrado
        $total_servicio = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->count();

        //contamos cuantas farmacias han cerrado en medicamentos
        $servicio_medicamento_cerrado = DB::table('can_servicio')
                                    ->where('medicamento_cerrado','!=',1)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();

        //contamos cuantas farmacias han cerrado en dispositivos
        $servicio_dispositivo_cerrado = DB::table('can_servicio')
                                    ->where('dispositivo_cerrado','!=',1)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();
        

        //si se han cerrado todas las farmacias con medicamentos y dispositivos, cerramos el establecimiento                                    
        if($servicio_medicamento_cerrado == $servicio_dispositivo_cerrado){
            if ($total_servicio == $servicio_medicamento_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
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
            if ($total_servicio == $servicio_medicamento_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado' => 2,
                            'rubro_pf' => 2,
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
                            'rubro_pf' => 1,
                            'updated_at'=>Carbon::now()
                ]); 
            }    
            if ($total_servicio == $servicio_dispositivo_cerrado){
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

        /******************************/
        
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.mostrar_servicios',[$can_id,$establecimiento_id]));
    }

public function update_servicio_establecimiento_rectificacion(Request $request,$can_id,$establecimiento_id,$servicio_id)
    {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }
        
        $medicamento=$request->input('cerrado_medicamento_rectificacion');

        if($medicamento==null)            
        {
            $medicamento=1;
        }
        else
        {   if($medicamento==1)            
                $medicamento=2;
        }
        
        
        $dispositivo=$request->input('cerrado_dispositivo_rectificacion');

        if($dispositivo==null)
        {
            $dispositivo=1;
        }
        else
        {   if($dispositivo==1)            
                $dispositivo=2;
        }
        
        
        /*switch ($servicio_id) {
            case 'value':
                # code...
                break;
            
            default:
                # code...
                break;
        }*/
        
        DB::table('can_servicio')
            ->where('can_id', $can_id)
            ->where('servicio_id', $servicio_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->update([
                        'medicamento_cerrado_rectificacion' => $medicamento,   
                        'dispositivo_cerrado_rectificacion' => $dispositivo,     
                        'updated_rectificacion'=>Carbon::now()
                    ]);

        /******************************/
        //actualizamos los estados de los medicamentos y dispositivos cerrado
        $total_servicio = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->count();

        //contamos cuantas farmacias han cerrado en medicamentos
        $servicio_medicamento_cerrado = DB::table('can_servicio')
                                    ->where('medicamento_cerrado_rectificacion','!=',1)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();

        //contamos cuantas farmacias han cerrado en dispositivos
        $servicio_dispositivo_cerrado = DB::table('can_servicio')
                                    ->where('dispositivo_cerrado_rectificacion','!=',1)
                                    ->where('can_id','=',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->count();
        

        //si se han cerrado todas las farmacias con medicamentos y dispositivos, cerramos el establecimiento                                    
        if($servicio_medicamento_cerrado == $servicio_dispositivo_cerrado){
            if ($total_servicio == $servicio_medicamento_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado_rectificacion' => 2,
                            'dispositivo_cerrado_rectificacion' => 2,
                            'updated_rectificacion'=>Carbon::now()
                ]);                                        
            } 
        }
        else
        {   
            if ($total_servicio == $servicio_medicamento_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado' => 2,
                            'rubro_pf' => 2,
                            'updated_rectificacion'=>Carbon::now()
                ]);                                        
            }
            else
            {
                DB::table('can_establecimiento')
                    ->where('can_id','=',$can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'medicamento_cerrado_rectificacion' => 1,
                            'rubro_pf' => 1,
                            'updated_rectificacion'=>Carbon::now()
                ]); 
            }    
            if ($total_servicio == $servicio_dispositivo_cerrado){
                DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'dispositivo_cerrado_rectificacion' => 2,
                            'updated_rectificacion'=>Carbon::now()
                ]);                                        
            }  
            else
            {
                DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'dispositivo_cerrado_rectificacion' => 1,
                            'updated_rectificacion'=>Carbon::now()
                ]); 
            }    
        }       

        /******************************/
        
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.mostrar_servicios',[$can_id,$establecimiento_id]));
    }
    
    public function habilitar_servicio_establecimiento($can_id, $establecimiento_id, $servicio_id)
    {
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$ici_id));
        }
        //2/4/4
        $establecimiento_can_servicio = DB::table('can_servicio')
                ->where('can_id',$can_id)
                ->where('servicio_id',$servicio_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();

        $cerrado_dispositivo=$establecimiento_can_servicio->get(0)->dispositivo_cerrado;        
        $cerrado_medicamento=$establecimiento_can_servicio->get(0)->medicamento_cerrado;        
        
        //dd($cerrado_medicamento);
        //dd($cerrado_dispositivo);

        return view('admin.cans.habilitar_can_servicio')
                    ->with('cerrado_medicamento', $cerrado_medicamento)
                    ->with('cerrado_dispositivo', $cerrado_dispositivo)
                    ->with('servicio_id', $servicio_id)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }

    public function update_habilitar_servicio(Request $request,$can_id,$establecimiento_id,$servicio_id)
    {
        $can = $this->canRepository->findWithoutFail($can_id);

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.show',$can_id));
        }
        
        $medicamento=$request->input('cerrado_medicamento');
        
        if($medicamento==null)            
        {
            $medicamento=3;
        }
        else
        {
            $medicamento=1;           
        }
        
        $dispositivo=$request->input('cerrado_dispositivo');
        
        if($dispositivo==null)
        {
            $dispositivo=3;
        }
        else
        {
            $dispositivo=1;            
        }
        

        
        DB::table('can_servicio')
            ->where('can_id', $can_id)
            ->where('servicio_id', $servicio_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->update([
                        'medicamento_cerrado' => $medicamento,   
                        'dispositivo_cerrado' => $dispositivo,     
                        'updated_at'=>Carbon::now()
                    ]);
        
        Flash::success('Actualizado satisfactoriamente.');

        return redirect(route('cans.mostrar_servicios',[$can_id,$establecimiento_id]));
        
    }

/************************************/
//Tablero de control
public function tablero_control($can_id)
    {
        
            $consolidar_indicadores = DB::table('abastecimientos')
             ->select(DB::raw('count(*) as contar, indicadores'))
             ->where('can_id',$can_id)
             ->groupBy('indicadores')
             ->get();

            $total_items = DB::table('abastecimientos')
                     ->where('can_id',$can_id)
                     ->count();
           
            if($total_items>0){


            //numero de items que existe al momento de consolidar
            $cantidad_items=count($consolidar_indicadores);

                        
            //recorremos cada indicador y asignamos las cantidades            
            for ($i = 0; $i < $cantidad_items; $i++){
                
                switch ($consolidar_indicadores->get($i)->indicadores) {
                    case 'NORMOSTOCK': 
                            $cantidad_normostock=$consolidar_indicadores->get($i)->contar;
                            break;

                    case 'SUBSTOCK': 
                            $cantidad_substock=$consolidar_indicadores->get($i)->contar;
                            break;

                    case 'SOBRESTOCK': 
                            $cantidad_sobrestock=$consolidar_indicadores->get($i)->contar;
                            break;

                    case 'SIN ROTACION': 
                            $cantidad_sinrotacion=$consolidar_indicadores->get($i)->contar;
                            break;

                    case 'DESABASTECIDO': 
                            $cantidad_desabastecido=$consolidar_indicadores->get($i)->contar;
                            break;
                }                
            }

            $cantidad_existente=$cantidad_normostock+$cantidad_substock+$cantidad_sobrestock;
            
            $cantidad_disponible=$cantidad_normostock+$cantidad_sobrestock;
            
            }
            return view('admin.cans.tablero')->with('cantidad_normostock', $cantidad_normostock)
                                      ->with('cantidad_substock', $cantidad_substock)
                                      ->with('cantidad_sobrestock', $cantidad_sobrestock)
                                      ->with('cantidad_sinrotacion', $cantidad_sinrotacion)
                                      ->with('cantidad_desabastecido', $cantidad_desabastecido)
                                      ->with('cantidad_existente', $cantidad_existente)
                                      ->with('cantidad_disponible', $cantidad_disponible)
                                      ->with('can_id', $can_id)
                                      ->with('total_items', $total_items);
                                      
    }
/************************************/
public function descargar_can($can_id)
{
    $can = $this->canRepository->findWithoutFail($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        
        $consulta = DB::table('consolidados')
                    ->join('tipo_dispositivo_medicos','tipo_dispositivo_medicos.id','consolidados.tipo_dispositivo.id')
                    ->join('tipo_usos','tipo_usos.id','consolidados.uso_id')
                    ->where('can_id',$can_id)
                    ->get();
        

        return view('admin.cans.tabla_consolidado')->with('can', $can)
                                      ->with('consulta', $consulta)
                                      ->with('descripcion', $descripcion);

}
public function descargar_productos($can_id,$establecimiento_id,$indicador)
    {
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;

        switch ($indicador) {
            case '2': $texto='NORMOSTOCK';            
                break;
            
            case '3': $texto='SUBSTOCK';
                break;

            case '4': $texto='SOBRESTOCK';                
                break;

            case '5': $texto='SIN ROTACION';                
                break;

            case '6': $texto='DESABASTECIDO';                
                break;

            case '7': $texto='NEGATIVO';                
                break;

            case '8': $texto='NEGATIVO';                
                break;
        }

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

                $data=DB::table('abastecimientos')->
                        where( function ( $query )
                        {
                            $query->orWhere('unidad_ingreso','>',0)
                                ->orWhere('valor_ingreso','>',0)
                                ->orWhere('stock_final','>',0)                                        
                                ->orWhere('total_salidas','>',0);
                        })
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('indicadores',$texto)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();            
        
        return view('admin.cans.reportes.descargar_productos')
            ->with('cans', $can)
            ->with('indicador', $indicador)
            ->with('can_id', $can_id)
            ->with('texto', $texto)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('abastecimientos', $data);
    }

/*********************************/
public function reportes($id, $can_id)
    {
        //$establecimientos=Establecimiento::all();
        $can = Can::find($can_id);
        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        } 
        if ($id > 8  ) {
            Flash::error('Reporte no encontrado');
            return redirect(route('estimacion.index'));
        }       
        //Consulta Normostock
        switch ($id) {
            case '1': //Consolidado
                    $indicadores=Indicador::orderBy('normostock_porcentaje', 'desc')
                                ->where('can_id', $can_id)
                                ->get();
                break;
            
            case '2': //NormoStock
                    $indicadores=Indicador::orderBy('normostock_porcentaje', 'desc')
                                ->where('can_id', $can_id)
                                ->get();
                break;

            case '3': //SubStock
                    $indicadores=Indicador::orderBy('substock_porcentaje', 'asc')
                                ->where('can_id', $can_id)
                                ->get();
                break;

            case '4': //Consulta Sobrestock
                    $indicadores=Indicador::orderBy('sobrestock_porcentaje', 'asc')
                                ->where('can_id', $can_id)
                                ->get();
                break;

            case '5': //Consulta Sinrotacion
                    $indicadores=Indicador::orderBy('sinrotacion_porcentaje', 'asc')
                                ->where('can_id', $can_id)
                                ->get();        
                break;

            case '6': //Consulta Desabastecido
                    $indicadores=Indicador::orderBy('desabastecido_porcentaje', 'asc')
                                ->where('can_id', $can_id)
                                ->get();
                break;

            case '7': //Consulta Existencia
                    $indicadores=Indicador::orderBy('existente_porcentaje', 'desc')
                                ->where('can_id', $can_id)
                                ->get();
                break;

            case '8': //Consulta Existencia
                    $indicadores=Indicador::orderBy('disponible_porcentaje', 'desc')
                                ->where('can_id', $can_id)
                                ->get();                
                break;
        }
        
        return view('admin.cans.reportes')
        ->with('indicadores', $indicadores)
        ->with('mes', $can->desc_mes)
        ->with('ano', $can->ano)
        ->with('can_id', $can_id)
        ->with('id', $id);
    }

//**********************************/
protected function calcular_indicadores($can_id,$establecimiento_id)
    {

            //Contamos los indicadores de cada establecimiento
            $consolidar_indicadores = DB::table('abastecimientos')
                     ->select(DB::raw('count(*) as contar, indicadores'))
                     ->where('can_id',$can_id)
                     ->where('establecimiento_id',$establecimiento_id)
                     ->groupBy('indicadores')
                     ->get();

            $total_items = DB::table('abastecimientos')
                     ->where('can_id',$can_id)
                     ->where('establecimiento_id',$establecimiento_id)
                     ->count();
           
            //numero de items que existe al momento de consolidar
            $cantidad_items=count($consolidar_indicadores);

            $cantidad_normostock=0;$puntaje_normostock=0;$porcentaje_normostock=0;
            $cantidad_substock=0;$puntaje_substock=0;$porcentaje_substock=0;
            $cantidad_sobrestock=0;$puntaje_sobrestock=0;$porcentaje_sobrestock=0;
            $cantidad_sinrotacion=0;$puntaje_sinrotacion=0;$porcentaje_sinrotacion=0;
            $cantidad_desabastecido=0;$puntaje_desabastecido=0;$porcentaje_desabastecido=0;
            $cantidad_existente=0;$puntaje_existente=0;$porcentaje_existente=0;
            $cantidad_disponible=0;$puntaje_disponible=0;$porcentaje_disponible=0;
                        
            //recorremos cada indicador y asignamos las cantidades            
            for ($i = 0; $i < $cantidad_items; $i++){
                
                switch ($consolidar_indicadores->get($i)->indicadores) {
                    case 'NormoStock': 
                            $cantidad_normostock=$consolidar_indicadores->get($i)->contar;
                            $porcentaje_normostock=$cantidad_normostock*100/$total_items;
                            $puntaje_normostock=$porcentaje_normostock/100;

                            break;
                    case 'SubStock': 
                            $cantidad_substock=$consolidar_indicadores->get($i)->contar;
                            $porcentaje_substock=$cantidad_substock*100/$total_items;
                            $puntaje_substock=1-($porcentaje_substock/100);

                            break;
                    case 'SobreStock': 
                            $cantidad_sobrestock=$consolidar_indicadores->get($i)->contar;
                            $porcentaje_sobrestock=$cantidad_sobrestock*100/$total_items;
                            $puntaje_sobrestock=1-($porcentaje_sobrestock/100);

                            break;
                    case 'Sin Rotacion': 
                            $cantidad_sinrotacion=$consolidar_indicadores->get($i)->contar;
                            $porcentaje_sinrotacion=$cantidad_sinrotacion*100/$total_items;
                            $puntaje_sinrotacion=1-(5*$porcentaje_sinrotacion/100);

                            break;
                    case 'Desabastecido': 
                            $cantidad_desabastecido=$consolidar_indicadores->get($i)->contar;
                            $porcentaje_desabastecido=$cantidad_desabastecido*100/$total_items;
                            $puntaje_desabastecido=2-(2*$porcentaje_desabastecido/100);

                            break;
                }                
            }
            $cantidad_existente=$cantidad_normostock+$cantidad_substock+$cantidad_sobrestock;
            $porcentaje_existente=$cantidad_existente*100/$total_items;
            $puntaje_existente=2*$porcentaje_existente/100;                    

            $cantidad_disponible=$cantidad_normostock+$cantidad_sobrestock;
            $porcentaje_disponible=$cantidad_disponible*100/$total_items;
            $puntaje_disponible=2*$porcentaje_disponible/100;    
    
            $total_puntaje=$puntaje_normostock+$puntaje_substock+$puntaje_sobrestock+$puntaje_sinrotacion+$puntaje_desabastecido+$puntaje_existente+$puntaje_disponible;

                DB::table('indicadores')
                ->where('can_id', $can_id)
                ->where('establecimiento_id', $establecimiento_id)
                ->update([
                        'normostock_cantidad'=>$cantidad_normostock,
                        'normostock_porcentaje'=>$porcentaje_normostock,
                        'normostock_puntaje'=>$puntaje_normostock,
                        
                        'substock_cantidad'=>$cantidad_substock,
                        'substock_porcentaje'=>$porcentaje_substock,
                        'substock_puntaje'=>$puntaje_substock,

                        'sobrestock_cantidad'=>$cantidad_sobrestock,
                        'sobrestock_porcentaje'=>$porcentaje_sobrestock,
                        'sobrestock_puntaje'=>$puntaje_sobrestock,
                        
                        'sinrotacion_cantidad'=>$cantidad_sinrotacion,
                        'sinrotacion_porcentaje'=>$porcentaje_sinrotacion,
                        'sinrotacion_puntaje'=>$puntaje_sinrotacion,

                        'desabastecido_cantidad'=>$cantidad_desabastecido,
                        'desabastecido_porcentaje'=>$porcentaje_desabastecido,
                        'desabastecido_puntaje'=>$puntaje_desabastecido,

                        'existente_cantidad'=>$cantidad_existente,
                        'existente_porcentaje'=>$porcentaje_existente,
                        'existente_puntaje'=>$puntaje_existente,

                        'disponible_cantidad'=>$cantidad_disponible,
                        'disponible_porcentaje'=>$porcentaje_disponible,
                        'disponible_puntaje'=>$puntaje_disponible,

                        'total_puntaje'=>$total_puntaje,
                        'total_items'=>$total_items,
                        'updated_at'=>Carbon::now()
                ]);            
    }

    public function medicamentos_rubros(Request $request,$can_id, $establecimiento_id, $rubro_id)
    {
        //buscamos el establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        //si encuentra o no el establecimiento
        if (empty($establecimiento)) {
            Flash::error('Establecimientos CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
        
        $can = Can::find($can_id);
        if (empty($can)) {
            Flash::error('No se tiene un CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
     
        $estimacion_rubro=DB::table('estimacion_rubro')
                        ->where('can_id',$can_id)
                        ->where('necesidad_anual','>',0)
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('rubro_id',$rubro_id)
                        ->get();
        
        return view('admin.cans.medicamentos.medicamentos')
            ->with('estimaciones_rubros', $estimacion_rubro)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('rubro_id', $rubro_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }

    public function medicamentos_servicios(Request $request,$can_id, $establecimiento_id, $servicio_id)
    {
        //buscamos el establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        //si encuentra o no el establecimiento
        if (empty($establecimiento)) {
            Flash::error('Establecimientos CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
        
        $can = Can::find($can_id);
        if (empty($can)) {
            Flash::error('No se tiene un CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
     
        $estimacion_servicio=DB::table('estimacion_servicio')
                        ->where('can_id',$can_id)
                        ->where('tipo_dispositivo_id',1)
                        //->where('necesidad_anual','>',0)
                        //->where('estado_necesidad',0)
                        //->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->get();

        $contar=DB::table('estimacion_servicio')
                        ->where('can_id',$can_id)
                        ->where('tipo_dispositivo_id',1)
                        //->where('necesidad_anual','>',0)
                        //->where('estado_necesidad',0)
                        //->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->count();

        $can_servicio=DB::table('can_servicio')
                        ->where('can_id',$can_id)                        
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->first();
        //dd($can_servicio);
        $cerrado=$can_servicio->medicamento_cerrado;
        
        if($contar==0)
            $nombre_servicio='';
        else            
            $nombre_servicio=$estimacion_servicio->get(0)->nombre_servicio;
        
        $comite=0;
        return view('admin.cans.medicamentos.medicamentos_servicios')
            ->with('estimaciones_servicios', $estimacion_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('cerrado', $cerrado)
            ->with('comite', $comite)
            ->with('servicio_id', $servicio_id)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }

    public function medicamentos_servicios_rectificacion(Request $request,$can_id, $establecimiento_id, $servicio_id)
    {
        //buscamos el establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        //si encuentra o no el establecimiento
        if (empty($establecimiento)) {
            Flash::error('Establecimientos CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
        
        $can = Can::find($can_id);
        if (empty($can)) {
            Flash::error('No se tiene un CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
     
        $estimacion_servicio=DB::table('estimacion_servicio')
                        ->where('can_id',$can_id)
                        ->where('tipo_dispositivo_id',1)
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->get();

        $contar=DB::table('estimacion_servicio')
                        ->where('can_id',$can_id)
                        ->where('tipo_dispositivo_id',1)
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->count();

        $can_servicio=DB::table('can_servicio')
                        ->where('can_id',$can_id)                        
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->first();
        $cerrado=$can_servicio->medicamento_cerrado;
        
        if($contar==0)
            $nombre_servicio='';
        else            
            $nombre_servicio=$estimacion_servicio->get(0)->nombre_servicio;
        
        $comite=0;
        return view('admin.cans.medicamentos.medicamentos_servicios_rectificacion')
            ->with('estimaciones_servicios', $estimacion_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('cerrado', $cerrado)
            ->with('comite', $comite)
            ->with('servicio_id', $servicio_id)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }

    public function medicamentos_servicios_rubros(Request $request,$can_id, $establecimiento_id, $rubro_id)
    {
        
        //buscamos el establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        //si encuentra o no el establecimiento
        if (empty($establecimiento)) {
            Flash::error('Establecimientos CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
        
        $can = Can::find($can_id);
        if (empty($can)) {
            Flash::error('No se tiene un CAN con esas caracteristicas');
            return redirect(route('estimacion.index'));
        }
     
        
        $contar=DB::table('estimacions')
                        ->where('can_id',$can_id)
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)                        
                        ->count();

        /***********************************/
        $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.necesidad_actual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id=1
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id=1
Order by ET.descripcion asc
';
$estimacion_servicio = DB::select($cad);

/****************************************************/
        
        if($contar==0)
            $nombre_servicio='';
        else            
            $nombre_servicio='Productos Farmaceuticos';

        $cerrado=0;
        $comite=1;
       
        return view('admin.cans.medicamentos.medicamentos_servicios_rubros')
            ->with('estimaciones_servicios', $estimacion_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $rubro_id)
            ->with('comite', $comite)
            ->with('cerrado', $cerrado)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }


    public function dispositivos_rubros(Request $request,$can_id, $establecimiento_id, $rubro_id)
    {
            //buscamos el establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimientos CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }
            $can = Can::find($can_id);
            if (empty($can)) {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }
         
        $estimacion_rubro=DB::table('estimacion_rubro')
                    ->where('can_id',$can_id)
                    ->where('necesidad_anual','>',0)
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('rubro_id',$rubro_id)
                    ->get();
        
        return view('admin.cans.dispositivos.dispositivos')
            ->with('estimaciones_rubros', $estimacion_rubro)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('rubro_id', $rubro_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }

    public function dispositivos_servicios(Request $request,$can_id, $establecimiento_id, $servicio_id)
    {
            //buscamos el establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimientos CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }
            $can = Can::find($can_id);
            if (empty($can)) {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }
         
        $estimacion_servicio=DB::table('estimacion_servicio')
                    ->where('can_id',$can_id)
                    ->where('necesidad_anual','>',0)
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado_necesidad',0)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('servicio_id',$servicio_id)
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

        $contar=DB::table('estimacion_servicio')
                            ->where('can_id',$can_id)
                            ->where('necesidad_anual','>',0)
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('estado_necesidad',0)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id',$servicio_id)
                            ->count();

        $can_servicio=DB::table('can_servicio')
                        ->where('can_id',$can_id)                        
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->first();
        $cerrado=$can_servicio->dispositivo_cerrado;

        
        if($contar==0)
            $nombre_servicio='';
        else            
            $nombre_servicio=$estimacion_servicio->get(0)->nombre_servicio;
    
        $comite=0;

        return view('admin.cans.dispositivos.dispositivos_servicios')
            ->with('estimaciones_servicios', $estimacion_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('comite', $comite)
            ->with('cerrado', $cerrado)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }

public function dispositivos_servicios_rectificacion(Request $request,$can_id, $establecimiento_id, $servicio_id)
    {
            //buscamos el establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimientos CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }
            $can = Can::find($can_id);
            if (empty($can)) {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }
         
        $estimacion_servicio=DB::table('estimacion_servicio')
                    ->where('can_id',$can_id)
                    ->where('necesidad_anual','>',0)
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('servicio_id',$servicio_id)
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

        $contar=DB::table('estimacion_servicio')
                            ->where('can_id',$can_id)
                            ->where('necesidad_anual','>',0)
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('estado','<>',2)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id',$servicio_id)
                            ->count();

        $can_servicio=DB::table('can_servicio')
                        ->where('can_id',$can_id)                        
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->first();
        $cerrado=$can_servicio->dispositivo_cerrado;

        
        if($contar==0)
            $nombre_servicio='';
        else            
            $nombre_servicio=$estimacion_servicio->get(0)->nombre_servicio;
    
        $comite=0;

        return view('admin.cans.dispositivos.dispositivos_servicios_rectificacion')
            ->with('estimaciones_servicios', $estimacion_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('comite', $comite)
            ->with('cerrado', $cerrado)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }

    public function dispositivos_servicios_rubros(Request $request,$can_id, $establecimiento_id, $rubro_id)
    {
            //buscamos el establecimiento
            $establecimiento = Establecimiento::find($establecimiento_id);
            //si encuentra o no el establecimiento
            if (empty($establecimiento)) {
                Flash::error('Establecimientos CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }
            $can = Can::find($can_id);
            if (empty($can)) {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('estimacion.index'));
            }

            switch ($rubro_id) {
                case 1: //PF
                        $tipo_cerrado='rubro_pf';
                        $nombre_servicio='Productos Farmaceuticos';
                        $tipo=1;
                    break;
                case 2: //MBMQPA
                    $tipo_cerrado='rubro_mb_iq_pa';
                    $nombre_servicio='Material Biomedico, Material Quirurgico y Productos Afines';
                    $tipo=2;$tipo2=3;$tipo3=7;

                    break;
                case 3: //MD
                    $tipo_cerrado='rubro_mid';
                    $nombre_servicio='Material Odontologico';
                    $tipo=4;
                    break;
                case 4: //ML
                    $tipo_cerrado='rubro_mil';
                    $nombre_servicio='Material de Laboratorio';
                    $tipo=5;$tipo2=10;
                    break;
                case 5: //MFF
                    $tipo_cerrado='rubro_mff';
                    $nombre_servicio='Material Fotografico y Fonotecnico';
                    $tipo=6;
                    break;
            
            }
         

        /**********************************/
        if($tipo==1 || $tipo==4 || $tipo==6){
            
            $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual,ET.necesidad_actual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
Order by ET.descripcion asc
';
$data = DB::select($cad);

            //eliminamos los registros anteriores                
            $contar=DB::table('estimacions')
                        ->where('can_id',$can_id)
                        ->where('tipo_dispositivo_id',$tipo)                
                        ->where('establecimiento_id',$establecimiento_id)
                        ->count();
        }
        else
        {
            if($tipo==5){
                /*$data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id', 5)
                                ->orWhere('tipo_dispositivo_id', 10);
                        })
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();    
*/
                $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.necesidad_actual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=5 or ET.tipo_dispositivo_id=10)
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 

                //eliminamos los registros anteriores                
                $contar=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id', 5)
                                ->orWhere('tipo_dispositivo_id', 10);
                        })
                        ->where('establecimiento_id',$establecimiento_id)
                        ->count();
            }   
            else
            {
                /*$data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id', 2)
                                ->orWhere('tipo_dispositivo_id', 3)
                                ->orWhere('tipo_dispositivo_id', 7);
                        })
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();
*/
                $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual,ET.necesidad_actual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=2 or ET.tipo_dispositivo_id=3 and ET.tipo_dispositivo_id=7)
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 

                //eliminamos los registros anteriores                
                $contar=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id', 2)
                                ->orWhere('tipo_dispositivo_id', 3)
                                ->orWhere('tipo_dispositivo_id', 7);
                        })
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();     

            }    
        }        
        /**********************************/

        $can_servicio=DB::table('can_establecimiento')
                        ->where('can_id',$can_id)                        
                        ->where('establecimiento_id',$establecimiento_id)                        
                        ->first();
        
        $cerrado=$can_servicio->dispositivo_cerrado;

        $comite=1;
        return view('admin.cans.dispositivos.dispositivos_servicios_rubros')
            ->with('estimaciones_servicios', $data)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $rubro_id)
            ->with('comite', $comite)
            ->with('cerrado', $cerrado)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
    }

public function editar_producto($id,$establecimiento_id,$destino)
    {
        //$establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        $nivel=$establecimiento->nivel_id;

        if ($nivel==1)
        {
            //$producto = Abastecimiento3::findOrFail($id)->get();
            $producto = DB::table('abastecimientos_copia')
                            ->where('id',$id)
                            ->get();
            $servicio_id= 0;
        }
        else
        {
            //$producto = Abastecimiento2::findOrFail($id)->get();
            $producto = DB::table('abastecimientos_servicios')
                            ->where('id',$id)
                            ->get();
            //dd($contact);
            $servicio_id= $producto->get(0)->servicio_id;
        }

       //dd($producto->get(0)->id); 
       $descripcion= $producto->get(0)->descripcion;
       $cpma= $producto->get(0)->cpma;
       $precio= $producto->get(0)->precio;
       $stock_incanal= $producto->get(0)->stock_incanal;
       $almacen_central= $producto->get(0)->almacen_central;
       $ingreso_almacen2= $producto->get(0)->ingreso_almacen2;
       $ingreso_proveedor= $producto->get(0)->ingreso_proveedor;
       $ingreso_transferencia= $producto->get(0)->ingreso_transferencia;
       $unidad_consumo= $producto->get(0)->unidad_consumo;
       $salida_transferencia= $producto->get(0)->salida_transferencia;
       $merma= $producto->get(0)->merma;
       $fecha_vencimiento= $producto->get(0)->fecha_vencimiento;
       $can_id= $producto->get(0)->can_id;
       $establecimiento_id= $producto->get(0)->establecimiento_id;



        return view('admin.cans.medicamentos.editar')
        ->with('descripcion', $descripcion)
        ->with('cpma', $cpma)
        ->with('stock_incanal', $stock_incanal)
        ->with('almacen_central', $almacen_central)
        ->with('ingreso_almacen2', $ingreso_almacen2)
        ->with('ingreso_proveedor', $ingreso_proveedor)
        ->with('ingreso_transferencia', $ingreso_transferencia)
        ->with('unidad_consumo', $unidad_consumo)
        ->with('salida_transferencia', $salida_transferencia)
        ->with('merma', $merma)
        ->with('fecha_vencimiento', $fecha_vencimiento)
        ->with('producto', $producto)
        ->with('id', $id)
        ->with('can_id', $can_id)
        ->with('establecimiento_id', $establecimiento_id)
        ->with('precio', $precio)      
        ->with('destino', $destino)
        ->with('servicio_id', $servicio_id);      

    }
    /******************************************************/
    public function eliminar_observacion($can_id,$establecimiento_id,$rubro_id,$id)
    {
        $rol_user=Auth::user()->rol;       

        DB::table('observaciones')
            ->where('id',$id)
            ->update([
                'estado' => 0                
        ]);

        Flash::success('Se elimino, satisfactoriamente');        
        
        return redirect(route('cans.listar_archivos_can_rubro',[$can_id,$establecimiento_id,$rubro_id]));                
    }

    /********************************************/
    public function eliminar_items($id,$establecimiento_id,$can_id,$destino,$servicio_id)
    {

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        $nivel=$establecimiento->nivel_id;

        if ($nivel==1)
        {
            $producto = DB::table('abastecimientos_copia')
                            ->where('id',$id)
                            ->delete();
        }
        else
        {
            $producto = DB::table('abastecimientos_servicios')
                            ->where('id',$id)
                            ->delete();
        }

        Flash::success('Borrado correctamente.');

        if($destino==1)
        {
            //medicamentos
            if ($nivel==1)
            {   //
                return redirect(route('cans.medicamentos',[$can_id,$establecimiento_id]));   
            }
            else
            {   //farmacia
                return redirect(route('cans.medicamentos_farmacia',[$can_id,$establecimiento_id,$servicio_id]));
            }    
        }
        else
        {
            if ($nivel==1)
            {
                return redirect(route('cans.dispositivos',[$can_id,$establecimiento_id]));
            }
            else
            {   //farmacia
                return redirect(route('cans.dispositivos_farmacia',[$can_id,$establecimiento_id,$servicio_id]));
            }//dispositivos
            
        }    
        
    }



public function nuevo_medicamento_dispositivo( $can_id, $establecimiento_id, $tipo_producto )
    {
        
        //Verificamos si el usuario es el mismo        
        if ($tipo_producto >0 && $tipo_producto <3 )
        {
            //Verificamos si el can es el ultimo
            $cans=DB::table('cans')->orderBy('id', 'desc')->first();
                $can = Can::find($cans->id);
                $can_id_ultimo=$cans->id;

            if($can_id_ultimo==$can_id){
                
                
                //buscamos el establecimiento
                $establecimiento = Establecimiento::find($establecimiento_id);
                $nivel=$establecimiento->nivel_id;
                
                //si encuentra o no el establecimiento
                if (empty($establecimiento)) {
                    Flash::error('No existe Establecimientos CAN con esas caracteristicas');
                    return redirect(route('estimacion.index'));
                }
                
                if($tipo_producto==1){ //// 1 si es medicamento
                    //Buscamos todos los medicamentos segun el nivel
                    $consulta_petitorio = DB::table('petitorios')
                        ->where('tipo_dispositivo_medicos_id',1)                    
                        ->get();    
                
                    
                //pasamos a un arreglo
                $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                
                //Buscamos los medicamentos segun el nivel 
                $consulta_medicamentos_nivel = DB::table('estimacions')
                    ->where('tipo_dispositivo_id',1)
                    ->where('can_id',$can_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->orderby('descripcion','asc');                            

                //pasamos a un arreglo
                $consulta_medicamento = $consulta_medicamentos_nivel->pluck('descripcion','petitorio_id')->toArray();

                //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                $descripcion=array_diff($petitorio,$consulta_medicamento);
                    
                }
                else
                {
                    //Buscamos todos los medicamentos segun el nivel
                    $consulta_petitorio = DB::table('petitorios')
                            ->where('tipo_dispositivo_medicos_id','>',1)
                            ->get();

                    //pasamos a un arreglo
                    $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                    
                    //Buscamos los medicamentos segun el nivel 
                    $consulta_medicamentos_nivel = DB::table('estimacions')
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('can_id',$can_id)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->orderby('descripcion','asc');                            

                    //pasamos a un arreglo
                    $consulta_medicamento = $consulta_medicamentos_nivel->pluck('descripcion','petitorio_id')->toArray();

                    //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                    $descripcion=array_diff($petitorio,$consulta_medicamento);
                
                }
            }
            else
            {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('farmacia_servicios.index'));
            }

            //Enviamos al formulario
            return view('admin.cans.nuevo.index')
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('can_id', $can_id)
                    ->with('nivel', $nivel)
                    ->with('destino', $tipo_producto)
                    ->with('descripcion', $descripcion);
            
        }
        else
        {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('farmacia_servicios.index'));
        }
        
    }

    public function grabar_nuevos_medicamentos_dispositivos(Request $request,$establecimiento_id,$can_id, $destino)
    {


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
        //$cod_petitorio=$petitorio->codigo_petitorio;
        
        if($nivel==1){
            DB::table('estimacions')
            ->insert([
                        'can_id' => $can_id,
                        'establecimiento_id'=>$establecimiento_id,
                        'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                        'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                        'petitorio_id'=>$petitorio_id,
                        'cod_petitorio'=>$cod_petitorio,
                        'descripcion'=>$descripcion,
                        'tipo_dispositivo_id'=>$tipo_dispositivo_id,
                        'cpma' => 0,
                        'necesidad_anual' => 0,
                        'mes1' => 0,
                        'mes2' => 0,
                        'mes3' => 0,
                        'mes4' => 0,
                        'mes5' => 0,
                        'mes6' => 0,
                        'mes7' => 0,
                        'mes8' => 0,
                        'mes9' => 0,
                        'mes10' => 0,                        
                        'mes11' => 0,
                        'mes12' => 0,
                        'justificacion' => '',
                        'created_at'=>Carbon::now(),
            ]);

        }
        else
        {
            
            $servicio = Servicio::find($destino);
            $nombre_servicio=$servicio->nombre_servicio;

            DB::table('estimacion_servicio')
            ->insert([
                        'can_id' => $can_id,
                        'establecimiento_id'=>$establecimiento_id,
                        'cod_establecimiento' => $establecimiento->codigo_establecimiento,
                        'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
                        'servicio_id'=>$destino,
                        'nombre_servicio'=>$nombre_servicio,
                        'petitorio_id'=>$petitorio_id,
                        'cod_petitorio'=>$cod_petitorio,
                        'descripcion'=>$descripcion,
                        'tipo_dispositivo_id'=>$tipo_dispositivo_id,
                        'cpma' => 0,
                        'necesidad_anual' => 0,
                        'mes1' => 0,
                        'mes2' => 0,
                        'mes3' => 0,
                        'mes4' => 0,
                        'mes5' => 0,
                        'mes6' => 0,
                        'mes7' => 0,
                        'mes8' => 0,
                        'mes9' => 0,
                        'mes10' => 0,                        
                        'mes11' => 0,
                        'mes12' => 0,
                        'justificacion' => '',
                        'created_at'=>Carbon::now(),
            ]);
        }
    
        Flash::success('Se ha guardado con exito');
        if($nivel==1){
            if($destino==1)
                return redirect(route('cans.medicamentos_estimaciones',[$can_id,$establecimiento_id]));
            else
                return redirect(route('cans.dispositivos_estimaciones',[$can_id,$establecimiento_id]));
        }
        else
        {
            if($destino==1)
                return redirect(route('cans.medicamentos_servicios',[$can_id,$establecimiento_id,$destino]));
            else
                return redirect(route('cans.dispositivos_servicios',[$can_id,$establecimiento_id,$destino]));
        }
        
    }
///////////////////////////////////////////////////////////////////////////////////
    public function medicamentos_estimaciones(Request $request,$can_id, $establecimiento_id)
    {       

        $establecimiento = Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado 1');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $can = Can::find($can_id);
        $tiempo = $can->tiempo;
        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }        
        
        $estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id',1)  
            ->where('estado','<>',2)
            //->where('estado_necesidad',0)
            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby('descripcion','asc')//cambiar desc
            ->get();
        
        $num_estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id',1)     
            ->where('estado','<>',2)
            //->where('estado_necesidad',0)
            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby('descripcion','asc')//cambiar desc
            ->count();

        $tipo=1;
        $descripcion='Medicamentos';
        $consolidado=0;
        $flat_estimacion=1;

        return view('admin.cans.medicamentos.descargar_medicamentos')
            ->with('estimaciones', $estimaciones)
            ->with('descripcion', $descripcion)
            ->with('flat_estimacion', $flat_estimacion)
            ->with('consolidado', $consolidado)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('nivel', $nivel)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('tiempo', $tiempo)
            ->with('can_id', $can_id);        
            
    }

    public function medicamentos_rectificaciones(Request $request,$can_id, $establecimiento_id)
    {       

        $establecimiento = Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado 1');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }        
        
        $estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id',1)            
            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby('descripcion','asc')//cambiar desc
            ->get();
        
        $num_estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id',1)            
            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby('descripcion','asc')//cambiar desc
            ->count();


        
        $tipo=1;
        $descripcion='Medicamentos';
        $consolidado=0;
        $flat_estimacion=1;

        return view('admin.cans.medicamentos.descargar_medicamentos_rectificaciones')
            ->with('estimaciones', $estimaciones)
            ->with('descripcion', $descripcion)
            ->with('flat_estimacion', $flat_estimacion)
            ->with('consolidado', $consolidado)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('nivel', $nivel)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);        
            
    }


    public function medicamentos_consolidados(Request $request,$can_id, $establecimiento_id)
    {       

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado 1');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }
        
        //SI LOS DATOS NO SON CARGADOS
        $estimaciones=DB::table('estimacions')
                        ->where('can_id',$can_id)
                        ->where('estado','<>',2)
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->get();

        $num_estimaciones=DB::table('estimacions')
                        ->where('can_id',$can_id)
                        ->where('estado','<>',2)
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->count();

        $tiempo = $can->tiempo;
        $tipo=1;
        $descripcion='Medicamentos';
        $consolidado=1;
        $flat_estimacion=0;
        return view('admin.cans.medicamentos.descargar_medicamentos')
            ->with('estimaciones', $estimaciones)
            ->with('descripcion', $descripcion)
            ->with('flat_estimacion', $flat_estimacion)
            ->with('consolidado', $consolidado)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('tiempo', $tiempo)
            ->with('nivel', $nivel)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
        
            
    }

    public function pdf_servicio_rectificacion_administrador($can_id,$establecimiento_id,$tipo, $servicio_id)
    {
        

        $establecimiento = Establecimiento::find($establecimiento_id);

        $name=Auth::user()->name;

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
        
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
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('necesidad_anual','>',0)
                    ->where('estado','<>',2)
                    ->where('servicio_id',$servicio_id)
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
    
        $pdf = \PDF::loadView('site.pdf.descargar_servicio_rectificacion_administrador_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream($nombre_pdf);
        
     }

     public function pdf($can_id,$establecimiento_id,$tipo,$servicio_id)
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
                        //->where('estado','<>',2)
                        ->where('estado_necesidad',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();

                $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        //->where('estado','<>',2)
                        ->where('estado_necesidad',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        //->where('estado','<>',2)
                        ->where('estado_necesidad',0)
                        ->where('tipo_dispositivo_id',1)                                                
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')                          
                        ->get();                    


                $descripcion_tipo='Medicamentos';
            }else
                {   if ($tipo==2) {
                        $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        //->where('estado','<>',2)
                        ->where('estado_necesidad',0)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();                    

                        $num_estimaciones=DB::table('estimacions')
                            ->where('necesidad_anual','>',0)
                            //->where('estado','<>',2)
                            ->where('estado_necesidad',0)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id','>',1)                            
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->count();


                        $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('estado_necesidad',0)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')  
                        //->orderby ('descripcion','asc')  
                        ->get();                    

                        $descripcion_tipo='Dispositivos';

                    }else
                    {
                        Flash::error('Datos no son correctos, error al descargar archivo');
                        return redirect(route('estimacion.index'));  
                    }
            }

            //if($establecimiento_id==1){
                $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)                
                ->where('rol',7)
                ->where('estado',1)
                ->first();    

                $name=$usuario->name;
                $servicio_id=$usuario->servicio_id;
                $user_id=$usuario->id;
                $cip=$usuario->cip;
                $dni=$usuario->dni;
            /*}
            else
            {
                $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)                
                ->where('rol',9)
                ->where('estado',1)
                ->first();    

                $name=$usuario->name;
                $servicio_id=$usuario->servicio_id;
                $user_id=$usuario->id;
                $cip=$usuario->cip;
                $dni=$usuario->dni;   
            }*/

        $responsables=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)      
                ->where('estado',1)
                ->get();        
        
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


        //dd($responsable);
        $responsables_rubros[]="";
        $responsables_rubros=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)      
                ->where('estado',1)   
                ->where('rol',3)          
                ->orderby('rol','asc')
                ->orderby('servicio_id','asc')
                ->get();    
        //dd($responsables_rubros);    
        $total_responsable=count($responsables_rubros);
        $x=0; $t=count($total_tipo_productos); $y=0;
        
        foreach ($responsables_rubros as $key => $resp_rubro) {
            switch ($resp_rubro->servicio_id) {
                case 1: //productos (1)
                    $responsable_rubro[0]=$resp_rubro->name;
                    $responsable_rubro[6]=$resp_rubro->grado;                    
                    $y++;
                    break;
                case 2: //insumos laboratorio (5,10)
                    $responsable_rubro[1]=$resp_rubro->name;
                    $responsable_rubro[7]=$resp_rubro->grado;
                    $x++;
                    break;
                case 3://biomedico,quirurgico,afines (2,3,7)
                    $responsable_rubro[2]=$resp_rubro->name;
                    $responsable_rubro[8]=$resp_rubro->grado;
                    $x++;
                    break;
                case 4://dentales (4)
                    $responsable_rubro[3]=$resp_rubro->name;
                    $responsable_rubro[9]=$resp_rubro->grado;
                    $x++;
                    break;
                case 5://fotografico (6)
                    $responsable_rubro[4]=$resp_rubro->name;
                    $responsable_rubro[10]=$resp_rubro->grado;
                    $x++;
                    break;
                case 6://dentales 2 (4)
                    $responsable_rubro[5]=$resp_rubro->name;
                    $responsable_rubro[11]=$resp_rubro->grado;
                    $x++;
                    break;
            }
        }
        $tx=0;$ty=0; $t=0;
        foreach ($total_tipo_productos as $key => $tp) {
            switch ($tp->tipo_dispositivo_id) {
                case 2: $tx++; break;
                case 3: $tx++; break;
                case 4: $t++; break;
                case 5: $ty++; break;
                case 6: $t++; break;
                case 7: $tx++; break;
                case 10:$ty++; break;
            }
        }   
        if($tx>1)$tx=1;
        if($ty>1)$ty=1;
        $dtotal=$t+$tx+$ty;
        
        //dd($total_tipo_productos); //x=4  t=6        
        if($x>=$dtotal and $y==1){

            //dd($responsable_rubro);
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
        //}

            $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                          'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                          'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable,'tipo'=>$tipo,]);
            $pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');
        }
        
        
     } 

/*
    public function pdf_previo($can_id,$establecimiento_id,$tipo)
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
            
            $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id, ET.cod_petitorio, ET.stock
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 



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
                    ->orderby ('tipo_dispositivo_id','asc')                          
                    ->get();                    


            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    /*$data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1                    
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();  */

/*
                    $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id, ET.cod_petitorio, ET.stock
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id>='.$tipo.'
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 



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
                    ->where('estado','<>',2)                        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1                    
                    ->groupby('tipo_dispositivo_id')
                    ->orderby ('tipo_dispositivo_id','asc')  
                    //->orderby ('descripcion','asc')  
                    ->get();                    

                    $descripcion_tipo='Dispositivos';

                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }

        if($tipo==1){
            $nombre_rubro='MEDICAMENTOS';
        }
        else
        {
            $nombre_rubro='DISPOSITIVOS';
        }
        
        $texto='CONSOLIDADO IPRESS';

        $pdf = \PDF::loadView('site.pdf.descargar_pdf_administrador',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'nombre_rubro'=>$nombre_rubro,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'tipo'=>$tipo,]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
    } 

*/

    
    public function pdf_previo($can_id,$establecimiento_id,$tipo)
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
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();

                $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('estado','<>',2)
                        ->where('tipo_dispositivo_id',1)                                                
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')                          
                        ->get();                    


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
                        ->orderby ('descripcion','asc')  
                        ->get();                    

                        $num_estimaciones=DB::table('estimacions')
                            ->where('necesidad_anual','>',0)
                            ->where('estado','<>',2)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id','>',1)                            
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->count();


                        $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')  
                        //->orderby ('descripcion','asc')  
                        ->get();                    

                        $descripcion_tipo='Dispositivos';

                    }else
                    {
                        Flash::error('Datos no son correctos, error al descargar archivo');
                        return redirect(route('estimacion.index'));  
                    }
            }

            
            

        
        
        //dd($total_tipo_productos); //x=4  t=6        
        if($tipo==1){
            $nombre_rubro='MEDICAMENTOS';
        }
        else
        {
            $nombre_rubro='DISPOSITIVOS';
        }
        
        $texto='CONSOLIDADO IPRESS';

        $pdf = \PDF::loadView('site.pdf.descargar_pdf_administrador',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'nombre_rubro'=>$nombre_rubro,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'tipo'=>$tipo,]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
     } 
    public function pdf_rectificacion($can_id,$establecimiento_id,$tipo)
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
         
            /***************************************************/
            $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.necesidad_actual, ET.requerimiento_usuario, ET.stock_actual,ET.tipo_dispositivo_id, ET.cod_petitorio, ET.stock
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 
            /****************************************************/


            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)                                            
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('tipo_dispositivo_id')
                    ->orderby ('tipo_dispositivo_id','asc')                          
                    ->get();                    


            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
  
                    $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.necesidad_actual, ET.requerimiento_usuario, ET.stock_actual,ET.tipo_dispositivo_id, ET.cod_petitorio, ET.stock
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id>='.$tipo.'
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id>='.$tipo.'
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 



                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $total_tipo_productos=DB::table('estimacions')
                    ->select('tipo_dispositivo_id')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)                        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1                    
                    ->groupby('tipo_dispositivo_id')
                    ->orderby ('tipo_dispositivo_id','asc')  
                    //->orderby ('descripcion','asc')  
                    ->get();                    

                    $descripcion_tipo='Dispositivos';

                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }

        if($tipo==1){
            $nombre_rubro='MEDICAMENTOS';
        }
        else
        {
            $nombre_rubro='DISPOSITIVOS';
        }
        
        $texto='CONSOLIDADO IPRESS';

        $pdf = \PDF::loadView('site.pdf.descargar_pdf_administrador_rectificacion',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'nombre_rubro'=>$nombre_rubro,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'tipo'=>$tipo,]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
    } 
/*
    public function pdf($can_id,$establecimiento_id,$tipo,$servicio_id)
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
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();

                $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('estado','<>',2)
                        ->where('tipo_dispositivo_id',1)                                                
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')                          
                        ->get();                    


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
                        ->orderby ('descripcion','asc')  
                        ->get();                    

                        $num_estimaciones=DB::table('estimacions')
                            ->where('necesidad_anual','>',0)
                            ->where('estado','<>',2)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('tipo_dispositivo_id','>',1)                            
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->count();


                        $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')  
                        //->orderby ('descripcion','asc')  
                        ->get();                    

                        $descripcion_tipo='Dispositivos';

                    }else
                    {
                        Flash::error('Datos no son correctos, error al descargar archivo');
                        return redirect(route('estimacion.index'));  
                    }
            }

            //if($establecimiento_id==1){
                $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)                
                ->where('rol',7)
                ->where('estado',1)
                ->first();    

                $name=$usuario->name;
                $servicio_id=$usuario->servicio_id;
                $user_id=$usuario->id;
                $cip=$usuario->cip;
                $dni=$usuario->dni;
            /*}
            else
            {
                $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)                
                ->where('rol',9)
                ->where('estado',1)
                ->first();    

                $name=$usuario->name;
                $servicio_id=$usuario->servicio_id;
                $user_id=$usuario->id;
                $cip=$usuario->cip;
                $dni=$usuario->dni;   
            }*/
/*

        $responsables=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)      
                ->where('estado',1)
                ->get();        
        
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


        //dd($responsable);
        $responsables_rubros[]="";
        $responsables_rubros=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)      
                ->where('estado',1)   
                ->where('rol',3)          
                ->orderby('rol','asc')
                ->orderby('servicio_id','asc')
                ->get();    
        //dd($responsables_rubros);    
        $total_responsable=count($responsables_rubros);
        $x=0; $t=count($total_tipo_productos); $y=0;
        
        foreach ($responsables_rubros as $key => $resp_rubro) {
            switch ($resp_rubro->servicio_id) {
                case 1: //productos (1)
                    $responsable_rubro[0]=$resp_rubro->name;
                    $responsable_rubro[6]=$resp_rubro->grado;                    
                    $y++;
                    break;
                case 2: //insumos laboratorio (5,10)
                    $responsable_rubro[1]=$resp_rubro->name;
                    $responsable_rubro[7]=$resp_rubro->grado;
                    $x++;
                    break;
                case 3://biomedico,quirurgico,afines (2,3,7)
                    $responsable_rubro[2]=$resp_rubro->name;
                    $responsable_rubro[8]=$resp_rubro->grado;
                    $x++;
                    break;
                case 4://dentales (4)
                    $responsable_rubro[3]=$resp_rubro->name;
                    $responsable_rubro[9]=$resp_rubro->grado;
                    $x++;
                    break;
                case 5://fotografico (6)
                    $responsable_rubro[4]=$resp_rubro->name;
                    $responsable_rubro[10]=$resp_rubro->grado;
                    $x++;
                    break;
                case 6://dentales 2 (4)
                    $responsable_rubro[5]=$resp_rubro->name;
                    $responsable_rubro[11]=$resp_rubro->grado;
                    $x++;
                    break;
            }
        }
        $tx=0;$ty=0; $t=0;
        foreach ($total_tipo_productos as $key => $tp) {
            switch ($tp->tipo_dispositivo_id) {
                case 2: $tx++; break;
                case 3: $tx++; break;
                case 4: $t++; break;
                case 5: $ty++; break;
                case 6: $t++; break;
                case 7: $tx++; break;
                case 10:$ty++; break;
            }
        }   
        if($tx>1)$tx=1;
        if($ty>1)$ty=1;
        $dtotal=$t+$tx+$ty;
        
        //dd($total_tipo_productos); //x=4  t=6        
        if($x>=$dtotal and $y==1){

            //dd($responsable_rubro);
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
        //}

            $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                          'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                          'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable,'tipo'=>$tipo,]);
            $pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');
        }
        else
        {
            Flash::error('Falta Ingresar Personal para poder imprimir su PDF ');
            return redirect(route('users.index_responsable'));
        }
        
     } 
*/
public function exportEstimacionDataConsolidadoEstablecimiento($descripcion,$can_productos,$type)
    {
    }

public function exportDataConsolidado($can_id,$establecimiento_id,$opt,$type)
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
                $objDrawing2->setCoordinates('R1');
                $objDrawing2->setWorksheet($sheet);
    
                $sheet->mergeCells('B1:O1');
                $sheet->cell('B1', function($cell) {$cell->setValue('CAN');  $cell->setFontSize(38); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle');
                });


                $sheet->mergeCells('B2:O2');
                $sheet->mergeCells('B3:O3');
                $sheet->cell('B2', function($cell) {$cell->setValue('CUADRO ANUAL DE NECESIDADES ');  $cell->setFontSize(28); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });
                
                //$nombre_servicio=Auth::user()->nombre_servicio;
                $nombre_establecimiento=$data->get(0)->nombre_establecimiento;

                
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

                           // $sheet->cell('R'.$i, function($cell) {$cell->setValue('OBSERVACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->mergeCells('F'.$i.':Q'.$i);
                            $sheet->cell('F'.$i, function($cell) {$cell->setValue('PRORRATEO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->mergeCells('A'.$d.':Q'.$d);
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
                                'columns' => array('E','D'),
                                'rows' => array(
                                    array($i,$j)                        
                                )
                            ));



                            
                            $k=$i+3;
                            $tipo_dispositivo_anterior=$value->tipo_dispositivo_id;
                            $m=1;


                        }

                        switch ($value->estado) {                                    
                                    case 0: $descripcion_observacion=' Ratificado '; break;
                                    case 1: $descripcion_observacion=' Nuevo '; break;
                                    case 2: $descripcion_observacion=' Eliminado '; break;
                                    case 3: $descripcion_observacion=' Actualizado, cpma_ant='.$value->cpma_anterior.' nec_ant='.$value->necesidad_anterior; break;   
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
                        //$sheet->cell('R'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });                          
                        //$sheet->cell('S'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        
                        $sheet->cell('A'.$k, $m); 
                        $sheet->cell('B'.$k, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$k, $value->descripcion); //nivel
                        
                        if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}
                        
                        if($value->necesidad_anual>0){ $sheet->cell('E'.$k, $value->necesidad_anual);}else{$sheet->cell('E'.$k, number_format($value->necesidad_anual, 2, '.', ','));}
                        //$sheet->cell('F'.$k, $descripcion_observacion);  
                        //if($value->stock>0){ $sheet->cell('E'.$k, $value->stock);}else{$sheet->cell('E'.$k, number_format($value->stock, 2, '.', ','));}
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

                        

                        //$sheet->cell('S'.$k, $descripcion_observacion);  

                        //$sheet->cell('S'.$k, $value->justificacion);  

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
                    
                    if($total_necesidad_anual>0){ $sheet->cell('E'.$n, $total_necesidad_anual);}else{$sheet->cell('E'.$n, number_format($total_necesidad_anual, 2, '.', ','));}
                    $sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });

                    //if($total_stock>0){ $sheet->cell('F'.$n, $total_stock);}else{$sheet->cell('F'.$n, number_format($total_stock, 2, '.', ','));}
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

                $sheet->cell('O'.$fh, 'Fecha y Hora de la Descarga: '.$now->format('d/m/Y H:i:s'));  
                

                /*$sheet->cell('C'.$m, function($cell) {$cell->setValue('____________________________________________________');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->cell('C'.$j, function($cell) {$cell->setValue('RESPONSABLE DEL ESTABLECIMIENTO DE SALUD');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('P2:Q2');
                $sheet->mergeCells('I'.$m.':'.'L'.$m);
                $sheet->cell('I'.$m, function($cell) {$cell->setValue('______________________________________');  $cell->setFontSize(14); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('I'.$j.':'.'L'.$j);
                $sheet->cell('I'.$j, function($cell) {$cell->setValue('RESPONSABLE DE LA FARMACIA');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });*/

            });
        })->download($type);
    }
    
    public function exportDataEstimacion($can_id,$establecimiento_id,$opt,$type)
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

        $archivo='CAN_Estimacion_'.$nombre_establecimiento.'_'.$can->desc_mes.'_'.$can->ano;
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
    

    public function exportDataModificado($can_id,$establecimiento_id,$type)
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

        
        $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('estado','>',0)
                    ->where('can_id',$can_id) //cambiar 22                        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();

        $nombre_producto='Productos Modificados';
        
        $archivo='CAN_Estimacion_'.$nombre_establecimiento.'_'.$can->desc_mes.'_'.$can->ano;
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
                
                $nombre_establecimiento=$data->get(0)->nombre_establecimiento;
                //$nombre_establecimiento=$data(0)->nombre_establecimiento;

                
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

                            $sheet->cell('S'.$i, function($cell) {$cell->setValue('OBSERVACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

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

                        //switch ($value->estado) {
                        switch ($value->estado) {                                    
                                    case 1: $descripcion_observacion=' Nuevo '; break;
                                    case 2: $descripcion_observacion=' Eliminado '; break;
                                    case 3: $descripcion_observacion=' Actualizado, cpma_ant='.$value->cpma_anterior.' nec_ant='.$value->necesidad_anterior; break;
                                    
                                }

                        $sheet->cell('S'.$k, $descripcion_observacion);  

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

    public function dispositivos_estimaciones(Request $request,$can_id, $establecimiento_id)
    {       

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado 3');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }


        $estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id','>',1)    
            //->where('estado_necesidad',0)
            ->where('estado','<>',2)
            //->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby ('tipo_dispositivo_id','asc')  
            ->get();

        $num_estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id','>',1)       
            ->where('estado','<>',2)
            //->where('estado_necesidad',0)
            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby('descripcion','asc')//cambiar desc
            ->count();
                        
        $tipo=2;
        $descripcion='Dispositivos Medicos';
        $consolidado=0;
        $flat_estimacion=1;
        //dd($abastecimientos);
        return view('admin.cans.dispositivos.descargar_dispositivos')
            ->with('estimaciones', $estimaciones)
            ->with('flat_estimacion', $flat_estimacion)
            ->with('descripcion', $descripcion)
            ->with('consolidado', $consolidado)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('nivel', $nivel)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
            
    }

    public function dispositivos_rectificaciones(Request $request,$can_id, $establecimiento_id)
    {       

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado 3');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }


        $estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id','>',1)            
            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby ('tipo_dispositivo_id','asc')  
            ->get();

        $num_estimaciones=DB::table('estimacions')            
            ->where('can_id',$can_id) //cambiar 22
            ->where('tipo_dispositivo_id','>',1)            
            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
            ->orderby('descripcion','asc')//cambiar desc
            ->count();
                        
        $tipo=2;
        $descripcion='Dispositivos Medicos';
        $consolidado=0;
        $flat_estimacion=1;
        //dd($abastecimientos);
        return view('admin.cans.dispositivos.descargar_dispositivos_rectificacion')
            ->with('estimaciones', $estimaciones)
            ->with('flat_estimacion', $flat_estimacion)
            ->with('descripcion', $descripcion)
            ->with('consolidado', $consolidado)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('nivel', $nivel)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
            
    }

    public function dispositivos_consolidados(Request $request,$can_id, $establecimiento_id)
    {       

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado 3');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }
        
        //SI LOS DATOS NO SON CARGADOS
        $estimaciones=DB::table('estimacions')
                        ->where('can_id',$can_id)
                        ->where('estado','<>',2)
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->get();

        $num_estimaciones=DB::table('consolidados')
                        ->where('can_id',$can_id)
                        ->where('estado','<>',2)
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->count();
                        
        $tipo=2;
        $descripcion='Dispositivos Medicos';
        $consolidado=1;
        $flat_estimacion=0;
        return view('admin.cans.dispositivos.descargar_dispositivos')
            ->with('estimaciones', $estimaciones)
            ->with('descripcion', $descripcion)
            ->with('flat_estimacion', $flat_estimacion)
            ->with('consolidado', $consolidado)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('tipo', $tipo)
            ->with('nivel', $nivel)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('can_id', $can_id);
            
    }

///////////////////////////9////////////////////////////////////77
    public function exportEstimacionDataNivel1($can_id,$establecimiento_id,$rubro_id,$opt,$type)
    {

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;

        $rubro = Rubro::find($rubro_id);
        $nombre_rubro=$rubro->descripcion;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($opt==1){
            $data=DB::table('estimacion_rubro')
                    ->where('necesidad_anual','>',0)
                    ->where('rubro_id',$rubro_id)
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
                    ->where('rubro_id',$rubro_id)
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
        

        $archivo='CAN_'.$nombre_producto.'_'.$nombre_establecimiento.'_'.$nombre_rubro.'_'.$can->desc_mes.'_'.$can->ano;
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
    ///////////////////////////9////////////////////////////////////77
///////////////////////////9////////////////////////////////////77
    public function exportEstimacionDataNivel2y3($can_id,$establecimiento_id,$servicio_id,$opt,$type)
    {

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;

        $servicio = Servicio::find($servicio_id);
        $nombre_servicio=$servicio->nombre_servicio;
        
        //dd($nombre_servicio);

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($opt==1){
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
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
                    ->orderby('descripcion','asc')
                    ->orderby ('tipo_dispositivo_id','asc')                    
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
                    //'R'     =>  10,
                    //'S'     =>  100,
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

                            //$sheet->cell('E'.$i, function($cell) {$cell->setValue('STOCK'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->cell('E'.$i, function($cell) {$cell->setValue('NECESIDAD'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

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

                            //$sheet->cell('R'.$i, function($cell) {$cell->setValue('JUSTIFICACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                            $sheet->mergeCells('F'.$i.':Q'.$i);
                            $sheet->cell('F'.$i, function($cell) {$cell->setValue('PRORRATEO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                            $sheet->mergeCells('A'.$d.':Q'.$d);
                            $sheet->cell('A'.$d, function($cell) {$cell->setValue(''); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
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
                        //$sheet->cell('R'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });  
                        //$sheet->cell('S'.$k, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        
                        $sheet->cell('A'.$k, $m); 
                        $sheet->cell('B'.$k, $value->cod_petitorio); //establecimiento 
                        $sheet->cell('C'.$k, $value->descripcion); //nivel
                        $user_rol=Auth::user()->rol;
                        if($user_rol!=2 ){
                            if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}

                            //if($value->stock>0){ $sheet->cell('E'.$k, $value->stock);}else{$sheet->cell('E'.$k, number_format($value->stock, 2, '.', ','));}
                        }
                        else
                        {
                            if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}
                            //$sheet->cell('E'.$k,'N/A');                            
                        }
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

                        //$sheet->cell('S'.$k, $value->justificacion);  

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
                    
                    if($user_rol!=2 ){
                        if($cpma_total>0){ $sheet->cell('D'.$n, $cpma_total);}else{$sheet->cell('D'.$n, number_format($cpma_total, 2, '.', ','));}

                        $sheet->cell('D'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                        //if($total_stock>0){ $sheet->cell('E'.$n, $total_stock);}else{$sheet->cell('E'.$n, number_format($total_stock, 2, '.', ','));}
                        //$sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    }
                    else
                    {
                        if($cpma_total>0){ $sheet->cell('D'.$n, $cpma_total);}else{$sheet->cell('D'.$n, number_format($cpma_total, 2, '.', ','));}
                        $sheet->cell('D'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                        
                        //$sheet->cell('E'.$n, 'N/A');                        
                    }
                    if($total_necesidad_anual>0){ $sheet->cell('E'.$n, $total_necesidad_anual);}else{$sheet->cell('F'.$n, number_format($total_necesidad_anual, 2, '.', ','));}
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
                    }
                }
                
            });
        })->download($type);
    }
    //////////////////////////////////////////////////////////////////7
    public function exportEstimacionDataComiteNivel2y3($can_id,$establecimiento_id,$rol,$type)
    {

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;

        //dd($nombre_servicio);

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        /**************************/
        switch ($rol) {
            case 1: //PF 1
                    $tipo=1;$mtipo=1;
                break;
            case 2: //MBMQPA 2
                $tipo=2;$tipo2=3;$tipo3=7;;$mtipo=2;
                break;
            case 3: //MD 3
                $tipo=4;$mtipo=2;
                break;
            case 4: //ML 4
                $tipo=5;$mtipo=2;
                break;
            case 5: //MFF 5
                $tipo=6;$mtipo=2;
                break;            
        }
        
        /*if($servicio_id!=0){ */
            if($tipo==1 || $tipo==4 || $tipo==6){
                /*
                $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)                        
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)        
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                */

                $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id, ET.cod_petitorio, ET.stock
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and tipo_dispositivo_id='.$tipo.'
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
Order by ET.descripcion asc
';
$data = DB::select($cad);

                if($tipo==1)
                    $descripcion_tipo='Medicamentos';
                else
                {
                    if($tipo==4)
                        $descripcion_tipo='Material e Insumo Odontologico';
                    else
                        $descripcion_tipo='Material Fotografico y Fonotecnico';
                }
            }
            else
            {
                if($tipo==5){
                    /*$data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22                        
                        ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',5)
                                              
                                              ->orWhere('tipo_dispositivo_id',10);

                                    })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();     
                    */
                    $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id, ET.cod_petitorio, ET.stock
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=5 or ET.tipo_dispositivo_id=10)
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 

                    $descripcion_tipo='Material e insumos de Laboratorio';
                }   
                else
                {
                    /*$data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22                        
                        ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',2)
                                              ->orWhere('tipo_dispositivo_id',3)
                                              ->orWhere('tipo_dispositivo_id',7);

                                    })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                    */

                    $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id, ET.cod_petitorio, ET.stock
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_cero
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_uno
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_dos
,(
select sum(necesidad_anual) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_tres
,(
select sum(necesidad_anterior) as necesidad_anual
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)stock_cuatro,
(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_cero
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=1 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_uno
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=2
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_dos
,(
select sum(cpma) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and estado=3
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_tres
,(
select sum(cpma_anterior) as cpma
from estimacion_servicio
where establecimiento_id='.$establecimiento_id.'
and can_id='.$can_id.'
and cpma_anterior!=0 
and petitorio_id=ET.petitorio_id
and ( tipo_dispositivo_id=2 or  tipo_dispositivo_id=3 or  tipo_dispositivo_id=7)
)cpma_cuatro
from estimacions ET
Where ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=2 or ET.tipo_dispositivo_id=3 and ET.tipo_dispositivo_id=7)
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); 

                    $descripcion_tipo='Material Biomedico, Instrumental Quirurgico y Productos afines';
                }    
            }
                        
            $num_estimaciones=count($data);

        $archivo='CAN_'.$nombre_establecimiento.'_'.$descripcion_tipo.'_'.$can->desc_mes.'_'.$can->ano;
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

                            $sheet->cell('S'.$i, function($cell) {$cell->setValue('OBSERVACIÓN');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

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
                        $user_rol=Auth::user()->rol;
                        if($user_rol!=2 ){
                            if($value->cpma>0){ $sheet->cell('D'.$k, $value->cpma);}else{$sheet->cell('D'.$k,number_format($value->cpma, 2, '.', ','));}

                            if($value->stock>0){ $sheet->cell('E'.$k, $value->stock);}else{$sheet->cell('E'.$k, number_format($value->stock, 2, '.', ','));}
                        }
                        else
                        {
                            $sheet->cell('D'.$k,'N/A');
                            $sheet->cell('E'.$k,'N/A');                            
                        }
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

                        $necesidad_anterior=$value->stock_cero+$value->stock_dos+$value->stock_cuatro;
                                    if($value->necesidad_anual==$value->stock_dos){
                                        $descripcion_observacion=' Eliminado ';
                                        $y=1;
                                    }
                                    else
                                    {
                                        $y=0;
                                    }

                                    if($value->necesidad_anual==$necesidad_anterior){
                                        if($y==0){
                                            $descripcion_observacion=' Ratificado ';  
                                        }
                                        else
                                        {
                                            $y=0;
                                        }
                                        
                                    }

                                    if($value->necesidad_anual==$value->stock_uno){
                                        $descripcion_observacion=' Nuevo ';
                                        $x=1;
                                    }
                                    else
                                    {
                                        $x=0;
                                    }
                                    if($value->necesidad_anual!=$necesidad_anterior){
                                        $cpma_anterior=$value->cpma_cero+$value->cpma_dos+$estimacion->cpma_cuatro;

                                        if($x!=1)                                            
                                        $descripcion_observacion=' Actualizado, cpma_ant='.$cpma_anterior.' nec_ant='.$necesidad_anterior;
                                        
                                    }

                        $sheet->cell('S'.$k, $descripcion_observacion);  


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
                        
                    
                    if($user_rol!=2 ){
                        if($cpma_total>0){ $sheet->cell('D'.$n, $cpma_total);}else{$sheet->cell('D'.$n, number_format($cpma_total, 2, '.', ','));}

                        $sheet->cell('D'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                        if($total_stock>0){ $sheet->cell('E'.$n, $total_stock);}else{$sheet->cell('E'.$n, number_format($total_stock, 2, '.', ','));}
                        $sheet->cell('E'.$n, function($cell) {$cell->setFontSize(12); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('right'); $cell->setFontWeight('bold'); });
                    }
                    else
                    {
                        $sheet->cell('D'.$n, 'N/A');
                        $sheet->cell('E'.$n, 'N/A');                        
                    }
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
    ///////////////////////////9////////////////////////////////////77
    public function exportDatos($can_id,$establecimiento_id,$indicador,$type)
    {
        
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;

        switch ($indicador) {
            case '2': $texto='NORMOSTOCK';                
                break;
            
            case '3': $texto='SUBSTOCK';
                break;

            case '4': $texto='SOBRESTOCK';                
                break;

            case '5': $texto='SIN ROTACION';                
                break;

            case '6': $texto='DESABASTECIDO';                
                break;

            case '7': $texto='NEGATIVO';                
                break;

            case '8': $texto='NEGATIVO';                
                break;
        }

        
        

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

                $data=DB::table('abastecimientos')->
                        where( function ( $query )
                        {
                            $query->orWhere('unidad_ingreso','>',0)
                                ->orWhere('valor_ingreso','>',0)
                                ->orWhere('stock_final','>',0)                                        
                                ->orWhere('total_salidas','>',0);
                        })
                        ->where('anomes',$can->anomes) //cambiar 201801
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->where('indicadores',$texto)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
            
        //$data = Abastecimiento::get()->toArray();
        $archivo='CAN_'.$texto.'_'.$can->desc_mes.'_'.$can->ano.'_'.$establecimiento->nombre_establecimiento;
        //$archivo='CAN_'.$can->desc_mes.'_'.$can->ano.'_'.$establecimiento->nombre_establecimiento;
        return Excel::create($archivo, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {   
                
                //6 filas agrupadas con las columnas A,B,C
                $sheet->setMergeColumn(array(
                    'columns' => array('A','B'),
                    'rows' => array(
                        array(1,6)                        
                    )
                ));

                //INSERTAR LOGOS
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo_sanidad.png')); //your image path
                $objDrawing->setCoordinates('A2');
                $objDrawing->setWorksheet($sheet);
                
                $objDrawing2 = new PHPExcel_Worksheet_Drawing;
                $objDrawing2->setPath(public_path('img/logo_pnp.png')); //your image path
                $objDrawing2->setCoordinates('R2');
                $objDrawing2->setWorksheet($sheet);
    

                $sheet->cell('B1', function($cell) {$cell->setValue('CAN');   });


                $sheet->setMergeColumn(array(
                    'columns' => array('R'),
                    'rows' => array(
                        array(1,5)                        
                    )
                ));
                $sheet->cell('B1', function($cell) {$cell->setValue('CAN');  $cell->setFontSize(48); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle');
                });

                $sheet->mergeCells('C1:Q1');
                $sheet->cell('C1', function($cell) {$cell->setValue('SISTEMA INTEGRADO DE SUMINISTRO DE  PRODUCTOS ESTRATÉGICOS SISPE-PNP');  $cell->setFontSize(18); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->setHeight(1, 40);
                $sheet->setHeight(2, 30);
                $sheet->setHeight(4, 30);
                $sheet->setHeight(8, 30);
                

                $sheet->mergeCells('C2:O2');
                $sheet->cell('C2', function($cell) {$cell->setValue('INFORME DE CONSUMO INTEGRADO');  $cell->setFontSize(14); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });


                $sheet->mergeCells('C3:Q3');
                
                $sheet->mergeCells('N5:Q5');

                $sheet->mergeCells('C5:M5');

                    $sheet->setMergeColumn(array(
                        'columns' => ['R'],
                        'rows' => [
                            [20, 21]
                        ]
                    ));

                $sheet->setMergeColumn(array(
                    'columns' => array('R'),
                    'rows' => array(
                        array(1,5)                        
                    )
                ));
                $sheet->cell('B1', function($cell) {$cell->setValue('CAN');   });

                $sheet->mergeCells('P2:Q2');
                $sheet->cell('P2', function($cell) {$cell->setValue('SISPE - PNP');   $cell->setFontSize(28); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                //colocammos el EE.SS.
                $sheet->cell('C4', function($cell) {$cell->setValue('EE.SS.');   $cell->setFontSize(13); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('I4:J4');
                $sheet->cell('I4', function($cell) {$cell->setValue('CODIGO IPRESS');   $cell->setFontSize(13); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('K4:L4');

                $sheet->mergeCells('M4:N4');
                $sheet->cell('M4', function($cell) {$cell->setValue('MES / AÑO');   $cell->setFontSize(13); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('O4:P4');

                $sheet->mergeCells('C6:M6');

                $sheet->cell('N6', function($cell) {$cell->setValue('(K+L+M)');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); });

                $sheet->cell('O6', function($cell) {$cell->setValue('((J+E)-N)'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); });            

                $sheet->cell('Q6', function($cell) {$cell->setValue('(O/D)'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); });            
                $sheet->cell('R6', function($cell) {$cell->setValue('(O-(Dx6)'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); });            

                $sheet->cell('A7', function($cell) {$cell->setValue('A'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('B7', function($cell) {$cell->setValue('B');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('C7', function($cell) {$cell->setValue('C'); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('D7', function($cell) {$cell->setValue('D'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('E7', function($cell) {$cell->setValue('E'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('F7', function($cell) {$cell->setValue('F'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('G7', function($cell) {$cell->setValue('G');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('H7', function($cell) {$cell->setValue('H'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('I7', function($cell) {$cell->setValue('I'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('J7', function($cell) {$cell->setValue('J'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('K7', function($cell) {$cell->setValue('K');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('L7', function($cell) {$cell->setValue('L'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center');  $cell->setFontWeight('bold'); });

                $sheet->cell('M7', function($cell) {$cell->setValue('M'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('N7', function($cell) {$cell->setValue('N'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('O7', function($cell) {$cell->setValue('O');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('P7', function($cell) {$cell->setValue('P'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('Q7', function($cell) {$cell->setValue('Q'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('R7', function($cell) {$cell->setValue('Q'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->setMergeColumn(array(
                    'columns' => array('A','B','C','D','R'),
                    'rows' => array(
                        array(8,11)                        
                    )
                ));

                $sheet->cell('A8', function($cell) {$cell->setValue('COD MED'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('B8', function($cell) {$cell->setValue('NOMBRE DEL PRODUCTO'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('C8', function($cell) {$cell->setValue('PRECIO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('D8', function($cell) {$cell->setValue('CPMA');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('R8', function($cell) {$cell->setValue('SOBRESTOCK');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cells('E8:J8', function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E8:J8');
                $sheet->cell('E8', function($cell) {$cell->setValue('INGRESOS'); $cell->setFontSize(13);  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cells('K8:N8', function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K8:N8');
                $sheet->cell('K8', function($cell) {$cell->setValue('SALIDAS'); $cell->setFontSize(13); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cells('O8:Q8', function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('O8:Q8');
                $sheet->cell('O8', function($cell) {$cell->setValue('STOCK'); $cell->setFontSize(13); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });                

                $sheet->setMergeColumn(array(
                    'columns' => array('K','Q'),
                    'rows' => array(
                        array(9,10)                        
                    )
                ));

                $sheet->cell('E9', function($cell) {$cell->setValue('STOCK'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setBorder('', '', '', ''); $cell->setFontWeight('bold'); }); 

                $sheet->cell('E10', function($cell) {$cell->setValue('INICIAL '); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setBorder('', '', '', ''); $cell->setFontWeight('bold'); }); 

                $sheet->cell('F9', function($cell) {$cell->setValue('INGRESO DE ALMACEN'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setBorder('', '', '', 'thin');  $cell->setFontWeight('bold'); }); 

                $sheet->cell('F10', function($cell) {$cell->setValue('SALUDPOL'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setBorder('', '', '', 'thin');  $cell->setFontWeight('bold'); }); 

                $sheet->cell('G9', function($cell) {$cell->setValue('INGRESO DE'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setBorder('', '', '', 'thin');  $cell->setFontWeight('bold'); }); 

                $sheet->cell('G10', function($cell) {$cell->setValue('SU ALMACEN'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setBorder('', '', '', 'thin');  $cell->setFontWeight('bold'); }); 

                $sheet->cell('H9', function($cell) {$cell->setValue('INGRESO DIRECTO '); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setBorder('', '', '', 'thin'); $cell->setFontWeight('bold');  }); 
                $sheet->cell('H10', function($cell) {$cell->setValue('DEL PROVEEDOR'); $cell->setAlignment('center'); $cell->setBorder('', '', '', 'thin'); $cell->setFontSize(10); $cell->setFontWeight('bold');  }); 

                $sheet->cell('I9', function($cell) {$cell->setValue('INGRESO POR'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('I10', function($cell) {$cell->setValue('TRANSFERENCIA'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold');  }); 

                $sheet->cell('J9', function($cell) {$cell->setValue('TOTAL DE'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('J10', function($cell) {$cell->setValue('INGRESO'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('K9', function($cell) {$cell->setValue('CONSUMO');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('L9', function($cell) {$cell->setValue('SALIDA POR'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('L10', function($cell) {$cell->setValue('TRANSFERENCIA'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('M9', function($cell) {$cell->setValue('PERDIDA/'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('M10', function($cell) {$cell->setValue('MERMA'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('N9', function($cell) {$cell->setValue('TOTAL DE'); $cell->setBorder('', '', '', 'thin');  $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('N10', function($cell) {$cell->setValue('SALIDAS'); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontSize(10); $cell->setFontWeight('bold'); }); 

                $sheet->cell('O9', function($cell) {$cell->setValue('STOCK '); $cell->setFontSize(10); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('O10', function($cell) {$cell->setValue('FINAL');$cell->setFontSize(10);  $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('P9', function($cell) {$cell->setValue('F.V. PROXIMA'); $cell->setFontSize(10); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('P10', function($cell) {$cell->setValue('(dia/mes/año)'); $cell->setFontSize(10); $cell->setBorder('', '', '', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('Q9', function($cell) {$cell->setValue('DISPONIBILIDAD');$cell->setFontSize(10);  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('E11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('F11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('G11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('H11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('I11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('J11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center');  $cell->setFontWeight('bold'); });

                $sheet->cell('K11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('L11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('M11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('N11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('O11', function($cell) {$cell->setValue('UND'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('P11', function($cell) {$cell->setValue('FECHAS');$cell->setFontSize(9);  $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('Q11', function($cell) {$cell->setValue('MESES'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->cell('R11', function($cell) {$cell->setValue('UNIDADES'); $cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });            

                $sheet->setWidth(array(
                    'A'     =>  20,
                    'B'     =>  100,
                    'C'     =>  12,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  20,
                    'G'     =>  20,
                    'H'     =>  20,
                    'I'     =>  18,
                    'J'     =>  12,
                    'K'     =>  12,
                    'L'     =>  18,
                    'M'     =>  12,
                    'N'     =>  12,
                    'O'     =>  12,
                    'P'     =>  15,
                    'Q'     =>  18,
                    'R'     =>  20,
                ));

                $i=13;

                if (!empty($data)) {

                    $cpma = 0;
                    $stock_incanal = 0;
                    $almacen_central= 0;
                    $mi_almacen= 0;
                    $ingreso_proveedor = 0;
                    $ingreso_transferencia = 0;
                    $unidad_ingreso = 0;
                    $unidad_consumo = 0;
                    $salida_transferencia = 0;
                    $merma = 0;
                    $total_salidas = 0;
                    $stock_final = 0;
                    $disponibilidad = 0;
                    $unidades_sobrestock = 0;
                    
                    $sheet->mergeCells('D4:H4');

                    foreach ($data as $key => $value) {
                        $i= $key+13;
                        $sheet->cell('A'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('B'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('C'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('D'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('E'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('F'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('G'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('H'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('I'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('J'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setBackground('#E0E6F8'); });    
                        $sheet->cell('K'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('L'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('M'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('N'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setBackground('#E0F8E0'); });     
                        $sheet->cell('O'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setBackground('#F5F6CE'); });     
                        $sheet->cell('P'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                        $sheet->cell('Q'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setBackground('#D8D8D8'); });    
                        $sheet->cell('R'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setBackground('#F8E0E0'); });    

                        $sheet->cell('A'.$i, $value->cod_petitorio); 
                        $sheet->cell('B'.$i, $value->descripcion); 
                        $sheet->cell('C'.$i, $value->precio); 
                        $sheet->cell('D'.$i, $value->cpma); 
                        $sheet->cell('E'.$i, $value->stock_incanal); 
                        $sheet->cell('F'.$i, $value->almacen_central); 
                        $sheet->cell('G'.$i, $value->ingreso_almacen2); 
                        $sheet->cell('H'.$i, $value->ingreso_proveedor); 
                        $sheet->cell('I'.$i, $value->ingreso_transferencia); 
                        $sheet->cell('J'.$i, $value->unidad_ingreso); 
                        $sheet->cell('K'.$i, $value->unidad_consumo); 
                        $sheet->cell('L'.$i, $value->salida_transferencia); 
                        $sheet->cell('M'.$i, $value->merma); 
                        $sheet->cell('N'.$i, $value->total_salidas); 
                        $sheet->cell('O'.$i, $value->stock_final); 
                        $sheet->cell('P'.$i, $value->fecha_vencimiento); 
                        $sheet->cell('Q'.$i, $value->disponibilidad); 
                        $sheet->cell('R'.$i, $value->unidades_sobrestock); 
                        $sheet->cell('D4', $value->nombre_establecimiento); 
                        $sheet->cell('K4', $value->cod_establecimiento);  

                        $anomes=$value->anomes;
                        $can_id=$value->can_id;
                        $establecimiento_id=$value->establecimiento_id;

                        $ano = substr($anomes, 0, 4);
                        $mes = substr($anomes, 4, 2);

                        switch ($mes) {
                            case '01':$meses='Enero';break;
                            case '02':$meses='Febrero';break;
                            case '03':$meses='Marzo';break;
                            case '04':$meses='Abril';break;
                            case '05':$meses='Mayo';break;
                            case '06':$meses='Junio';break;
                            case '07':$meses='Julio';break;
                            case '08':$meses='Agosto';break;
                            case '09':$meses='Setiembre';break;
                            case '10':$meses='Octubre';break;
                            case '11':$meses='Noviembre';break;
                            case '12':$meses='Dcanembre';break;
                        }

                        $mes_ano=$meses.' del '.$ano;

                        //////////////Mes - Año   
                        $sheet->cell('O4', $mes_ano);  


                        $consulta=DB::table('can_establecimiento')
                                ->where('can_id',$can_id) //cambiar 22
                                ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                                ->get();

                        $fecha_hora=$consulta->get(0)->updated_at;

                        //Calcular sumatoria
                        $cpma = $value->cpma + $cpma;
                        $stock_incanal = $value->stock_incanal+$stock_incanal;
                        $almacen_central= $value->almacen_central+$almacen_central;
                        $mi_almacen= $value->ingreso_almacen2+$mi_almacen;
                        $ingreso_proveedor = $value->ingreso_proveedor + $ingreso_proveedor;
                        $ingreso_transferencia = $value->ingreso_transferencia + $ingreso_transferencia;
                        $unidad_ingreso = $value->unidad_ingreso + $unidad_ingreso;
                        $unidad_consumo = $value->unidad_consumo + $unidad_consumo;
                        $salida_transferencia = $value->salida_transferencia + $salida_transferencia;
                        $merma = $value->merma + $merma;
                        $total_salidas = $value->total_salidas + $total_salidas;
                        $stock_final = $value->stock_final + $stock_final;
                        $disponibilidad = $value->disponibilidad + $disponibilidad;
                        $unidades_sobrestock = $value->unidades_sobrestock + $unidades_sobrestock;

                    }

                    $sheet->cell('D12', $cpma);
                    $sheet->cell('D12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('E12', $stock_incanal);
                    $sheet->cell('E12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('F12', $almacen_central);
                    $sheet->cell('F12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('G12', $mi_almacen);
                    $sheet->cell('G12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('H12', $ingreso_proveedor);
                    $sheet->cell('H12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('I12', $ingreso_transferencia);
                    $sheet->cell('I12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('J12', $unidad_ingreso);
                    $sheet->cell('J12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('K12', $unidad_consumo);
                    $sheet->cell('K12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('L12', $salida_transferencia);
                    $sheet->cell('L12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('M12', $merma);
                    $sheet->cell('M12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('N12', $total_salidas);     
                    $sheet->cell('N12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('O12', $stock_final);
                    $sheet->cell('O12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('Q12', $disponibilidad);
                    $sheet->cell('Q12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                    $sheet->cell('R12', $unidades_sobrestock);
                    $sheet->cell('R12', function($cell) {$cell->setFontSize(9); $cell->setBorder('thin', 'thin', 'thin', 'thin');  $cell->setAlignment('center'); $cell->setFontWeight('bold'); });
                }

                $i=$i+10;
                $j=$i+1;

                $fh=$i+2;
                

                $sheet->cell('O'.$fh, $fecha_hora);  
                

                $sheet->cell('B'.$i, function($cell) {$cell->setValue('____________________________________________________');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->cell('B'.$j, function($cell) {$cell->setValue('RESPONSABLE DEL ESTABLECIMIENTO DE SALUD');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('P2:Q2');
                $sheet->mergeCells('I'.$i.':'.'L'.$i);
                $sheet->cell('I'.$i, function($cell) {$cell->setValue('______________________________________');  $cell->setFontSize(14); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

                $sheet->mergeCells('I'.$j.':'.'L'.$j);
                $sheet->cell('I'.$j, function($cell) {$cell->setValue('RESPONSABLE DE LA FARMACIA');   $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle'); });

            });
        })->download($type);
    }
    
    ///////////////////////////9////////////////////////////////////
    public function listar_archivos_can($can_id,$establecimiento_id)
    {
        $cans = DB::table('cans')                
                ->orderby('id',$can_id)
                ->orderby('id','desc')
                ->first();                
        
        $can_id=$cans->id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        $nivel_id=$establecimiento->nivel_id;

        $ano=$cans->ano;

        $archivos= DB::table('archivos')        
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('can_id', $can_id)
                            ->where('rubro_id',0)
                            ->where('estado',1)
                            ->get();

        $archivos_comite= DB::table('archivos')        
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('can_id', $can_id)
                            ->where('servicio_id',0)
                            ->where('estado',1)
                            ->get();
    

        $observaciones= DB::table('observaciones')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('can_id', $can_id)
                            ->where('estado',1)
                            ->get();

        
        return view('admin.cans.listar_archivos')
                    ->with('can_id', $can_id)    
                    ->with('nivel_id', $nivel_id)                    
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('observaciones', $observaciones)
                    ->with('archivos', $archivos)   
                    ->with('archivos_comite', $archivos_comite);        
    }

    public function listar_archivos_can_rubro($can_id,$establecimiento_id,$rubro_id)
    {
        $cans = DB::table('cans')                
                ->where('id',$can_id)
                ->orderby('id','desc')
                ->first();

        $can_id=$cans->id;

        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        $ano=$cans->ano;

        $archivos= DB::table('archivos')        
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('rubro_id', $rubro_id)
                            ->where('can_id', $can_id)
                            ->where('estado',1)
                            ->get();

        $observaciones= DB::table('observaciones')    
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('rubro_id', $rubro_id)
                            //->where('can_id', $can_id)
                            ->where('can_id', 4)
                            ->where('estado',1)
                            ->get();

        switch ($rubro_id) {
            case 1: $descripcion='Productos Farmaceuticos'; break;
            case 2: $descripcion='Material Biomedico, Instrumental Quirurgico y Productos Afines'; break;
            case 3: $descripcion='Material e Insumos Dentales'; break;
            case 4: $descripcion='Material e Insumos de Laboratorio'; break;
            case 5: $descripcion='Material Fotografico y Fonotecnico'; break;
            
            
        }
        
        return view('admin.cans.listar_archivos_rubros')
                    ->with('can_id', $can_id)            
                    ->with('servicio_id', $rubro_id) 
                    ->with('descripcion', $descripcion)                    
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('observaciones', $observaciones)
                    ->with('archivos', $archivos);        
    }


    public function listar_archivos_can_servicio($can_id,$establecimiento_id,$servicio_id)
    {
        $cans = DB::table('cans')                
                ->where('id',$can_id)
                ->orderby('id','desc')
                ->first();

        $can_id=$cans->id;

        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        $ano=$cans->ano;

        $archivos= DB::table('archivos')        
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id', $servicio_id)
                            ->where('can_id',$can_id)
                            ->where('estado',1)
                            ->get();

        $observaciones= DB::table('observaciones')    
                            ->where('establecimiento_id',$establecimiento_id)
                            //->where('can_id',$can_id)
                            ->where('can_id',$can_id)
                            ->where('estado',1)
                            ->get();
        
        return view('admin.cans.listar_archivos_servicios')
                    ->with('can_id', $can_id)            
                    ->with('servicio_id', $servicio_id)                    
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('observaciones', $observaciones)
                    ->with('archivos', $archivos);        
    }

    public function subiendo_archivos_can($can_id,$establecimiento_id)
    {
        $cans = DB::table('cans')                
                ->orderby('id',$can_id)
                ->orderby('id','desc')
                ->first();                
        
        $can_id=$cans->id;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        $ano=$cans->ano;

        $archivos= DB::table('observaciones')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('can_id',$can_id)
                            ->where('estado',1)
                            ->get();
        
        return view('admin.cans.subiendo_archivos')
                    ->with('can_id', $can_id)                    
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('archivos', $archivos);        
    }

    public function subiendo_archivos_rubro($can_id,$establecimiento_id,$rubro_id)
    {
        $cans = DB::table('cans')                
                ->orderby('id',$can_id)
                ->orderby('id','desc')
                ->first();                
        
        $can_id=$cans->id;        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        $ano=$cans->ano;
        $archivos= DB::table('observaciones')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('estado',1)
                            ->where('rubro_id',$rubro_id)
                            ->get();

        switch ($rubro_id) {
            case 1: $descripcion='Productos Farmaceuticos'; break;
            case 2: $descripcion='Material Biomedico, Instrumental Quirurgico y Productos Afines'; break;
            case 3: $descripcion='Material e Insumos Dentales'; break;
            case 4: $descripcion='Material e Insumos de Laboratorio'; break;
            case 5: $descripcion='Material Fotografico y Fonotecnico'; break;            
            
        }
        
        return view('admin.cans.subiendo_archivos_rubro')
                    ->with('can_id', $can_id)   
                    ->with('descripcion', $descripcion)   
                    ->with('servicio_id', $rubro_id)                    
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('archivos', $archivos);        
    }

    public function subir_archivo_correccion(Request $request,$id,$can_id, $establecimiento_id)
    {
        $input = $request->all();
        $user_id=Auth::user()->id;
        

        if ($request->hasFile('photo')){
            $name_photo = time().'-'.$request->photo->getClientOriginalName();
            $original_name=$request->photo->getClientOriginalName();
            $input['photo'] = '/upload/observaciones/'.$id.'/'.$name_photo;            
            $request->photo->move(public_path('/upload/observaciones/'.$id.'/'), $input['photo']);
            $extension_archivo= $request->photo->getClientOriginalExtension();
        }

        DB::table('observaciones')
            ->insert([
                'establecimiento_id' => $establecimiento_id,                
                'nombre_archivo'=>$original_name,
                'responsable_id'=>$user_id,
                'descarga_archivo'=>$input['photo'],
                'can_id'=>$can_id,
                'descripcion_archivo'=>$request->descripcion,
                'extension_archivo'=>$extension_archivo,
                'created_at'=>Carbon::now(),
        ]);       
        
        return redirect(route('cans.listar_archivos_can',[$can_id,$id]));
    }

    public function subir_archivo_rubro(Request $request,$id,$can_id, $establecimiento_id, $servicio_id)
    {
        $input = $request->all();
        $user_id=Auth::user()->id;
        

        if ($request->hasFile('photo')){
            $name_photo = time().'-'.$request->photo->getClientOriginalName();
            $original_name=$request->photo->getClientOriginalName();
            $input['photo'] = '/upload/observaciones/'.$id.'/'.$name_photo;            
            $request->photo->move(public_path('/upload/observaciones/'.$id.'/'), $input['photo']);
            $extension_archivo= $request->photo->getClientOriginalExtension();
        }

        DB::table('observaciones')
            ->insert([
                'establecimiento_id' => $establecimiento_id,                
                'nombre_archivo'=>$original_name,
                'rubro_id'=>$servicio_id,
                'responsable_id'=>$user_id,
                'can_id'=>$can_id,
                'descarga_archivo'=>$input['photo'],
                'descripcion_archivo'=>$request->descripcion,
                'extension_archivo'=>$extension_archivo,
                'created_at'=>Carbon::now(),
        ]);       
        
        //return redirect(route('cans.listar_archivos_can_rubro',[$can_id,$establecimiento_id,$servicio_id]));
        return redirect(route('cans.listar_archivos_can_servicio',[$can_id,$establecimiento_id,$servicio_id]));
        
    }

/**********************************************************************************************/
//select * from estimacions es where can_id in (9) and necesidad_anual_1 in (0) and estado <>2 and necesidad_anual > 0 
public function actualiza_necesidad()
{
    $estimacions= DB::table('estimacions')
                            ->where('estado','<>',2)
                            ->where('can_id',9)
                            ->where('necesidad_anual','>',0)
                            ->where('necesidad_anual_1',0)
                            ->get();
  
            $i=0;
            

            foreach ($estimacions as $key => $value) {
                
                $necesidad_anual_1 = $value->mes1_1 + $value->mes2_1 + $value->mes3_1 + $value->mes4_1 + $value->mes5_1 + $value->mes6_1 + $value->mes7_1 + $value->mes8_1 + $value->mes9_1 + $value->mes10_1 + $value->mes11_1 + $value->mes12_1;
                $necesidad_anual_2 = $value->mes1_2 + $value->mes2_2 + $value->mes3_2 + $value->mes4_2 + $value->mes5_2 + $value->mes6_2 + $value->mes7_2 + $value->mes8_2 + $value->mes9_2 + $value->mes10_2 + $value->mes11_2 + $value->mes12_2;

                if($necesidad_anual_1 == 0 ){
                    $mes1 = $value->mes1 + intval($value->mes1 * 0.05);
                    $mes2 = $value->mes2 + intval($value->mes2 * 0.05);
                    $mes3 = $value->mes3 + intval($value->mes3 * 0.05);
                    $mes4 = $value->mes4 + intval($value->mes4 * 0.05);
                    $mes5 = $value->mes5 + intval($value->mes5 * 0.05);
                    $mes6 = $value->mes6 + intval($value->mes6 * 0.05);
                    $mes7 = $value->mes7 + intval($value->mes7 * 0.05);
                    $mes8 = $value->mes8 + intval($value->mes8 * 0.05);
                    $mes9 = $value->mes9 + intval($value->mes9 * 0.05);
                    $mes10 = $value->mes10 + intval($value->mes10 * 0.05);
                    $mes11 = $value->mes11 + intval($value->mes12 * 0.05);
                    $mes12 = $value->mes12 + intval($value->mes12 * 0.05);
                    $necesidad_anual_1 = $mes1 + $mes2 + $mes3 + $mes4 + $mes5 + $mes6 + $mes7 + $mes8 + $mes9 + $mes10 + $mes11 + $mes12;

                    DB::table('estimacions')
                        ->where('id',$value->id)
                        ->update([
                            'necesidad_anual_1' => $necesidad_anual_1,
                            'mes1_1' => $mes1,
                            'mes2_1' => $mes2,
                            'mes3_1' => $mes3,
                            'mes4_1' => $mes4,
                            'mes5_1' => $mes5,
                            'mes6_1' => $mes6,
                            'mes7_1' => $mes7,
                            'mes8_1' => $mes8,
                            'mes9_1' => $mes9,
                            'mes10_1' => $mes10,
                            'mes11_1' => $mes11,
                            'mes12_1' => $mes12,
                    ]);
                }

                if($necesidad_anual_2 == 0 ){
                    if($necesidad_anual_1 == 0 ){
                    $mes1_1 = $value->mes1 + intval($value->mes1 * 0.10);
                    $mes2_1 = $value->mes1 + intval($value->mes2 * 0.10);
                    $mes3_1 = $value->mes1 + intval($value->mes3 * 0.10);
                    $mes4_1 = $value->mes1 + intval($value->mes4 * 0.10);
                    $mes5_1 = $value->mes1 + intval($value->mes5 * 0.10);
                    $mes6_1 = $value->mes1 + intval($value->mes6 * 0.10);
                    $mes7_1 = $value->mes1 + intval($value->mes7 * 0.10);
                    $mes8_1 = $value->mes1 + intval($value->mes8 * 0.10);
                    $mes9_1 = $value->mes1 + intval($value->mes9 * 0.10);
                    $mes10_1 = $value->mes1 + intval($value->mes10 * 0.10);
                    $mes11_1 = $value->mes1 + intval($value->mes12 * 0.10);
                    $mes12_1 = $value->mes1 + intval($value->mes12 * 0.10);
                    $necesidad_anual_2 = $mes1_1 + $mes2_1 + $mes3_1 + $mes4_1 + $mes5_1 + $mes6_1 + $mes7_1 + $mes8_1 + $mes9_1 + $mes10_1 + $mes11_1 + $mes12_1;
                    DB::table('estimacions')
                    ->where('id',$value->id)
                    ->update([
                        'necesidad_anual_2' => $necesidad_anual_2, 
                        'mes1_2' => $mes1_1,
                        'mes2_2' => $mes2_1,
                        'mes3_2' => $mes3_1,
                        'mes4_2' => $mes4_1,
                        'mes5_2' => $mes5_1,
                        'mes6_2' => $mes6_1,
                        'mes7_2' => $mes7_1,
                        'mes8_2' => $mes8_1,
                        'mes9_2' => $mes9_1,
                        'mes10_2' => $mes10_1,
                        'mes11_2' => $mes11_1,
                        'mes12_2' => $mes12_1, 
                ]);
                }
            } 

                DB::table('estimacions')
                    ->where('id',$value->id)
                    ->update([
                        'necesidad_anual_1' => $necesidad_anual_1,
                        'necesidad_anual_2' => $necesidad_anual_2, 
                ]);
            }

    

}

public function ejecutar_matchado($id,$establecimiento_id,$tipo)
    {
        //$can = $this->canRepository->findWithoutFail($id);
        
        $can = DB::table('cans')->orderby('cans.id','desc')->get();
        
        
        //$can_id=$cans->get(0)->id;

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<3205;$i++){
            for($j=0;$j<5;$j++){ //total servicios
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        
            
            switch ($tipo) {
                case 1: $compara=1;$descripcion_tipo="PRODUCTOS FARMACEUTICOS";break;
                case 2: $compara=2;$descripcion_tipo="MATERIAL BIOMEDICO";break;
                case 3: $compara=3;$descripcion_tipo="INSTRUMENTAL QUIRURGICO";break;
                case 4: $compara=4;$descripcion_tipo="MATERIAL E INSUMOS ODONTOLOGICOS";break;
                case 5: $compara=5;$descripcion_tipo="MATERIAL E INSUMOS DE LABORATORIO";break;
                case 6: $compara=6;$descripcion_tipo="MATERIAL FOTOGRAFICO Y FONOTECNICO";break;
                case 7: $compara=7;$descripcion_tipo="PRODUCTOS AFINES";break;
                case 11: $compara=11;$descripcion_tipo="INSTRUMENTAL TRAUMATOLOGIA";break;
                case 12: $compara=12;$descripcion_tipo="MATERIAL TRAUMATOLOGIA";break;
                case 13: $compara=13;$descripcion_tipo="AYUDAS BIOMECANICAS";break;
                

            }
        if ($tipo!=5) {
            $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as anual,sum(necesidad_actual) as actual,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id','estimacion_servicio.nombre_servicio'))
                    //->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                    //->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                    //->join('servicios', function($join)
                    //    {
                    //        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                    //             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                    //    })
                    //->join('petitorio_servicio', function($join)
                    //    {
                    //        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                    //             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                    //    })
                    //->join('petitorios', function($join)
                    //    {
                    //        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                    //             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                    //    })
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)
                    ->where('estimacion_servicio.estado','<>',2)
                    ->where('estimacion_servicio.petitorio','=',1)
                    ->where('estimacion_servicio.tipo_dispositivo_id',$tipo)
                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                    ->where('estimacion_servicio.can_id',$id)
                    //->where('estimacion_servicio.petitorio_id',6)
                    ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', )
                    ->orderby('estimacion_servicio.petitorio_id','asc')
                    //->orderby('servicios.id','asc')
                    ->get();

            
        }

        else
            
        {   

                 $consulta = DB::table('estimacion_servicio')
                                    ->select(DB::raw('sum(necesidad_anual) as anual,  sum(necesidad_actual) as actual,estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id'))
                                    //->join('establecimientos', 'establecimientos.id', 'estimacion_servicio.establecimiento_id')
                                    //->join('can_servicio', 'can_servicio.establecimiento_id', 'establecimientos.id')
                                    //->join('servicios', function($join)
                                    //    {
                                    //        $join->on('servicios.id', '=', 'can_servicio.servicio_id')
                                    //             ->on('servicios.id', '=', 'estimacion_servicio.servicio_id');
                                    //    })
                                    //->join('petitorio_servicio', function($join)
                                    //    {
                                    //        $join->on('servicios.id', '=', 'petitorio_servicio.servicio_id')
                                    //             ->on('estimacion_servicio.petitorio_id', '=', 'petitorio_servicio.petitorio_id');
                                    //    })
                                    //->join('petitorios', function($join)
                                    //    {
                                    //        $join->on('petitorios.id', '=', 'petitorio_servicio.petitorio_id')
                                    //             ->on('petitorios.id', '=', 'estimacion_servicio.petitorio_id');
                                    //    })
                                    ->where('estimacion_servicio.necesidad_anual','>',0)
                                    ->where('estimacion_servicio.can_id',$id)
                                    ->where('estimacion_servicio.estado','<>',2)
                                    ->where('estimacion_servicio.petitorio','=',1)
                                    ->where( function ( $query )
                                            {
                                                $query->orWhere('tipo_dispositivo_id',5)     
                                                    ->orWhere('tipo_dispositivo_id',10);

                                            })
                                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                                    ->where('estimacion_servicio.can_id',$id)
                                    ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id',)
                                    ->orderby('estimacion_servicio.petitorio_id','asc')
                                    //->orderby('servicios.id','asc')
                                    ->get();

                                $descripcion_tipo="MATERIAL DE LABORATORIO E INSUMO DE LABORATORIO";
            
        }   

        
            
            $servicios_x = DB::table('servicios')
                            ->select('servicios.id','servicios.nombre_servicio')
                            ->join('can_servicio','servicios.id','can_servicio.servicio_id')
                            ->where('can_servicio.establecimiento_id',$establecimiento_id)
                            ->where('can_servicio.can_id',$id)
                               ->orderby('servicios.id','asc')
                               ->get();

            $i=0;
            //dd($servicios_x);

            foreach ($servicios_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_servicio;                   
                $i++;
            }
            
            $fila_anterior=5000; $x=-1; $y=0; $z=0;
            
            //dd($consulta);

            foreach ($consulta as $key => $value) {
                
                $fila=$value->petitorio_id;
                
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }
                $n=0;
                for($k=0;$k<$i;$k++){
                    if($value->servicio_id==$descripcion[$k][0]){
                        
                        $m=$k*2;                        
                        $n=$m+1;                        
                        $can_productos[$x][1]=$value->descripcion;
                        $can_productos[$x][0]=$value->petitorio_id;
                        $can_productos[$x][2]=$can_productos[$x][2]+$value->anual;
                        $can_productos[$x][3]=$can_productos[$x][3]+$value->anual;
                    }
                }
                $y++;
            }
            $x++;

        
        if($tipo!=5){
            $cad='select ET.id, ET.petitorio_id,
                ET.descripcion, ET.necesidad_anual, ET.necesidad_actual,ET.tipo_dispositivo_id            
                from estimacions ET
                Where ET.estado !=2 and ET.can_id ='. $id .' and ET.petitorio = 1 and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
                Order by ET.petitorio_id asc
                ';
                $data = DB::select($cad);
        }
        else
        {
            $cad='select ET.id, ET.petitorio_id,
            ET.descripcion, ET.necesidad_anual, ET.necesidad_actual,ET.tipo_dispositivo_id            
            from estimacions ET
            Where ET.estado !=2 and ET.can_id ='. $id .'and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=5 or ET.tipo_dispositivo_id=10) and ET.petitorio = 1 
            Order by ET.petitorio_id asc
            ';
            $data = DB::select($cad);
        }

        $total=count($data);
        

        $x=0;
//dd($can_productos);

        foreach ($data as $key => $value) {
                
            for($k=0;$k<$total;$k++){

                if($can_productos[$k][0]==$value->petitorio_id){
                    //if($can_productos[$k][2]!=$can_productos[$k][3]){
                        $necesidad_actual=$can_productos[$k][3];                            
                        DB::table('estimacions')
                            ->where('id',$value->id)
                            ->update([
                                'necesidad_anual' => $necesidad_actual             
                        ]);

                    //}
                }
            }
            
        }
    }

/*************************************************************************************************/
public function actualiza_final($can_id,$establecimiento_id)
    {
        $model_estimacion = new Estimacion();

        $estimaciones_x = DB::table('estimacion_servicio')
                            ->select('estimacion_servicio.petitorio_id')
                            ->where('estimacion_servicio.estado','<>',2)
                            ->where('estimacion_servicio.can_id',$can_id)
                            ->where('estimacion_servicio.petitorio',1)
                            ->where('estimacion_servicio.necesidad_anual','>',0)
                            ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                            ->groupby('estimacion_servicio.petitorio_id')
                            ->orderby('estimacion_servicio.petitorio_id','asc')
                            ->get();

            $i=0;


            foreach ($estimaciones_x as $key => $value) {
                $grabando  = $model_estimacion->ActualizaFinalProductoNivel2y3($value->petitorio_id,$can_id,$establecimiento_id);
                $i++;
            }
        
    }


}
    ///////////////////////////9////////////////////////////////////77