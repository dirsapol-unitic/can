<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateDepartamentoRequest;
use App\Http\Requests\UpdateDepartamentoRequest;
use App\Repositories\DepartamentoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Models\Provincia;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DepartamentoController extends AppBaseController
{
    /** @var  DepartamentoRepository */
    private $departamentoRepository;

    public function __construct(DepartamentoRepository $departamentoRepo)
    {
        $this->departamentoRepository = $departamentoRepo;
    }

    /**
     * Display a listing of the Departamento.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->departamentoRepository->pushCriteria(new RequestCriteria($request));
        $departamentos = $this->departamentoRepository->all();

        return view('admin.departamentos.index')
            ->with('departamentos', $departamentos);
    }

    /**
     * Show the form for creating a new Departamento.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.departamentos.create');
    }

    /**
     * Store a newly created Departamento in storage.
     *
     * @param CreateDepartamentoRequest $request
     *
     * @return Response
     */
    public function store(CreateDepartamentoRequest $request)
    {
        $input = $request->all();

        $departamento = $this->departamentoRepository->create($input);

        Flash::success('Departamento guardado correctamente.');

        return redirect(route('departamentos.index'));
    }

    /**
     * Display the specified Departamento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $departamento = $this->departamentoRepository->findWithoutFail($id);

        if (empty($departamento)) {
            Flash::error('Departamento no encontrado');

            return redirect(route('departamentos.index'));
        }

        return view('admin.departamentos.show')->with('departamento', $departamento);
    }

    /**
     * Show the form for editing the specified Departamento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $departamento = $this->departamentoRepository->findWithoutFail($id);

        if (empty($departamento)) {
            Flash::error('Departamento no encontrado');

            return redirect(route('departamentos.index'));
        }

        return view('admin.departamentos.edit')->with('departamento', $departamento);
    }

    /**
     * Update the specified Departamento in storage.
     *
     * @param  int              $id
     * @param UpdateDepartamentoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDepartamentoRequest $request)
    {
        $departamento = $this->departamentoRepository->findWithoutFail($id);

        if (empty($departamento)) {
            Flash::error('Departamento no encontrado');

            return redirect(route('departamentos.index'));
        }

        $departamento = $this->departamentoRepository->update($request->all(), $id);

        Flash::success('Departamento actualizado satisfactoriamente.');

        return redirect(route('departamentos.index'));
    }

    /**
     * Remove the specified Departamento from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $departamento = $this->departamentoRepository->findWithoutFail($id);

        if (empty($departamento)) {
            Flash::error('Departamento no encontrado');

            return redirect(route('departamentos.index'));
        }

        $this->departamentoRepository->delete($id);

        Flash::success('Departamento eliminado.');

        return redirect(route('departamentos.index'));
    }

}
