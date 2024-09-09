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
use DB;
use App\Models\Establecimiento;
use App\Models\Can;
use Illuminate\Support\Facades\Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;      

class ResponsableFarmaciaNivel2_3Controller extends AppBaseController
{
    /** @var  EstimacionRepository */  
    public function __construct()
    {
        
    }

    public function index(Request $request)
    {
        //Verifico de que establecimiento es el usuario
        $nombre_establecimiento=Auth::user()->nombre_establecimiento;
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        $cans=DB::table('can_establecimiento')
                    ->join('cans', 'can_establecimiento.can_id','cans.id')                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('cans.id', 'desc')
                    ->get();  

        
        $establecimiento_id=Auth::user()->establecimiento_id;
        $tipo_servicio_id=Auth::user()->rol;
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel_id=$establecimiento->nivel_id;
        $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->get();

        return view('site.responsable_farmacia_hospital.listar_servicios')
                    ->with('servicios', $servicios)
                    ->with('tipo_servicio_id', $tipo_servicio_id)
                    ->with('can_id', $cans->get(0)->can_id)
                    ->with('cans', $cans)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nivel_id', $nivel_id);        
    }

    public function listar_medicos($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $nombre_establecimiento=Auth::user()->nombre_establecimiento;
        $establecimiento_id=Auth::user()->establecimiento_id;

        $cans=DB::table('can_establecimiento')
                    ->join('cans', 'can_establecimiento.can_id','cans.id')                    
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->where('can_id',$can_id)//cambiar 1                    
                    ->get();  
        $establecimiento_id=Auth::user()->establecimiento_id;
        $tipo_servicio_id=Auth::user()->rol;
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel_id=$establecimiento->nivel_id;
        $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')
            ->where('can_id',$can_id)  
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->orderby('nombre_servicio', 'asc')
            ->get(); 

        


        $responsables= DB::table('responsables')
                            ->where('rol',2)      
                            ->where('etapa',1)                            
                            ->where('can_id',$can_id)  
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id','>',0)
                            ->get();
        

        return view('site.responsable_farmacia_hospital.table_medicos')
                    ->with('servicios', $servicios)
                    ->with('tipo_servicio_id', $tipo_servicio_id)
                    ->with('responsables', $responsables)
                    ->with('can_id', $cans->get(0)->can_id)
                    ->with('cans', $cans->get(0))
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nivel_id', $nivel_id);        
    }

    public function listar_servicios($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        $tipo_servicio_id=Auth::user()->rol;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;
        
        $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')            
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->where('can_id',$can_id)   
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
        
        $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();

        $medicamento_cerrado=$establecimiento_cerrado->get(0)->medicamento_cerrado;
        $medicamento_cerrado_rectificacion=$establecimiento_cerrado->get(0)->medicamento_cerrado_rectificacion;
        $dispositivo_cerrado=$establecimiento_cerrado->get(0)->dispositivo_cerrado;
        $dispositivo_cerrado_rectificacion=$establecimiento_cerrado->get(0)->dispositivo_cerrado_rectificacion;
        
        $rubro_pf=$establecimiento_cerrado->get(0)->rubro_pf; //3
        $rubro_mb_iq_pa=$establecimiento_cerrado->get(0)->rubro_mb_iq_pa; //4
        $rubro_mid=$establecimiento_cerrado->get(0)->rubro_mid; //5
        $rubro_mil=$establecimiento_cerrado->get(0)->rubro_mil; //6
        $rubro_mff=$establecimiento_cerrado->get(0)->rubro_mff; //8

        return view('site.responsable_farmacia_hospital.listar_servicios')
                    ->with('servicios', $servicios)
                    ->with('tipo_servicio_id', $tipo_servicio_id)
                    ->with('responsables', $responsables)
                    ->with('can_id', $can_id)
                    ->with('medicamento_cerrado', $medicamento_cerrado)
                    ->with('dispositivo_cerrado', $dispositivo_cerrado)
                    ->with('medicamento_cerrado_rectificacion', $medicamento_cerrado_rectificacion)
                    ->with('dispositivo_cerrado_rectificacion', $dispositivo_cerrado_rectificacion)
                    ->with('ano', $ano)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('rubro_pf', $rubro_pf)
                    ->with('rubro_mb_iq_pa', $rubro_mb_iq_pa)
                    ->with('rubro_mid', $rubro_mid)
                    ->with('rubro_mil', $rubro_mil)
                    ->with('rubro_mff', $rubro_mff)
                    ->with('nivel_id', $nivel_id);        
    }

    public function listar_responsables_servicios($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        $tipo_servicio_id=Auth::user()->rol;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
            
        //Nivel del Establecimiento
        $nivel_id=$establecimiento->nivel_id;
        
        $servicios = DB::table('servicios')
            ->join('can_servicio', 'can_servicio.servicio_id','servicios.id')            
            ->where('can_servicio.establecimiento_id',$establecimiento_id)
            ->where('can_id',$can_id)   
            ->orderby('nombre_servicio','asc')
            ->get();

        $cans=DB::table('cans')->orderBy('id', 'desc')->first();
                    $can = Can::find($cans->id);
                    $can_id_ultimo=$cans->id;


        if($can_id==$can_id_ultimo):
            $responsables= DB::table('users')
                            ->where('rol',2)                                                              
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id','>',0)
                            ->where('estado',1)
                            ->get();     
            $can_activo=1;   
        else:            
            $responsables= DB::table('responsables')
                            ->where('rol',2)    
                            ->where('etapa',1)      
                            ->where('can_id',$can_id)                            
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('servicio_id','>',0)
                            ->get();
            $can_activo=0;        
        endif;
        
        $jefe_ipress= DB::table('users')
                            ->where('rol',9)    
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('estado',1)
                            ->first();
        //dd($jefe_ipress);
        $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();

        return view('site.responsable_farmacia_hospital.listar_responsables_servicios')
                    ->with('servicios', $servicios)
                    ->with('tipo_servicio_id', $tipo_servicio_id)
                    ->with('responsables', $responsables)
                    ->with('jefe_ipress', $jefe_ipress)
                    ->with('can_activo', $can_activo)                    
                    ->with('can_id', $can_id)                    
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    
                    ->with('nivel_id', $nivel_id);        
    }

    public function listar_archivos_nivel1($can_id)
    {
        $cans = DB::table('cans')                
                ->orderby('id','desc')
                ->first();        
        
        $ultimo_id=$cans->id; 
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        $rol_user=Auth::user()->rol;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id); 

        //$ano=$cans->ano;

        $cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->get();  
        
