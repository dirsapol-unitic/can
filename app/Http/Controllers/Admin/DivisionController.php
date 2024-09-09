<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use App\Repositories\DivisionRepository;
use App\Repositories\DivRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use DB;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Establecimiento;
use App\Models\Division;
use App\Models\Unidad;
use Carbon\Carbon;      

class DivisionController extends AppBaseController
{
    /** @var  DivisionRepository */
    private $divisionRepository;
    private $divRepository;

    public function __construct(DivisionRepository $divisionRepo,DivRepository $divRepo)
    {
        $this->divisionRepository = $divisionRepo;
        $this->divRepository = $divRepo;
    }

    /**
     * Display a listing of the Division.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->divisionRepository->pushCriteria(new RequestCriteria($request));
        $divisions = $this->divisionRepository->all();

        return view('admin.divisions.index')
            ->with('divisions', $divisions);
    }

    public function listar_division($establecimiento_id)
    {
        
        $divisions = DB::table('divisions')->where('establecimiento_id',$establecimiento_id)->get();

        $establecimiento=DB::table('establecimientos')
                            ->where('id',$establecimiento_id)
                            ->get();
        $nombre_establecimiento=$establecimiento->get(0)->nombre_establecimiento;


        return view('admin.divisions.index')
            ->with('establecimiento_id', $establecimiento_id)
            ->with('nombre_establecimiento', $nombre_establecimiento)
            ->with('divisions', $divisions);
    }

    /**
     * Show the form for creating a new Division.
     *
     * @return Response
    
    public function create($establecimiento_id)
    {   
        return view('admin.divisions.create')
                ->with('establecimiento_id', $establecimiento_id);
    }
    */

    public function create()
    {   
        return view('admin.divisions.create');
    }
    /**
     * Store a newly created Division in storage.
     *
     * @param CreateDivisionRequest $request
     *
     * @return Response
     */
    public function store(CreateDivisionRequest $request)
    {
        $input = $request->all();
        //$nombre_division = $request->input("nombre_division");  
        //dd($request->establecimiento_id);
        $division = $this->divisionRepository->create($input);

/*        $division = DB::table('divisions')
                        ->insert([  "nombre_division"=>$nombre_division,
                                    "created_at"=>Carbon::now(),
                                    "updated_at"=>Carbon::now()]
                        );
*/

        Flash::success('Division guardado correctamente.');

        //return redirect(route('divisions.listar_division',['establecimiento_id'=>$request->establecimiento_id]));
        return redirect(route('divisions.index'));
    }

