<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFemaRequest;
use App\Http\Requests\UpdateFemaRequest;
use App\Repositories\FemaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Unidad;
use App\Models\Establecimiento;
use App\Models\Petitorio;
use App\Models\Fema;
use DB;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FemaController extends AppBaseController
{
    /** @var  FemaRepository */
    private $femaRepository;

    public function __construct(FemaRepository $femaRepo)
    {
        $this->femaRepository = $femaRepo;
    }

    /**
     * Display a listing of the Fema.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->femaRepository->pushCriteria(new RequestCriteria($request));
        $femas = $this->femaRepository->all();

        return view('femas.index')
            ->with('femas', $femas);
    }

    /**
     * Show the form for creating a new Fema.
     *
     * @return Response
     */
    public function create()
    {
        return view('femas.create');
    }

    /**
     * Store a newly created Fema in storage.
     *
     * @param CreateFemaRequest $request
     *
     * @return Response
     */
    public function store(CreateFemaRequest $request)
    {
        $input = $request->all();

        $fema = $this->femaRepository->create($input);

        Flash::success('Fema saved successfully.');

        return redirect(route('femas.index'));
    }

    /**
     * Display the specified Fema.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fema = $this->femaRepository->findWithoutFail($id);

        if (empty($fema)) {
            Flash::error('Fema not found');

            return redirect(route('femas.index'));
        }

        return view('femas.show')->with('fema', $fema);
    }

    /**
     * Show the form for editing the specified Fema.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fema = $this->femaRepository->findWithoutFail($id);

        if (empty($fema)) {
            Flash::error('Fema not found');

            return redirect(route('femas.index'));
        }

        return view('femas.edit')->with('fema', $fema);
    }

    /**
     * Update the specified Fema in storage.
     *
     * @param  int              $id
     * @param UpdateFemaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFemaRequest $request)
    {
        $fema = $this->femaRepository->findWithoutFail($id);

        if (empty($fema)) {
            Flash::error('Fema not found');

            return redirect(route('femas.index'));
        }

        $fema = $this->femaRepository->update($request->all(), $id);

        Flash::success('Fema updated successfully.');

        return redirect(route('femas.index'));
    }

    /**
     * Remove the specified Fema from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fema = $this->femaRepository->findWithoutFail($id);

        if (empty($fema)) {
            Flash::error('Fema not found');

            return redirect(route('femas.index'));
        }

        $this->femaRepository->delete($id);

        Flash::success('Fema deleted successfully.');

        return redirect(route('femas.index'));
    }

    
/////////////////////////////////////////////////////////////////////////////
         //////mostrar medicamentos 
    public function listar_dispositivos(Request $request,$establecimiento_id)
    {
        ///mostrar todos los petitorios del establecimiento
        $establecimientos = Fema::find($establecimiento_id);

        if (empty($establecimientos)) {
            Flash::error('Fema no encontrado');

            return redirect(route('femas.index'));
        }

        $nivel_id=$establecimientos->nivel_id;

        $petitorios = DB::table('femas')
                ->join('fema_petitorio', 'fema_petitorio.establecimiento_id', '=', 'femas.establecimiento_id')
                  ->join('petitorios', 'fema_petitorio.petitorio_id', '=', 'petitorios.id')
                  ->where('fema_petitorio.establecimiento_id',$establecimiento_id)
                  ->where('tipo_dispositivo_medicos_id',1)
                  ->get();


        return view('admin.femas.dispositivos.ver_dispositivos')            
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nivel_id', $nivel_id)
            ->with('femas', $femas)
            ->with('petitorios', $petitorios);
                    
    }

    public function guardar_dispositivos(Request $request, $establecimiento_id)
    {
        
        $femas = Establecimiento::find($establecimiento_id);

        if (empty($femas)) {
            Flash::error('Fema no encontrado');
            return redirect(route('femas.listar_dispositivos'));
        }
        
        //Buscar todos los medicamentos
        $num_dispositivos=DB::table('fema_petitorio')
                            ->where('establecimiento_id',$establecimiento_id)
                            ->count();        
        
        if (empty($request->petitorios)) {
            Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
            
        }else
        {
            //Si esta vacio y no ha sido asignado ningun valor aun a la bd
            if($num_dispositivos==0){
                $femas->petitorios()->sync($request->petitorios); 
            }
            else
            {               

                $dispositivos_bd=DB::table('fema_petitorio')
                                ->where('establecimiento_id',$establecimiento_id)
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
                    $femas->femas()->attach($request->petitorios); //attach       
                }
                else
                {
                    ///eliminamos los que no estan en los checkbox
                    $femas->femas()->detach($dispositivos_desmarcado); //attach       
                    ///Insertamos los nuevos
                    $femas->femas()->attach($dispositivos_nuevos); //attach       

                }    

            }

            foreach($request->petitorios as $key => $value){
                $consulta=DB::table('petitorios')->where('id',$value)->get();
                $tipo_dispositivo=$consulta->get(0)->tipo_dispositivo_medicos_id;

                DB::table('fema_petitorio')
                ->where('petitorio_id', $value)
                ->update([
                            'tipo_dispositivo_medico_id' => $tipo_dispositivo,
                            
                 ]);
            }

            Flash::success('Fema guardado correctamente.');    
        }
        
        //dd($request->petitorios);
        
        foreach($request->petitorios as $key => $dispositivo){

                    $distribucion_update=DB::table('fema_petitorio')
                                            ->where('establecimiento_id',$establecimiento_id)
                                            ->where('petitorio_id',$dispositivo)
                                            ->update(['tipo_dispositivo_medico_id'=>2]);
        }        
      

        return redirect(route('femas.listar_dispositivos',[$establecimiento_id]));

    }

    public function asignar_dispositivos($establecimiento_id)
    {

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



        return view('admin.femas.dispositivos.asignar_dispositivos')
            ->with('establecimiento_id', $establecimiento_id)
            ->with('establecimiento', $establecimiento)
            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
            ->with('petitorios', $petitorios);

    }
}