        if($cerrado->get(0)->medicamento_cerrado==2 or $cerrado->get(0)->dispositivo_cerrado==2)
        {
            $archivos= DB::table('archivos')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('can_id', $can_id)
                    ->where('estado',1)
                    ->get();

            return view('site.responsable_farmacia_hospital.listar_archivos')
                    ->with('can_id', $can_id)
                    ->with('ultimo_id', $ultimo_id)
                    ->with('nivel', 1)
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('archivos', $archivos);        
        }
        else
        {
            Flash::error('Debe cerrar su petitorio tanto de medicamentos como de dispositivos para ver esta opcion');
            return redirect(route('farmacia.index'));   
        }
            
        
    }

    public function listar_archivos_comite($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        $rol_user=Auth::user()->rol;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        switch ($rol_user) {
            case 3: $descripcion='rubro_pf'; $rubro_id=1; break;
            case 4: $descripcion='rubro_mb_iq_pa'; $rubro_id=2; break;
            case 5: $descripcion='rubro_mid'; $rubro_id=3; break;
            case 6: $descripcion='rubro_mil'; $rubro_id=4; break;
            case 8: $descripcion='rubro_mff'; $rubro_id=5; break;
            
        }

        $cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id)
                    ->where($descripcion,2)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->count();  

        $cans = DB::table('cans')                
                ->orderby('id','desc')
                ->first();        
        
        $ultimo_id=$cans->id; 
        
        if($cerrado==1)
        {
            $archivos= DB::table('archivos')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('can_id', $can_id)
                    ->where('rubro_id', $rubro_id)
                    ->where('estado',1)
                    ->get();

            return view('site.responsable_farmacia_hospital.listar_archivos_comite')
                    ->with('can_id', $can_id)
                    ->with('ultimo_can', $ultimo_id)
                    ->with('nivel', 1)
                    ->with('rubro_id', $rubro_id)
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('archivos', $archivos);        
        }
        else
        {
            Flash::error('Deben de haber cerrado todos los servicios para ver esta opcion');
            return redirect(route('farmacia_servicios.index'));   
        }
            
        
    }

    public function listar_observaciones_nivel1($can_id)
    {
        /*$cans = DB::table('cans')                
                ->orderby('id','desc')
                ->first();        
        
        $can_id=$cans->id;
        //Verifico de que establecimiento es el usuario
        */
        $establecimiento_id=Auth::user()->establecimiento_id;
        $rol_user=Auth::user()->rol;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id); 

        //$ano=$cans->ano;

        $cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->get();  
            
        $observaciones= DB::table('observaciones')
                        ->where('establecimiento_id',$establecimiento_id)   
                        ->where('can_id',$can_id)               
                        ->where('estado',1)
                        ->get();

        return view('site.responsable_farmacia_hospital.listar_observaciones')
                ->with('can_id', $can_id)
                ->with('nivel', 1)
                ->with('establecimiento_id', $establecimiento_id)  
                ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                ->with('observaciones', $observaciones);
        
    }

    public function listar_observaciones_comite($can_id)
    {
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        $rol_user=Auth::user()->rol;
        
        switch ($rol_user) {
            case 3: $descripcion='rubro_pf'; $rubro_id=1; break;
            case 4: $descripcion='rubro_mb_iq_pa'; $rubro_id=2; break;
            case 5: $descripcion='rubro_mid'; $rubro_id=3; break;
            case 6: $descripcion='rubro_mil'; $rubro_id=4; break;
            case 8: $descripcion='rubro_mff'; $rubro_id=5; break;   
        }

        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        $cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->get();  
            
        $observaciones= DB::table('observaciones')
                        ->where('establecimiento_id',$establecimiento_id)               
                        ->where('rubro_id',$rubro_id)               
                        ->where('can_id',$can_id)               
                        ->where('estado',1)
                        ->get();

        return view('site.responsable_farmacia_hospital.listar_observaciones')
                ->with('can_id', $can_id)                
                ->with('nivel', 2)
                ->with('establecimiento_id', $establecimiento_id)  
                ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                ->with('observaciones', $observaciones);
        
    }

    public function listar_archivos_nivel2y3($can_id)
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

        $cans_ultimo= Can::latest('id')->first();
        $ultimo_id=$cans_ultimo->id;

        if($cerrado->get(0)->medicamento_cerrado==2 or $cerrado->get(0)->dispositivo_cerrado==2)
        {
            
            $archivos= DB::table('archivos')
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('can_id', $can_id)
                        ->where('servicio_id', $servicio_id)
                        ->where('estado',1)
                        ->get();

            return view('site.responsable_farmacia_hospital.listar_archivos')
                    ->with('can_id', $can_id)
                    ->with('ultimo_id', $ultimo_id)
                    ->with('nivel', 2)
                    ->with('establecimiento_id', $establecimiento_id)  
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('archivos', $archivos);   
        }
        else
        {
            
            Flash::error('Debe cerrar su petitorio de medicamentos o dispositivos para ver esta opcion');
            return redirect(route('estimacion_servicio.index'));   
        }     
            
    }

    public function listar_archivos_servicios()
    {
        $cans = DB::table('cans')                
                ->orderby('id','desc')
                ->first();        
        
        $can_id=$cans->id;
        //Verifico de que establecimiento es el usuario
        $establecimiento_id=Auth::user()->establecimiento_id;
        $rol_user=Auth::user()->rol;
        
        //Busco datos del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id); 

        $ano=$cans->ano;

        $cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->get();  
        
            if($rol_user==2)
            {   
                $servicio_id=Auth::user()->servicio_id;
                $cerrado=DB::table('can_servicio')
                    ->where('can_id',$can_id)
                    ->where('servicio_id',$servicio_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->get();  

                if($cerrado->get(0)->medicamento_cerrado==2 or $cerrado->get(0)->dispositivo_cerrado==2)
                {
                
                    $archivos= DB::table('archivos')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('can_id', $can_id)
                                ->where('servicio_id', $servicio_id)
                                ->where('estado',1)
                                ->get();

                    $observaciones= DB::table('observaciones')                      
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('estado',1)
                                    ->get();
                
                    return view('site.responsable_farmacia_hospital.listar_archivos')
                            ->with('can_id', $can_id)
                            ->with('establecimiento_id', $establecimiento_id)  
                            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                            ->with('observaciones', $observaciones)
                            ->with('archivos', $archivos);   
                }
                else
                {
                    Flash::error('Debe cerrar su petitorio  de medicamentos o de dispositivos para ver esta opcion');
                    return redirect(route('farmacia.index'));   
                }     
            }
            else
            {
                if($cerrado->get(0)->medicamento_cerrado==2 or $cerrado->get(0)->dispositivo_cerrado==2)
                {
                    $archivos= DB::table('archivos')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('can_id', $can_id)
                            ->where('estado',1)
                            ->get();

                    $observaciones= DB::table('observaciones')
                                    ->where('establecimiento_id',$establecimiento_id)               
                                    ->where('estado',1)
                                    ->get();
            
                    return view('site.responsable_farmacia_hospital.listar_archivos')
                            ->with('can_id', $can_id)
                            ->with('establecimiento_id', $establecimiento_id)  
                            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                            ->with('observaciones', $observaciones)
                            ->with('archivos', $archivos);        
                }
                else
                {
                    Flash::error('Debe cerrar su petitorio de medicamentos o de dispositivos para ver esta opcion');
                    return redirect(route('farmacia.index'));   
                }
            }
        
    }

    public function subir_archivo(Request $request,$id,$can_id)
    {
        $input = $request->all();
        $user_id=Auth::user()->id;

        $rol_user=Auth::user()->rol;

        if($rol_user==2){
            $servicio_id=Auth::user()->servicio_id;
            $nivel=2;
        }
        else{
            $servicio_id=0;
            $nivel=1;
        }
        
        if ($request->hasFile('photo')){
            $name_photo = time().'-'.$request->photo->getClientOriginalName();
            $original_name=$request->photo->getClientOriginalName();

            $input['photo'] = '/upload/establecimientos/'.$id.'/'.$name_photo;            
            $request->photo->move(public_path('/upload/establecimientos/'.$id.'/'), $input['photo']);
            $extension_archivo= $request->photo->getClientOriginalExtension();
        }

        DB::table('archivos')
            ->insert([
                'can_id' => $can_id,
                'servicio_id' => $servicio_id,
                'establecimiento_id' => $id,
                'nombre_archivo'=>$original_name,
                'responsable_id'=>$user_id,
                'descarga_archivo'=>$input['photo'],
                'descripcion_archivo'=>$request->descripcion,
                'extension_archivo'=>$extension_archivo,
                'created_at'=>Carbon::now(),
        ]);

        if($nivel==1)
            return redirect(route('farmacia_servicios.listar_archivos_nivel1',[$can_id]));
        else
            return redirect(route('farmacia_servicios.listar_archivos_nivel2y3',[$can_id]));
    }

    public function subir_archivo_comite(Request $request,$id,$can_id,$rubro_id)
    {
        $input = $request->all();
        $user_id=Auth::user()->id;
        $rol_user=Auth::user()->rol;

        if ($request->hasFile('photo')){
            $name_photo = time().'-'.$request->photo->getClientOriginalName();
            $original_name=$request->photo->getClientOriginalName();
            $input['photo'] = '/upload/establecimientos/'.$id.'/'.$name_photo;            
            $request->photo->move(public_path('/upload/establecimientos/'.$id.'/'), $input['photo']);
            $extension_archivo= $request->photo->getClientOriginalExtension();
        }

        DB::table('archivos')
            ->insert([
                'can_id' => $can_id,
                'rubro_id' => $rubro_id,
                'servicio_id' => 0,
                'establecimiento_id' => $id,
                'nombre_archivo'=>$original_name,
                'responsable_id'=>$user_id,
                'descarga_archivo'=>$input['photo'],
                'descripcion_archivo'=>$request->descripcion,
                'extension_archivo'=>$extension_archivo,
                'created_at'=>Carbon::now(),
        ]);

        
        return redirect(route('farmacia_servicios.listar_archivos_comite',[$can_id]));
        
    }


    public function eliminar_archivo($id,$can_id)
    {
        $rol_user=Auth::user()->rol;
        

        DB::table('archivos')
            ->where('id',$id)
            ->update([
                'estado' => 0                
        ]);

        Flash::success('Se elimino, satisfactoriamente');        
        if($rol_user==2):
            return redirect(route('farmacia_servicios.listar_archivos_nivel2y3',[$can_id]));
        else:
            if($rol_user==7):
                return redirect(route('farmacia_servicios.listar_archivos_nivel1',[$can_id]));
            //else:

            endif;

        endif;
                
    }

    
    public function manual($id)
    {
        return view('site.estimacions.manual')
        ->with('id', $id);   ;     
    }
    
    public function show($id){

    }
    public function show_avance($petitorio_id)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        $estimaciones = DB::table('estimacion_servicio as B')
                    ->select('B.nombre_servicio','B.necesidad_anual')
                    ->addselect('C.name as nombre')
                    ->join('users as C', 'C.servicio_id','B.servicio_id')
                    ->where('B.establecimiento_id',$establecimiento_id)
                    ->where('B.can_id',9)
                    ->where('B.petitorio',1)
                    ->where('B.petitorio_id',$petitorio_id)
                    ->where('C.establecimiento_id',$establecimiento_id)
                    ->where('C.rol',2)
                    ->where('B.estado','<>',2)
                    ->distinct()
                    ->groupby('B.nombre_servicio','B.necesidad_anual','C.name')
                    ->get();
                          
        
        $descripcionproducto='';
        
        return view('site.responsable_farmacia_hospital.medicamentos.mostrar_datos2')->with('estimaciones', $estimaciones)->with('descripcionproducto',$descripcionproducto);     
    }


    public function show_farmacia($id)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;
        //$estimacion = Estimacion::findOrFail($id); //estimacion_servicio
        //$petitorio_id=($estimacion->petitorio_id);
        $petitorio_id=($id);
        //$establecimiento_id=($estimacion->establecimiento_id);
        
        $estimaciones = DB::table('estimacions as A')
                    ->join('responsables as C', 'C.rol','A.consolidado')
                    ->where('A.establecimiento_id',$establecimiento_id)
                    ->where('C.establecimiento_id',$establecimiento_id)
                    ->where('A.petitorio_id',$petitorio_id)
                    ->where( function ( $query )
                    {
                        $query->orWhere('A.consolidado',3)
                            ->orWhere('A.consolidado',4);
                    })
                    ->distinct()
                    ->get();

        //dd($estimaciones);
        $descripcionproducto=($estimaciones->get(0)->descripcion);
        return view('site.responsable_farmacia_hospital.medicamentos.mostrar_datos')->with('estimaciones', $estimaciones)->with('descripcionproducto',$descripcionproducto);     
    }

    public function edit($id)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);
            
        $nivel=$establecimiento->nivel_id;

        $contact = Estimacion::findOrFail($id); //estimacion_servicio
        
        return $contact;        
    }
/****************************************************************************/
 //////////////////////////////////////////////////////////////////77
/*    public function activar_rubro($can_id, $establecimiento_id,$rubro_id )
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
*/
/*****************4******************/

