<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateTipoUsoRequest;
use App\Http\Requests\UpdateTipoUsoRequest;
use App\Repositories\TipoUsoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TipoUsoController extends AppBaseController
{
    /** @var  TipoUsoRepository */
    private $tipoUsoRepository;

    public function __construct(TipoUsoRepository $tipoUsoRepo)
    {
        $this->tipoUsoRepository = $tipoUsoRepo;
    }

    /**
     * Display a listing of the TipoUso.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tipoUsoRepository->pushCriteria(new RequestCriteria($request));
        $tipoUsos = $this->tipoUsoRepository->all();

        return view('admin.tipo_usos.index')
            ->with('tipoUsos', $tipoUsos);
    }

    /**
     * Show the form for creating a new TipoUso.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.tipo_usos.create');
    }

    /**
     * Store a newly created TipoUso in storage.
     *
     * @param CreateTipoUsoRequest $request
     *
     * @return Response
     */
    public function store(CreateTipoUsoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_usos'
        ]);

        $input = $request->all();

        $tipoUso = $this->tipoUsoRepository->create($input);

        Flash::success('Tipo Uso guardado con éxito.');

        return redirect(route('tipoUsos.index'));
    }

    /**
     * Display the specified TipoUso.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tipoUso = $this->tipoUsoRepository->findWithoutFail($id);

        if (empty($tipoUso)) {
            Flash::error('Tipo Uso no encontrado');

            return redirect(route('tipoUsos.index'));
        }

        return view('admin.tipo_usos.show')->with('tipoUso', $tipoUso);
    }

    /**
     * Show the form for editing the specified TipoUso.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tipoUso = $this->tipoUsoRepository->findWithoutFail($id);

        if (empty($tipoUso)) {
            Flash::error('Tipo Uso no encontrado');

            return redirect(route('tipoUsos.index'));
        }

        return view('admin.tipo_usos.edit')->with('tipoUso', $tipoUso);
    }

    /**
     * Update the specified TipoUso in storage.
     *
     * @param  int              $id
     * @param UpdateTipoUsoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTipoUsoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_usos'
        ]);

        $tipoUso = $this->tipoUsoRepository->findWithoutFail($id);

        if (empty($tipoUso)) {
            Flash::error('Tipo Uso no encontrado');

            return redirect(route('tipoUsos.index'));
        }

        $tipoUso = $this->tipoUsoRepository->update($request->all(), $id);

        Flash::success('Tipo Uso actualizado.');

        return redirect(route('tipoUsos.index'));
    }

    /**
     * Remove the specified TipoUso from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tipoUso = $this->tipoUsoRepository->findWithoutFail($id);

        if (empty($tipoUso)) {
            Flash::error('Tipo Uso no encontrado');

            return redirect(route('tipoUsos.index'));
        }

        $this->tipoUsoRepository->delete($id);

        Flash::success('Tipo Uso eliminado.');

        return redirect(route('tipoUsos.index'));
    }
}
