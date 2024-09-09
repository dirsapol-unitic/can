<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateDistritoRequest;
use App\Http\Requests\UpdateDistritoRequest;
use App\Repositories\DistritoRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use DB;

class DistritoController extends AppBaseController
{
    /** @var  DistritoRepository */
    private $distritoRepository;

    public function __construct(DistritoRepository $distritoRepo)
    {
        $this->distritoRepository = $distritoRepo;
    }

    /**
     * Display a listing of the Distrito.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->distritoRepository->pushCriteria(new RequestCriteria($request));
        $distritos = $this->distritoRepository->all();

        return view('admin.distritos.index')
            ->with('distritos', $distritos);
    }

    /**
     * Show the form for creating a new Distrito.
     *
     * @return Response
     */
    public function create()
    {
        //$departamento_id=Departamento::pluck('nombre_dpto','id');
        $departamento = Departamento::pluck("nombre_dpto","id")->all();
        $provincia=Provincia::pluck('nombre_prov','id')->all();

        return view('admin.distritos.create',compact(["departamento","provincia"]));
    }

    /**
     * Store a newly created Distrito in storage.
     *
     * @param CreateDistritoRequest $request
     *
     * @return Response
     */
    public function store(CreateDistritoRequest $request)
    {
        $input = $request->all();

        $distrito = $this->distritoRepository->create($input);

        Flash::success('Distrito guardado correctamente.');

        return redirect(route('distritos.index'));
    }

    /**
     * Display the specified Distrito.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $distrito = $this->distritoRepository->findWithoutFail($id);

        if (empty($distrito)) {
            Flash::error('Distrito no encontrado');

            return redirect(route('distritos.index'));
        }

        return view('admin.distritos.show')->with('distrito', $distrito);
    }

    /**
     * Show the form for editing the specified Distrito.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $distrito = $this->distritoRepository->findWithoutFail($id);

        if (empty($distrito)) {
            Flash::error('Distrito no encontrado');

            return redirect(route('distritos.index'));
        }

        $departamento = Departamento::pluck("nombre_dpto","id")->all();
        $provincia=Provincia::pluck('nombre_prov','id')->all();
        
        return view('admin.distritos.edit')->with('distrito', $distrito)
                ->with('provincia', $provincia)
                ->with('departamento', $departamento);
    }

    /**
     * Update the specified Distrito in storage.
     *
     * @param  int              $id
     * @param UpdateDistritoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDistritoRequest $request)
    {
        $distrito = $this->distritoRepository->findWithoutFail($id);

        if (empty($distrito)) {
            Flash::error('Distrito no encontrado');

            return redirect(route('distritos.index'));
        }

        $distrito = $this->distritoRepository->update($request->all(), $id);

        Flash::success('Distrito actualizado satisfactoriamente.');

        return redirect(route('distritos.index'));
    }

    /**
     * Remove the specified Distrito from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $distrito = $this->distritoRepository->findWithoutFail($id);

        if (empty($distrito)) {
            Flash::error('Distrito no encontrado');

            return redirect(route('distritos.index'));
        }

        $this->distritoRepository->delete($id);

        Flash::success('Distrito eliminado.');

        return redirect(route('distritos.index'));
    }

    public function getProvincias(Request $request, $id,$departamento_id){
    
        if($request->ajax()){
            $provincias = Provincia::provincias($departamento_id);
            return response()->json($provincias);
        }
    }


    public function getMiProvincias(Request $request, $departamento_id){
        
        if($request->ajax()){
            $provincias = Provincia::provincias($departamento_id);
            return response()->json($provincias);
        }
    }

    public function cargardistritos($id_dpto, $id_prov)
    {
        $model_distritos= new Distrito();
        $distritos = $model_distritos->getDistrito($id_dpto, $id_prov);

        return $distritos;
    }
}
