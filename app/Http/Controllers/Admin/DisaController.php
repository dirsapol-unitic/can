<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateDisaRequest;
use App\Http\Requests\UpdateDisaRequest;
use App\Repositories\DisaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DisaController extends AppBaseController
{
    /** @var  DisaRepository */
    private $disaRepository;

    public function __construct(DisaRepository $disaRepo)
    {
        $this->disaRepository = $disaRepo;
    }

    /**
     * Display a listing of the Disa.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->disaRepository->pushCriteria(new RequestCriteria($request));
        $disas = $this->disaRepository->all();

        return view('admin.disas.index')
            ->with('disas', $disas);
    }

    /**
     * Show the form for creating a new Disa.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.disas.create');
    }

    /**
     * Store a newly created Disa in storage.
     *
     * @param CreateDisaRequest $request
     *
     * @return Response
     */
    public function store(CreateDisaRequest $request)
    {
        $input = $request->all();

        $disa = $this->disaRepository->create($input);

        Flash::success('DISA guardado correctamente.');

        return redirect(route('disas.index'));
    }

    /**
     * Display the specified Disa.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $disa = $this->disaRepository->findWithoutFail($id);

        if (empty($disa)) {
            Flash::error('DISA no encontrada');

            return redirect(route('disas.index'));
        }

        return view('admin.disas.show')->with('disa', $disa);
    }

    /**
     * Show the form for editing the specified Disa.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $disa = $this->disaRepository->findWithoutFail($id);

        if (empty($disa)) {
            Flash::error('DISA no encontrada');

            return redirect(route('disas.index'));
        }

        return view('admin.disas.edit')->with('disa', $disa);
    }

    /**
     * Update the specified Disa in storage.
     *
     * @param  int              $id
     * @param UpdateDisaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDisaRequest $request)
    {
        $disa = $this->disaRepository->findWithoutFail($id);

        if (empty($disa)) {
            Flash::error('DISA no encontrada');

            return redirect(route('disas.index'));
        }

        $disa = $this->disaRepository->update($request->all(), $id);

        Flash::success('DISA actualizada satisfactoriamente.');

        return redirect(route('disas.index'));
    }

    /**
     * Remove the specified Disa from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $disa = $this->disaRepository->findWithoutFail($id);
        if (empty($disa)) {
            Flash::error('DISA no encontrada');

            return redirect(route('disas.index'));
        }
        $this->disaRepository->delete($id);
        Flash::success('DISA eliminado correctamente.');
        return redirect(route('disas.index'));
    }
}
