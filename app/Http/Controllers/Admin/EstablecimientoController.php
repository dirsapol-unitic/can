<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Requests\CreateEstablecimientoRequest;
use App\Http\Requests\UpdateEstablecimientoRequest;
use App\Repositories\EstablecimientoRepository;
use App\Repositories\CanRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Petitorio;
use App\Models\Categoria;
use App\Models\Disa;
use App\Models\Distrito;
use App\Models\Nivel;
use App\Models\Provincia;
use App\Models\Departamento;
use App\Models\Region;
use App\Models\TipoEstablecimiento;
use App\Models\TipoInternamiento;
use App\Models\Establecimiento;
use App\Models\Rubro;
use App\Models\Servicio;
use App\Models\Division;
use Carbon\Carbon;

use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class EstablecimientoController extends AppBaseController
{
    /** @var  EstablecimientosRepository */
    private $establecimientoRepository;
    private $canRepository;

    public function __construct(EstablecimientoRepository $establecimientoRepo,CanRepository $canRepo)
    {
        $this->establecimientoRepository = $establecimientoRepo;
        $this->canRepository = $canRepo;
    }


    /**
     * Display a listing of the Establecimientos.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->establecimientoRepository->pushCriteria(new RequestCriteria($request));
        $establecimientos = $this->establecimientoRepository->orderBy('id')->get();
        

        return view('admin.establecimientos.index')
            ->with('establecimientos', $establecimientos);
    }

    /**
     * Show the form for creating a new Establecimientos.
     *
     * @return Response
     */
    public function create()
    {
        $nivel_id=Nivel::pluck('descripcion','id');
        $region_id=Region::pluck('descripcion','id');
        $categoria_id=Categoria::pluck('descripcion','id');
        $tipo_establecimiento_id=TipoEstablecimiento::pluck('descripcion','id');
        $tipo_internamiento_id=TipoInternamiento::pluck('descripcion','id');
        $departamento = DB::table('departamentos')->get();
        $departamento_id = 0;
        $disa_id=Disa::pluck('descripcion','id');
        $tipo=1;//nuevo

        return view('admin.establecimientos.create',compact(["nivel_id","region_id","categoria_id","tipo_establecimiento_id","tipo_internamiento_id","departamento","departamento_id","disa_id","tipo"]));

        
    }

    /**
     * Store a newly created Establecimientos in storage.
     *
     * @param CreateEstablecimientosRequest $request
     *
     * @return Response
     */
    public function store(CreateEstablecimientoRequest $request)
    {
        $input = $request->all();

        $establecimientos = $this->establecimientoRepository->create($input);

        Flash::success('Establecimientos guardado correctamente.');

        return redirect(route('establecimientos.index'));

        
    }

    /**
     * Display the specified Establecimientos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $establecimientos = $this->establecimientoRepository->findWithoutFail($id);

        if (empty($establecimientos)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimiento.index'));
        }

        return view('admin.establecimientos.show')->with('establecimientos', $establecimientos);
    }

    //////mostrar medicamentos 
    /*public function ver_medicamentos(Request $request,$establecimiento_id)
    {

        ///mostrar todos los petitorios del establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        $petitorios = Establecimiento::find($establecimiento_id)->petitorios
                        ->where('tipo_dispositivo_medicos_id',1)
                        ->where('nivel_id','<=',$establecimiento->nivel_id);
            

        //dd($petitorios); 
        return view('admin.establecimientos.medicamentos.ver_medicamentos')
            ->with('petitorios', $petitorios)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('establecimiento_id', $establecimiento_id);
            
    }

    ////////////////////
    public function asignar_medicamentos($establecimiento_id)
    {
        //mostrar todos los petitorios que sean del nivel del establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }


        $petitorios=Petitorio::where('tipo_dispositivo_medicos_id',1)
                        ->where('nivel_id','<=',$establecimiento->nivel_id)
                        ->pluck('descripcion','id');

        return view('admin.establecimientos.medicamentos.asignar_medicamentos',compact('petitorios','establecimiento'));     
    }

    ////////////////
    public function guardar_medicamentos(UpdateEstablecimientoRequest $request, $establecimiento_id)
    {
        
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimiento.index'));
        }

        //Buscar todos los medicamentos
        $num_medicamentos=DB::table('establecimiento_petitorio')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_medico_id',1)
                            ->orwhere('tipo_dispositivo_medico_id','>',1)
                            ->count();
        
        //Si esta vacio y no ha sido asignado ningun valor aun a la bd
        if($num_medicamentos==0){
            $establecimiento->petitorios()->sync($request->petitorios); 
        }
        else
        {
            $medicamentos_bd=DB::table('establecimiento_petitorio')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_medico_id',1)
                            ->get();
            //checkbox marcados
            $medicamentos_iguales=$medicamentos_bd->pluck('petitorio_id')->intersect($request->petitorios)->count();
            
            //checkbox desmarcados
            $medicamentos_desmarcado=$medicamentos_bd->pluck('petitorio_id')->diff($request->petitorios);

            //convertimos a arreglo
            $arreglo = $medicamentos_bd->pluck('petitorio_id')->toArray();

            //los nuevos checkbox
            $medicamentos_nuevos=array_diff($request->petitorios,$arreglo);

            //dd($medicamentos_diferentes);            
            if($medicamentos_iguales==0)
            {
                $establecimiento->petitorios()->attach($request->petitorios); //attach       
            }
            else
            {
                ///eliminamos los que no estan en los checkbox
                $establecimiento->petitorios()->detach($medicamentos_desmarcado); //attach       
                ///Insertamos los nuevos
                $establecimiento->petitorios()->attach($medicamentos_nuevos); //attach       

            }    

        }    

        Flash::success('Establecimientos guardado correctamente.');

        return redirect(route('establecimientos.ver_medicamentos',[$establecimiento_id]));
    }
*/
    //////mostrar medicamentos 
    public function ver_dispositivos(Request $request,$establecimiento_id)
    {

        ///mostrar todos los petitorios del establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        $petitorios = Establecimiento::find($establecimiento_id)->petitorios
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id','<=',$establecimiento->nivel_id);
        
        //dd($petitorios); 
        return view('admin.establecimientos.dispositivos.ver_dispositivos')
            ->with('petitorios', $petitorios)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('establecimiento_id', $establecimiento_id);
            
    }

    ////////////////////
    public function asignar_dispositivos($establecimiento_id)
    {
        //encontrar el establecimiento que se encuentra para asignar
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        //si encontramos el establecimiento buscado
        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        //Mostramos los petitorios que son dispositivos medicos con el nivel del establecimiento
        $petitorios=Petitorio::select('descripcion','id','descripcion_tipo_dispositivo')->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id','<=',$establecimiento->nivel_id)
                        ->get();
                        //->pluck('descripcion','id');

        return view('admin.establecimientos.dispositivos.asignar_dispositivos',compact('petitorios','establecimiento'));     
    }

    ////////////////
    public function guardar_dispositivos(UpdateEstablecimientoRequest $request, $establecimiento_id)
    {
        
        //Busco el establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        //Si se ha digitado en la url un establecimiento que no corresponde
        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimiento.index'));
        }

        //Buscar todos los medicamentos
        $num_medicamentos=DB::table('establecimiento_petitorio')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_medico_id',1)
                            ->orwhere('tipo_dispositivo_medico_id','>',1)
                            ->count();
        
        //Si esta vacio y no ha sido asignado ningun valor aun a la bd
        if($num_medicamentos==0){
            $establecimiento->petitorios()->sync($request->petitorios); 

        }
        else
        {
            $dispositivos_bd=DB::table('establecimiento_petitorio')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->where('tipo_dispositivo_medico_id','>',1)
                            ->get();
            //checkbox marcados
            $dispositivos_iguales=$dispositivos_bd->pluck('petitorio_id')->intersect($request->petitorios)->count();
            
            //checkbox desmarcados
            $dispositivos_desmarcado=$dispositivos_bd->pluck('petitorio_id')->diff($request->petitorios);

            //convertimos a arreglo
            $arreglo = $dispositivos_bd->pluck('petitorio_id')->toArray();

            //los nuevos checkbox
            $dispositivos_nuevos=array_diff($request->petitorios,$arreglo);

            //dd($medicamentos_diferentes);            
            if($dispositivos_iguales==0)
            {
                $establecimiento->petitorios()->attach($request->petitorios); //attach       
            }
            else
            {
                ///eliminamos los que no estan en los checkbox
                $establecimiento->petitorios()->detach($dispositivos_desmarcado); //attach       
                ///Insertamos los nuevos
                $establecimiento->petitorios()->attach($dispositivos_nuevos); //attach       
                

            }    

        }    

        ///Actualizamos los tipos de dispositivos a 2
        foreach ($request->petitorios as $key => $petitorio) {
            DB::table('establecimiento_petitorio')
                    ->where('petitorio_id', $petitorio )
                    ->update(['tipo_dispositivo_medico_id' => 2]);
        }
        

        Flash::success('Dispositivos guardado correctamente.');

        return redirect(route('establecimientos.ver_dispositivos',[$establecimiento_id]));
    }