/****************************************************************************/
    //////////////////////////////////////////////////////////////////77
    public function activar_rubro($can_id, $establecimiento_id,$servicio_id )
    {
        
        //Rubro del Responsable de Farmacia
        $rubro_id=Auth::user()->rol;
        //$rubro_id=Auth::user()->id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('farmacia_servicios.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('No se ha encontrado');
            return redirect(route('farmacia_servicios.index'));
        }
        //2/4/4
        $servicio = DB::table('can_servicio')
                ->where('can_servicio.can_id',$can_id)
                ->where('can_servicio.servicio_id',$servicio_id)
                ->where('can_servicio.establecimiento_id',$establecimiento_id)
                ->get();

            $cerrado_medicamento=$servicio->get(0)->medicamento_cerrado;

            $cerrado_dispositivo=$servicio->get(0)->dispositivo_cerrado;
        

        return view('site.responsable_farmacia_hospital.activar_servicio')
                                    ->with('can',$can)
                                    ->with('cerrado_medicamento',$cerrado_medicamento)
                                    ->with('cerrado_dispositivo',$cerrado_dispositivo)
                                    ->with('rubro_id',$rubro_id)
                                    ->with('servicio_id',$servicio_id)
                                    ->with('establecimiento_id',$establecimiento_id);

    }

    ///////////////////////////9////////////////////////////////////77
    public function update_petitorio_rubro(Request $request,$can_id,$establecimiento_id,$servicio_id)
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

        $servicio=DB::table('establecimiento_servicio')->where('servicio_id',$servicio_id)->where('establecimiento_id',$establecimiento_id);
        if (empty($servicio)) {
            Flash::error('No se ha encontrado');
            return redirect(route('estimacion.index'));
        }
        
        $model_actualizar = new Estimacion();

        if(!empty($request->cerrado_medicamento))
        {
            if($request->cerrado_medicamento==1):
                $cambio = $model_actualizar->ActivaServicio($can_id, $servicio_id, $establecimiento_id, 'medicamento_cerrado', 2);
            endif;
        }
        else{

            if($request->cerrado_medicamento!=3):
                $cambio = $model_actualizar->ActivaServicio($can_id, $servicio_id, $establecimiento_id, 'medicamento_cerrado', 1);
            endif;
        }
        
        if(!empty($request->cerrado_dispositivo))
        {
            if($request->cerrado_dispositivo==1):
                $cambio = $model_actualizar->ActivaServicio($can_id, $servicio_id, $establecimiento_id, 'dispositivo_cerrado', 2);
            endif;
        }
        else{
            
            if($request->cerrado_dispositivo!=3):
                $cambio = $model_actualizar->ActivaServicio($can_id, $servicio_id, $establecimiento_id, 'dispositivo_cerrado', 1);
            endif;
        }
        
        Flash::success('Actualizado satisfactoriamente.');
        return redirect(route('farmacia.listar_servicios',[$can_id]));
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
/*
    public function apiConsolidado($can_id,$establecimiento_id,$tipo)
    {

        //averiguamos el nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;

        
            if($tipo==1){
                $contact=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                $descripcion_tipo='Medicamentos';
            }
            else
            {   if ($tipo==2) {
                        $contact=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();                    
                        $descripcion_tipo='Dispositivos';
                }
                else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion_servicio.index'));  
                }
            }
        
        return Datatables::of($contact)
            ->addColumn('action', function($contact){
              return '<a onclick="editForm('. $contact->id .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></a>' ;                
            })
                ->rawColumns(['justificacion', 'action'])->make(true);    
        
    }
*/


    public function grabar($id,Request $request)
    {
       
        $estimacion = Estimacion::find($id); //servicios
        
        if (empty($estimacion)) {
            Flash::error('Estimación no encontrado');

            return redirect(route('estimacions.index'));
        }
        
        
        $necesidad_anual = $request->input("necesidad_anual");
        $stock = $request->input("stock");   
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
        $justificacion = $request->input("justificacion");

        
        
        DB::table('estimacions')
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
                    'necesidad_actual'=>3,
                    'justificacion' => $justificacion,
                    'updated_at'=>Carbon::now()
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
                
            return view('site.responsable_farmacia_hospital.medicamentos.medicamentos')
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
            return view('site.responsable_farmacia_hospital.medicamentos.medicamentos')
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
            return redirect(route('estimacion.index'));
        }

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('abastecimiento.show',$ici_id));
        }
            
        $nivel=$establecimiento->nivel_id;

        switch (Auth::user()->rol) {
            case 3: //PF
                    $tipo_cerrado='rubro_pf';
                    $tipo=1;
                break;
            case 4: //MBMQPA
                $tipo_cerrado='rubro_mb_iq_pa';
                $tipo=2;$tipo2=3;$tipo3=7;
                break;
            case 5: //MD
                $tipo_cerrado='rubro_mid';
                $tipo=4;
                break;
            case 6: //ML
                $tipo_cerrado='rubro_mil';
                $tipo=5;$tipo2=10;
                break;
            case 8: //MFF
                $tipo_cerrado='rubro_mff';
                $tipo=6;
                break;
            
        }
        
        //buscamos todos los medicamentos llenados
        if($tipo==1 || $tipo==4 || $tipo==6){
            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('cpma','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$tipo)        
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();     

            //eliminamos los registros anteriores                
            DB::table('consolidados')
                ->where('can_id',$can_id)
                ->where('tipo_dispositivo_id',$tipo)                
                ->where('establecimiento_id',$establecimiento_id)
                ->delete();
            /*if($tipo==1)
                $tipo_consolidado=3
            else
            */
        }
        else
        {
            if($tipo==5){
                $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('cpma','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',5)
                                          
                                          ->orWhere('tipo_dispositivo_id',10);

                                })
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();     

                //eliminamos los registros anteriores                
                DB::table('consolidados')
                    ->where('can_id',$can_id)
                    ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',5)
                    
                                          ->orWhere('tipo_dispositivo_id',10);

                                })
                    ->where('establecimiento_id',$establecimiento_id)
                    ->delete();
            }   
            else
            {
                $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('cpma','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',2)
                                          ->orWhere('tipo_dispositivo_id',3)
                                          ->orWhere('tipo_dispositivo_id',7);

                                })
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();     

                //eliminamos los registros anteriores                
                DB::table('consolidados')
                    ->where('can_id',$can_id)
                    ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',2)
                                          ->orWhere('tipo_dispositivo_id',3)
                                          ->orWhere('tipo_dispositivo_id',7);

                                })
                    
                    ->where('establecimiento_id',$establecimiento_id)
                    ->delete();
            }    
        }        
        
        
        //dd($data);
        $cpma_cero=0;

        if($cpma_cero==0){
            $nuevos_productos=$data->pluck('petitorio_id');
        
            foreach($nuevos_productos as $key => $producto){

                $num_estimacion_ingresado=DB::table('consolidados')
                    ->where('can_id',$can_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('petitorio_id',$producto)
                    ->count();

                if ($num_estimacion_ingresado==0){
                
                    $est_insert = DB::table('estimacions')
                                    ->select('petitorio_id', 
                                        DB::raw('SUM(stock) as stock'),
                                        DB::raw('SUM(necesidad_anual) as necesidad_anual'),
                                        DB::raw('SUM(cpma) as cpma'),
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
                                    ->where('petitorio_id',$producto)
                                    ->get(); 
                                    

                    $est_consult=DB::table('estimacions')
                                    ->select('petitorio_id',
                                        'can_id',
                                        'establecimiento_id',
                                        'cod_establecimiento',
                                        'nombre_establecimiento',
                                        'tipo_dispositivo_id',
                                        'petitorio_id',
                                        'cod_petitorio',
                                        'cod_siga',
                                        'uso_id',                    
                                        'descripcion')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->where('petitorio_id',$producto)
                                    ->get();
                    
                    DB::table('consolidados')
                        ->insert([
                            'can_id' => $est_consult->get(0)->can_id,
                            'cpma' => $est_insert->get(0)->cpma,
                            'establecimiento_id' => $est_consult->get(0)->establecimiento_id,
                            'cod_establecimiento' => $est_consult->get(0)->cod_establecimiento,
                            'nombre_establecimiento' => $est_consult->get(0)->nombre_establecimiento,
                            'tipo_dispositivo_id' => $est_consult->get(0)->tipo_dispositivo_id,
                            'petitorio_id' => $est_consult->get(0)->petitorio_id,
                            'cod_petitorio' => $est_consult->get(0)->cod_petitorio,
                            'cod_siga' => $est_consult->get(0)->cod_siga,
                            'descripcion' => $est_consult->get(0)->descripcion,
                            'necesidad_anual' => $est_insert->get(0)->necesidad_anual,
                            'stock' => $est_insert->get(0)->stock,
                            'mes1' => $est_insert->get(0)->mes1,
                            'mes2' => $est_insert->get(0)->mes2,
                            'mes3' => $est_insert->get(0)->mes3,
                            'mes4' => $est_insert->get(0)->mes4,
                            'mes5' => $est_insert->get(0)->mes5,
                            'mes6' => $est_insert->get(0)->mes6,
                            'mes7' => $est_insert->get(0)->mes7,
                            'mes8' => $est_insert->get(0)->mes8,
                            'mes9' => $est_insert->get(0)->mes9,
                            'mes10' => $est_insert->get(0)->mes10,
                            'mes11' => $est_insert->get(0)->mes11,
                            'mes12' => $est_insert->get(0)->mes12,
                            'created_at'=>Carbon::now(),
                            'uso_id'=>$est_consult->get(0)->uso_id,
                    ]);
                }
            }
        }
        else
        {
            Flash::error('CPMA con valores Cero, por favor editar dichos productos.');
            return redirect(route('farmacia_servicios.ver_farmacia_servicios',[$tipo,$can_id]));
        }
        
        $servicio_id=Auth::user()->servicio_id;

            //actualizamos estado 
                DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            $tipo_cerrado => 2,
                            'updated_at'=>Carbon::now()
                ]);

                $modifica_cerrado=DB::table('can_establecimiento')
                                ->where('can_id', $can_id)
                                ->where('establecimiento_id', $establecimiento_id)
                                ->get();
                
                if($modifica_cerrado->get(0)->rubro_pf==2){
                        DB::table('can_establecimiento')
                        ->where('can_id', $can_id)
                        ->where('establecimiento_id', $establecimiento_id)
                        ->update([
                                'medicamento_cerrado' => 2,
                                'updated_at'=>Carbon::now()
                    ]);                    
                }
                
                if($modifica_cerrado->get(0)->rubro_mb_iq_pa==2 and $modifica_cerrado->get(0)->rubro_mid==2 and $modifica_cerrado->get(0)->rubro_mil==2 and $modifica_cerrado->get(0)->rubro_mff==2){
                    DB::table('can_establecimiento')
                    ->where('can_id', $can_id)
                    ->where('establecimiento_id', $establecimiento_id)
                    ->update([
                            'dispositivo_cerrado' => 2,
                            'updated_at'=>Carbon::now()
                    ]);                    
                }
        
        Flash::success('Petitorio Cerrado.');
    

        return redirect(route('farmacia_servicios.ver_consolidado_farmacia_servicios',[$tipo,$can_id]));

    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function establecimientos_servicio_can($id,$tipo)
    {
        
        $can=Can::find($id);
        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        
        if(Auth::user()->rol==3){
            $consolidado=3; //almacen
        }
        else
        {
            $consolidado=4; //farmacia   
        }

        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento=Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento No se ha encontrado');
            return redirect(route('abastecimiento.show',$ici_id));
        }

        $nivel=$establecimiento->nivel_id;

        for($i=0;$i<3205;$i++){
            for($j=0;$j<250;$j++){ //servicios * 5
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
                ->where('can_servicio.can_id',$id)
                ->where('establecimientos.id',$establecimiento_id)
                ->where('estimacion_servicio.tipo_dispositivo_id',1)
                ->where('estimacion_servicio.consolidado',$consolidado)
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
                ->where('can_servicio.can_id',$id)
                ->where('establecimientos.id',$establecimiento_id)
                ->where('estimacion_servicio.tipo_dispositivo_id','>',1)
                ->where('estimacion_servicio.consolidado',$consolidado)
                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio')
                ->orderby('estimacion_servicio.petitorio_id')
                ->count();
        }
        
        if($contar_nivel>0){

            if($tipo==1){
                        
                if($consolidado==4){
                    $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,servicios.codigo'))
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
                    ->where('can_servicio.can_id',$id)
                    ->where('establecimientos.id',$establecimiento_id)
                    ->where('estimacion_servicio.tipo_dispositivo_id',1)
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where( function ( $query )
                                        {
                                            $query->orWhere('estimacion_servicio.uso_id',2)
                                                ->orWhere('estimacion_servicio.uso_id',5)
                                                ->orWhere('estimacion_servicio.uso_id',7);

                                        })
                    ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','servicios.codigo')
                    ->orderby('estimacion_servicio.petitorio_id','asc')
                    ->get();
                }
                else {
                    $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,servicios.codigo'))
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
                    ->where('can_servicio.can_id',$id)
                    ->where('establecimientos.id',$establecimiento_id)
                    ->where('estimacion_servicio.tipo_dispositivo_id',1)
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where( function ( $query )
                                        {
                                            $query->orWhere('estimacion_servicio.uso_id','<>',5)
                                                  ->Where('estimacion_servicio.uso_id','<>',7);

                                        })
                    ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','servicios.codigo')
                    ->orderby('estimacion_servicio.petitorio_id','asc')
                    ->get();
                }
            }
            else
            {
                if($consolidado==4){
                     $consulta = DB::table('estimacion_servicio')
                        //->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id'))
                        ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,servicios.codigo'))
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
                        ->where('can_servicio.can_id',$id)
                        ->where('establecimientos.id',$establecimiento_id)
                        ->where('estimacion_servicio.tipo_dispositivo_id','>',1)
                        ->where('estimacion_servicio.necesidad_anual','>',0)
              //          ->where('estimacion_servicio.petitorio_id',3183)
                        ->where( function ( $query )
                            {
                                $query->orWhere('estimacion_servicio.uso_id',2)
                                      ->orWhere('estimacion_servicio.uso_id',5);

                            })
                        //->where('estimacion_servicio.uso_id',2)
                        //->groupby('estimacion_servicio.petitorio_id')
                        ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','servicios.codigo')
                        ->orderby('estimacion_servicio.petitorio_id','asc')
                        //->orderby('estimacion_servicio.servicio_id','asc')
                        ->get();   
                }
                else
                {
                    $consulta = DB::table('estimacion_servicio')
                        //->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id'))
                        ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,servicios.codigo'))
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
                        ->where('can_servicio.can_id',$id)
                        ->where('establecimientos.id',$establecimiento_id)
                        ->where('estimacion_servicio.tipo_dispositivo_id','>',1)
                        ->where('estimacion_servicio.uso_id','<>',5)
                        ->where('estimacion_servicio.necesidad_anual','>',0)
                        //->where('estimacion_servicio.consolidado',$consolidado)
                        //->where('estimacion_servicio.uso_id',2)
                        //->groupby('estimacion_servicio.petitorio_id')
                        ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','servicios.codigo')
                        ->orderby('estimacion_servicio.petitorio_id','asc')
                        //->orderby('estimacion_servicio.servicio_id','asc')
                        ->get();   
                }
            }
            //dd($consulta);
            $servicios_x = DB::table('servicios')
                                ->join('can_servicio','servicios.id','can_servicio.servicio_id')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->orderby('servicios.id','asc')
                                ->get();

            $cont_servicios_x = DB::table('servicios')
                                ->join('can_servicio','servicios.id','can_servicio.servicio_id')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->orderby('servicios.id','asc')
                                ->count();

            $i=0;

            foreach ($servicios_x as $key => $value) {
                $descripcion[$i][0]=$value->servicio_id;   
                $descripcion[$i][1]=$value->nombre_servicio;   
                $i++;
            }

            $fila_anterior=5000; $x=-1; $y=0; $z=0;

            foreach ($consulta as $key => $value) {
                # code...
                $fila=$value->petitorio_id;
                
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }

                for($k=0;$k<$i;$k++){
                    if($value->servicio_id==$descripcion[$k][0]){
                        $can_productos[$x][$k]=$value->necesidad;
                        $can_productos[$x][250]=$value->descripcion;
                        $can_productos[$x][249]=$can_productos[$x][249]+$can_productos[$x][$k];
                    }
                }
                $y++;
            }
            $x++;
        }

        return view('admin.cans.servicio_show')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('y', $cont_servicios_x)
                                      ->with('descripcion', $descripcion);
    }
