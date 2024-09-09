<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateUnidadRequest;
use App\Http\Requests\UpdateUnidadRequest;
use App\Repositories\UnidadRepository;
use App\Repositories\DptoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use DB;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Establecimiento;
use App\Models\Division;
use App\Models\Unidad;
use App\Models\Servicio;
use Carbon\Carbon;    

class UnidadController extends AppBaseController
{
    /** @var  UnidadRepository */
    private $unidadRepository;
    private $dptoRepository;

    public function __construct(UnidadRepository $unidadRepo,DptoRepository $dptoRepo)
    {
        $this->unidadRepository = $unidadRepo;
        $this->dptoRepository = $dptoRepo;
    }

    /**
     * Display a listing of the Unidad.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->unidadRepository->pushCriteria(new RequestCriteria($request));
        $unidads = $this->unidadRepository->all();

        return view('admin.unidads.index')
            ->with('unidads', $unidads);
    }

    public function listar_unidad($division_id,$establecimiento_id)
    {
        
        $unidads = DB::table('unidads')->where('division_id',$division_id)->get();

        $establecimiento=DB::table('establecimientos')
                            ->where('id',$establecimiento_id)
                            ->get();
        $nombre_establecimiento=$establecimiento->get(0)->nombre_establecimiento;

        $divisiones=DB::table('divisions')
                            ->where('id',$division_id)
                            ->get();
        $nombre_division=$divisiones->get(0)->nombre_division;
        
        return view('admin.unidads.index')
            ->with('division_id', $division_id)
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $nombre_establecimiento)
            ->with('nombre_division', $nombre_division)
            ->with('unidads', $unidads);
            //->with('unidad', $unidads);
    }
    /**
     * Show the form for creating a new Unidad.
     *
     * @return Response
     
    public function create($division_id)
    {
        $establecimiento=DB::table('divisions')
                        ->where('id',$division_id)
                        ->get();
        $establecimiento_id=$establecimiento->get(0)->establecimiento_id;       
        
        return view('admin.unidads.create')
                ->with('establecimiento_id', $establecimiento_id)
                ->with('division_id', $division_id);
    }
    */
    public function create()
    {
        return view('admin.unidads.create');
    }
    /**
     * Store a newly created Unidad in storage.
     *
     * @param CreateUnidadRequest $request
     *
     * @return Response
     */
    public function store(CreateUnidadRequest $request)
    {
        $input = $request->all();

        $unidad = $this->unidadRepository->create($input);

        Flash::success('Unidad guardado correctamente.');

        //return redirect(route('unidads.listar_unidad',['division_id'=>$request->division_id,'establecimiento_id'=>$request->establecimiento_id]));
        return redirect(route('unidads.index'));
    }

    /**
     * Display the specified Unidad.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $unidad = $this->unidadRepository->findWithoutFail($id);

        if (empty($unidad)) {
            Flash::error('Unidad no encontrado');

            return redirect(route('unidads.index'));
        }

        return view('admin.unidads.show')->with('unidad', $unidad);
    }

    /**
     * Show the form for editing the specified Unidad.
     *
     * @param  int $id
     *
     * @return Response
     */
/*public function edit($id)
    {
        $unidad = $this->unidadRepository->findWithoutFail($id);
        $establecimiento=DB::table('divisions')
                            ->where('id',$division_id)
                            ->get();
        $establecimiento_id=$establecimiento->get(0)->establecimiento_id;
        if (empty($unidad)) {
            Flash::error('Unidad no encontrado');

            return redirect(route('unidads.index'));
        }

        return view('admin.unidads.edit')
                    ->with('unidad', $unidad)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('division_id', $division_id);
    }
*/
    public function edit($id)
    {
        $unidad = $this->unidadRepository->findWithoutFail($id);
        
        if (empty($unidad)) {
            Flash::error('Unidad no encontrado');

            return redirect(route('unidads.index'));
        }

        return view('admin.unidads.edit')
                    ->with('unidad', $unidad);
    }
    /**
     * Update the specified Unidad in storage.
     *
     * @param  int              $id
     * @param UpdateUnidadRequest $request
     *
     * @return Response
  
    public function update($id, UpdateUnidadRequest $request)
    {
        $unidad = $this->unidadRepository->findWithoutFail($id);

        if (empty($unidad)) {
            Flash::error('Unidad no encontrado');

            return redirect(route('unidads.index'));
        }

        $unidad = $this->unidadRepository->update($request->all(), $id);

        $establecimiento=DB::table('divisions')
                            ->where('id',$request->division_id)
                            ->get();
        $establecimiento_id=$establecimiento->get(0)->establecimiento_id;


        Flash::success('Unidad actualizado correctamente.');

        return redirect(route('unidads.listar_unidad',['division_id'=>$request->division_id,'establecimiento_id'=>$establecimiento_id]));

    }
*/
    public function update($id, UpdateUnidadRequest $request)
    {
        $unidad = $this->unidadRepository->findWithoutFail($id);

        if (empty($unidad)) {
            Flash::error('Unidad no encontrado');

            return redirect(route('unidads.index'));
        }

        $unidad = $this->unidadRepository->update($request->all(), $id);

        Flash::success('Unidad actualizado correctamente.');

        return redirect(route('unidads.index'));

    }