//////////////////////////////////////////////////////////////////////////////////////////////7
    public function ver_rubros(Request $request,$establecimiento_id)
    {

        ///mostrar todos los petitorios del establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        //$rubros = Establecimiento::find($establecimiento_id)->rubros;
        $rubros = Establecimiento::find($establecimiento_id)->servicios;

        return view('admin.establecimientos.rubros.ver_rubros')
            ->with('rubros', $rubros)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('establecimiento_id', $establecimiento_id);
            
    }

    ////////////////////
    public function asignar_rubros($establecimiento_id)
    {
        $num_rubros=Servicio::pluck('nombre_servicio','id')->count();
        //dd($num_rubros);
        if($num_rubros>0){
            $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

            //si encontramos el establecimiento buscado
            if (empty($establecimiento)) {
                Flash::error('Establecimientos no encontrado');

                return redirect(route('establecimientos.index'));
            }

            //Mostramos los petitorios que son dispositivos medicos con el nivel del establecimiento
            $rubros=Servicio::pluck('nombre_servicio','id');
            
            return view('admin.establecimientos.rubros.asignar_rubros',compact('rubros','establecimiento'));         
        }
        else
        {
            Flash::error('No hay rubros registrado, debe registrar aunque sea uno para asignar al establecimientos');
            return redirect(route('establecimientos.ver_rubros',$establecimiento_id));
        }
        
    }

    ////////////////
    public function guardar_rubros(UpdateEstablecimientoRequest $request, $establecimiento_id)
    {
        //Busco el establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);
        //Si se ha digitado en la url un establecimiento que no corresponde
        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimiento.index'));
        }
        $num_cans = DB::table('cans')->where('active', TRUE)->count();
        
        if($num_cans>0){
            $cans=DB::table('cans')->orderBy('id', 'desc')->first();
        }
            //Buscar todos los medicamentos
            $num_rubros=DB::table('establecimiento_servicio')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->count();
            
            //Si esta vacio y no ha sido asignado ningun valor aun a la bd
            if($num_rubros==0){
                $establecimiento->servicios()->sync($request->rubros); 

                //insertamos a la tabla de can_rubros
                foreach ($request->rubros as $key => $rubro) {            
                    
                    $contar_medicamentos=DB::table('petitorio_servicio')                
                                        ->where('servicio_id',$rubro)
                                        ->where('tipo_dispositivo_medico_id',1)
                                        ->count();
                    $contar_dispositivos=DB::table('petitorio_servicio')                
                                        ->where('servicio_id',$rubro)
                                        ->where('tipo_dispositivo_medico_id','>',1)
                                        ->count();
                    $valor_medicamento=1;
                    if($contar_medicamentos>0){
                        $valor_medicamento=3;
                    }
                    $valor_dispositivo=1;
                    if($contar_dispositivos>0){
                        $valor_dispositivo=3;
                    }

                    if($num_cans>0){
                        DB::table('can_servicio')
                            ->insert([
                                    'establecimiento_id' => $establecimiento_id,
                                    'servicio_id' => $rubro,
                                    'can_id' => $cans->id,
                                    'medicamento_cerrado' => $valor_medicamento,
                                    'dispositivo_cerrado' => $valor_dispositivo,
                                    'created_at'=>Carbon::now(),
                                    'updated_at'=>Carbon::now()
                        ]);
                    }  
                }

            }
            else
            {
                $rubros_bd=DB::table('establecimiento_servicio')
                                ->where('establecimiento_id',$establecimiento_id)
                                ->get();
                
                //convertimos a arreglo
                $arreglo = $rubros_bd->pluck('servicio_id')->toArray();
                
                //checkbox desmarcados
                $rubros_desmarcado=$rubros_bd->pluck('servicio_id')->diff($request->rubros);

                if(is_null($request->rubros))
                {
                    $establecimiento->servicios()->detach($rubros_desmarcado); //attach  
                    //insertamos a la tabla de can_rubros
                    if($num_cans>0){
                        foreach ($rubros_desmarcado as $key => $rubro) {            
                            DB::table('can_servicio')
                                ->where('servicio_id',$rubro)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->where('can_id',$cans->id)
                                ->delete();
                            
                        }
                    }
                }
                else
                {
                    //los nuevos checkbox
                    $rubros_nuevos=array_diff($request->rubros,$arreglo);
                    ///eliminamos los que no estan en los checkbox
                    $establecimiento->servicios()->detach($rubros_desmarcado); //attach 
                    if($num_cans>0){
                        foreach ($rubros_desmarcado as $key => $rubro) {            
                            DB::table('can_servicio')
                                ->where('servicio_id',$rubro)
                                ->where('can_id',$cans->id)
                                ->where('establecimiento_id',$establecimiento_id)
                                ->delete();
                            
                        }
                    }   
                    ///Insertamos los nuevos
                    $establecimiento->servicios()->attach($rubros_nuevos); //attach       
                    
                    foreach ($rubros_nuevos as $key => $rubro) {            
                        $contar_medicamentos=DB::table('petitorio_servicio')                
                                        ->where('servicio_id',$rubro)
                                        ->where('tipo_dispositivo_medico_id',1)
                                        ->count();
                        $contar_dispositivos=DB::table('petitorio_servicio')                
                                            ->where('servicio_id',$rubro)
                                            ->where('tipo_dispositivo_medico_id','>',1)
                                            ->count();
                        $valor_medicamento=1;
                        if($contar_medicamentos>0){
                            $valor_medicamento=3;
                        }
                        $valor_dispositivo=1;
                        if($contar_dispositivos>0){
                            $valor_dispositivo=3;
                        }

                        if($num_cans>0){

                            DB::table('can_servicio')
                                ->insert([
                                    'establecimiento_id' => $establecimiento_id,
                                    'servicio_id' => $rubro,
                                    'can_id' => $cans->id,
                                    'medicamento_cerrado' => $valor_medicamento,
                                    'dispositivo_cerrado' => $valor_dispositivo,
                                    'created_at'=>Carbon::now(),
                                    'updated_at'=>Carbon::now()
                                ]);
                        }  
                    }
                }    

            }
            Flash::success('Rubros guardado correctamente.');
            

        

        return redirect(route('establecimientos.ver_rubros',[$establecimiento_id]));
    }