/////////////////////DESCARGA POR IPRESS ///////////////////////////////////////////////////////////////////
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
        return view('site.responsable_farmacia_hospital.medicamentos.medicamentos')
            ->with('estimacions', $data)
            ->with('servicio_id', $servicio_id)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }
//////////////////////////////////////////////////////////////////////////////////////////
    public function ver_farmacia_servicios($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
        $servicio_id=Auth::user()->servicio_id;

        $nivel=$establecimiento->nivel_id;
        $sum=$nivel+1;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }


        if($tipo==3 || $tipo==5 || $tipo==8){ //tipo==rol usuario
            
            switch ($tipo) {
                case 3: $compara=1;break;
                case 5: $compara=4;break;
                case 8: $compara=6;break;
            }
            
            $establecimiento_cerrado=DB::table('can_establecimiento')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->get();  

            $medicamento_cerrado=$establecimiento_cerrado->get(0)->medicamento_cerrado;

            
            $numero_medicamentos=DB::table('estimacion_servicio')
            //$numero_medicamentos=DB::table('estimacions')
                            ->where('can_id',$can_id)
                            ->where('estado','<>',2)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_id',$compara)
                            //->groupby('estimacion_servicio.petitorio_id')
                            ->count(); 

            
        }else
            
        {   if ($tipo==4) {

                    $establecimiento_cerrado=DB::table('can_establecimiento')
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->get();  

                    $medicamento_cerrado=$establecimiento_cerrado->get(0)->dispositivo_cerrado;

                    
                    $numero_medicamentos=DB::table('estimacions')
                                    ->where('can_id',$can_id)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('estado','<>',2)
                                    ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_dispositivo_id',2)
                                                  ->orWhere('tipo_dispositivo_id',3)
                                                  ->orWhere('tipo_dispositivo_id',7);

                                        })
                                    ->count(); 

            }
            else
            {
                    $establecimiento_cerrado=DB::table('can_establecimiento')
                            ->where('can_id',$can_id) //cambiar 22
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->get();  

                    $medicamento_cerrado=$establecimiento_cerrado->get(0)->dispositivo_cerrado;

                    
                    $numero_medicamentos=DB::table('estimacions')
                                    ->where('can_id',$can_id)
                                    ->where('estado','<>',2)
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_dispositivo_id',5)     
                                                    ->orWhere('tipo_dispositivo_id',10);

                                        })
                                    ->count(); 

            }   
            
        
        }

        if($tipo==3)
            $cerrado_medicamento=DB::table('can_servicio')->where('medicamento_cerrado',1)->where('establecimiento_id',$establecimiento_id)->count();
        else
            $cerrado_medicamento=DB::table('can_servicio')->where('dispositivo_cerrado',1)->where('establecimiento_id',$establecimiento_id)->count();

        $cans=DB::table('cans')->orderBy('id', 'desc')->first();
        $can = Can::find($cans->id);
        $can_id_ultimo=$cans->id;
        
        return view('site.responsable_farmacia_hospital.medicamentos.medicamentos')
            ->with('cerrado_medicamento', $cerrado_medicamento)            
            ->with('num_estimaciones', $numero_medicamentos)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('cans', $can)
            ->with('can_id', $can_id_ultimo);
    }
//////////////////////////////////////////////////////////////////////////////////
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
                    $consolidado=Auth::user()->rol;
                    //buscamos el establecimiento
                    $establecimiento = Establecimiento::find($establecimiento_id);
                    //si encuentra o no el establecimiento
                    if (empty($establecimiento)) {
                        Flash::error('Establecimientos ICI con esas caracteristicas');
                        return redirect(route('estimacion.index'));
                    }
                    
                    $nivel_sum=$establecimiento->nivel_id+1;

                    if($tipo_producto==1){ //// 1 si es medicamento
                        
                        if($consolidado==3){
                            //Buscamos todos los medicamentos segun el nivel
                            $consulta_petitorio = DB::table('petitorios')
                                ->where('tipo_dispositivo_medicos_id',1)
                                ->where('nivel_id','<',$nivel_sum)
                                ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_uso_id','<>',5)
                                            ->orWhere('tipo_uso_id','<>',7);
                                    })
                                ->get();    
                        }
                        else
                        {
                            if($establecimiento->nivel_id==3){
                                //Buscamos todos los medicamentos segun el nivel
                                $consulta_petitorio = DB::table('petitorios')
                                    ->where('tipo_dispositivo_medicos_id',1)
                                    ->where('nivel_id','<',$nivel_sum)
                                    ->get();    
                            }
                            else
                            {
                                //Buscamos todos los medicamentos segun el nivel
                                $consulta_petitorio = DB::table('petitorios')
                                    ->where('tipo_dispositivo_medicos_id',1)
                                    ->where('nivel_id','<',$nivel_sum)
                                    ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_uso_id',5)
                                                ->orWhere('tipo_uso_id',7)
                                                ->orWhere('tipo_uso_id',2);
                                        })
                                    ->get();       
                            }
                            
                        }
                        
                        
                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                        
                        //Buscamos los medicamentos segun el nivel 
                        $consulta_medicamentos_nivel = DB::table('estimacions')
                            ->where('tipo_dispositivo_id',1)
                            ->where('can_id',$can_id)
                            ->where('consolidado',$consolidado)
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
                                ->where('nivel_id','<',$nivel_sum)
                                ->get();

                        if($consolidado==3){
                            //Buscamos todos los medicamentos segun el nivel
                            $consulta_petitorio = DB::table('petitorios')
                                ->where('tipo_dispositivo_medicos_id','>',1)
                                ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_uso_id','<>',5)
                                            ->orWhere('tipo_uso_id','<>',7);
                                    })
                                ->where('nivel_id','<',$nivel_sum)
                                ->get();    
                        }
                        else
                        {
                            if($establecimiento->nivel_id==3){
                                //Buscamos todos los medicamentos segun el nivel
                                $consulta_petitorio = DB::table('petitorios')
                                    ->where('tipo_dispositivo_medicos_id','>',1)
                                    ->where('nivel_id','<',$nivel_sum)
                                    ->get();
                            }
                            else
                            {
                                //Buscamos todos los medicamentos segun el nivel
                                $consulta_petitorio = DB::table('petitorios')
                                    ->where('tipo_dispositivo_medicos_id','>',1)
                                    ->where('nivel_id','<',$nivel_sum)
                                    ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_uso_id',5)
                                                ->orWhere('tipo_uso_id',7)
                                                ->orWhere('tipo_uso_id',2);
                                        })
                                    ->get();
                            }
                        }


                        //pasamos a un arreglo
                        $petitorio = $consulta_petitorio->pluck('descripcion','id')->toArray();
                        
                        //Buscamos los medicamentos segun el nivel 
                        $consulta_medicamentos_nivel = DB::table('estimacions')
                            ->where('tipo_dispositivo_id','>',1)
                            ->where('can_id',$can_id)
                            ->where('consolidado',$consolidado)
                            ->where('establecimiento_id',$establecimiento_id)
                            ->orderby('descripcion','asc');                            

                        //pasamos a un arreglo
                        $consulta_medicamento = $consulta_medicamentos_nivel->pluck('descripcion','petitorio_id')->toArray();

                        //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                        $descripcion=array_diff($petitorio,$consulta_medicamento);
                        
                    }

                    //Enviamos al formulario
                    return view('site.responsable_farmacia_hospital.nuevo.index')
                            ->with('establecimiento_id', $establecimiento_id)
                            ->with('can_id', $can_id)
                            ->with('destino', $tipo_producto)
                            ->with('descripcion', $descripcion);
                }
                else
                {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('farmacia_servicios.index'));
                }
            }
            else
            {
                    Flash::error('No se tiene un CAN con esas caracteristicas');
                    return redirect(route('farmacia_servicios.index'));
            }
        }
        else
        {
                Flash::error('No se tiene un CAN con esas caracteristicas');
                return redirect(route('farmacia_servicios.index'));
        }
    }

    public function grabar_nuevo_medicamento_dispositivo(Request $request,$establecimiento_id,$can_id, $destino)
    {

        if (Auth::user()->establecimiento_id == $establecimiento_id ){

            //id del servicio o distribuidor
            $servicio_id=Auth::user()->servicio_id;

            //
            $consolidado=Auth::user()->rol;

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
                        'descripcion'=>$descripcion,
                        'tipo_dispositivo_id'=>$tipo_dispositivo_id,
                        'cpma' => $cpma,
                        'stock' => $stock,
                        'necesidad_anual' => $necesidad_anual,
                        'consolidado' => $consolidado,
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
                        'estado_necesidad'=>1,
                        'justificacion' => $justificacion,
                        'created_at'=>Carbon::now(),
            ]);

        
            if($necesidad_anual<0){
                Flash::error('No se ha podido guardar el medicamento, la suma total de ingreso es menor a la suma total de salida');
            }
            else
            {
                Flash::success('Se ha guardado con exito');
            }

            return redirect(route('farmacia_servicios.ver_farmacia_servicios',[$destino,$can_id]));
        }   
        
    }
///////////////////////////////////////////////////////////////////////////////////

public function ver_consolidado_ipress($tipo,$can_id)
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
                    ->select(DB::raw('sum(cpma) as cpma,sum(stock) as stock,sum(necesidad_anual) as necesidad_anual,sum(mes1) as mes1,sum(mes2) as mes2, sum(mes3) as mes3, sum(mes4) as mes4, sum(mes5) as mes5, sum(mes6) as mes6, sum(mes7) as mes7, sum(mes8) as mes8, sum(mes9) as mes9, sum(mes10) as mes10, sum(mes11) as mes11, sum(mes12) as mes12,descripcion,petitorio_id, tipo_dispositivo_id'))
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('descripcion','petitorio_id','tipo_dispositivo_id')
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
                    ->select(DB::raw('sum(cpma) as cpma,sum(stock) as stock,sum(necesidad_anual) as necesidad_anual,sum(mes1) as mes1,sum(mes2) as mes2, sum(mes3) as mes3, sum(mes4) as mes4, sum(mes5) as mes5, sum(mes6) as mes6, sum(mes7) as mes7, sum(mes8) as mes8, sum(mes9) as mes9, sum(mes10) as mes10, sum(mes11) as mes11, sum(mes12) as mes12,descripcion,petitorio_id, tipo_dispositivo_id'))
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('descripcion','petitorio_id','tipo_dispositivo_id')
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
        //dd($data);
        $condicion_boton=2; //ver
        return view('site.responsable_farmacia_hospital.medicamentos.ver_productos_consolidados')
            ->with('condicion_boton', $condicion_boton)
            ->with('estimacions', $data)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('servicio_id', $servicio_id)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('can_id', $can_id);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function ver_consolidado_farmacia_servicios($tipo,$can_id)
    {       
        
        $establecimiento_id=Auth::user()->establecimiento_id;

        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
        //$servicio_id=Auth::user()->servicio_id;
        $servicio_id=0;

        $nivel=$establecimiento->nivel_id;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrada');
            return redirect(route('estimacion.index'));
        }

        $tipo=Auth::user()->rol;
        
        if($tipo==3 || $tipo==5 || $tipo==8){ //tipo==rol usuario
            switch ($tipo) {
                case 3: $compara=1;break;
                case 5: $compara=4;break;
                case 8: $compara=6;break;
            }

            /********** SI VALE **************
            $data=DB::table('consolidados')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$compara)           
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

            $num_estimaciones=DB::table('consolidados')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$compara)                      
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();
            */

            $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$compara)           
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

            $num_estimaciones=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('estado','<>',2)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$compara)                      
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();

            if($tipo==3)
                $descripcion_tipo='Medicamentos';
            else
                $descripcion_tipo='Dispositivos';


        }else
            
        {   if ($tipo==4) {

                /*$data=DB::table('consolidados')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id',2)
                                  ->orWhere('tipo_dispositivo_id',3)
                                  ->orWhere('tipo_dispositivo_id',7);

                        })          
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('consolidados')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',2)
                                          ->orWhere('tipo_dispositivo_id',3)
                                          ->orWhere('tipo_dispositivo_id',7);

                                })                      
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();                    
                */

                $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('estado','<>',2)
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id',2)
                                  ->orWhere('tipo_dispositivo_id',3)
                                  ->orWhere('tipo_dispositivo_id',7);

                        })          
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',2)
                                          ->orWhere('tipo_dispositivo_id',3)
                                          ->orWhere('tipo_dispositivo_id',7);

                                })                      
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();                    

                


                    $descripcion_tipo='Dispositivos';         

            }
            else
            {
                    /*
                    $data=DB::table('consolidados')
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id',5)
                                  ->orWhere('tipo_dispositivo_id',10);

                        })          
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('consolidados')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',5)
                                          ->orWhere('tipo_dispositivo_id',10);

                                })                      
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();                    
                    */

                    $data=DB::table('estimacions')
                    ->where('necesidad_anual','>',0)
                    ->where('estado','<>',2)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_id',5)
                                  ->orWhere('tipo_dispositivo_id',10);

                        })          
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();                    

                    $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('estado','<>',2)
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',5)
                                          ->orWhere('tipo_dispositivo_id',10);

                                })                      
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $descripcion_tipo='Dispositivos';         
            }   
            
        
        }

        $can=DB::table('cans')
                    ->where('id',$can_id)
                    ->get();
                        

        
                
        $nombre_servicio="CONSOLIDADO";
        //dd($num_estimaciones);
        $condicion_boton=2; //ver
        return view('site.responsable_farmacia_hospital.medicamentos.descargar_medicamentos')
            ->with('condicion_boton', $condicion_boton)
            ->with('estimacions', $data)
            ->with('nivel', $nivel)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('servicio_id', $servicio_id)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('tipo', $tipo)
            ->with('cans', $can->get(0))
            ->with('can_id', $can_id);
    }
