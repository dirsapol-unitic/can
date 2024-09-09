<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateRubroRequest;
use App\Http\Requests\UpdateRubroRequest;
use App\Repositories\RubroRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use DB;
use App\Models\Rubro;
use App\Models\Petitorio;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;

class RubroController extends AppBaseController
{
    /** @var  RubroRepository */
    private $rubroRepository;

    public function __construct(RubroRepository $rubroRepo)
    {
        $this->rubroRepository = $rubroRepo;
    }

    /**
     * Display a listing of the Rubro.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->rubroRepository->pushCriteria(new RequestCriteria($request));
        $rubros = $this->rubroRepository->all();

        return view('admin.rubros.index')
            ->with('rubros', $rubros);
    }

    /**
     * Show the form for creating a new Rubro.
     *
     * @return Response
     */
    public function create()
    {   
        $tipo=1; $consolidado=1; // 1: farmacia, 2:almacen
        return view('admin.rubros.create')->with('tipo',$tipo)->with('consolidado',$consolidado);
        
    }

    /**
     * Store a newly created Rubro in storage.
     *
     * @param CreateRubroRequest $request
     *
     * @return Response
     */
    public function store(CreateRubroRequest $request)
    {
        $input = $request->all();

        $rubro = $this->rubroRepository->create($input);

        Flash::success('Rubro guardado correctamente.');

        return redirect(route('rubros.index'));
    }

