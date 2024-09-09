<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateTipoEstablecimientoRequest;
use App\Http\Requests\UpdateTipoEstablecimientoRequest;
use App\Repositories\TipoEstablecimientoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TipoEstablecimientoController extends AppBaseController
{
    /** @var  TipoEstablecimientoRepository */
    private $tipoEstablecimientoRepository;

    public function __construct(TipoEstablecimientoRepository $tipoEstablecimientoRepo)
    {
        $this->tipoEstablecimientoRepository = $tipoEstablecimientoRepo;
    }

    /**
     * Display a listing of the TipoEstablecimiento.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tipoEstablecimientoRepository->pushCriteria(new RequestCriteria($request));
        $tipoEstablecimientos = $this->tipoEstablecimientoRepository->all();

        return view('admin.tipo_establecimientos.index')
            ->with('tipoEstablecimientos', $tipoEstablecimientos);
    }

    /**
     * Show the form for creating a new TipoEstablecimiento.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.tipo_establecimientos.create');
    }

    /**
     * Store a newly created TipoEstablecimiento in storage.
     *
     * @param CreateTipoEstablecimientoRequest $request
     *
     * @return Response
     */
    public function store(CreateTipoEstablecimientoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_establecimientos'
        ]);

        $input = $request->all();

        $tipoEstablecimiento = $this->tipoEstablecimientoRepository->create($input);

        Flash::success('Tipo Establecimiento guardado correctamente.');

        return redirect(route('tipoEstablecimientos.index'));
    }

    /**
     * Display the specified TipoEstablecimiento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tipoEstablecimiento = $this->tipoEstablecimientoRepository->findWithoutFail($id);

        if (empty($tipoEstablecimiento)) {
            Flash::error('Tipo Establecimiento no encontrado');

            return redirect(route('tipoEstablecimientos.index'));
        }

        return view('admin.tipo_establecimientos.show')->with('tipoEstablecimiento', $tipoEstablecimiento);
    }

    /**
     * Show the form for editing the specified TipoEstablecimiento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tipoEstablecimiento = $this->tipoEstablecimientoRepository->findWithoutFail($id);

        if (empty($tipoEstablecimiento)) {
            Flash::error('Tipo Establecimiento no encontrado');

            return redirect(route('tipoEstablecimientos.index'));
        }

        return view('admin.tipo_establecimientos.edit')->with('tipoEstablecimiento', $tipoEstablecimiento);
    }

    /**
     * Update the specified TipoEstablecimiento in storage.
     *
     * @param  int              $id
     * @param UpdateTipoEstablecimientoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTipoEstablecimientoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_establecimientos'
        ]);

        $tipoEstablecimiento = $this->tipoEstablecimientoRepository->findWithoutFail($id);

        if (empty($tipoEstablecimiento)) {
            Flash::error('Tipo Establecimiento no encontrado');

            return redirect(route('tipoEstablecimientos.index'));
        }

        $tipoEstablecimiento = $this->tipoEstablecimientoRepository->update($request->all(), $id);

        Flash::success('Tipo Establecimiento actualizado correctamente.');

        return redirect(route('tipoEstablecimientos.index'));
    }

    /**
     * Remove the specified TipoEstablecimiento from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tipoEstablecimiento = $this->tipoEstablecimientoRepository->findWithoutFail($id);

        if (empty($tipoEstablecimiento)) {
            Flash::error('Tipo Establecimiento no encontrado');

            return redirect(route('tipoEstablecimientos.index'));
        }

        $this->tipoEstablecimientoRepository->delete($id);

        Flash::success('Tipo Establecimiento deleted successfully.');

        return redirect(route('tipoEstablecimientos.index'));
    }
}