    /**
     * Remove the specified Unidad from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $unidad = $this->unidadRepository->findWithoutFail($id);

        if (empty($unidad)) {
            Flash::error('Unidad no encontrado');

            return redirect(route('unidads.index'));
        }

        $this->unidadRepository->delete($id);

        Flash::success('Unidad eliminado correctamente.');

        return redirect(route('unidads.index'));
    }

    //////////////////////////////////////////////////////////////////////////////////////////////7
    public function ver_servicios(Request $request,$dpto_id,$div_id,$establecimiento_id)
    {

        ///mostrar todos los petitorios del establecimiento
        //$unidad = $this->unidadRepository->findWithoutFail($unidad_id);
        $unidad=DB::table('dpto')
                    ->where('id',$dpto_id)
                    ->get();
        
        if (empty($unidad)) {
            Flash::error('Departamento no encontrado');

            return redirect(route('unidads.index'));
        }

        //dd($dpto_id);

        if($establecimiento_id==1 || $establecimiento_id==2 || $establecimiento_id==3 || $establecimiento_id==30 || $establecimiento_id==69){

                $servicios = DB::table('serv')
                                ->where('dpto_id',$dpto_id)
                                ->get();

                $establecimiento = Establecimiento::find($establecimiento_id);

                $divisions=DB::table('division_establecimiento')
                            ->where('id',$div_id)
                            ->get();

                return view('admin.unidads.servicios.ver_servicios')
                    ->with('servicios', $servicios)
                    ->with('nombre_unidad', $unidad->get(0)->nombre_unidad)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nombre_division', $divisions->get(0)->nombre_division)            
                    ->with('division_id', $div_id)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('unidad_id', $dpto_id);
        }
        else
        {
            Flash::error('Establecimiento no corresponde ver servicios');
            return redirect(route('establecimientos.index'));
        }            
    }

    ////////////////////
    public function asignar_servicios($dpto_id,$div_id,$establecimiento_id)
    {
        //encontrar el establecimiento que se encuentra para asignar
        $dptos=DB::table('dpto')
                    ->where('id',$dpto_id)
                    ->get();

        $regresar=$dptos->get(0)->id;
        
        $unidads = $this->unidadRepository->findWithoutFail($dptos->get(0)->unidad_id);

        //si encontramos el establecimiento buscado
        if (empty($unidads)) {
            Flash::error('Departamento no encontrado');

            return redirect(route('unidads.index'));
        }

        /////////////////////////////////////////////////
        $establecimiento = Establecimiento::find($establecimiento_id);
        if (empty($establecimiento)) {
            Flash::error('Establecimiento no encontrado');

            return redirect(route('unidads.index'));
        }

        $divisions=DB::table('division_establecimiento')
                            ->where('id',$div_id)
                            ->get();

        //si encontramos el establecimiento buscado
        if (empty($divisions)) {
            Flash::error('División no encontrado');

            return redirect(route('divisions.index'));
        }

        if($establecimiento_id==1 || $establecimiento_id==2 || $establecimiento_id==3 || $establecimiento_id==30 || $establecimiento_id==69){

            //Buscamos todos los departamentos y lo convertimos en arreglo
            $servicios=Servicio::pluck('nombre_servicio','id')->toArray();

            $consulta_servicios = DB::table('serv')
                        ->join('dpto','dpto.id','serv.dpto_id')
                        ->join('division_establecimiento','division_establecimiento.id','dpto.division_establecimiento_id')
                        ->where('division_establecimiento.establecimiento_id',$establecimiento_id)
                        ->orderby('serv.nombre_servicio','asc');         
            
                //pasamos a un arreglo
            $consulta = $consulta_servicios->pluck('nombre_servicio','servicio_id')->toArray();
            //dd($consulta);

            //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
            $descripcion=array_diff($servicios,$consulta);

            //dd($consulta);
            return view('admin.unidads.servicios.asignar_servicios')    
                    ->with('nombre_unidad', $dptos->get(0)->nombre_unidad)
                    ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                    ->with('nombre_division', $divisions->get(0)->nombre_division)            
                    ->with('descripcion', $descripcion)
                    ->with('dpto_id', $dpto_id)
                    ->with('regresar', $regresar)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('division_id', $div_id)
                    ->with('unidads', $unidads);    
        }
        else
        {
            Flash::error('Establecimiento no corresponde asignar servicios');
            return redirect(route('establecimientos.index'));
        }    
    }

    public function guardar_servicios(Request $request, $dpto_id,$div_id,$establecimiento_id)
    {
        
        $dptos=DB::table('dpto')
                    ->where('id',$dpto_id)
                    ->get();

        $num_cans = DB::table('cans')->where('active', TRUE)->count();
        
        //$unidad = $this->dptoRepository->findWithoutFail($dpto_id);
        $unidad = $this->unidadRepository->findWithoutFail($dptos->get(0)->unidad_id);

        //Si se ha digitado en la url un establecimiento que no corresponde
        if (empty($unidad)) {
            Flash::error('Departamento no encontrado');

            return redirect(route('unidads.index'));
        }

        $servicio = Servicio::find($request->descripcion);

        if (empty($servicio)) {
                Flash::error('Servicio no encontrado');

                return redirect(route('unidads.index'));
            }

        $nombre_servicio=$servicio->nombre_servicio;
        $codigo=$servicio->codigo;
        $servicio_id=$request->descripcion;

        DB::table('serv')
            ->insert([
                        'servicio_id' => $servicio_id,
                        'codigo' => $codigo,
                        'dpto_id'=>$dpto_id,
                        'nombre_servicio'=>$nombre_servicio,
                        'created_at'=>Carbon::now(),                        
             ]);  

            
            $contar_medicamentos=DB::table('petitorio_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('tipo_dispositivo_medico_id',1)
                                ->count();
            
            $contar_dispositivos=DB::table('petitorio_servicio')                
                                ->where('servicio_id',$servicio_id)
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
                //buscamos el ultimo can            
                $cans=DB::table('cans')->orderBy('id', 'desc')->first();

                DB::table('can_servicio')
                            ->insert([
                                'dpto_id' => $dpto_id,
                                'servicio_id' => $servicio_id,
                                'can_id' => $cans->id,
                                'establecimiento_id' => $establecimiento_id,
                                'medicamento_cerrado' => $valor_medicamento,
                                'dispositivo_cerrado' => $valor_dispositivo,
                                'created_at'=>Carbon::now(),
                                'updated_at'=>Carbon::now()
                            ]);                   
            }

        Flash::success('Servicios guardado correctamente.');
        return redirect(route('unidads.ver_servicios',[$dpto_id,$div_id,$establecimiento_id]));
    }

///////////////////////////////  34/9/26/3
    public function eliminar_servicio($id,$dpto_id,$div_id,$establecimiento_id)
    {

        $busca_servicio = DB::table('serv')
                        ->where('id',$id)
                        ->get();
        $num_cans = DB::table('cans')->where('active', TRUE)->count();

        $ervicio_id=$busca_servicio->get(0)->servicio_id;

        $servicio = DB::table('serv')
                        ->where('id',$id)
                        ->delete();

        if($num_cans>0)
            {
                $cans=DB::table('cans')->orderBy('id', 'desc')->first();
                DB::table('can_servicio')
                        ->where('servicio_id',$ervicio_id)
                        ->where('dpto_id',$dpto_id)
                        ->where('can_id',$cans->id)
                        ->delete();
            }

        Flash::success('Borrado correctamente.');

        return redirect(route('unidads.ver_servicios',[$dpto_id,$div_id,$establecimiento_id]));
             
    }
    
}