    /**
     * Display the specified Rubro.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $rubro = $this->rubroRepository->findWithoutFail($id);

        if (empty($rubro)) {
            Flash::error('Rubro no encontrado');

            return redirect(route('rubros.index'));
        }

        return view('admin.rubros.show')->with('rubro', $rubro);
    }

    /**
     * Show the form for editing the specified Rubro.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $rubro = $this->rubroRepository->findWithoutFail($id);

        if (empty($rubro)) {
            Flash::error('Rubro no encontrado');

            return redirect(route('rubros.index'));
        }
        $consolidado=$rubro->consolidado; $tipo=2;
        
        //dd($tipo);

        return view('admin.rubros.edit')->with('rubro', $rubro)->with('consolidado',$consolidado)->with('tipo',$tipo);
    }

    /**
     * Update the specified Rubro in storage.
     *
     * @param  int              $id
     * @param UpdateRubroRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRubroRequest $request)
    {
        $rubro = $this->rubroRepository->findWithoutFail($id);

        if (empty($rubro)) {
            Flash::error('Rubro no encontrado');

            return redirect(route('rubros.index'));
        }

        $rubro = $this->rubroRepository->update($request->all(), $id);

        Flash::success('Rubro actualizado correctamente.');

        return redirect(route('rubros.index'));
    }

    /**
     * Remove the specified Rubro from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $rubro = $this->rubroRepository->findWithoutFail($id);

        if (empty($rubro)) {
            Flash::error('Rubro no encontrado');

            return redirect(route('rubros.index'));
        }

        //$this->rubroRepository->delete($id);

        //Eliminamos datos de petitorio_rubro
        $elimina_bd=DB::table('establecimiento_rubro')
                                ->where('rubro_id',$id)
                                ->delete();

        //Eliminamos datos de petitorio_rubro
        $elimina_bd=DB::table('petitorio_rubro')
                                ->where('rubro_id',$id)
                                ->delete();

        //elimino datos de can_rubro
        $elimina_bd=DB::table('can_rubro')
                                ->where('rubro_id',$id)
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
        $elimina_bd=DB::table('rubros')
                                ->where('id',$id)
                                ->delete();        
        
        
        Flash::success('Rubro eliminado correctamente.');

        return redirect(route('rubros.index'));
    }

    public function ver_medicamentos(Request $request,$rubro_id)
    {
        ///mostrar todos los petitorios del establecimiento
        $rubros = $this->rubroRepository->findWithoutFail($rubro_id);

        if (empty($rubros)) {
            Flash::error('Rubro no encontrado');

            return redirect(route('rubros.index'));
        }

        $petitorios = DB::table('rubros')
                ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                  ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                  ->where('petitorio_rubro.rubro_id',$rubro_id)
                  ->where('tipo_dispositivo_medicos_id',1)
                  ->get();

        $num_productos = DB::table('rubros')
                ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                  ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                  ->where('petitorio_rubro.rubro_id',$rubro_id)
                  ->where('tipo_dispositivo_medicos_id',1)
                  ->count();

        return view('admin.rubros.medicamentos.ver_medicamentos')
            ->with('num_productos', $num_productos)
            ->with('petitorios', $petitorios)
            ->with('rubro_id', $rubro_id)
            ->with('rubros', $rubros);
    }

    public function guardar_medicamentos(Request $request, $rubro_id)
    {
        
        $rubros = $this->rubroRepository->findWithoutFail($rubro_id);

        if (empty($rubros)) {
            Flash::error('Rubro no encontrado');
            return redirect(route('rubros.index'));
        }

        //Buscar todos los medicamentos
        $num_medicamentos=DB::table('petitorio_rubro')
                            ->where('rubro_id',$rubro_id)
                            ->where('tipo_dispositivo_medico_id',1)
                            ->count();
        
        //Sino se ha marcado ningun checkbox
        if (empty($request->petitorios)) {
           if($num_medicamentos>0){
                $elimina_bd=DB::table('petitorio_rubro')
                                ->where('rubro_id',$rubro_id)
                                ->where('tipo_dispositivo_medico_id',1)
                                ->delete();
                Flash::success('Medicamentos guardado correctamente.');    
           }
           else
           {
            Flash::error('No se ha seleccionado ninguna opción,debe de seleccionar al menos una opción para guardar');
           }
        }else
        {
            //si esta vacio llenamos
            if($num_medicamentos==0){
                //insertar
                $rubros->petitorios()->attach($request->petitorios); 
                //actualizamos
                foreach($request->petitorios as $key => $medicamentos){
                    $petitorios=Petitorio::where('id',$medicamentos)->get();

                    $medicamentos_update=DB::table('petitorio_rubro')
                                            ->where('rubro_id',$rubro_id)
                                            ->where('petitorio_id',$medicamentos)
                                            ->update([
                                                        'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                        'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                        
                                                    ]);
                }
            }
            else
            {
                $medicamentos_bd=DB::table('petitorio_rubro')
                                ->where('rubro_id',$rubro_id)
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

                    $rubros->petitorios()->attach($medicamentos_nuevos); //insertamos
                    //actualizamos
                    foreach($medicamentos_nuevos as $key => $medicamentos){
                        $petitorios=Petitorio::where('id',$medicamentos)->get();

                        $medicamentos_update=DB::table('petitorio_rubro')
                                            ->where('rubro_id',$rubro_id)
                                            ->where('petitorio_id',$medicamentos)
                                            ->update([
                                                        'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                        'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                        
                                                    ]);
                    }

                }
                ///eliminamos los que no estan en los checkbox
                if($num_medicamentos_desmarcado>0){
            
                    $rubros->petitorios()->detach($medicamentos_desmarcado); //attach           

                }
            }
            Flash::success('Medicamentos guardado correctamente.');    
        }    
        return redirect(route('rubros.ver_medicamentos',[$rubro_id]));
    }

    public function asignar_medicamentos($rubro_id)
    {
        //mostrar todos los petitorios que sean del nivel del establecimiento
        $rubros = Rubro::find($rubro_id);

        if (empty($rubros)) {
            Flash::error('Rubro no encontrado');
            return redirect(route('rubros.index'));
        }

        $petitorios=Petitorio::where('tipo_dispositivo_medicos_id',1)
                        ->where('nivel_id','<=',3)
                        ->orderby('descripcion','asc')
                        ->get();

        return view('admin.rubros.medicamentos.asignar_medicamentos')
            ->with('rubros', $rubros)
            ->with('petitorios', $petitorios);
    }

    public function ver_dispositivos(Request $request,$rubro_id)
    {
        ///mostrar todos los petitorios del establecimiento
        $rubros = $this->rubroRepository->findWithoutFail($rubro_id);

        if (empty($rubros)) {
            Flash::error('Rubro no encontrado');
            return redirect(route('rubros.index'));
        }

        $petitorios = DB::table('rubros')
                ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                  ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                  ->where('petitorio_rubro.rubro_id',$rubro_id)
                  ->where('tipo_dispositivo_medicos_id','>',1)
                  ->get();

        $num_productos = DB::table('rubros')
                ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                  ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                  ->where('petitorio_rubro.rubro_id',$rubro_id)
                  ->where('tipo_dispositivo_medicos_id','>',1)
                  ->count();

        return view('admin.rubros.dispositivos.ver_dispositivos')
            ->with('num_productos', $num_productos)
            ->with('petitorios', $petitorios)
            ->with('rubro_id', $rubro_id)
            ->with('rubros', $rubros);
    }

    public function guardar_dispositivos(Request $request, $rubro_id)
    {
        
        $rubros = $this->rubroRepository->findWithoutFail($rubro_id);

        if (empty($rubros)) {
            Flash::error('Rubro no encontrado');

            return redirect(route('rubros.index'));
        }

        //Buscar todos los medicamentos
        $num_medicamentos=DB::table('petitorio_rubro')
                            ->where('rubro_id',$rubro_id)
                            ->where('tipo_dispositivo_medico_id','>',1)
                            ->count();

        //Sino se ha marcado ningun checkbox
        if (empty($request->petitorios)) {
           if($num_medicamentos>0){
                $elimina_bd=DB::table('petitorio_rubro')
                                ->where('rubro_id',$rubro_id)
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
                $rubros->petitorios()->attach($request->petitorios); 
                foreach($request->petitorios as $key => $medicamentos){
                    $petitorios=Petitorio::where('id',$medicamentos)->get();

                    $medicamentos_update=DB::table('petitorio_rubro')
                                            ->where('rubro_id',$rubro_id)
                                            ->where('petitorio_id',$medicamentos)
                                            ->update([
                                                        'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                        'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                        
                                                    ]);
                }
            }
            else
            {
                $medicamentos_bd=DB::table('petitorio_rubro')
                                ->where('rubro_id',$rubro_id)
                                ->where('tipo_dispositivo_medico_id','>',1)
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
                    $rubros->petitorios()->attach($medicamentos_nuevos); //attach  

                    foreach($medicamentos_nuevos as $key => $medicamentos){
                        $petitorios=Petitorio::where('id',$medicamentos)->get();

                        $medicamentos_update=DB::table('petitorio_rubro')
                                                ->where('rubro_id',$rubro_id)
                                                ->where('petitorio_id',$medicamentos)
                                                ->update([
                                                            'tipo_dispositivo_medico_id'=>$petitorios->get(0)->tipo_dispositivo_medicos_id,
                                                            'uso_id'=>$petitorios->get(0)->tipo_uso_id,
                                                            
                                                        ]);
                    }   
                }
                ///eliminamos los que no estan en los checkbox
                if($num_medicamentos_desmarcado>0){
                    $rubros->petitorios()->detach($medicamentos_desmarcado); //attach           
                }
            }

            Flash::success('Dispositivos guardado correctamente.');    
         
            foreach($request->petitorios as $key => $dispositivo){

            $distribucion_update=DB::table('petitorio_rubro')
                                    ->where('rubro_id',$rubro_id)
                                    ->where('petitorio_id',$dispositivo)
                                    ->update(['tipo_dispositivo_medico_id'=>2]);
            }
        }    
        
                

        return redirect(route('rubros.ver_dispositivos',[$rubro_id]));
    }

    public function asignar_dispositivos($rubro_id)
    {
        //mostrar todos los petitorios que sean del nivel del establecimiento
        $rubros = Rubro::find($rubro_id);

        if (empty($rubros)) {
            Flash::error('Rubro no encontrado');

            return redirect(route('rubros.index'));
        }

        $petitorios=Petitorio::where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id','<=',3)
                        //->where( function ( $query )
                        //  {
                        //      $query->orWhere('tipo_uso_id','<>',5);                                      
                        //  })
                        ->get();
                        //->pluck('descripcion','id');

        return view('admin.rubros.dispositivos.asignar_dispositivos')
            ->with('rubros', $rubros)
            ->with('petitorios', $petitorios);

    }

    public function exportRubro($type,$rubro_id,$tipo)
    {
        $nombre_rubro=DB::table('rubros')->where('id',$rubro_id)->get();
        
        switch ($tipo) {
            case '1': //medicamentos
                    $data = DB::table('rubros')
                        ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                        ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_rubro.rubro_id',$rubro_id)
                        ->where('tipo_dispositivo_medicos_id',1)
                        ->where( function ( $query )
                        {
                            $query->orWhere('uso_id',2)
                                ->orWhere('uso_id',5);
                        })
                        ->get();

                    $num_data = DB::table('rubros')
                        ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                        ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_rubro.rubro_id',$rubro_id)
                        ->where('tipo_dispositivo_medicos_id',1)
                        ->where( function ( $query )
                        {
                            $query->orWhere('uso_id',2)
                                ->orWhere('uso_id',5);
                        })
                        ->count();
                break;
            
            case '2': //dispositivos
                    $data = DB::table('rubros')
                        ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                        ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_rubro.rubro_id',$rubro_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where( function ( $query )
                            {
                                $query->orWhere('uso_id',2)
                                    ->orWhere('uso_id',5);
                            })
                        ->get();

                    $num_data = DB::table('rubros')
                        ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                        ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_rubro.rubro_id',$rubro_id)
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where( function ( $query )
                            {
                                $query->orWhere('uso_id',2)
                                    ->orWhere('uso_id',5);
                            })
                        ->count();
                break;

            case '3': //todos
                    $data = DB::table('rubros')
                        ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                        ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_rubro.rubro_id',$rubro_id)
                        ->get();

                    $num_data = DB::table('rubros')
                        ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                        ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                        ->where('petitorio_rubro.rubro_id',$rubro_id)
                        ->count();

                break;
        }
        
        if($num_data>0){
        
            $archivo='Petitorio_2018_Rubro_de_'.$nombre_rubro->get(0)->descripcion;
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
            Flash::error('No hay productos asignados para este rubro');
            return redirect(route('rubros.index'));
        }
    }

    public function pdf_rubro($rubro_id,$tipo)
    {
        
        $nombre_rubro = DB::table('rubros')->where('id',$rubro_id)->get();

        
        if($tipo==1){

            $data = DB::table('rubros')
            ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
            ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
            ->where('petitorio_rubro.rubro_id',$rubro_id)
            ->where('tipo_dispositivo_medicos_id',1)
            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
            ->get();

            $num_data = DB::table('rubros')
            ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
            ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
            ->where('petitorio_rubro.rubro_id',$rubro_id)
            ->where('tipo_dispositivo_medicos_id',1)
            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
            ->count();


            

            $descripcion_tipo='Medicamentos';
        }else
            {   if ($tipo==2) {
                    $data = DB::table('rubros')
                            ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                            ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                            ->where('petitorio_rubro.rubro_id',$rubro_id)
                            ->where('tipo_dispositivo_medicos_id','>',1)
                            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
                            ->get();

                    $num_data = DB::table('rubros')
                            ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                            ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                            ->where('petitorio_rubro.rubro_id',$rubro_id)
                            ->where('tipo_dispositivo_medicos_id','>',1)
                            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
                            ->count();   

                    $descripcion_tipo='Dispositivos';
                }else
                {
                    $data = DB::table('rubros')
                            ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                            ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                            ->where('petitorio_rubro.rubro_id',$rubro_id)
                            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
                            ->get();

                    $num_data = DB::table('rubros')
                            ->join('petitorio_rubro', 'petitorio_rubro.rubro_id', '=', 'rubros.id')
                            ->join('petitorios', 'petitorio_rubro.petitorio_id', '=', 'petitorios.id')
                            ->where('petitorio_rubro.rubro_id',$rubro_id)
                            ->orderby('tipo_dispositivo_medicos_id','asc')//cambiar desc
                            ->count();   

            
                    $descripcion_tipo='Medicamentos y Dispositivos';
                }
        }
        
        if($num_data>0){
            $nombre_rubro=$nombre_rubro->get(0)->descripcion;
            
            $texto='RUBRO';
            
            //dd($data);

            $pdf = \PDF::loadView('admin.pdf.descargar_medicamentos_pdf',['petitorios'=>$data,
                          'descripcion_tipo' =>$descripcion_tipo,'nombre_rubro'=>$nombre_rubro,'texto'=>$texto]);

            
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream('archivo.pdf');    
        }
        else
        {

            Flash::error('No hay productos asignados para este rubro');
            return redirect(route('rubros.index'));
        
        }
        
        
     } 

    public function cargarrubros($id)
    {
        $model_rubros= new Rubro();
        $rubros = $model_rubros->getRubro($id);

        if(count($rubros)==0)
            $rubros = collect(['id' => '0','descripcion' => 'NO APLICA']);

        return $rubros;

    }

}
