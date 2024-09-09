<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateUnidadMedidaRequest;
use App\Http\Requests\UpdateUnidadMedidaRequest;
use App\Repositories\UnidadMedidaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UnidadMedidaController extends AppBaseController
{
    /** @var  UnidadMedidaRepository */
    private $unidadMedidaRepository;

    public function __construct(UnidadMedidaRepository $unidadMedidaRepo)
    {
        $this->unidadMedidaRepository = $unidadMedidaRepo;
    }

    /**
     * Display a listing of the UnidadMedida.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->unidadMedidaRepository->pushCriteria(new RequestCriteria($request));
        $unidadMedidas = $this->unidadMedidaRepository->all();

        return view('admin.unidad_medidas.index')
            ->with('unidadMedidas', $unidadMedidas);
    }

    /**
     * Show the form for creating a new UnidadMedida.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.unidad_medidas.create');
    }

    /**
     * Store a newly created UnidadMedida in storage.
     *
     * @param CreateUnidadMedidaRequest $request
     *
     * @return Response
     */
    public function store(CreateUnidadMedidaRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:unidad_medidas'

        ]);

        $input = $request->all();

        $unidadMedida = $this->unidadMedidaRepository->create($input);

        Flash::success('Unidad Medida guardada satisfactoriamente.');

        return redirect(route('unidadMedidas.index'));
    }

    /**
     * Display the specified UnidadMedida.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $unidadMedida = $this->unidadMedidaRepository->findWithoutFail($id);

        if (empty($unidadMedida)) {
            Flash::error('Unidad Medida no encontrada');

            return redirect(route('unidadMedidas.index'));
        }

        return view('admin.unidad_medidas.show')->with('unidadMedida', $unidadMedida);
    }

    /**
     * Show the form for editing the specified UnidadMedida.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $unidadMedida = $this->unidadMedidaRepository->findWithoutFail($id);

        if (empty($unidadMedida)) {
            Flash::error('Unidad Medida no encontrada');

            return redirect(route('unidadMedidas.index'));
        }

        return view('admin.unidad_medidas.edit')->with('unidadMedida', $unidadMedida);
    }

    /**
     * Update the specified UnidadMedida in storage.
     *
     * @param  int              $id
     * @param UpdateUnidadMedidaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUnidadMedidaRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:unidad_medidas'
        ]);

        $unidadMedida = $this->unidadMedidaRepository->findWithoutFail($id);

        if (empty($unidadMedida)) {
            Flash::error('Unidad Medida no encontrada');

            return redirect(route('unidadMedidas.index'));
        }

        $unidadMedida = $this->unidadMedidaRepository->update($request->all(), $id);

        Flash::success('Unidad Medida actualizada satisfactoriamente.');

        return redirect(route('unidadMedidas.index'));
    }

    /**
     * Remove the specified UnidadMedida from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $unidadMedida = $this->unidadMedidaRepository->findWithoutFail($id);

        if (empty($unidadMedida)) {
            Flash::error('Unidad Medida no encontrada');

            return redirect(route('unidadMedidas.index'));
        }

        $this->unidadMedidaRepository->delete($id);

        Flash::success('Unidad Medida eliminada satisfactoriamente.');

        return redirect(route('unidadMedidas.index'));
    }

    
}
