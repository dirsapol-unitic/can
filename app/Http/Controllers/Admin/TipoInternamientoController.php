<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateTipoInternamientoRequest;
use App\Http\Requests\UpdateTipoInternamientoRequest;
use App\Repositories\TipoInternamientoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TipoInternamientoController extends AppBaseController
{
    /** @var  TipoInternamientoRepository */
    private $tipoInternamientoRepository;

    public function __construct(TipoInternamientoRepository $tipoInternamientoRepo)
    {
        $this->tipoInternamientoRepository = $tipoInternamientoRepo;
    }

    /**
     * Display a listing of the TipoInternamiento.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tipoInternamientoRepository->pushCriteria(new RequestCriteria($request));
        $tipoInternamientos = $this->tipoInternamientoRepository->all();

        return view('admin.tipo_internamientos.index')
            ->with('tipoInternamientos', $tipoInternamientos);
    }

    /**
     * Show the form for creating a new TipoInternamiento.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.tipo_internamientos.create');
    }

    /**
     * Store a newly created TipoInternamiento in storage.
     *
     * @param CreateTipoInternamientoRequest $request
     *
     * @return Response
     */
    public function store(CreateTipoInternamientoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_internamientos'
        ]);
        

        $input = $request->all();

        $tipoInternamiento = $this->tipoInternamientoRepository->create($input);

        Flash::success('Tipo Internamiento guardado correctamente.');

        return redirect(route('tipoInternamientos.index'));
    }

    /**
     * Display the specified TipoInternamiento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tipoInternamiento = $this->tipoInternamientoRepository->findWithoutFail($id);

        if (empty($tipoInternamiento)) {
            Flash::error('Tipo Internamiento no encontrado');

            return redirect(route('tipoInternamientos.index'));
        }

        return view('admin.tipo_internamientos.show')->with('tipoInternamiento', $tipoInternamiento);
    }

    /**
     * Show the form for editing the specified TipoInternamiento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tipoInternamiento = $this->tipoInternamientoRepository->findWithoutFail($id);

        if (empty($tipoInternamiento)) {
            Flash::error('Tipo Internamiento no encontrado');

            return redirect(route('tipoInternamientos.index'));
        }

        return view('admin.tipo_internamientos.edit')->with('tipoInternamiento', $tipoInternamiento);
    }

    /**
     * Update the specified TipoInternamiento in storage.
     *
     * @param  int              $id
     * @param UpdateTipoInternamientoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTipoInternamientoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_internamientos'
        ]);
        
        $tipoInternamiento = $this->tipoInternamientoRepository->findWithoutFail($id);

        if (empty($tipoInternamiento)) {
            Flash::error('Tipo Internamiento no encontrado');

            return redirect(route('tipoInternamientos.index'));
        }

        $tipoInternamiento = $this->tipoInternamientoRepository->update($request->all(), $id);

        Flash::success('Tipo Internamiento actualizado satisfactoriamente.');

        return redirect(route('tipoInternamientos.index'));
    }

    /**
     * Remove the specified TipoInternamiento from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tipoInternamiento = $this->tipoInternamientoRepository->findWithoutFail($id);

        if (empty($tipoInternamiento)) {
            Flash::error('Tipo Internamiento no encontrado');

            return redirect(route('tipoInternamientos.index'));
        }

        $this->tipoInternamientoRepository->delete($id);

        Flash::success('Tipo Internamiento eliminado correctamente.');

        return redirect(route('tipoInternamientos.index'));
    }
}