    /**
     * Display the specified Division.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $division = $this->divisionRepository->findWithoutFail($id);


        if (empty($division)) {
            Flash::error('Division no encontrado');

            return redirect(route('divisions.index'));
        }

        return view('admin.divisions.show')->with('division', $division);
    }

    /**
     * Show the form for editing the specified Division.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $division = $this->divisionRepository->findWithoutFail($id);

        if (empty($division)) {
            Flash::error('Division no encontrado');

            return redirect(route('divisions.index'));
        }

        return view('admin.divisions.edit')->with('division', $division);
    }

    /**
     * Update the specified Division in storage.
     *
     * @param  int              $id
     * @param UpdateDivisionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDivisionRequest $request)
    {
        $division = $this->divisionRepository->findWithoutFail($id);

        if (empty($division)) {
            Flash::error('Division no encontrado');

            return redirect(route('divisions.index'));
        }

        $division = $this->divisionRepository->update($request->all(), $id);

        Flash::success('Division actualizado correctamente.');

        return redirect(route('divisions.index'));
    }

    /**
     * Remove the specified Division from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $division = $this->divisionRepository->findWithoutFail($id);

        if (empty($division)) {
            Flash::error('Division no encontrado');

            return redirect(route('divisions.index'));
        }

        $this->divisionRepository->delete($id);

        Flash::success('Division eliminado correctamente.');

        return redirect(route('divisions.index'));
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
public function ver_departamentos(Request $request,$div_id,$establecimiento_id)
    {
        $divisions=DB::table('division_establecimiento')
                            ->where('id',$div_id)
                            ->get();

        if (empty($divisions)) {
            Flash::error('División no encontrado');

            return redirect(route('divisions.index'));
        }

        if($establecimiento_id==1 || $establecimiento_id==2 || $establecimiento_id==3 || $establecimiento_id==30 || $establecimiento_id==69){

            //$departamentos = Division::find($division_id)->unidads;
            $departamentos = DB::table('dpto')
                                ->where('division_establecimiento_id',$div_id)
                                ->get();
            $establecimiento = Establecimiento::find($establecimiento_id);

            return view('admin.divisions.unidads.ver_departamentos')
                ->with('unidads', $departamentos)
                ->with('nombre_division', $divisions->get(0)->nombre_division)
                ->with('division_id', $div_id)
                ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                ->with('establecimiento_id', $establecimiento_id);
        }
        else
        {
            Flash::error('Establecimiento no corresponde ver departamentos');
            return redirect(route('establecimientos.index'));
        }        
            
    }

    ////////////////////
    public function asignar_departamentos($div_id,$establecimiento_id)
    {
        //encontrar el establecimiento que se encuentra para asignar
        $divisions = $this->divRepository->findWithoutFail($div_id);
        //$divisions = DB::table('division_establecimiento')->where('division_id',$div_id)->get();

        //si encontramos el establecimiento buscado
        if (empty($divisions)) {
            Flash::error('División no encontrado');

            return redirect(route('divisions.index'));
        }

        if($establecimiento_id==1 || $establecimiento_id==2 || $establecimiento_id==3 || $establecimiento_id==30 || $establecimiento_id==69){

                $establecimiento = Establecimiento::find($establecimiento_id);

                    //Buscamos todos los departamentos y lo convertimos en arreglo
                    $unidads=Unidad::pluck('nombre_unidad','id')->toArray();

                    //consultamos las unidades que no estan registrados en el dpto
                    $consulta_unidades = DB::table('dpto')
                        ->join('division_establecimiento','division_establecimiento.id','dpto.division_establecimiento_id')
                        ->where('division_establecimiento.establecimiento_id',$establecimiento_id)
                        ->orderby('dpto.nombre_unidad','asc');                        
                    
                        //pasamos a un arreglo
                        $consulta = $consulta_unidades->pluck('nombre_unidad','unidad_id')->toArray();
                //dd($consulta);

                    //Comparamos los dos arreglos y cargamos los que no estan ingresados en la bd
                    $descripcion=array_diff($unidads,$consulta);
                //dd($descripcion);

                return view('admin.divisions.unidads.asignar_departamentos')
                            ->with('divisions',$divisions)
                            ->with('nombre_division', $divisions->nombre_division)
                            ->with('nombre_establecimiento', $establecimiento->nombre_establecimiento)
                            ->with('establecimiento_id',$establecimiento_id)
                            ->with('descripcion',$descripcion)
                            ->with('division_id',$div_id);             
        }
        else
        {
            Flash::error('Establecimiento no corresponde ver servicios');
            return redirect(route('establecimientos.index'));
        }
    }

    ////////////////
    public function guardar_departamentos(Request $request, $div_id,$establecimiento_id)
    {
        
            //Busco la division correspondiente
            $divisions = $this->divRepository->findWithoutFail($div_id);

            //Si se ha digitado en la url una division que no corresponde
            if (empty($divisions)) {
                Flash::error('División no encontrado');

                return redirect(route('divisions.index'));
            }

            $unidad = Unidad::find($request->descripcion);

            //Si se ha digitado en la url una division que no corresponde
            if (empty($unidad)) {
                Flash::error('Unidad no encontrado');

                return redirect(route('divisions.index'));
            }

            $nombre_unidad=$unidad->nombre_unidad;
            $unidad_id=$request->descripcion;

            DB::table('dpto')
                    ->insert([
                                'division_establecimiento_id' => $div_id,
                                'unidad_id'=>$unidad_id,
                                'nombre_unidad'=>$nombre_unidad,
                                'created_at'=>Carbon::now(),
                     ]);

        Flash::success('Departamento guardado correctamente.');
           
        return redirect(route('divisions.ver_departamentos',[$div_id,$establecimiento_id]));
    }

    public function eliminar_unidad($id,$div_id,$establecimiento_id)
    {
        //borrar primero los servicios
        $servicio = DB::table('serv')
                        ->where('dpto_id',$id)
                        ->delete();

        //borrar las unidades
        $unidad = DB::table('dpto')
                        ->where('id',$id)
                        ->delete();
        

        Flash::success('Borrado correctamente.');

        return redirect(route('divisions.ver_departamentos',[$div_id,$establecimiento_id]));             
    }

}