//////////////////////////////////////////////////////////////////////////////////////////////
public function ver_division(Request $request,$establecimiento_id)
    {

        ///mostrar todos los petitorios del establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        if($establecimiento_id==1 || $establecimiento_id==2 || $establecimiento_id==3 || $establecimiento_id==30 || $establecimiento_id==69){

                $divisions=DB::table('division_establecimiento')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->orderBy('division_id','asc')
                            ->get();

                //$divisions = Establecimiento::find($establecimiento_id)->divisions;
                
                //dd($divisions); 
                return view('admin.establecimientos.divisions.ver_division')
                    ->with('divisions', $divisions)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('establecimiento_id', $establecimiento_id);
        }
        else
        {
            Flash::error('Establecimiento no corresponde ver divisiones');
            return redirect(route('establecimientos.index'));
        }
    }

    ////////////////////
    public function asignar_divisions($establecimiento_id)
    {
            
            $num_divisions=Division::pluck('nombre_division','id')->count();
        if($num_divisions>0) {           
            //encontrar el establecimiento que se encuentra para asignar
            $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

            //si encontramos el establecimiento buscado
            if (empty($establecimiento)) {
                Flash::error('Establecimientos no encontrado');
                return redirect(route('establecimientos.index'));
            }

            if($establecimiento_id==1 || $establecimiento_id==2 || $establecimiento_id==3 || $establecimiento_id==30 || $establecimiento_id==69){

                    //Mostramos los petitorios que son dispositivos medicos con el nivel del establecimiento
                    $divisions=Division::pluck('nombre_division','id');
                    return view('admin.establecimientos.divisions.asignar_division',compact('divisions','establecimiento'));
            }
            else
            {
                Flash::error('Establecimiento no corresponde ver divisiones');
                return redirect(route('establecimientos.index'));   
            }        
        }
        else
        {
            Flash::error('No hay divisiones registrado, debe registrar aunque sea uno para asignar al establecimientos');
            return redirect(route('establecimientos.ver_division',$establecimiento_id));
        }
    }

    ////////////////
    public function guardar_divisions(UpdateEstablecimientoRequest $request, $establecimiento_id)
    {
        
        //Busco el establecimiento
        $establecimiento = $this->establecimientoRepository->findWithoutFail($establecimiento_id);

        //Si se ha digitado en la url un establecimiento que no corresponde
        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimiento.index'));
        }

        //Buscar todos los medicamentos
        $num_divisions=DB::table('division_establecimiento')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->count();
        
        //Si esta vacio y no ha sido asignado ningun valor aun a la bd
        if($num_divisions==0){
            $establecimiento->divisions()->sync($request->divisions); 

        }
        else
        {
            $divisions_bd=DB::table('division_establecimiento')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->get();

            //convertimos a arreglo
            $arreglo = $divisions_bd->pluck('division_id')->toArray();
            
            //checkbox desmarcados
            $divisions_desmarcado=$divisions_bd->pluck('division_id')->diff($request->divisions);            

 
            //dd($medicamentos_diferentes);            
            if(is_null($request->divisions))
            {
                //eliminamos todos los dptos y servicios asignados
                $this->elimina_departamento_servicio($divisions_desmarcado,$establecimiento_id);
                $establecimiento->divisions()->detach($divisions_desmarcado);                
            }
            
            else
            {   
                //los nuevos checkbox
                $divisions_nuevos=array_diff($request->divisions,$arreglo);
                //eliminamos todos los dptos y servicios asignados
                $this->elimina_departamento_servicio($divisions_desmarcado,$establecimiento_id);     
                ///eliminamos los que no estan en los checkbox
                $establecimiento->divisions()->detach($divisions_desmarcado); //attach                   
                ///Insertamos los nuevos
                $establecimiento->divisions()->attach($divisions_nuevos); //attach       
            }    

        }  
        //Si no hay ningun checkbox marcado, entonces no entra
        if(is_null($request->divisions)==False)
            {   
                //Actualizamos los campos de nombre_unidad y establecimiento_id
                foreach($request->divisions as $key => $value){
                    $consulta=DB::table('divisions')->where('id',$value)->get();
                    DB::table('division_establecimiento')
                    ->where('division_id', $value)
                    ->update([
                                'nombre_division' => $consulta->get(0)->nombre_division
                     ]);
                }
            }  

        Flash::success('Division guardado correctamente.');

        return redirect(route('establecimientos.ver_division',[$establecimiento_id]));
    }

    protected function elimina_departamento_servicio($divisions_desmarcado,$establecimiento_id){
        
        //foreach($divisions_desmarcado as $key => $value){
        foreach($divisions_desmarcado as $value){ //1
            //buscamos 
            $busca_si_existe_division=DB::table('division_establecimiento')
                                        ->where('division_establecimiento.division_id',$value)
                                        ->where('division_establecimiento.establecimiento_id',$establecimiento_id)
                                        ->count();

            if($busca_si_existe_division>0){

                $busca_div=DB::table('division_establecimiento')
                                ->where('division_establecimiento.division_id',$value)
                                ->where('division_establecimiento.establecimiento_id',$establecimiento_id)
                                ->get();

                $id_div=$busca_div->get(0)->id;

                $busca_si_existe_dpto=DB::table('dpto')
                                        ->where('dpto.division_establecimiento_id',$id_div)
                                        ->count();
               
                if($busca_si_existe_dpto>0){
                    $busca_dpto=DB::table('dpto')
                                ->where('dpto.division_establecimiento_id',$id_div)
                                ->get();
                    $id=$busca_dpto->get(0)->id;

                    //borrar primero los servicios
                    $servicio = DB::table('serv')
                        ->where('dpto_id',$id)
                        ->delete();

                    //borrar las unidades
                    $unidad = DB::table('dpto')
                        ->where('id',$id)
                        ->delete();    

                }

            }                        

            
        }   
    }

