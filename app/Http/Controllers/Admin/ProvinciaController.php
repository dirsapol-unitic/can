<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateProvinciaRequest;
use App\Http\Requests\UpdateProvinciaRequest;
use App\Repositories\ProvinciaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Provincia;
use DB;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ProvinciaController extends AppBaseController
{
    /** @var  ProvinciaRepository */
    private $provinciaRepository;

    public function __construct(ProvinciaRepository $provinciaRepo)
    {
        $this->provinciaRepository = $provinciaRepo;
    }

    /**
     * Display a listing of the Provincia.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->provinciaRepository->pushCriteria(new RequestCriteria($request));
        $provincias = $this->provinciaRepository->all();

        return view('admin.provincias.index')
            ->with('provincias', $provincias);
    }

    /**
     * Show the form for creating a new Provincia.
     *
     * @return Response
     */
    public function create()
    {
        $departamento_id=Departamento::pluck('nombre_dpto','id');
        
        return view('admin.provincias.create',compact(["departamento_id"]));
    }

    /**
     * Store a newly created Provincia in storage.
     *
     * @param CreateProvinciaRequest $request
     *
     * @return Response
     */
    public function store(CreateProvinciaRequest $request)
    {
        $input = $request->all();

        $provincia = $this->provinciaRepository->create($input);

        Flash::success('Provincia guardado correctamente.');

        return redirect(route('provincias.index'));
    }

    /**
     * Display the specified Provincia.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $provincia = $this->provinciaRepository->findWithoutFail($id);

        if (empty($provincia)) {
            Flash::error('Provincia no encontrado');

            return redirect(route('provincias.index'));
        }

        return view('admin.provincias.show')->with('provincia', $provincia);
    }

    /**
     * Show the form for editing the specified Provincia.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $provincia = $this->provinciaRepository->findWithoutFail($id);

        if (empty($provincia)) {
            Flash::error('Provincia no encontrado');

            return redirect(route('provincias.index'));
        }

        $departamento_id=Departamento::pluck('nombre_dpto','id');

        return view('admin.provincias.edit')->with('provincia', $provincia)->with('departamento_id', $departamento_id);
    }

    /**
     * Update the specified Provincia in storage.
     *
     * @param  int              $id
     * @param UpdateProvinciaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProvinciaRequest $request)
    {
        $provincia = $this->provinciaRepository->findWithoutFail($id);

        if (empty($provincia)) {
            Flash::error('Provincia no encontrado');

            return redirect(route('provincias.index'));
        }

        $provincia = $this->provinciaRepository->update($request->all(), $id);

        Flash::success('Provincia actualizado satisfactoriamente.');

        return redirect(route('provincias.index'));
    }

    /**
     * Remove the specified Provincia from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $provincia = $this->provinciaRepository->findWithoutFail($id);

        if (empty($provincia)) {
            Flash::error('Provincia no encontrado');

            return redirect(route('provincias.index'));
        }

        $this->provinciaRepository->delete($id);

        Flash::success('Provincia eliminado.');

        return redirect(route('provincias.index'));
    }

    public function cargarprovincias($id)
    {
        $model_provincias= new Provincia();
        $provincias = $model_provincias->getProvincia($id);

        return $provincias;
    }
}
