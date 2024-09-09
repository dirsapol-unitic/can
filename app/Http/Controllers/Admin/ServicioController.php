<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use App\Repositories\ServicioRepository;
use App\Repositories\ServRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Petitorio;
use App\Models\Unidad;
use App\Models\Establecimiento;
use App\Models\Servicio;
use App\Models\Especialidad;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;

class ServicioController extends AppBaseController
{
    /** @var  ServicioRepository */
    private $servicioRepository;
    private $servRepository;

    public function __construct(ServicioRepository $servicioRepo,ServRepository $servRepo)
    {
        $this->servicioRepository = $servicioRepo;
        $this->servRepository = $servRepo;
    }

    /**
     * Display a listing of the Servicio.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->servicioRepository->pushCriteria(new RequestCriteria($request));
        $servicios = $this->servicioRepository->all();

        return view('admin.servicios.index')
            ->with('servicios', $servicios);
    }

    /**
     * Show the form for creating a new Servicio.
     *
     * @return Response
     */
       public function create()
    {
        
        return view('admin.servicios.create');
    }

    /**
     * Store a newly created Servicio in storage.
     *
     * @param CreateServicioRequest $request
     *
     * @return Response
     */
    public function store(CreateServicioRequest $request)
    {
        $input = $request->all();

        $servicio = $this->servicioRepository->create($input);

        Flash::success('Servicio guardado satsifactoriamente.');

        return redirect(route('servicios.index'));
    }