/////////////////////DESCARGA POR SERVICIO - DISTRIBUCION///////////////////////////////////////////////////////
    public function descargar_estimacion_farmacia_servicios($tipo,$can_id,$establecimiento_id,$servicio_id,$id_user)
    {       
        
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }
        
        $nivel=$establecimiento->nivel_id;
        $opt=2;
        $can=DB::table('cans')
                    ->where('id',$can_id)
                    ->orderby('id', 'desc')
                    ->first();

        if($nivel>1){
            $table='estimacion_servicio';
            $condicion1='servicio_id';
        }    

        if($tipo==3 || $tipo==5 || $tipo==8){ //tipo==rol usuario
            switch ($tipo) {
                case 3: $compara=1;break;
                case 5: $compara=4;break;
                case 8: $compara=6;break;
            }
            $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$compara)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

            $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('estado','<>',2)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',$compara)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();
            
            if($tipo==3):
                $descripcion_tipo='Medicamentos';
                $opt=1;
            else:
                $opt=2;
                $descripcion_tipo='Dispositivos';
            endif;
        
        }
        else
        {   if ($tipo==4) {

                    $data=DB::table($table)
                        ->where('necesidad_anual','>',0)
                        ->where($condicion1,$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('estado','<>',2)
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',2)
                                          ->orWhere('tipo_dispositivo_id',3)
                                          ->orWhere('tipo_dispositivo_id',7);

                                })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->get();                    

                    $num_estimaciones=DB::table($table)
                        ->where('necesidad_anual','>',0)
                        ->where($condicion1,$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('estado','<>',2)
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',2)
                                          ->orWhere('tipo_dispositivo_id',3)
                                          ->orWhere('tipo_dispositivo_id',7);

                                })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();                    

                    $descripcion_tipo='Dispositivos';         

            }
            else
            {
                    $data=DB::table($table)
                        ->where('necesidad_anual','>',0)
                        ->where($condicion1,$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('estado','<>',2)
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',5)
                                          ->orWhere('tipo_dispositivo_id',10);

                                })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->get();                    

                    $num_estimaciones=DB::table($table)
                        ->where('necesidad_anual','>',0)
                        ->where($condicion1,$servicio_id)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where( function ( $query )
                                {
                                    $query->orWhere('tipo_dispositivo_id',5)     
                                          ->orWhere('tipo_dispositivo_id',10);

                                })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                    $descripcion_tipo='Dispositivos';         
            }   
            
        }
        
        $condicion_boton=1; //no ver
        
        if($num_estimaciones>0){
            $nombre_servicio=$data->get(0)->nombre_servicio;
            
        }
        else
        {
            $servicios=DB::table('servicios')
                        ->where('id',$servicio_id)
                        ->get();
            $nombre_servicio=$servicios->get(0)->nombre_servicio;
        }

        return view('site.responsable_farmacia_hospital.medicamentos.descargar_medicamentos_servicios')
            ->with('condicion_boton', $condicion_boton)
            ->with('estimacions', $data)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('tipo', $tipo)
            ->with('id_user', $id_user)
            ->with('opt', $opt)
            ->with('cans', $can)
            ->with('nivel', $nivel)
            ->with('can_id', $can_id);
    }

    public function descargar_estimacion_farmacia_servicios_ver($tipo,$can_id,$establecimiento_id,$servicio_id)
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
            $table='estimacion_servicio';
            $condicion1='servicio_id';
        }    

        if($tipo==1){ //tipo==rol usuario
            $data=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id', 1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get();

            $num_estimaciones=DB::table($table)
                    ->where('necesidad_anual','>',0)
                    ->where($condicion1,$servicio_id)
                    ->where('estado','<>',2)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->count();
            
            
            $descripcion_tipo='Medicamentos';
            
        }
        else
        {  
            $data=DB::table($table)
                ->where('necesidad_anual','>',0)
                ->where($condicion1,$servicio_id)
                ->where('can_id',$can_id) //cambiar 22
                ->where('estado','<>',2)
                ->where('tipo_dispositivo_id', '>', 1)
                ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                ->orderby ('tipo_dispositivo_id','asc')  
                ->get();                    

            $num_estimaciones=DB::table($table)
                ->where('necesidad_anual','>',0)
                ->where($condicion1,$servicio_id)
                ->where('can_id',$can_id) //cambiar 22
                ->where('estado','<>',2)
                ->where('tipo_dispositivo_id', '>', 1)
                ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                ->count();                    

            $descripcion_tipo='Dispositivos';  
        }
        
        $condicion_boton=1; //no ver
        
        if($num_estimaciones>0){
            $nombre_servicio=$data->get(0)->nombre_servicio;
            
        }
        else
        {
            $servicios=DB::table('servicios')
                        ->where('id',$servicio_id)
                        ->get();
            $nombre_servicio=$servicios->get(0)->nombre_servicio;
        }

        return view('site.responsable_farmacia_hospital.medicamentos.descargar_medicamentos_ver')
            ->with('condicion_boton', $condicion_boton)
            ->with('estimacions', $data)
            ->with('num_estimaciones', $num_estimaciones)
            ->with('descripcion_tipo', $descripcion_tipo)
            ->with('nombre_servicio', $nombre_servicio)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('servicio_id', $servicio_id)
            ->with('tipo', $tipo)
            ->with('nivel', $nivel)
            ->with('can_id', $can_id);
    }
    public function cargar_datos_consolidado($can_id, $establecimiento_id, $tipo)
    {
        //averiguamos el nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;


            if($tipo==3 || $tipo==5 || $tipo==8){ //tipo==rol usuario
                switch ($tipo) {
                    case 3: $compara=1;break;
                    case 5: $compara=4;break;
                    case 8: $compara=6;break;
                }
               /* $contact=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$compara)   
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                        */

                $contact=DB::table('estimacion_servicio')
                    ->select('petitorio_id', 'descripcion',
                                        DB::raw('SUM(stock) as stock'),
                                        DB::raw('SUM(necesidad_anual) as necesidad_anual'),
                                        DB::raw('SUM(necesidad_actual) as necesidad_actual'),
                                        DB::raw('SUM(cpma) as cpma'),
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
                                    ->groupby('petitorio_id','descripcion')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->where('estado','<>',2)
                                    ->where('tipo_dispositivo_id',$compara)
                                    ->where('necesidad_anual','>',0)                                            
                                    ->orderby('descripcion','asc')//cambiar desc
                                    ->get(); 

                if($tipo==3)
                    $descripcion_tipo='Medicamentos';
                else
                    $descripcion_tipo='Dispositivos';


            }
            else
            {   if ($tipo==4) {
                        
                        $contact=DB::table('estimacion_servicio')
                            ->select('petitorio_id', 'descripcion',
                                        DB::raw('SUM(stock) as stock'),
                                        DB::raw('SUM(necesidad_anual) as necesidad_anual'),
                                        DB::raw('SUM(necesidad_actual) as necesidad_actual'),
                                        DB::raw('SUM(cpma) as cpma'),
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
                                    ->groupby('petitorio_id','descripcion')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->where('estado','<>',2)
                                    ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_dispositivo_id',2)
                                                  ->orWhere('tipo_dispositivo_id',3)
                                                  ->orWhere('tipo_dispositivo_id',7);

                                        })                      
                                    ->where('necesidad_anual','>',0)                                            
                                    ->orderby('descripcion','asc')//cambiar desc
                                    ->get();               
                        $descripcion_tipo='Dispositivos';
                }
                else
                {
  
                    $contact=DB::table('estimacion_servicio')
                    ->select('petitorio_id', 'descripcion',
                                        DB::raw('SUM(stock) as stock'),
                                        DB::raw('SUM(necesidad_anual) as necesidad_anual'),
                                        DB::raw('SUM(necesidad_actual) as necesidad_actual'),
                                        DB::raw('SUM(cpma) as cpma'),
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
                                    ->groupby('petitorio_id','descripcion')
                                    ->where('establecimiento_id',$establecimiento_id)
                                    ->where('can_id',$can_id)
                                    ->where('estado','<>',2)
                                    ->where( function ( $query )
                                        {
                                            $query->orWhere('tipo_dispositivo_id',5)     
                                          ->orWhere('tipo_dispositivo_id',10);

                                        })                      
                                    ->where('necesidad_anual','>',0)                                            
                                    ->orderby('descripcion','asc')//cambiar desc
                                    ->get();               

                    $descripcion_tipo='Dispositivos';
                }
            }
  
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
        $rol=Auth::user()->rol;
        $nombre_servicio=Auth::user()->nombre_servicio;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        
        /**************************/
        switch ($rol) {
            case 3: //PF
                    $tipo=1;$mtipo=1;
                break;
            case 4: //MBMQPA
                $tipo=2;$tipo2=3;$tipo3=7;;$mtipo=2;
                break;
            case 5: //MD
                $tipo=4;$mtipo=2;
                break;
            case 6: //ML
                $tipo=5;$mtipo=2;
                break;
            case 8: //MFF
                $tipo=6;$mtipo=2;
                break;
            
        }
        
        /*if($servicio_id!=0){ */
            if($tipo==1 || $tipo==4 || $tipo==6){
                /*
                $data=DB::table('consolidados')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)   
                        ->where('estado','<>',2)                     
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                */

                $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)   
                        ->where('estado','<>',2)                     
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();

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
                    /*
                    $data=DB::table('consolidados')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)
                        ->where('can_id',$can_id) //cambiar 22                        
                        ->where('estado','<>',2)
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

                    $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)
                        ->where('can_id',$can_id) //cambiar 22                        
                        ->where('estado','<>',2)
                        ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',5)
                                              
                                              ->orWhere('tipo_dispositivo_id',10);

                                    })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();     

                    $descripcion_tipo='Material e insumos de Laboratorio';
                }   
                else
                {
                    /*
                    $data=DB::table('consolidados')
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

                    $data=DB::table('estimacions')
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

                    $descripcion_tipo='Material Biomedico, Instrumental Quirurgico y Productos afines';
                }    
            }

                        
            $num_estimaciones=count($data);
        
            
        
        /*************************/
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

    public function exportEstimacionDataConsolidadaPrevio($can_id,$establecimiento_id,$opt,$type)
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

        if(Auth::user()->rol==3){
            $consolidado=3; //almacen
        }
        else
        {
            $consolidado=4; //farmacia   
        }

        if($opt==1){
            /*$data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();*/
            $data=DB::table('estimacions')
                    ->select(DB::raw('sum(cpma) as cpma,sum(stock) as stock,sum(necesidad_anual) as necesidad_anual,sum(mes1) as mes1,sum(mes2) as mes2, sum(mes3) as mes3, sum(mes4) as mes4, sum(mes5) as mes5, sum(mes6) as mes6, sum(mes7) as mes7, sum(mes8) as mes8, sum(mes9) as mes9, sum(mes10) as mes10, sum(mes11) as mes11, sum(mes12) as mes12,descripcion,petitorio_id, tipo_dispositivo_id, id, cod_petitorio'))
                    ->where('necesidad_anual','>',0)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('estado','<>',2)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('descripcion','petitorio_id','tipo_dispositivo_id','id','cod_petitorio')
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby ('descripcion','asc')  
                    ->get();                    

            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    /*$data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();*/

                    $data=DB::table('estimacions')
                    ->select(DB::raw('sum(cpma) as cpma,sum(stock) as stock,sum(necesidad_anual) as necesidad_anual,sum(mes1) as mes1,sum(mes2) as mes2, sum(mes3) as mes3, sum(mes4) as mes4, sum(mes5) as mes5, sum(mes6) as mes6, sum(mes7) as mes7, sum(mes8) as mes8, sum(mes9) as mes9, sum(mes10) as mes10, sum(mes11) as mes11, sum(mes12) as mes12,descripcion,petitorio_id, tipo_dispositivo_id, id, cod_petitorio'))
                    ->where('necesidad_anual','>',0)
                    ->where('estado','<>',2)
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->groupby('descripcion','petitorio_id','tipo_dispositivo_id','id','cod_petitorio')
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
                

            });
        })->download($type);
    }

    public function pdf_estimacion_nivel2y3($can_id,$establecimiento_id,$rol,$servicio_id)
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

        switch ($rol) {
            case 3: //PF
                    $tipo=1;$mtipo=1;
                break;
            case 4: //MBMQPA
                $tipo=2;$tipo2=3;$tipo3=7;;$mtipo=2;
                break;
            case 5: //MD
                $tipo=4;$mtipo=2;
                break;
            case 6: //ML
                $tipo=5;$mtipo=2;
                break;
            case 8: //MFF
                $tipo=6;$mtipo=2;
                break;
            
        }

        if($servicio_id!=0){
            if($tipo==1 || $tipo==4 || $tipo==6){
                $data=DB::table('estimacion_servicio')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)
                        ->where('servicio_id',$servicio_id)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->get();


                if($tipo==1)
                    $descripcion_tipo='Medicamentos';
                else
                    $descripcion_tipo='Dispositivos';
            }
            else
            {
                if($tipo==5){
                    $data=DB::table('estimacion_servicio')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('servicio_id',$servicio_id)
                        ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',5)
                                              
                                              ->orWhere('tipo_dispositivo_id',10);

                                    })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->get();     

                    $descripcion_tipo='Dispositivos';
                }   
                else
                {
                    $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('cpma','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('servicio_id',$servicio_id)
                        ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',2)
                                              ->orWhere('tipo_dispositivo_id',3)
                                              ->orWhere('tipo_dispositivo_id',7);

                                    })
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby('descripcion','asc')//cambiar desc
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->get();

                    $descripcion_tipo='Dispositivos';
                }    
            }

            $num_estimaciones=count($data);
        
            $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('servicio_id',$servicio_id)
                ->where('rol',2)
                ->get();

            if(count($usuario)==0){
                $name="";                
                $nombre_rubro="";
                $user_id="";
                $cip="";
                $dni="";
            }
            else
            {
                $name=$usuario->get(0)->name;
                $servicio_id=$usuario->get(0)->servicio_id;
                $nombre_rubro=$usuario->get(0)->nombre_servicio;
                $user_id=$usuario->get(0)->id;
                $cip=$usuario->get(0)->cip;
                $dni=$usuario->get(0)->dni;    
            }
            

            $cierre_servicio=DB::table('can_servicio')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->where('servicio_id',$servicio_id)->get();
            $cierre=$cierre_servicio->get(0)->updated_at;

            $texto='SERVICIO';

            $pdf = \PDF::loadView('site.pdf.descargar_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]);

            $pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');
        }
        else
        {

            if($mtipo==1){
                $data=DB::table('consolidados')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc') 
                        ->orderby ('descripcion','asc')   
                        ->get();

                $descripcion_tipo='Medicamentos';
            }else
                {   if ($mtipo==2) {
                        $data=DB::table('consolidados')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>',1)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();                    

                        $descripcion_tipo='Dispositivos';
                    }else
                    {
                        Flash::error('Datos no son correctos, error al descargar archivo');
                        return redirect(route('estimacion.index'));  
                    }
            }
            $num_estimaciones=count($data);

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
        

            $name=Auth::user()->name;
            $servicio_id=Auth::user()->servicio_id;
            $user_id=Auth::user()->id;
            $cip=Auth::user()->cip;
            $dni=Auth::user()->dni;

            $responsables=DB::table('users')
                    ->where('establecimiento_id',$establecimiento_id)      
                    ->where('estado',1)
                    ->get();        
            //dd($responsables);
            $responsable[]="";
            foreach ($responsables as $key => $resp) {
                switch ($resp->rol) {
                    case 4:
                        $responsable[0]=$resp->name;
                        break;

                    case 5:
                        $responsable[1]=$resp->name;
                        break;

                    case 7:
                        $responsable[2]=$resp->name;
                        break;
                }
            }

            //dd($responsable);
            $responsables_rubros[]="";
            $responsables_rubros=DB::table('users')
                    ->where('establecimiento_id',$establecimiento_id)      
                    ->where('estado',1)   
                    ->where('rol',2)          
                    ->orderby('rol','desc')
                    ->get();        


            foreach ($responsables_rubros as $key => $resp_rubro) {
                switch ($resp_rubro->servicio_id) {
                    case 1: //productos
                        $responsable_rubro[0]=$resp_rubro->name;
                        break;
                    case 2: //insumos laboratorio
                        $responsable_rubro[1]=$resp_rubro->name;
                        break;
                    case 3://biomedico,quirurgico,afines
                        $responsable_rubro[2]=$resp_rubro->name;
                        break;
                    case 4://dentales
                        $responsable_rubro[3]=$resp_rubro->name;
                        break;
                    case 5://fotografico
                        $responsable_rubro[4]=$resp_rubro->name;
                        break;
                    case 6://dentales 2
                        $responsable_rubro[5]=$resp_rubro->name;
                        break;
                }
            }
        /*
    
        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'servicio_id'=>$servicio_id]); */

        $pdf = \PDF::loadView('site.pdf.descargar_rubro_servicio_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_name'=>$name,'nombre_rubro'=>$nombre_rubro,'cierre'=>$cierre,'cip'=>$cip,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'tipo'=>$tipo,'can_id'=>$can_id,'responsables'=>$responsables,'responsable_rubro'=>$responsable_rubro,'responsable'=>$responsable]);
        
        
        }
    }    

