<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateDistribucionRequest;
use App\Http\Requests\UpdateDistribucionRequest;
use App\Repositories\DistribucionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Petitorio;
use App\Models\Establecimiento;
use App\Models\Distribucion;
use DB;


class DistribucionController extends AppBaseController
{
    /** @var  DistribucionRepository */
    private $distribucionRepository;

    public function __construct(DistribucionRepository $distribucionRepo)
    {
        $this->distribucionRepository = $distribucionRepo;
    }

    /**
     * Display a listing of the Distribucion.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        /*$this->distribucionRepository->pushCriteria(new RequestCriteria($request));
        $distribucions = $this->distribucionRepository->all();

        return view('admin.distribucions.index')
            ->with('distribucions', $distribucions);*/

        return redirect(route('establecimientos.index'));
    }

    /**
     * Show the form for creating a new Distribucion.
     *
     * @return Response
     */
    public function create()
    {
        $establecimiento_id=Establecimiento::pluck('nombre_establecimiento','id');
        return view('admin.distribucions.create', compact(["establecimiento_id"]));
    }

    /**
     * Store a newly created Distribucion in storage.
     *
     * @param CreateDistribucionRequest $request
     *
     * @return Response
     */
    public function store(CreateDistribucionRequest $request)
    {
        
        $distribucions =DB::table('distribucions')
                    ->insert([
                        'descripcion'=>$request->descripcion,
                        'establecimiento_id'=>$request->establecimiento_id
                    ]);

        Flash::success('Distribución guardado correctamente.');
        
        return redirect(route('distribuciones.ver_distribucion',['establecimiento_id'=>$request->establecimiento_id,'nivel_id'=>$request->nivel]));
    }

    /**
     * Display the specified Distribucion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $distribucion = $this->distribucionRepository->findWithoutFail($id);

        if (empty($distribucion)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribucions.index'));
        }

        return view('admin.distribucions.show')->with('distribucion', $distribucion);
    }

    /**
     * Show the form for editing the specified Distribucion.
     *
     * @param  int $id
     *
     * @return Response 
     */
    public function edit($id)
    {
        $distribucion = $this->distribucionRepository->findWithoutFail($id);

        if (empty($distribucion)) {
            Flash::error('Distribución no encontrado');
            return redirect(route('distribucions.index'));
        }

        $establecimiento_id=$distribucion->establecimiento_id;
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel_id=$establecimiento->nivel_id;

        if (empty($establecimiento)) {
            Flash::error('Establecimiento no encontrada');
            return redirect(route('establecimientos.index'));
        }
        return view('admin.distribucions.edit')->with('distribucion', $distribucion)->with('establecimiento_id', $establecimiento_id)->with('nivel_id', $nivel_id);
    }

    /**
     * Update the specified Distribucion in storage.
     *
     * @param  int              $id
     * @param UpdateDistribucionRequest $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $distribucion = $this->distribucionRepository->findWithoutFail($id);

        if (empty($distribucion)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribuciones.index'));
        }

        DB::table('distribucions')
                ->where('id', $id)
                ->update([
                            'descripcion' => $request->descripcion,
                            'establecimiento_id' => $request->establecimiento_id,
                        ]);
           

        Flash::success('Distribucion actualizado correctamente.');

        return redirect(route('distribuciones.ver_distribucion',['establecimiento_id'=>$request->establecimiento_id,'nivel_id'=>$request->nivel]));
    }

    /**
     * Remove the specified Distribucion from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $distribucion = $this->distribucionRepository->findWithoutFail($id);

        if (empty($distribucion)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribuciones.index'));
        }

        $this->distribucionRepository->delete($id);

        Flash::success('Distribución borrado correctamente.');

        return redirect(route('distribuciones.index'));
    }

    /////////////////////////////////////////////////////////////////////////////
    public function ver_distribucion(Request $request,$establecimiento_id,$nivel_id)
    {
        if ($nivel_id == 1){

            //$farmacias = $this->farmaciaRepository->find($id_establecimiento);
            $establecimiento = Establecimiento::find($establecimiento_id);

            if (empty($establecimiento)) {
                Flash::error('Establecimiento no encontrada');

                return redirect(route('establecimientos.index'));
            }
            
            $distribucions = $this->distribucionRepository->all()->where('establecimiento_id',$establecimiento_id);

            return view('admin.distribucions.ver_distribucion')
                    ->with('distribucions', $distribucions)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nivel_id', $nivel_id);
        }
        else
        {
                Flash::error('No hay Distribución para esta IPRESS');

                return redirect(route('establecimientos.index'));
        }    
        
    }

    public function crear_distribucion($establecimiento_id,$nivel_id)
    {
        return view('admin.distribucions.create')
                ->with('establecimiento_id', $establecimiento_id)
                ->with('nivel_id', $nivel_id);
    }

    //////mostrar medicamentos 
    public function ver_medicamentos(Request $request,$establecimiento_id,$distribucion_id)
    {
        ///mostrar todos los petitorios del establecimiento
        $distribucions = $this->distribucionRepository->findWithoutFail($distribucion_id);

        if (empty($distribucions)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribuciones.index'));
        }

        ///mostrar todos los petitorios del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        $nivel_id=$establecimiento->nivel_id;

        $petitorios = DB::table('distribucions')
                ->join('distribucion_petitorio', 'distribucion_petitorio.distribucion_id', '=', 'distribucions.id')
                  ->join('petitorios', 'distribucion_petitorio.petitorio_id', '=', 'petitorios.id')
                  ->where('distribucion_petitorio.distribucion_id',$distribucion_id)
                  ->where('tipo_dispositivo_medicos_id',1)
                  ->get();

        return view('admin.distribucions.medicamentos.ver_medicamentos')
            ->with('petitorios', $petitorios)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nivel_id', $nivel_id)
            ->with('distribucions', $distribucions)
            ->with('descripcion', $distribucions->descripcion)
            ->with('distribucion_id', $distribucion_id);            
    }

    public function guardar_medicamentos(Request $request, $establecimiento_id,$distribucion_id)
    {
        
        $distribucions = $this->distribucionRepository->findWithoutFail($distribucion_id);

        if (empty($distribucions)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribucions.index'));
        }

        //Buscar todos los medicamentos
        $num_medicamentos=DB::table('distribucion_petitorio')
                            ->where('distribucion_id',$distribucion_id)
                            ->count();
        
        if (empty($request->petitorios)) {
            Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
            
        }else
        {

            //Si esta vacio y no ha sido asignado ningun valor aun a la bd
            if($num_medicamentos==0){
                $distribucions->petitorios()->sync($request->petitorios); 
            }
            else
            {
                $medicamentos_bd=DB::table('distribucion_petitorio')
                                ->where('distribucion_id',$distribucion_id)
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
                    $distribucions->petitorios()->attach($request->petitorios); //attach       
                }
                else
                {
                    ///eliminamos los que no estan en los checkbox
                    $distribucions->petitorios()->detach($medicamentos_desmarcado); //attach       
                    ///Insertamos los nuevos
                    $distribucions->petitorios()->attach($medicamentos_nuevos); //attach       

                }    
            }
        }    

        Flash::success('Distribución guardado correctamente.');

        return redirect(route('distribuciones.ver_medicamentos',[$establecimiento_id,$distribucion_id]));
    }

    public function asignar_medicamentos($establecimiento_id,$distribucion_id)
    {
        //mostrar todos los petitorios que sean del nivel del establecimiento
        $distribucions = Distribucion::find($distribucion_id);

        if (empty($distribucions)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribuciones.index'));
        }

        //mostrar todos los petitorios que sean del nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        $petitorios=Petitorio::where('tipo_dispositivo_medicos_id',1)
                        ->where('nivel_id','<=',$establecimiento->nivel_id)
                        ->pluck('descripcion','id');

        
        return view('admin.distribucions.medicamentos.asignar_medicamentos')
            ->with('distribucions', $distribucions)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('establecimiento', $establecimiento)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('petitorios', $petitorios);

    }

/////////////////////////////////////////////////////////////////////////////
         //////mostrar medicamentos 
    public function ver_dispositivos(Request $request,$establecimiento_id,$distribucion_id)
    {

        ///mostrar todos los petitorios del establecimiento
        $distribucions = $this->distribucionRepository->findWithoutFail($distribucion_id);

        if (empty($distribucions)) {
            Flash::error('Distribucion no encontrado');

            return redirect(route('distribuciones.index'));
        }

        ///mostrar todos los petitorios del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }

        $nivel_id=$establecimiento->nivel_id;

        $petitorios = DB::table('distribucions')
                ->join('distribucion_petitorio', 'distribucion_petitorio.distribucion_id', '=', 'distribucions.id')
                  ->join('petitorios', 'distribucion_petitorio.petitorio_id', '=', 'petitorios.id')
                  ->where('distribucion_petitorio.distribucion_id',$distribucion_id)
                  ->where('tipo_dispositivo_medicos_id','>',1)
                  ->get();

        return view('admin.distribucions.dispositivos.ver_dispositivos')
            ->with('petitorios', $petitorios)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nivel_id', $nivel_id)
            ->with('distribucions', $distribucions)
            ->with('descripcion', $distribucions->descripcion)
            ->with('distribucion_id', $distribucion_id);            
    }

    public function guardar_dispositivos(Request $request, $establecimiento_id,$distribucion_id)
    {
        
        $distribucions = $this->distribucionRepository->findWithoutFail($distribucion_id);

        if (empty($distribucions)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribuciones.index'));
        }

        //Buscar todos los medicamentos
        $num_dispositivos=DB::table('distribucion_petitorio')
                            ->where('distribucion_id',$distribucion_id)
                            ->count();        
        
        if (empty($request->petitorios)) {
            Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
            
        }else
        {
            //Si esta vacio y no ha sido asignado ningun valor aun a la bd
            if($num_dispositivos==0){
                $distribucions->petitorios()->sync($request->petitorios); 
            }
            else
            {               

                $dispositivos_bd=DB::table('distribucion_petitorio')
                                ->where('distribucion_id',$distribucion_id)
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
                    $distribucions->petitorios()->attach($request->petitorios); //attach       
                }
                else
                {
                    ///eliminamos los que no estan en los checkbox
                    $distribucions->petitorios()->detach($dispositivos_desmarcado); //attach       
                    ///Insertamos los nuevos
                    $distribucions->petitorios()->attach($dispositivos_nuevos); //attach       

                }    

            }

            foreach($request->petitorios as $key => $value){
                $consulta=DB::table('petitorios')->where('id',$value)->get();
                $tipo_dispositivo=$consulta->get(0)->tipo_dispositivo_medicos_id;

                DB::table('distribucion_petitorio')
                ->where('petitorio_id', $value)
                ->update([
                            'tipo_dispositivo_medico_id' => $tipo_dispositivo,
                            
                 ]);
            }

            Flash::success('Distribución guardado correctamente.');    
        }
        
        //dd($request->petitorios);
        
        foreach($request->petitorios as $key => $dispositivo){

                    $distribucion_update=DB::table('distribucion_petitorio')
                                            ->where('distribucion_id',$distribucion_id)
                                            ->where('petitorio_id',$dispositivo)
                                            ->update(['tipo_dispositivo_medico_id'=>2]);
        }        


        

        return redirect(route('distribuciones.ver_dispositivos',[$establecimiento_id,$distribucion_id]));

    }

    public function asignar_dispositivos($establecimiento_id,$distribucion_id)
    {
        //mostrar todos los petitorios que sean del nivel del establecimiento
        $distribucions = Distribucion::find($distribucion_id);

        if (empty($distribucions)) {
            Flash::error('Distribución no encontrado');

            return redirect(route('distribuciones.index'));
        }

        //mostrar todos los petitorios que sean del nivel del establecimiento
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');

            return redirect(route('establecimientos.index'));
        }    

        $petitorios=Petitorio::where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id','<=',$establecimiento->nivel_id)
                        ->get();
                        //->pluck('descripcion','id');

        return view('admin.distribucions.dispositivos.asignar_dispositivos')
            ->with('distribucions', $distribucions)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('establecimiento', $establecimiento)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('petitorios', $petitorios);

    }

}
