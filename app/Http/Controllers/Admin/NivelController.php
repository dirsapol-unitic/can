<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateNivelRequest;
use App\Http\Requests\UpdateNivelRequest;
use App\Repositories\NivelRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class NivelController extends AppBaseController
{
    /** @var  NivelRepository */
    private $nivelRepository;

    public function __construct(NivelRepository $nivelRepo)
    {
        $this->nivelRepository = $nivelRepo;
    }

    /**
     * Display a listing of the Nivel.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->nivelRepository->pushCriteria(new RequestCriteria($request));
        $nivels = $this->nivelRepository->all();

        return view('admin.nivels.index')
            ->with('nivels', $nivels);
    }

    /**
     * Show the form for creating a new Nivel.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.nivels.create');
    }

    /**
     * Store a newly created Nivel in storage.
     *
     * @param CreateNivelRequest $request
     *
     * @return Response
     */
    public function store(CreateNivelRequest $request)
    {
        
        //dd($request->all());
        //dd($this->route('nivel'));
        //validar nivel
        $this->validate($request, [
            'descripcion'=>'required|unique:nivels'
        ]);


        $input = $request->all();

        $nivel = $this->nivelRepository->create($input);

        Flash::success('Nivel guardado satisfactoriamente.');

        return redirect(route('nivels.index'));
    }

    /**
     * Display the specified Nivel.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $nivel = $this->nivelRepository->findWithoutFail($id);

        if (empty($nivel)) {
            Flash::error('Nivel no encontrado');

            return redirect(route('nivels.index'));
        }

        return view('admin.nivels.show')->with('nivel', $nivel);
    }

    /**
     * Show the form for editing the specified Nivel.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        

        $nivel = $this->nivelRepository->findWithoutFail($id);

        if (empty($nivel)) {
            Flash::error('Nivel no encontrado');

            return redirect(route('nivels.index'));
        }

        return view('admin.nivels.edit')->with('nivel', $nivel);
    }

    /**
     * Update the specified Nivel in storage.
     *
     * @param  int              $id
     * @param UpdateNivelRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNivelRequest $request)
    {
       // dd($request->route('nivel'));

       
        $nivel = Nivel::find($id);
        // Redirect to department list if updating department wasn't existed
        if ($nivel == null || count($nivel) == 0) {
            return redirect()->intended('/system-management/department');
        }



        $nivel = $this->nivelRepository->findWithoutFail($id);

        if (empty($nivel)) {
            Flash::error('Nivel no encontrado');

            return redirect(route('nivels.index'));
        }

        $nivel = $this->nivelRepository->update($request->all(), $id);

        Flash::success('Nivel actualizado satisfactoriamente.');

        return redirect(route('nivels.index'));
    }

    /**
     * Remove the specified Nivel from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $nivel = $this->nivelRepository->findWithoutFail($id);

        if (empty($nivel)) {
            Flash::error('Nivel no encontrado');

            return redirect(route('nivels.index'));
        }

        $this->nivelRepository->delete($id);

        Flash::success('Nivel eliminado con éxito.');

        return redirect(route('nivels.index'));
    }
    public function medicamentos(){

        return $this->hasMany('App\Models\Medicamentos','id');
    }
}