/*
    //public function pdf_previo($can_id,$establecimiento_id,$tipo)
    public function pdf_final_estimacion_nivel2y3($can_id,$establecimiento_id,$rol)
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

        switch ($rol) {
            case 3: //PF
                    $tipo=1;$mtipo=1;
                break;
            case 4: //MBMQPA
                $tipo=2;$tipo2=3;$tipo3=7;$mtipo=2;
                break;
            case 5: //MD
                $tipo=4;$mtipo=2;
                break;
            case 6: //ML
                $tipo=5;$mtipo=2;
                break;
            case 8: //MFF
                $tipo=6;$mtipo=2;
                break;
            
        }

            if($tipo==1||$tipo==4||$tipo==6){
                $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();

                $num_estimaciones=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)                        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->count();

                $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('estado','<>',2)
                        ->where('tipo_dispositivo_id',$tipo)                                                
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')                          
                        ->get();                    


                $descripcion_tipo='Medicamentos';
            }else
                {   if ($tipo==5) {
                        $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22                        
                        ->where('estado','<>',2)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1  
                        ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',5)
                                              
                                              ->orWhere('tipo_dispositivo_id',10);

                                    }) 
                                          
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();                    

                        $num_estimaciones=DB::table('estimacions')
                            ->where('necesidad_anual','>',0)
                            ->where('estado','<>',2)
                            ->where('can_id',$can_id) //cambiar 22
                            ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',5)
                                              
                                              ->orWhere('tipo_dispositivo_id',10);

                                    })                       
                            ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                            ->count();


                        $total_tipo_productos=DB::table('estimacions')
                        ->select('tipo_dispositivo_id')
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where( function ( $query )
                                    {
                                        $query->orWhere('tipo_dispositivo_id',5)
                                              
                                              ->orWhere('tipo_dispositivo_id',10);

                                    })                       
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->groupby('tipo_dispositivo_id')
                        ->orderby ('tipo_dispositivo_id','asc')  
                        //->orderby ('descripcion','asc')  
                        ->get();                    

                        $descripcion_tipo='Dispositivos';

                    }else
                    {   if ($tipo==4||$tipo==5||$tipo==6) {
                        $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();

                        $num_estimaciones=DB::table('estimacions')
                                ->where('necesidad_anual','>',0)
                                ->where('estado','<>',2)
                                ->where('can_id',$can_id) //cambiar 22
                                ->where('tipo_dispositivo_id',$tipo)                        
                                ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                                ->count();

                        $total_tipo_productos=DB::table('estimacions')
                                ->select('tipo_dispositivo_id')
                                ->where('can_id',$can_id) //cambiar 22
                                ->where('estado','<>',2)
                                ->where('tipo_dispositivo_id',$tipo)                                                
                                ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                                ->groupby('tipo_dispositivo_id')
                                ->orderby ('tipo_dispositivo_id','asc')                          
                                ->get(); 
                        }else 
                        {

                        Flash::error('Datos no son correctos, error al descargar archivo');
                        return redirect(route('estimacion.index'));  
                        }
                    }
            }

            
            

        
        
        $num_estimaciones=count($data);
        
            $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',$rol)
                ->get();

            if(count($usuario)==0){
                $name="";
                $cip="";
                $dni="";
                $name2="";
                $cip2="";
                $dni2="";
            }
            else
            {
                if(count($usuario)==2){
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2=$usuario->get(1)->name;
                    $cip2=$usuario->get(1)->cip;
                    $dni2=$usuario->get(1)->dni;    
                }
                else
                {
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2="";
                    $cip2="";
                    $dni2="";
                }
            }

            
            $jefes=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',9)
                ->get();

            if(count($jefes)==0){
                $name_jefe="";
                $cip_jefe="";
                $dni_jefe="";
            }
            else
            {
                $name_jefe=$jefes->get(0)->name;
                $cip_jefe=$jefes->get(0)->cip;
                $dni_jefe=$jefes->get(0)->dni;    
            }


            $cierre_servicio=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
            $cierre=$cierre_servicio->get(0)->updated_at;

            $texto='CONSOLIDADO';

            $pdf = \PDF::loadView('site.pdf.descargar_comite_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_1'=>$name,'responsable_2'=>$name2,'cip'=>$cip,'cip2'=>$cip2,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'dni2'=>$dni2,'name_jefe'=>$name_jefe,'cip_jefe'=>$cip_jefe,'dni_jefe'=>$dni_jefe,'cierre'=>$cierre]);

            $pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');
        
        
     } */

//4,3,3
public function pdf_final_estimacion_nivel2y3($can_id,$establecimiento_id,$rol)
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

        switch ($rol) {
            case 3: //PF
                    $tipo=1;$mtipo=1;
                break;
            case 4: //MBMQPA
                $tipo=2;$tipo2=3;$tipo3=7;$mtipo=2;
                break;
            case 5: //MD
                $tipo=4;$mtipo=2;
                break;
            case 6: //ML
                $tipo=5;$mtipo=2;
                break;
            case 8: //MFF
                $tipo=6;$mtipo=2;
                break;
            
        }
        
        /*if($servicio_id!=0){ */
            if($tipo==1 || $tipo==4 || $tipo==6){
                
                $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        //->where('cpma','>',0)                        
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo)        
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1                        
                        ->orderby ('tipo_dispositivo_id','asc')
                        ->orderby('descripcion','asc')//cambiar desc
                        ->get();
                //dd($data);
                
