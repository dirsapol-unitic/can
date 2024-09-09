<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Repositories\CategoriaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CategoriaController extends AppBaseController
{
    
    private $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepo)
    {
        $this->categoriaRepository = $categoriaRepo;
    }

    public function index(Request $request)
    {
        $this->categoriaRepository->pushCriteria(new RequestCriteria($request));
        $categorias = $this->categoriaRepository->all();

        return view('admin.categorias.index')
            ->with('categorias', $categorias);
    }
    
    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(CreateCategoriaRequest $request)
    {
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:categorias'
        ]);

        $input = $request->all();

        $categoria = $this->categoriaRepository->create($input);

        Flash::success('Categoria guardado correctamente.');

        return redirect(route('categorias.index'));
    }

    public function show($id)
    {
        $categoria = $this->categoriaRepository->findWithoutFail($id);

        if (empty($categoria)) {
            Flash::error('Categoria no encontrado');

            return redirect(route('categorias.index'));
        }

        return view('admin.categorias.show')->with('categoria', $categoria);
    }

    public function edit($id)
    {
        $categoria = $this->categoriaRepository->findWithoutFail($id);

        if (empty($categoria)) {
            Flash::error('Categoria no encontrado');

            return redirect(route('categorias.index'));
        }

        return view('admin.categorias.edit')->with('categoria', $categoria);
    }

    public function update($id, UpdateCategoriaRequest $request)
    {
        //dd($id);
        //validar requerido y unico
        $this->validate($request, [
            'descripcion'=>'required|unique:categorias,descripcion,'.$id
            //$this->route('categoria')
        ]);

        $categoria = $this->categoriaRepository->findWithoutFail($id);

        if (empty($categoria)) {
            Flash::error('Categoria no encontrado');

            return redirect(route('categorias.index'));
        }

        $categoria = $this->categoriaRepository->update($request->all(), $id);
        Flash::success('Categoria actualizado satisfactoriamente.');
        return redirect(route('categorias.index'));
    }

    public function destroy($id)
    {
        $categoria = $this->categoriaRepository->findWithoutFail($id);

        if (empty($categoria)) {
            Flash::error('Categoria no encontrado');

            return redirect(route('categorias.index'));
        }

        $this->categoriaRepository->delete($id);
        Flash::success('Categoria eliminado correctamente.');
        return redirect(route('categorias.index'));
    }
}
