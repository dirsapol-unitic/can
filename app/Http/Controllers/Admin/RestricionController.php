<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateRestricionRequest;
use App\Http\Requests\UpdateRestricionRequest;
use App\Repositories\RestricionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RestricionController extends AppBaseController
{
    /** @var  RestricionRepository */
    private $restricionRepository;

    public function __construct(RestricionRepository $restricionRepo)
    {
        $this->restricionRepository = $restricionRepo;
    }

    /**
     * Display a listing of the Restricion.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->restricionRepository->pushCriteria(new RequestCriteria($request));
        $restricions = $this->restricionRepository->all();

        return view('admin.restricions.index')
            ->with('restricions', $restricions);
    }

    /**
     * Show the form for creating a new Restricion.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.restricions.create');
    }

    /**
     * Store a newly created Restricion in storage.
     *
     * @param CreateRestricionRequest $request
     *
     * @return Response
     */
    public function store(CreateRestricionRequest $request)
    {
        $input = $request->all();

        $restricion = $this->restricionRepository->create($input);

        Flash::success('Restricción guardado.');

        return redirect(route('restricions.index'));
    }

    /**
     * Display the specified Restricion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $restricion = $this->restricionRepository->findWithoutFail($id);

        if (empty($restricion)) {
            Flash::error('Restricción no encontrada');

            return redirect(route('restricions.index'));
        }

        return view('admin.restricions.show')->with('restricion', $restricion);
    }

    /**
     * Show the form for editing the specified Restricion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $restricion = $this->restricionRepository->findWithoutFail($id);

        if (empty($restricion)) {
            Flash::error('Restricción no encontrada');

            return redirect(route('restricions.index'));
        }

        return view('admin.restricions.edit')->with('restricion', $restricion);
    }

    /**
     * Update the specified Restricion in storage.
     *
     * @param  int              $id
     * @param UpdateRestricionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRestricionRequest $request)
    {
        $restricion = $this->restricionRepository->findWithoutFail($id);

        if (empty($restricion)) {
            Flash::error('Restricción no encontrada');

            return redirect(route('restricions.index'));
        }

        $restricion = $this->restricionRepository->update($request->all(), $id);

        Flash::success('Restricción actualizado correctamente.');

        return redirect(route('restricions.index'));
    }

    /**
     * Remove the specified Restricion from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $restricion = $this->restricionRepository->findWithoutFail($id);

        if (empty($restricion)) {
            Flash::error('Restricción no encontrada');

            return redirect(route('restricions.index'));
        }

        $this->restricionRepository->delete($id);

        Flash::success('Restriccion borrado correctamente.');

        return redirect(route('restricions.index'));
    }
}