/*
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
Where ET.can_id='.$can_id.' and ET.estado!=2 and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
Order by ET.descripcion asc
';
$data = DB::select($cad);*/


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
                    
                    $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('estado','<>',2)
                        //->where('cpma','>',0)
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
                        /*

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
Where ET.can_id='.$can_id.' and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=5 or ET.tipo_dispositivo_id=10)
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); */

                    $descripcion_tipo='Material e insumos de Laboratorio';
                }   
                else
                {
                    
                    $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        //->where('cpma','>',0)
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
                        /*
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
Where ET.can_id='.$can_id.' and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=2 or ET.tipo_dispositivo_id=3 or ET.tipo_dispositivo_id=7)
Order by ET.tipo_dispositivo_id, ET.descripcion asc
';
$data = DB::select($cad); */

                    $descripcion_tipo='Material Biomedico, Instrumental Quirurgico y Productos afines';
                }    
            }

                        
            $num_estimaciones=count($data);
        
            $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',$rol)
                ->get();

            if(count($usuario)==0){
                $name="";
                $cip="";
                $dni="";
                $name2="";
                $cip2="";
                $dni2="";
            }
            else
            {
                if(count($usuario)==2){
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2=$usuario->get(1)->name;
                    $cip2=$usuario->get(1)->cip;
                    $dni2=$usuario->get(1)->dni;    
                }
                else
                {
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2="";
                    $cip2="";
                    $dni2="";
                }
            }

            
            $jefes=DB::table('responsables')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('can_id',$can_id)
                ->where('rol',9)
                ->get();

            if(count($jefes)==0){
                $name_jefe="";
                $cip_jefe="";
                $dni_jefe="";
            }
            else
            {
                $name_jefe=$jefes->get(0)->nombre;
                $cip_jefe=$jefes->get(0)->cip;
                $dni_jefe=$jefes->get(0)->dni;    
            }


            $cierre_servicio=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
            $cierre=$cierre_servicio->get(0)->updated_at;

            $texto='CONSOLIDADO';

            $pdf = \PDF::loadView('site.pdf.descargar_comite_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_1'=>$name,'responsable_2'=>$name2,'cip'=>$cip,'cip2'=>$cip2,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'dni2'=>$dni2,'name_jefe'=>$name_jefe,'cip_jefe'=>$cip_jefe,'dni_jefe'=>$dni_jefe,'cierre'=>$cierre]);

            $pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');
        
        
    }    

public function pdf_final_estimacion_nivel2y3_modificado($can_id,$establecimiento_id,$tipo)
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

            if($tipo==1 || $tipo==4 || $tipo==6){
                $cad='select 
ET.descripcion, ET.necesidad_anual,ET.necesidad_actual, ET.tipo_dispositivo_id
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
Where ET.estado !=2 and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
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

                    $cad='select 
ET.descripcion, ET.necesidad_anual,ET.necesidad_actual, ET.tipo_dispositivo_id
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
            }

                        
            $num_estimaciones=count($data);
        
            $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',7)
                ->get();

            if(count($usuario)==0){
                $name="";
                $cip="";
                $dni="";
                $name2="";
                $cip2="";
                $dni2="";
            }
            else
            {
                if(count($usuario)==2){
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2=$usuario->get(1)->name;
                    $cip2=$usuario->get(1)->cip;
                    $dni2=$usuario->get(1)->dni;    
                }
                else
                {
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2="";
                    $cip2="";
                    $dni2="";
                }
            }

            
            $jefes=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',9)
                ->get();

            if(count($jefes)==0){
                $name_jefe="";
                $cip_jefe="";
                $dni_jefe="";
            }
            else
            {
                $name_jefe=$jefes->get(0)->name;
                $cip_jefe=$jefes->get(0)->cip;
                $dni_jefe=$jefes->get(0)->dni;    
            }


            $cierre_servicio=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
            $cierre=$cierre_servicio->get(0)->updated_at;

            $texto='CONSOLIDADO';

            $pdf = \PDF::loadView('site.pdf.descargar_comite_farmacia_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_1'=>$name,'responsable_2'=>$name2,'cip'=>$cip,'cip2'=>$cip2,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'dni2'=>$dni2,'name_jefe'=>$name_jefe,'cip_jefe'=>$cip_jefe,'dni_jefe'=>$dni_jefe,'cierre'=>$cierre]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');


        
        
    }    

public function pdf_final_estimacion_nivel3($can_id,$establecimiento_id,$tipo)
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

        /*if($servicio_id!=0){ */
        if($tipo==2 || $tipo==3 || $tipo==7){
                
        
            $cad='select 
ET.descripcion, ET.cpma, ET.necesidad_anual, ET.mes1, ET.mes2, ET.mes3, ET.mes4, ET.mes5, ET.mes6, ET.mes7, ET.mes8, ET.mes9, ET.mes10, ET.mes11, ET.mes12,ET.tipo_dispositivo_id
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
Where ET.estado!=2 and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
Order by ET.descripcion asc
';
$data = DB::select($cad);

            $descripcion_tipo='Material Biomedico, Instrumental Quirurgico y Productos afines';
            
            }

                        
            $num_estimaciones=count($data);
        
            $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',4)
                ->get();

            if(count($usuario)==0){
                $name="";
                $cip="";
                $dni="";
                $name2="";
                $cip2="";
                $dni2="";
            }
            else
            {
                if(count($usuario)==2){
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2=$usuario->get(1)->name;
                    $cip2=$usuario->get(1)->cip;
                    $dni2=$usuario->get(1)->dni;    
                }
                else
                {
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2="";
                    $cip2="";
                    $dni2="";
                }
            }

            
            $jefes=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',9)
                ->get();

            if(count($jefes)==0){
                $name_jefe="";
                $cip_jefe="";
                $dni_jefe="";
            }
            else
            {
                $name_jefe=$jefes->get(0)->name;
                $cip_jefe=$jefes->get(0)->cip;
                $dni_jefe=$jefes->get(0)->dni;    
            }


            $cierre_servicio=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
            $cierre=$cierre_servicio->get(0)->updated_at;

            $texto='CONSOLIDADO';

            $pdf = \PDF::loadView('site.pdf.descargar_comite_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$descripcion_tipo,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_1'=>$name,'responsable_2'=>$name2,'cip'=>$cip,'cip2'=>$cip2,'dni'=>$dni,'texto'=>$texto,'nivel'=>$nivel,'can_id'=>$can_id,'dni2'=>$dni2,'name_jefe'=>$name_jefe,'cip_jefe'=>$cip_jefe,'dni_jefe'=>$dni_jefe,'cierre'=>$cierre]);

            $pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');
        
        
    }    