/*    public function cargar_datos_medicamentos($establecimiento_id)
    {
        $abastecimientos = DB::table('establecimientos')
                ->join('establecimiento_petitorio', 'establecimiento_petitorio.establecimiento_id', '=', 'establecimientos.id')
                ->join('petitorios', 'establecimiento_petitorio.petitorio_id', '=', 'petitorios.id')
                ->where('establecimiento_petitorio.establecimiento_id',$establecimiento_id)
                ->get();
        
        //dd($abastecimientos);

        foreach($abastecimientos as $key => $abastecimiento){
                 DB::table('abastecimientos')
                     ->insert([
                        'anomes' => '201802',
                        'establecimiento_id' => $abastecimiento->establecimiento_id,
                        'cod_establecimiento' => $abastecimiento->codigo_establecimiento,
                        'nombre_establecimiento' => $abastecimiento->nombre_establecimiento,
                        'tipo_dispositivo_id' => $abastecimiento->tipo_dispositivo_medico_id,
                        'petitorio_id' => $abastecimiento->petitorio_id,
                        'cod_petitorio' => $abastecimiento->codigo_petitorio,
                        'descripcion' => $abastecimiento->descripcion,
                        'precio' => $abastecimiento->precio,
                        
             ]);
        }


    }
*/

    /**
     * Show the form for editing the specified Establecimientos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $establecimientos = $this->establecimientoRepository->findWithoutFail($id);

        if (empty($establecimientos)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimiento.index'));
        }

        $nivel_id=Nivel::pluck('descripcion','id');
        $region_id=Region::pluck('descripcion','id');
        $categoria_id=Categoria::pluck('descripcion','id');
        $tipo_establecimiento_id=TipoEstablecimiento::pluck('descripcion','id');
        $tipo_internamiento_id=TipoInternamiento::pluck('descripcion','id');
        
        $departamento = DB::table('departamentos')->get();
        $provincia = DB::table('provincias')->where('departamento_id',$establecimientos->departamento_id)->get();
        $distrito = DB::table('distritos')->where('departamento_id',$establecimientos->departamento_id)->where('provincia_id',$establecimientos->provincia_id)->get();
        
        $departamento_id=$establecimientos->departamento_id;        
        $provincia_id=$establecimientos->provincia_id;        
        $distrito_id=$establecimientos->distrito_id;        
        $disa_id=Disa::pluck('descripcion','id'); 

        $estado=$establecimientos->estado;  

        $tipo=2;//edicion

        return view('admin.establecimientos.edit')->with('establecimientos', $establecimientos)->with('nivel_id', $nivel_id)->with('region_id', $region_id)->with('categoria_id', $categoria_id)->with('tipo_establecimiento_id', $tipo_establecimiento_id)->with('tipo_internamiento_id', $tipo_internamiento_id)->with('departamento_id', $departamento_id)->with('provincia_id', $provincia_id)->with('distrito_id', $distrito_id)->with('disa_id', $disa_id)->with('departamento', $departamento)->with('provincia', $provincia)->with('distrito', $distrito)->with('estado', $estado)->with('tipo', $tipo);
    }

    /**
     * Update the specified Establecimientos in storage.
     *
     * @param  int              $id
     * @param UpdateEstablecimientoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEstablecimientoRequest $request)
    {
        $establecimientos = $this->establecimientoRepository->findWithoutFail($id);

        if (empty($establecimientos)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        $establecimientos = $this->establecimientoRepository->update($request->all(), $id);

        $estado = $request->input("estado");
        if($estado!="")$estado = 1;
        else $estado=0;

        //dd($establecimientos)
        $descripcion = $request->input("nombre_establecimiento");

        DB::table('establecimientos')
            ->where('id', $id )
            ->update([
                "estado"=>$estado,
                ]);

        /*MODIFICAR A TODO estimacion_servicio*/
        DB::table('consolidados')
            ->where('establecimiento_id', $id )
            ->update([
                "nombre_establecimiento"=>$descripcion
            ]);

        /*MODIFICAR A TODO estimacions*/
        DB::table('estimacions')
            ->where('establecimiento_id', $id )
            ->update([
                "nombre_establecimiento"=>$descripcion
            ]);
            
        /*MODIFICAR A TODO consolidados*/
        DB::table('estimacion_servicio')
            ->where('establecimiento_id', $id )
            ->update([
                "nombre_establecimiento"=>$descripcion
            ]);
        //$petitorio = $this->petitorioRepository->update($request->all(), $id);



        Flash::success('Establecimientos actualizado satisfactoriamente.');

        return redirect(route('establecimientos.index'));
    }

    /**
     * Remove the specified Establecimientos from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $establecimiento = $this->establecimientoRepository->findWithoutFail($id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimiento.index'));
        }

        $this->establecimientoRepository->delete($id);

        Flash::success('Establecimientos eliminado.');

        return redirect(route('establecimiento.index'));
    }
}