    /**
     * Display the specified Servicio.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $servicio = $this->servicioRepository->findWithoutFail($id);

        if (empty($servicio)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }

        return view('admin.servicios.show')->with('servicio', $servicio);
    }

    /**
     * Show the form for editing the specified Servicio.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $servicio = $this->servicioRepository->findWithoutFail($id);

        if (empty($servicio)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }

        return view('admin.servicios.edit')->with('servicio', $servicio);

    }

    /**
     * Update the specified Servicio in storage.
     *
     * @param  int              $id
     * @param UpdateServicioRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServicioRequest $request)
    {
        $servicio = $this->servicioRepository->findWithoutFail($id);

        if (empty($servicio)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }

        $servicio = $this->servicioRepository->update($request->all(), $id);

        Flash::success('Servicio actualizado correctamente.');
        
        return redirect(route('servicios.index'));
    }

    /**
     * Remove the specified Servicio from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $servicio = $this->servicioRepository->findWithoutFail($id);

        if (empty($servicio)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }
        //$this->servicioRepository->delete($id);

        $elimina_bd=DB::table('serv')
                            ->where('servicio_id',$id)
                            ->delete();

        //Eliminamos datos de petitorio_rubro
        $elimina_bd=DB::table('petitorio_servicio')
                            ->where('servicio_id',$id)
                            ->delete();

        //elimino datos de can_rubro
        $elimina_bd=DB::table('can_servicio')
                            ->where('servicio_id',$id)
                            ->delete();
        //actualizo datos de usuarios
        $update_bd=DB::table('users')
                            ->where('servicio_id',$id)
                            ->update(['servicio_id'=>0]);

        //actualizo datos de responsable
        $update_bd=DB::table('responsables')
                            ->where('servicio_id',$id)
                            ->update(['servicio_id'=>0]);

        //Eliminamos datos del rubro
        $elimina_bd=DB::table('servicios')
                            ->where('id',$id)
                            ->delete();        

        Flash::success('Servicio borrado correctamente.');

        return redirect(route('servicios.index'));
    }

     
/////////////////////////////////////////////////////////////////////////////
    public function listar_servicio(Request $request,$unidad_id,$division_id)
    {
            //$farmacias = $this->farmaciaRepository->find($id_establecimiento);
            $unidad = Unidad::find($unidad_id);

            if (empty($unidad)) {
                Flash::error('Unidad no encontrada');

                return redirect(route('establecimientos.index'));
            }
            
            $establecimientos = DB::table('establecimientos')
                ->join('divisions','divisions.establecimiento_id','establecimientos.id')
                ->where('divisions.id',$division_id)
                ->get();
            $nombre_establecimiento=$establecimientos->get(0)->nombre_establecimiento;                
            $nivel_id=$establecimientos->get(0)->nivel_id;
            $establecimiento_id=$establecimientos->get(0)->establecimiento_id;

            $servicios = $this->servicioRepository->all()->where('unidad_id',$unidad_id);

            return view('admin.servicios.index')
                    ->with('servicios', $servicios)
                    ->with('unidad_id', $unidad_id)
                    ->with('division_id', $division_id)
                    ->with('establecimiento_id', $establecimiento_id)
                    ->with('nombre_establecimiento', $nombre_establecimiento)
                    ->with('nivel_id', $nivel_id);
    }
 
    public function crear_servicio($unidad_id,$nivel_id,$division_id)
    {
        $unidades = Unidad::find($division_id);
        $especialidades=Especialidad::pluck('nombre_servicio','id');
        
        return view('admin.servicios.create')
                ->with('unidades', $unidades)
                ->with('especialidades', $especialidades)
                ->with('unidad_id', $unidad_id)
                ->with('division_id', $division_id);
    }

    //////mostrar medicamentos 
    public function ver_medicamentos(Request $request,$servicio_id)
    {

        ///mostrar todos los petitorios del establecimiento
        $servicios = $this->servicioRepository->findWithoutFail($servicio_id);

        if (empty($servicios)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }

        $petitorios = DB::table('servicios')
                    ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                    ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                    ->where('petitorio_servicio.servicio_id',$servicio_id)
                    ->where('tipo_dispositivo_medicos_id',1)
                    ->get();
        
        
        $num_productos = DB::table('servicios')
                    ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                    ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                    ->where('petitorio_servicio.servicio_id',$servicio_id)
                    ->where('tipo_dispositivo_medicos_id',1)
                    ->count();

        return view('admin.servicios.medicamentos.ver_medicamentos')
            ->with('petitorios', $petitorios)
            ->with('num_productos', $num_productos)
            ->with('servicios', $servicios)
            ->with('servicio_id', $servicio_id);            
    }

    public function guardar_medicamentos(Request $request, $servicio_id)
    {
        
            $servicio = $this->servicioRepository->findWithoutFail($servicio_id);

            if (empty($servicio)) {
                Flash::error('Servicio no encontrado');

                return redirect(route('servicios.index'));
            }

            //Buscar todos los medicamentos
        $num_medicamentos=DB::table('petitorio_servicio')
                                ->where('servicio_id',$servicio_id) //cambiar el nombre en la tabla
                                ->where('tipo_dispositivo_medico_id',1)
                                ->count();

        //Sino se ha marcado ningun checkbox
        if (empty($request->petitorios)) {
           if($num_medicamentos>0){
                $elimina_bd=DB::table('petitorio_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('tipo_dispositivo_medico_id',1)
                                ->delete();

                Flash::success('Dispositivos guardado correctamente.');    
           }
           else
           {
            Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
           }
        }else
        {
            //si esta vacio llenamos
            if($num_medicamentos==0){
                $servicio->petitorios()->attach($request->petitorios); 
                foreach($request->petitorios as $key => $medicamentos){
                    $petitorios=Petitorio::where('id',$medicamentos)->where('estado',1)->get();

                    $dispositivos_update=DB::table('petitorio_servicio')
                                            ->where('servicio_id',$servicio_id)
                                            ->where('petitorio_id',$medicamentos)
                                            ->update([
                                                        'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                        'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                        
                                                    ]);
                }

            }
            else
            {
                    $medicamentos_bd=DB::table('petitorio_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('tipo_dispositivo_medico_id',1)
                                ->get();

                //checkbox desmarcados
                $medicamentos_desmarcado=$medicamentos_bd->pluck('petitorio_id')->diff($request->petitorios);
                $num_medicamentos_desmarcado=count($medicamentos_bd->pluck('petitorio_id')->diff($request->petitorios));

                //convertimos a arreglo
                $arreglo = $medicamentos_bd->pluck('petitorio_id')->toArray();

                //los nuevos checkbox
                $num_medicamentos_nuevos=count(array_diff($request->petitorios,$arreglo));
                $medicamentos_nuevos=array_diff($request->petitorios,$arreglo);

                ///Insertamos los nuevos           
                if($num_medicamentos_nuevos>0){
                    $servicio->petitorios()->attach($medicamentos_nuevos); //attach  
                    foreach($medicamentos_nuevos as $key => $medicamentos){
                        $petitorios=Petitorio::where('id',$medicamentos)->where('estado',1)->get();
                        $dispositivos_update=DB::table('petitorio_servicio')
                                                ->where('servicio_id',$servicio_id)
                                                ->where('petitorio_id',$medicamentos)
                                                ->update([
                                                            'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                            'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                            
                                                        ]);
                    }   
                }
                ///eliminamos los que no estan en los checkbox
                if($num_medicamentos_desmarcado>0){
            
                    $servicio->petitorios()->detach($medicamentos_desmarcado); //attach           
                }
            }
            
            Flash::success('Dispositivos guardado correctamente.');    
        }
           

        return redirect(route('servicios.ver_medicamentos',[$servicio_id]));
    }

    public function asignar_medicamentos($servicio_id)
    {
        //mostrar todos los petitorios que sean del nivel del establecimiento
        //$servicios = $this->servicioRepository->findWithoutFail($servicio_id);
        $servicios = Servicio::find($servicio_id);

        if (empty($servicios)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }

        //$petitorios=Petitorio::where('tipo_dispositivo_medicos_id',1)
        //                     ->pluck('descripcion','id');

        $petitorios=Petitorio::where('tipo_dispositivo_medicos_id',1)->where('estado',1)->orderby('descripcion','asc')
                        ->get();

        return view('admin.servicios.medicamentos.asignar_medicamentos')
            ->with('servicios', $servicios)
            ->with('petitorios', $petitorios);


    }

/////////////////////////////////////////////////////////////////////////////
         //////mostrar medicamentos 
    public function ver_dispositivos(Request $request,$servicio_id)
    {

        ///mostrar todos los petitorios del establecimiento
        $servicios = $this->servicioRepository->findWithoutFail($servicio_id);

        if (empty($servicios)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }

        
        $petitorios = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->get();

        $num_productos = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->count();

        return view('admin.servicios.dispositivos.ver_dispositivos')
            ->with('num_productos', $num_productos)
            ->with('petitorios', $petitorios)
            ->with('servicios', $servicios)
            ->with('servicio_id', $servicio_id);     

    }

    public function insertar_servicio($ici_id)
    {
        $petitorios = DB::table('petitorios')
                    ->where('estado',1)
                    ->where( function ( $query )
                        {
                            $query->orWhere('tipo_dispositivo_medicos_id',2)
                                ->orWhere('tipo_dispositivo_medicos_id',7)
                                ->orWhere('tipo_dispositivo_medicos_id',3);
                        });                    
        
        //dd($petitorios);

        for ($servicio_id = 1; $servicio_id < 30; $servicio_id++){        
            foreach ($petitorios as $key => $petitorio) {
                DB::table('petitorio_servicio')
                        ->insert([
                                'petitorio_id' => $petitorio->id,
                                'servicio_id'=>$servicio_id,
                                'tipo_dispositivo_medicos_id'=>$petitorio->tipo_dispositivo_medicos_id,
                            ]);
            }
        } 
        return redirect(route('servicios.index'));
    }

    public function guardar_dispositivos2($servicio_id,$tipo_dispositivo_medicos_id)
    {
        
        
        //Buscar todos los medicamentos
        $num_medicamentos=DB::table('petitorio_servicio')
                            ->where('servicio_id',$servicio_id)
                            ->where('tipo_dispositivo_medico_id','>',1)
                            ->count();

        //dd($request->petitorios);
        //dd(request()->all());

        //Sino se ha marcado ningun checkbox
            //si esta vacio llenamos

                
                ///Insertamos los nuevos           
                if($num_medicamentos_nuevos>0){
                    $servicio->petitorios()->attach($medicamentos_nuevos); //attach 
                    foreach($medicamentos_nuevos as $key => $medicamentos){
                        $petitorios=Petitorio::where('id',$medicamentos)->where('estado',1)->get();

                        $dispositivos_update=DB::table('petitorio_servicio')
                                                ->where('servicio_id',$servicio_id)
                                                ->where('petitorio_id',$medicamentos)
                                                ->update([
                                                            'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                            'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                        ]);
                    }    
                }

            Flash::success('Dispositivos guardado correctamente.');         

            foreach($request->petitorios as $key => $dispositivo){

            $distribucion_update=DB::table('petitorio_servicio')
                                    ->where('servicio_id',$servicio_id)
                                    ->where('petitorio_id',$dispositivo)
                                    ->update(['tipo_dispositivo_medico_id'=>2]);
            }
        
        return redirect(route('servicios.ver_dispositivos',[$servicio_id]));
    }

    public function guardar_dispositivos(UpdateServicioRequest $request, $servicio_id)
    {
        
        $servicio = $this->servicioRepository->findWithoutFail($servicio_id);
        
        if (empty($servicio)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }

        //Buscar todos los medicamentos
        $num_medicamentos=DB::table('petitorio_servicio')
                            ->where('servicio_id',$servicio_id)
                            ->where('tipo_dispositivo_medico_id','>',1)
                            ->count();

        //dd($request->petitorios);
        //dd(request()->all());

        //Sino se ha marcado ningun checkbox
        if (empty($request->petitorios)) {
           if($num_medicamentos>0){
                $elimina_bd=DB::table('petitorio_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('tipo_dispositivo_medico_id','>',1)
                                ->delete();
                Flash::success('Dispositivos guardado correctamente.');    
           }
           else
           {
            Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
           }
        }else
        {
            //si esta vacio llenamos
            if($num_medicamentos==0){
                $servicio->petitorios()->attach($request->petitorios); 
                foreach($request->petitorios as $key => $medicamentos){
                    $petitorios=Petitorio::where('id',$medicamentos)->where('estado',1)->get();

                    $dispositivos_update=DB::table('petitorio_servicio')
                                            ->where('servicio_id',$servicio_id)
                                            ->where('petitorio_id',$medicamentos)
                                            ->update([
                                                        'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                        'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                        
                                                    ]);
                }
            }
            else
            {
                $medicamentos_bd=DB::table('petitorio_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('tipo_dispositivo_medico_id','>',1)
                                ->get();

                $num_medicamentos_bd=DB::table('petitorio_servicio')
                                ->where('servicio_id',$servicio_id)
                                ->where('tipo_dispositivo_medico_id','>',1)
                                ->count();

                //checkbox desmarcados
                $medicamentos_desmarcado=$medicamentos_bd->pluck('petitorio_id')->diff($request->petitorios);
                $num_medicamentos_desmarcado=count($medicamentos_bd->pluck('petitorio_id')->diff($request->petitorios));
                //convertimos a arreglo
                $arreglo = $medicamentos_bd->pluck('petitorio_id')->toArray();

                //los nuevos checkbox
                $num_medicamentos_nuevos=count(array_diff($request->petitorios,$arreglo));
                $medicamentos_nuevos=array_diff($request->petitorios,$arreglo);

                ///Insertamos los nuevos           
                if($num_medicamentos_nuevos>0){
                    $servicio->petitorios()->attach($medicamentos_nuevos); //attach 
                    foreach($medicamentos_nuevos as $key => $medicamentos){
                        $petitorios=Petitorio::where('id',$medicamentos)->where('estado',1)->get();

                        $dispositivos_update=DB::table('petitorio_servicio')
                                                ->where('servicio_id',$servicio_id)
                                                ->where('petitorio_id',$medicamentos)
                                                ->update([
                                                            'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                            'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                        ]);
                    }    
                }
                ///eliminamos los que no estan en los checkbox
                if($num_medicamentos_desmarcado>0){            
                    $servicio->petitorios()->detach($medicamentos_desmarcado); //attach           
                }
            }
            Flash::success('Dispositivos guardado correctamente.');         
            foreach($request->petitorios as $key => $dispositivo){

            $distribucion_update=DB::table('petitorio_servicio')
                                    ->where('servicio_id',$servicio_id)
                                    ->where('petitorio_id',$dispositivo)
                                    ->update(['tipo_dispositivo_medico_id'=>2]);
            }
        }    
        
        return redirect(route('servicios.ver_dispositivos',[$servicio_id]));
    }

    public function asignar_dispositivos($servicio_id)
    {
        //mostrar todos los petitorios que sean del nivel del establecimiento
        $servicios = Servicio::find($servicio_id);
        

        
        if (empty($servicios)) {
            Flash::error('Servicio no encontrado');

            return redirect(route('servicios.index'));
        }
 
        $petitorios=Petitorio::where('tipo_dispositivo_medicos_id','>',1)->where('estado',1)->orderby('descripcion','asc')
                        ->get();
        
        //dd($servicios->petitorios->pluck('id'));

        //
        return view('admin.servicios.dispositivos.asignar_dispositivos')
            ->with('servicios', $servicios)
            ->with('petitorios', $petitorios);
    }

    public function exportServicio($type,$servicio_id,$tipo)
    {

        switch ($tipo) {
            case '1': //medicamentos
                    $data = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->where('tipo_dispositivo_medicos_id',1)
                        ->get();

                    $num_data = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->where('tipo_dispositivo_medicos_id',1)
                        ->count();
                break;
            
            case '2': //dispositivos
                    $data = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->get();

                    $num_data = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->count();
                break;

            case '3': //todos
                    $data = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->get();                
                    $num_data = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->count();                
                break;
        }
        if($num_data>0){
        
            $archivo='Petitorio_2018_Servicio_de_'.$data->get(0)->nombre_servicio;
            return Excel::create($archivo, function($excel) use ($data) {
                $excel->sheet('mySheet', function($sheet) use ($data)
                {   
                    
                    //6 filas agrupadas con las columnas A,B,C
                    $sheet->setMergeColumn(array(
                        'columns' => array('B','K'),
                        'rows' => array(
                            array(1,4)                        
                        )
                    ));

                    //INSERTAR LOGOS
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo_sanidad.png')); //your image path
                    $objDrawing->setCoordinates('B1');
                    $objDrawing->setWorksheet($sheet);
                    
                    $objDrawing2 = new PHPExcel_Worksheet_Drawing;
                    $objDrawing2->setPath(public_path('img/logo_pnp.png')); //your image path
                    $objDrawing2->setCoordinates('K1');
                    $objDrawing2->setWorksheet($sheet);
        
                    $sheet->mergeCells('C1:J1');
                    $sheet->cell('C1', function($cell) {$cell->setValue(' PETITORIO ');  $cell->setFontSize(38); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle');
                    });


                    $sheet->setHeight(1, 10);
                    $sheet->setHeight(2, 10);
                    $sheet->setHeight(3, 10);
                    
                    $sheet->cell('A7', function($cell) {$cell->setValue('N°'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('B7', function($cell) {$cell->setValue('COD MED'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('C7', function($cell) {$cell->setValue('PRINCIPIO ACTIVO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('D7', function($cell) {$cell->setValue('CONCENT.'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('E7', function($cell) {$cell->setValue('FORM FARM');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                    
                    $sheet->cell('F7', function($cell) {$cell->setValue('PRES.'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('G7', function($cell) {$cell->setValue('UND. MEDIDA');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('H7', function($cell) {$cell->setValue('NIVEL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('I7', function($cell) {$cell->setValue('T. USO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                    
                    $sheet->cell('J7', function($cell) {$cell->setValue('TIPO DE MEDICAMENTO.'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->cell('K7', function($cell) {$cell->setValue('TRATAMIENTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                    $sheet->setWidth(array(
                        'A'     =>  5,
                        'B'     =>  10,
                        'C'     =>  100,
                        'D'     =>  12,
                        'E'     =>  12,
                        'F'     =>  12,
                        'G'     =>  12,
                        'H'     =>  12,
                        'I'     =>  12,
                        'J'     =>  35,
                        'K'     =>  35,                
                    ));

                    //ordenar
                    $k=1;

                    if (!empty($data)) {

                        foreach ($data as $key => $value) {
                            $i= $key+8;
                            $sheet->cell('A'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                            $sheet->cell('B'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                            $sheet->cell('C'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                            $sheet->cell('D'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                            $sheet->cell('E'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });    
                            $sheet->cell('F'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                            $sheet->cell('G'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                            $sheet->cell('H'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                            $sheet->cell('I'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                            $sheet->cell('J'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                            $sheet->cell('K'.$i, function($cell) {$cell->setBorder('thin', 'thin', 'thin', 'thin');  });
                            
                            
                            $sheet->cell('A'.$i, $k); 
                            $sheet->cell('B'.$i, $value->codigo_petitorio); //establecimiento 
                            $sheet->cell('C'.$i, $value->principio_activo); //items 
                            $sheet->cell('D'.$i, $value->concentracion); 
                            $sheet->cell('E'.$i, $value->form_farm); 
                            $sheet->cell('F'.$i, $value->presentacion); 
                            $sheet->cell('G'.$i, $value->descripcion_unidad_medida); 
                            $sheet->cell('H'.$i, $value->descripcion_nivel); 
                            $sheet->cell('I'.$i, $value->descripcion_tipo_uso); 
                            $sheet->cell('J'.$i, $value->descripcion_tipo_dispositivo); 
                            $sheet->cell('K'.$i, $value->descripcion_restriccion); 

                            $k++;
                    
                        }

                        
                    }
                    
                });
            })->download($type);
        }
        else
        {
            Flash::error('No hay productos asignados para este servicio');
            return redirect(route('servicios.index'));
        }
    }

    public function pdf($can_id,$establecimiento_id,$opt)
    {
        $establecimiento = Establecimiento::find($establecimiento_id);

        if (empty($establecimiento)) {
            Flash::error('Establecimientos no encontrado');
            return redirect(route('estimacion.index'));
        }

        $nivel=$establecimiento->nivel_id;
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;

        $servicio_id=Auth::user()->servicio_id;
        $nombre_servicio=Auth::user()->nombre_servicio;

        $can = Can::find($can_id);

        if (empty($can)) {
            Flash::error('CAN no encontrado');
            return redirect(route('estimacion.index'));
        }

        if($opt==1){
            $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
            $nombre_producto='Medicamentos';
        }else
            {   if ($opt==2) {
                    $data=DB::table('estimacions')
                    ->where('can_id',$can_id) //cambiar 22
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();                    

                    $nombre_producto='Dispositivos';
                }else
                {
                    Flash::error('Datos no son correctos, error al descargar archivo');
                    return redirect(route('estimacion.index'));  
                }
        }

        $pdf = \PDF::loadView('admin.cans.medicamentos.descargar_medicamentos_pdf',['estimaciones'=>$data,
                      'establecimiento_id'=>$establecimiento_id,
                      'nombre_establecimiento'=>$establecimiento->nombre_establecimiento,
                      'can_id'=>$can_id]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
     } 

     public function pdf_servicio($servicio_id,$tipo)
    {
        
        $nombre_servicio = DB::table('servicios')->where('id',$servicio_id)->get();

        
        if($tipo==1){

            $data = DB::table('servicios')
            ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
            ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
            ->where('petitorio_servicio.servicio_id',$servicio_id)
            ->where('tipo_dispositivo_medicos_id',1)
            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
            ->get();

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data = DB::table('servicios')
                        ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                        ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_servicio.servicio_id',$servicio_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
                        ->get();

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    $data = DB::table('servicios')
                            ->join('petitorio_servicio', 'petitorio_servicio.servicio_id', '=', 'servicios.id')
                            ->join('petitorios', 'petitorio_servicio.petitorio_id', '=', 'petitorios.id')
                            ->where('petitorio_servicio.servicio_id',$servicio_id)
                            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
                            ->get();
           
                    $descripcion_tipo='Medicamentos y Dispositivos';
                }
        }
        
        $nombre=$nombre_servicio->get(0)->nombre_servicio;
        
        $texto='SERVICIO';
        
        //dd($data);

        $pdf = \PDF::loadView('admin.pdf.descargar_medicamentos_pdf',['petitorios'=>$data,
                      'descripcion_tipo' =>$descripcion_tipo,'nombre_rubro'=>$nombre,'texto'=>$texto]);

        
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream('archivo.pdf');
        
     } 

    public function cargarrubros($id)
    {
        $model_rubros= new Servicio();
        $rubros = $model_rubros->getServicio($id);

        if(count($rubros)==0)
            $rubros = collect(['id' => '0','nombre_servicio' => 'NO APLICA']);

        return $rubros;

    }
}