public function pdf_final_estimacion_nivel3_modificado($can_id,$establecimiento_id,$tipo)
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

        if($tipo!=5){

        
            $cad='select 
            ET.descripcion, ET.cpma, ET.necesidad_anual, ET.necesidad_actual,ET.tipo_dispositivo_id
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
            Where ET.estado !=2 and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and ET.tipo_dispositivo_id='.$tipo.'
            Order by ET.descripcion asc
            ';
            $data = DB::select($cad);
        }
        else
        {
            
            $cad='select 
            ET.descripcion, ET.cpma, ET.necesidad_anual, ET.necesidad_actual,ET.tipo_dispositivo_id
            ,(
            select sum(necesidad_anual) as necesidad_anual
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=0 
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )stock_cero
            ,(
            select sum(necesidad_anual) as necesidad_anual
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=1 
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )stock_uno
            ,(
            select sum(necesidad_anual) as necesidad_anual
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=2
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )stock_dos
            ,(
            select sum(necesidad_anual) as necesidad_anual
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=3
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )stock_tres
            ,(
            select sum(necesidad_anterior) as necesidad_anual
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and cpma_anterior!=0 
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )stock_cuatro,
            (
            select sum(cpma) as cpma
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=0 
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )cpma_cero
            ,(
            select sum(cpma) as cpma
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=1 
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )cpma_uno
            ,(
            select sum(cpma) as cpma
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=2
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )cpma_dos
            ,(
            select sum(cpma) as cpma
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and estado=3
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )cpma_tres
            ,(
            select sum(cpma_anterior) as cpma
            from estimacion_servicio
            where establecimiento_id='.$establecimiento_id.'
            and can_id='.$can_id.'
            and cpma_anterior!=0 
            and petitorio_id=ET.petitorio_id
            and (tipo_dispositivo_id=5 or  tipo_dispositivo_id=10)
            )cpma_cuatro
            from estimacions ET
            Where ET.estado !=2 and ET.establecimiento_id='.$establecimiento_id.' and ET.necesidad_anual>0 and (ET.tipo_dispositivo_id=5 or ET.tipo_dispositivo_id=10)
            Order by ET.tipo_dispositivo_id, ET.descripcion asc
            ';
            $data = DB::select($cad);
        }
            $num_estimaciones=count($data);
        
            $usuario=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',7)
                ->get();

            if(count($usuario)==0){
                $name="";
                $cip="";
                $dni="";
                $name2="";
                $cip2="";
                $dni2="";
            }
            else
            {
                if(count($usuario)==2){
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2=$usuario->get(1)->name;
                    $cip2=$usuario->get(1)->cip;
                    $dni2=$usuario->get(1)->dni;    
                }
                else
                {
                    $name=$usuario->get(0)->name;                    
                    $cip=$usuario->get(0)->cip;
                    $dni=$usuario->get(0)->dni;    
                    $name2="";
                    $cip2="";
                    $dni2="";
                }
            }

            
            $jefes=DB::table('users')
                ->where('establecimiento_id',$establecimiento_id)
                ->where('rol',9)
                ->get();

            if(count($jefes)==0){
                $name_jefe="";
                $cip_jefe="";
                $dni_jefe="";
            }
            else
            {
                $name_jefe=$jefes->get(0)->name;
                $cip_jefe=$jefes->get(0)->cip;
                $dni_jefe=$jefes->get(0)->dni;    
            }

            

            switch ($tipo) {
            case 1: $nombre_rubro="MEDICAMENTOS"; break;
            case 2: $nombre_rubro="MATERIAL BIOMEDICO"; break;
            case 3: $nombre_rubro="INSTRUMENTAL QUIRURGICO"; break;
            case 4: $nombre_rubro="MATERIAL E INSUMO ODONTOLOGICO"; break;
            case 5: $nombre_rubro="INSUMO DE LABORATORIO"; break;
            case 6: $nombre_rubro="MATERIAL FOTOGRAFICO Y FONOTECNICO"; break;
            case 7: $nombre_rubro="PRODUCTOS AFINES"; break;
            case 8: $nombre_rubro="NN"; break;
            case 9: $nombre_rubro="DISPOSITIVO MEDICO DE USO RESTRINGIDO"; break;
            case 10: $nombre_rubro="MATERIAL DE LABORATORIO"; break;
        }

            $cierre_servicio=DB::table('can_establecimiento')->where('can_id',$can_id)->where('establecimiento_id',$establecimiento_id)->get();
            $cierre=$cierre_servicio->get(0)->updated_at;

            $texto='CONSOLIDADO';

            $pdf = \PDF::loadView('site.pdf.descargar_servicio_rectificacion_administrador_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,'descripcion_tipo' =>$nombre_rubro,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,'responsable_1'=>$name,'responsable_2'=>$name2,'cip'=>$cip,'cip2'=>$cip2,'dni'=>$dni,'texto'=>$texto,'nombre_rubro'=>$nombre_rubro,'nivel'=>$nivel,'can_id'=>$can_id,'dni2'=>$dni2,'name_jefe'=>$name_jefe,'cip_jefe'=>$cip_jefe,'dni_jefe'=>$dni_jefe,'cierre'=>$cierre]);

            $pdf->setPaper('A4', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');
        
        
    }    


    public function productos_servicio_tipo($id,$tipo)
    {
        //$can = $this->canRepository->findWithoutFail($id);
        
        $can = DB::table('cans')->where('id',$id)->get();
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //$can_id=$cans->get(0)->id;

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<3205;$i++){
            for($j=0;$j<301;$j++){ //total servicios
                $can_productos[$i][$j]=0;
                $descripcion[$j][0]="";
                $descripcion[$j][1]="";
            }
        }

        if($tipo==3 || $tipo==5 || $tipo==8){ //tipo==rol usuario
            
            switch ($tipo) {
                case 3: $compara=1;$descripcion_tipo="PRODUCTOS FARMACEUTICOS";break;
                case 5: $compara=4;$descripcion_tipo="MATERIAL E INSUMO ODONTOLOGICO";break;
                case 8: $compara=6;$descripcion_tipo="MATERIAL FOTOGRAFICO Y FONOTECNICO";break;break;
            }
            
            $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  petitorio_id,descripcion, nombre_servicio,servicio_id,establecimiento_id, cod_petitorio'))
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)                                      
                    ->where('estimacion_servicio.estado','<>',2)
                    ->where('estimacion_servicio.tipo_dispositivo_id',$compara)
                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                    ->groupby('petitorio_id','descripcion','nombre_servicio', 'servicio_id', 'establecimiento_id', 'cod_petitorio')
                    ->orderby('petitorio_id','asc')
                    ->orderby('servicio_id','asc')
                    ->get();

            

            
        }else
            
        {   if ($tipo==4) {
                    /*
                   $consulta = DB::table('estimacion_servicio')
                                ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id, estimacion_servicio.cod_petitorio'))
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
                                ->where('estimacion_servicio.necesidad_anual','>',0)
                                ->where('estimacion_servicio.can_id',$id)
                                ->where('estimacion_servicio.estado','<>',2)
                                ->where( function ( $query )
                                        {
                                            $query->orWhere('estimacion_servicio.tipo_dispositivo_id',2)
                                                  ->orWhere('estimacion_servicio.tipo_dispositivo_id',3)
                                                  ->orWhere('estimacion_servicio.tipo_dispositivo_id',7);

                                        })
                                ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                                ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', 'servicios.id','estimacion_servicio.cod_petitorio')
                                ->orderby('estimacion_servicio.petitorio_id','asc')
                                ->orderby('servicios.id','asc')
                                ->get();    */


                    $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  petitorio_id,descripcion, nombre_servicio,servicio_id,establecimiento_id, cod_petitorio'))
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)                                      
                    ->where('estimacion_servicio.estado','<>',2)                    
                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                    ->where( function ( $query )
                                        {
                                            $query->orWhere('estimacion_servicio.tipo_dispositivo_id',2)
                                                  ->orWhere('estimacion_servicio.tipo_dispositivo_id',3)
                                                  ->orWhere('estimacion_servicio.tipo_dispositivo_id',7);

                                        })
                    ->groupby('petitorio_id','descripcion','nombre_servicio', 'servicio_id', 'establecimiento_id', 'cod_petitorio')
                    ->orderby('petitorio_id','asc')
                    ->orderby('servicio_id','asc')
                    ->get();

                    $descripcion_tipo="MATERIAL BIOMEDICO, INSTRUMENTAL QUIRURGICO, PRODUCTOS AFINES";
            }
            else
            {   /*
                 $consulta = DB::table('estimacion_servicio')
                                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, servicios.nombre_servicio,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id, estimacion_servicio.cod_petitorio'))
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
                                    ->where('estimacion_servicio.necesidad_anual','>',0)
                                    ->where('estimacion_servicio.can_id',$id)
                                    ->where('estimacion_servicio.estado','<>',2)
                                    ->where( function ( $query )
                                            {
                                                $query->orWhere('tipo_dispositivo_id',5)     
                                                    ->orWhere('tipo_dispositivo_id',10);

                                            })
                                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                                    ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', 'servicios.id','estimacion_servicio.cod_petitorio')
                                    ->orderby('estimacion_servicio.petitorio_id','asc')
                                    ->orderby('servicios.id','asc')
                                    ->get(); */

                    $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as necesidad,  petitorio_id,descripcion, nombre_servicio,servicio_id,establecimiento_id, cod_petitorio'))
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)                                      
                    ->where('estimacion_servicio.estado','<>',2)                    
                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                    ->where( function ( $query )
                                        {
                                                $query->orWhere('tipo_dispositivo_id',5)     
                                                    ->orWhere('tipo_dispositivo_id',10);

                                            })
                    ->groupby('petitorio_id','descripcion','nombre_servicio', 'servicio_id', 'establecimiento_id', 'cod_petitorio')
                    ->orderby('petitorio_id','asc')
                    ->orderby('servicio_id','asc')
                    ->get();

                                $descripcion_tipo="MATERIAL DE LABORATORIO E INSUMO DE LABORATORIO";
            }   
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

            foreach ($consulta as $key => $value) {
                
                $fila=$value->petitorio_id;
                
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }
                
                for($k=0;$k<$i;$k++){
                    if($value->servicio_id==$descripcion[$k][0]){
                        $m=($k);                        
                        $can_productos[$x][$m]=$value->necesidad;
                        $can_productos[$x][300]=$value->cod_petitorio;
                        $can_productos[$x][299]=$value->descripcion;
                        $can_productos[$x][298]=$can_productos[$x][298]+$can_productos[$x][$m];
                    }
                }
                $y++;
            }
            $x++;
        
        return view('site.responsable_farmacia_hospital.servicio_show')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('y', count($servicios_x))
                                      ->with('descripcion_tipo', $descripcion_tipo)
                                      ->with('descripcion', $descripcion);
    }

    public function productos_servicio_tipo_mof($id,$tipo)
    {
        //$can = $this->canRepository->findWithoutFail($id);
        
        $can = DB::table('cans')->orderby('cans.id','desc')->get();
        $establecimiento_id=Auth::user()->establecimiento_id;
        
        //$can_id=$cans->get(0)->id;

        if (empty($can)) {
            Flash::error('No se ha encontrado');

            return redirect(route('cans.index'));
        }
        for($i=0;$i<4200;$i++){
            for($j=0;$j<300;$j++){ //total servicios
                $can_productos[$i][$j]=0;
                $can_productos1[$i][$j]=0;
                $can_productos2[$i][$j]=0;
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
                case 10: $compara=10;$descripcion_tipo="INSTRUMENTAL TRAUMATOLOGIA";break;
                case 11: $compara=11;$descripcion_tipo="MATERIAL TRAUMATOLOGIA";break;
                

            }
        if ($tipo!=5) {
            $consulta = DB::table('estimacion_servicio')
                    ->select(DB::raw('sum(necesidad_anual) as anual, sum(necesidad_anual_1) as anual_1, sum(necesidad_anual_2) as anual_2,  estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, estimacion_servicio.cod_petitorio, servicios.nombre_servicio,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id'))
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
                    ->where('estimacion_servicio.necesidad_anual','>',0)
                    ->where('estimacion_servicio.can_id',$id)
                    ->where('can_servicio.can_id',$id)
                    ->where('estimacion_servicio.estado','<>',2)
                    ->where('estimacion_servicio.tipo_dispositivo_id',$compara)
                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                    ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', 'servicios.id','estimacion_servicio.cod_petitorio')
                    ->orderby('estimacion_servicio.petitorio_id','asc')
                    ->orderby('servicios.id','asc')
                    ->get();

            
        }

        else
            
        {   

                 $consulta = DB::table('estimacion_servicio')
                                    ->select(DB::raw('sum(necesidad_anual) as anual, sum(necesidad_anual_1) as anual_1, sum(necesidad_anual_2) as anual_2, estimacion_servicio.petitorio_id,estimacion_servicio.descripcion, estimacion_servicio.cod_petitorio,servicios.nombre_servicio,estimacion_servicio.servicio_id,estimacion_servicio.establecimiento_id'))
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
                                    ->where('estimacion_servicio.necesidad_anual','>',0)
                                    ->where('estimacion_servicio.can_id',$id)
                                    ->where('can_servicio.can_id',$id)
                                    ->where('estimacion_servicio.estado','<>',2)
                                    ->where( function ( $query )
                                            {
                                                $query->orWhere('tipo_dispositivo_id',5)     
                                                    ->orWhere('tipo_dispositivo_id',10);

                                            })
                                    ->where('estimacion_servicio.establecimiento_id',$establecimiento_id)
                                    ->groupby('estimacion_servicio.petitorio_id','estimacion_servicio.descripcion','servicios.nombre_servicio', 'estimacion_servicio.servicio_id','estimacion_servicio.establecimiento_id', 'servicios.id','estimacion_servicio.cod_petitorio')
                                    ->orderby('estimacion_servicio.petitorio_id','asc')
                                    ->orderby('servicios.id','asc')
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
/*            

            foreach ($servicios_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_servicio;                   
                $i++;
            }
            
            $fila_anterior=5000; $x=-1; $y=0; $z=0;
            print_r($servicios_x);
            dd($consulta);

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
                        $can_productos[$x][$m]=$value->anual; 
                        $can_productos[$x][$n]=$value->actual;                        
                        $can_productos[$x][299]=$value->descripcion;
                        $can_productos[$x][298]=$can_productos[$x][298]+$can_productos[$x][$m];
                        //$can_productos[$x][297]=$can_productos[$x][297]+$can_productos[$x][$n];
                    }
                }
                $y++;
            }
            $x++;

            */

            foreach ($servicios_x as $key => $value) {
                $descripcion[$i][0]=$value->id;   
                $descripcion[$i][1]=$value->nombre_servicio;                   
                $i++;
            }
            
            $fila_anterior=5000; $x=-1; $y=0; $z=0;

            foreach ($consulta as $key => $value) {
                
                $fila=$value->petitorio_id;
                
                if($fila_anterior!=$fila){
                    $fila_anterior=$fila;
                    $x++;
                }
                
                for($k=0;$k<$i;$k++){
                    if($value->servicio_id==$descripcion[$k][0]){
                        $m=($k);                        
                        $can_productos[$x][$m]=$value->anual;
                        //$can_productos1[$x][$m]=$value->anual_1;
                        $can_productos2[$x][$m]=$value->anual_2;
                        $can_productos[$x][300]=$value->cod_petitorio;
                        //$can_productos1[$x][300]=$value->cod_petitorio;
                        //$can_productos2[$x][300]=$value->cod_petitorio;
                        $can_productos[$x][299]=$value->descripcion;
                        //$can_productos1[$x][299]=$value->descripcion;
                        //$can_productos2[$x][299]=$value->descripcion;
                        $can_productos[$x][298]=$can_productos[$x][298]+$can_productos[$x][$m];
                        //$can_productos1[$x][298]=$can_productos[$x][298]+$can_productos[$x][$m];
                        //$can_productos2[$x][298]=$can_productos[$x][298]+$can_productos[$x][$m];
                    }
                }

                
                $y++;
            }
            $x++;

        //dd($descripcion);
        //return view('site.responsable_farmacia_hospital.servicio_show_actual')->with('can', $can)
        return view('site.responsable_farmacia_hospital.servicio_show')->with('can', $can)
                                      ->with('can_productos', $can_productos)
                                      //->with('can_productos1', $can_productos1)
                                      //->with('can_productos2', $can_productos2)
                                      ->with('fila', $x)
                                      ->with('col', $i)
                                      ->with('y', count($servicios_x))
                                      ->with('descripcion_tipo', $descripcion_tipo)
                                      ->with('descripcion', $descripcion);
    }

    public function pdf_servicio_rectificacion_farmacia($can_id,$establecimiento_id,$tipo, $servicio_id)
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

    /* public function activar_servicio_establecimiento($can_id, $establecimiento_id, $servicio_id)
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

        $establecimiento_can_servicio = DB::table('can_servicio')
                ->where('can_id',$can_id)
                ->where('servicio_id',$servicio_id)
                ->where('establecimiento_id',$establecimiento_id)
                ->get();

        
        $cerrado_dispositivo=$establecimiento_can_servicio->get(0)->dispositivo_cerrado;        
        $cerrado_medicamento=$establecimiento_can_servicio->get(0)->medicamento_cerrado;        
        
        return view('site.responsable_farmacia_hospital.activar_servicio')
                    ->with('cerrado_medicamento', $cerrado_medicamento)
                    ->with('cerrado_dispositivo', $cerrado_dispositivo)
                    ->with('servicio_id', $servicio_id)
                    ->with('can',$can)
                    ->with('establecimiento_id',$establecimiento_id);

    }
    */

}