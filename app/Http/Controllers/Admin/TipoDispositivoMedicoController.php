<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateTipoDispositivoMedicoRequest;
use App\Http\Requests\UpdateTipoDispositivoMedicoRequest;
use App\Repositories\TipoDispositivoMedicoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;


class TipoDispositivoMedicoController extends AppBaseController
{
    /** @var  TipoDispositivoMedicoRepository */
    private $tipoDispositivoMedicoRepository;

    public function __construct(TipoDispositivoMedicoRepository $tipoDispositivoMedicoRepo)
    {
        $this->tipoDispositivoMedicoRepository = $tipoDispositivoMedicoRepo;
    }

    /**
     * Display a listing of the TipoDispositivoMedico.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tipoDispositivoMedicoRepository->pushCriteria(new RequestCriteria($request));
        $tipoDispositivoMedicos = $this->tipoDispositivoMedicoRepository->all();

        return view('admin.tipo_dispositivo_medicos.index')
            ->with('tipoDispositivoMedicos', $tipoDispositivoMedicos);
    }

    /**
     * Show the form for creating a new TipoDispositivoMedico.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.tipo_dispositivo_medicos.create');
    }

    /**
     * Store a newly created TipoDispositivoMedico in storage.
     *
     * @param CreateTipoDispositivoMedicoRequest $request
     *
     * @return Response
     */
    public function store(CreateTipoDispositivoMedicoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_dispositivo_medicos'
        ]);
        
        $input = $request->all();

        $tipoDispositivoMedico = $this->tipoDispositivoMedicoRepository->create($input);

        Flash::success('Tipo Dispositivo Medico guardado correctamente.');

        return redirect(route('tipoDispositivoMedicos.index'));
    }

    /**
     * Display the specified TipoDispositivoMedico.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tipoDispositivoMedico = $this->tipoDispositivoMedicoRepository->findWithoutFail($id);

        if (empty($tipoDispositivoMedico)) {
            Flash::error('Tipo Dispositivo Medico no encontrado');

            return redirect(route('tipoDispositivoMedicos.index'));
        }

        return view('admin.tipo_dispositivo_medicos.show')->with('tipoDispositivoMedico', $tipoDispositivoMedico);
    }

    /**
     * Show the form for editing the specified TipoDispositivoMedico.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tipoDispositivoMedico = $this->tipoDispositivoMedicoRepository->findWithoutFail($id);

        if (empty($tipoDispositivoMedico)) {
            Flash::error('Tipo Dispositivo Medico no encontrado');

            return redirect(route('tipoDispositivoMedicos.index'));
        }

        return view('admin.tipo_dispositivo_medicos.edit')->with('tipoDispositivoMedico', $tipoDispositivoMedico);
    }

    /**
     * Update the specified TipoDispositivoMedico in storage.
     *
     * @param  int              $id
     * @param UpdateTipoDispositivoMedicoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTipoDispositivoMedicoRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:tipo_dispositivo_medicos'
        ]);

        $tipoDispositivoMedico = $this->tipoDispositivoMedicoRepository->findWithoutFail($id);

        if (empty($tipoDispositivoMedico)) {
            Flash::error('Tipo Dispositivo Medico no encontrado');

            return redirect(route('tipoDispositivoMedicos.index'));
        }

        $tipoDispositivoMedico = $this->tipoDispositivoMedicoRepository->update($request->all(), $id);

        Flash::success('Tipo Dispositivo Medico actualizado correctamente.');

        return redirect(route('tipoDispositivoMedicos.index'));
    }

    /**
     * Remove the specified TipoDispositivoMedico from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tipoDispositivoMedico = $this->tipoDispositivoMedicoRepository->findWithoutFail($id);

        if (empty($tipoDispositivoMedico)) {
            Flash::error('Tipo Dispositivo Medico no encontrado');

            return redirect(route('tipoDispositivoMedicos.index'));
        }

        $this->tipoDispositivoMedicoRepository->delete($id);

        Flash::success('Tipo Dispositivo Medico eliminado satisfactoriamente.');

        return redirect(route('tipoDispositivoMedicos.index'));
    }
}
