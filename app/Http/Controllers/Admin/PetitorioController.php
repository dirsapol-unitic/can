<?php

namespace App\Http\Controllers\Admin;

use DB;
use Carbon\Carbon;
use App\Http\Requests\CreatePetitorioRequest;
use App\Http\Requests\UpdatePetitorioRequest;
use App\Repositories\PetitorioRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Nivel;
use App\Models\TipoDispositivoMedico;
use App\Models\TipoUso;
use App\Models\Restricion;
use App\Models\UnidadMedida;
use App\Models\Petitorio;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;



use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PetitorioController extends AppBaseController
{
    private $petitorioRepository;

    public function __construct(petitorioRepository $petitorioRepo)
    {
        $this->petitorioRepository = $petitorioRepo;
    }

    public function index(Request $request)
    { 
        $model = new Petitorio;
        $petitorios = $model->mostrar_petitorio();
        $rol=Auth::user()->rol;

        /*//inicializamos los petitorios
        $this->petitorioRepository->pushCriteria(new RequestCriteria($request));
        
        //Consultamos todos los medicamentos y dispositivos medicos que hay en el petitorio
        $petitorios = $this->petitorioRepository->all();    
        */
        //dd($petitorios);
        //mostramos la vista con la consulta realizada anteriormente
        return view('admin.petitorios.index')
            ->with('petitorios', $petitorios)->with('rol', $rol);

    }


    public function create()
    {
        //cargamos los niveles
        $nivel_id=Nivel::pluck('descripcion','id');
        //cargamos las unidades de medida
        $unidad_medida_id=UnidadMedida::pluck('descripcion','id');
        //cargamos las unidades de medida
        $unidad_descripcion=UnidadMedida::pluck('descripcion','descripcion');
        //cargamos tipo de uso
        $tipo_uso_id=TipoUso::pluck('descripcion','id');
        //cargamos los tipos de dispositvos medicos
        $tipo_dispositivo_medicos_id=TipoDispositivoMedico::pluck('descripcion','id');
        
        
        $edit=0;
        //mostramos la vista
        return view('admin.petitorios.create',compact(["nivel_id","unidad_medida_id","tipo_uso_id","tipo_dispositivo_medicos_id","unidad_descripcion","edit"]));
    }

    public function store(CreatePetitorioRequest $request)
    {
        
        //recogemos los datos enviados por el formulario
        $codigo_petitorio = $request->input("codigo_nuevo");  
        $codigo_siga = $request->input("codigo_siga");  
        $principio_activo = $request->input("principio_activo"); 
        $concentracion = $request->input("concentracion");
        $form_farm = $request->input("form_farm");  
        $precio = $request->input("precio");  
        $presentacion = $request->input("presentacion");  
        $unidad_medida_id = $request->input("unidad_medida_id");  
        $tipo_uso_id = $request->input("tipo_uso_id");
        $nivel_id = $request->input("nivel_id");                
        $descripcion_restriccion = $request->input("descripcion_restriccion");   
        $descripcion_siga = $request->input("descripcion_siga");                
        $tipo_dispositivo_id = $request->input("tipo_dispositivo_medicos_id");

        //$tipo = TipoDispositivoMedico::where('id',$tipo_dispositivo_id);
        $tipo = TipoDispositivoMedico::findOrFail($tipo_dispositivo_id);
        $unidad = UnidadMedida::findOrFail($unidad_medida_id);
        $uso = TipoUso::findOrFail($tipo_uso_id);
        $nivel = Nivel::findOrFail($nivel_id);
        
        //En la descripcion concatenamos el principio activo + concentracion + formmula farmaceutica + presentacion 
        //si unidad de medida es 1 'N/A' 
        if($tipo_dispositivo_id==1)
            $descripcion = $principio_activo.' '.$concentracion.' '.$presentacion.' '.$form_farm;
        else
            $descripcion = $principio_activo.' '.$unidad->descripcion;
        
        $producto = new Petitorio;
        if($codigo_petitorio!='SC'){
            $existe = $producto->ExisteProducto($codigo_petitorio);
            if ($existe) {
            
                return redirect('/petitorios/create')
                                ->withInput()
                                ->withErrors(array('El producto ya existe'));
            }    
        }
        

        //guardamos la informacion en la tabla de petitorios
        DB::table('petitorios')->insert([
            "tipo_dispositivo_medicos_id"=> $tipo_dispositivo_id,
            "descripcion_tipo_dispositivo"=>$tipo->descripcion,
            "codigo_petitorio"=>$codigo_petitorio,
            "codigo_siga"=>$codigo_siga,
            "principio_activo"=>$principio_activo,
            "concentracion"=>$concentracion,
            "form_farm"=>$form_farm,
            "precio"=>$precio,
            "presentacion"=>$presentacion,
            "unidad_medida_id"=>$unidad_medida_id,
            "descripcion_unidad_medida"=>$unidad->descripcion,
            "descripcion_restriccion"=>$descripcion_restriccion,
            "descripcion_siga"=>$descripcion_siga,
            "nivel_id"=>$nivel_id,
            "descripcion_tipo_uso"=>$uso->descripcion,
            "tipo_uso_id"=>$tipo_uso_id,
            "descripcion_nivel"=>$nivel->descripcion,
            "descripcion"=>$descripcion,
            "created_at"=>Carbon::now(),
            "updated_at"=>Carbon::now()
        ]);
        
       //sino hay ningun inconveniente mostramos mensaje de exito 
       Flash::success('Medicamento/Dispositivo guardado correctamente.');

       //redireccionamos al index 
        return redirect(route('petitorios.index'));
    
    }

    
    public function show($id)
    {
        $petitorio = $this->petitorioRepository->findWithoutFail($id);

        if (empty($petitorio)) {
            Flash::error('Medicamento/Petitorio no encontrado');

            return redirect(route('petitorios.index',$id));
        }

        return view('admin.petitorios.show')->with('petitorio', $petitorio)
                                            ->with('id',$id);

        
    }

    /**
     * Display the specified petitorio.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function petitorio_medicamento($id)
    {
        $petitorio = $this->petitorioRepository->findWithoutFail($id);

        if (empty($petitorio)) {
            Flash::error('Medicamento/Petitorio no encontrado');

            return redirect(route('petitorios.medicamentos1'));
        }

        return view('admin.petitorios.show')->with('petitorio', $petitorio);

        
    }

    /**
     * Show the form for editing the specified petitorio.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $petitorio = $this->petitorioRepository->findWithoutFail($id);

        if (empty($petitorio)) {
            Flash::error('Medicamento o Dispositivo no encontrado');

            return redirect(route('petitorios.index'));
        }

        $estado=$petitorio->estado;
        $covid=$petitorio->covid;

        $nivel_id=Nivel::pluck('descripcion','id');
        $unidad_medida_id=UnidadMedida::pluck('descripcion','id');
        $tipo_uso_id=TipoUso::pluck('descripcion','id');
        $tipo_dispositivo_medicos_id=TipoDispositivoMedico::pluck('descripcion','id');
        $edit=1;

        return view('admin.petitorios.edit')
                ->with('petitorio', $petitorio)
                ->with('edit', $edit)
                ->with('estado', $estado)
                ->with('covid', $covid)
                ->with('nivel_id', $nivel_id)
                ->with('unidad_medida_id', $unidad_medida_id)
                ->with('tipo_uso_id', $tipo_uso_id)
                ->with('tipo_dispositivo_medicos_id', $tipo_dispositivo_medicos_id);
    }

    /**
     * Update the specified petitorio in storage.
     *
     * @param  int              $id
     * @param UpdatepetitorioRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePetitorioRequest $request)
    {
        $petitorio = $this->petitorioRepository->findWithoutFail($id);

        $codigo_nuevo = $request->input("codigo_nuevo");  

        if (empty($petitorio)) {
            Flash::error('Producto no encontrado');

            return redirect(route('petitorios.index'));
        }

        if($codigo_nuevo!=""){
            $existe = $petitorio->ExisteProducto($codigo_nuevo);
            if ($existe) {
                return redirect('/petitorios/'.$id.'/edit')->withInput()
                        ->withErrors(array('El producto con el código digitado ya existe'));
            }                              
            $codigo_antiguo = $petitorio->codigo_petitorio;            
            $codigo_petitorio = $request->input("codigo_nuevo"); 
        }
        else
        {
         $codigo_antiguo ='';   
         $codigo_petitorio = $petitorio->codigo_petitorio;
        }

        $estado = $request->input("estado");
        
        if($estado!="")$estado = 1;
        else $estado=0;

        $covid = $request->input("covid");
        if($covid!="")$covid = 1;
        else $covid=0;

        $principio_activo = $request->input("principio_activo"); 
        $codigo_siga = $request->input("codigo_siga"); 
        $descripcion_siga = $request->input("descripcion_siga"); 
        
        $concentracion = $request->input("concentracion");
        $form_farm = $request->input("form_farm");  
        $precio = $request->input("precio");  
        $presentacion = $request->input("presentacion");  
        $unidad_medida_id = $request->input("unidad_medida_id");  
        $tipo_uso_id = $request->input("tipo_uso_id");
        $nivel_id = $request->input("nivel_id");                
        $tipo_dispositivo_id = $request->input("tipo_dispositivo_medicos_id");
        $descripcion_restriccion = $request->input("descripcion_restriccion");


        //recogemos los datos enviados por el formulario

        
        //$tipo = TipoDispositivoMedico::where('id',$tipo_dispositivo_id);
        $tipo = TipoDispositivoMedico::findOrFail($tipo_dispositivo_id);
        $unidad = UnidadMedida::findOrFail($unidad_medida_id);
        $uso = TipoUso::findOrFail($tipo_uso_id);
        $nivel = Nivel::findOrFail($nivel_id);

        //En la descripcion concatenamos el principio activo + concentracion + formmula farmaceutica + presentacion 
        //si unidad de medida es 1 'N/A' 
        //if   ($unidad->descripcion == 'N/A')
        
        if($tipo_dispositivo_id==1)
            $descripcion = $principio_activo.' '.$concentracion.' '.$presentacion.' '.$form_farm;
        else
            $descripcion = $principio_activo.' '.$unidad->descripcion;
        
        //guardamos la informacion en la tabla de petitorios
        DB::table('petitorios')
            ->where('id', $id )
            ->update([
                "tipo_dispositivo_medicos_id"=> $tipo_dispositivo_id,
                "descripcion_tipo_dispositivo"=>$tipo->descripcion,
                "codigo_petitorio"=>$codigo_petitorio,
                "codigo_antiguo"=>$codigo_antiguo,
                "codigo_siga"=>$codigo_siga,
                "estado"=>$estado,
                "covid"=>$covid,
                "principio_activo"=>$principio_activo,
                "concentracion"=>$concentracion,
                "form_farm"=>$form_farm,
                "precio"=>$precio,
                "presentacion"=>$presentacion,
                "unidad_medida_id"=>$unidad_medida_id,
                "descripcion_unidad_medida"=>$unidad->descripcion,
                "descripcion_restriccion"=>$descripcion_restriccion,
                "descripcion_siga"=>$descripcion_siga,
                "nivel_id"=>$nivel_id,
                "descripcion_tipo_uso"=>$uso->descripcion,
                "tipo_uso_id"=>$tipo_uso_id,
                "descripcion_nivel"=>$nivel->descripcion,
                "descripcion"=>$descripcion,
                "created_at"=>Carbon::now(),
                "updated_at"=>Carbon::now()
            ]);

        /*MODIFICAR A TODO estimacion_servicio*/
        DB::table('consolidados')
            ->where('petitorio_id', $id )
            ->update([
                "descripcion"=>$descripcion
            ]);

        /*MODIFICAR A TODO estimacions*/
        DB::table('estimacions')
            ->where('petitorio_id', $id )
            ->update([
                "descripcion"=>$descripcion
            ]);
            
        /*MODIFICAR A TODO consolidados*/
        DB::table('estimacion_servicio')
            ->where('petitorio_id', $id )
            ->update([
                "descripcion"=>$descripcion
            ]);
        //$petitorio = $this->petitorioRepository->update($request->all(), $id);

        Flash::success('Medicamento/Petitorio actualizado.');

        return redirect(route('petitorios.index'));
    }

    /**
     * Remove the specified petitorio from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $petitorio = $this->petitorioRepository->findWithoutFail($id);

        if (empty($petitorio)) {
            Flash::error('Medicamento/Petitorio no encontrado');

            return redirect(route('petitorios.index'));
        }

        $this->petitorioRepository->delete($id);

        Flash::success('Medicamento/Petitorio eliminado.');

        return redirect(route('petitorios.index'));
    }

    ///dispositivo por nivel
    public function buscarenpetitorio(Request $request,$nivel,$tipo)
    {
        $petitorios=DB::table('petitorios')
            ->where('nivel_id',$nivel)
            ->where('tipo_dispositivo_medicos_id',$tipo)
            ->get();
   
        return view('admin.petitorios.dispositivo.index')
            ->with('petitorios', $petitorios);
    }

    public function exportPetitorio($type)
    {

        $data=DB::table('petitorios')
                    ->where('estado',1)
                    ->orderby('descripcion','asc')//cambiar desc
                    ->get();
        
        $archivo='Petitorio_SANIDADPNP';
        return Excel::create($archivo, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {   
                
                //6 filas agrupadas con las columnas A,B,C
                $sheet->setMergeColumn(array(
                    'columns' => array('B','K'),
                    'rows' => array(
                        array(1,5)                        
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
                $sheet->cell('C1', function($cell) {$cell->setValue(' PETITORIO SANIDAD PNP');  $cell->setFontSize(38); $cell->setFontWeight('bold'); $cell->setAlignment('center'); $cell->setValignment('middle');
                });

                $sheet->setHeight(1, 50);

                $sheet->cell('A6', function($cell) {$cell->setValue('N°'); $cell->setFontSize(10);   $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('B6', function($cell) {$cell->setValue('COD MED'); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setFontSize(10); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('C6', function($cell) {$cell->setValue('PRINCIPIO ACTIVO'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('D6', function($cell) {$cell->setValue('CONCENT.'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('E6', function($cell) {$cell->setValue('FORM FARM');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 
                
                $sheet->cell('F6', function($cell) {$cell->setValue('PRES.'); $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('G6', function($cell) {$cell->setValue('UND. MEDIDA');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->cell('H6', function($cell) {$cell->setValue('NIVEL');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('I6', function($cell) {$cell->setValue('USO');  $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('J6', function($cell) {$cell->setValue('TIPO DE MEDICAMENTO');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); });

                $sheet->cell('K6', function($cell) {$cell->setValue('COVID-19');  $cell->setFontSize(10); $cell->setBorder('thin', 'thin', 'thin', 'thin'); $cell->setAlignment('center'); $cell->setFontWeight('bold'); }); 

                $sheet->setWidth(array(
                    'A'     =>  5,
                    'B'     =>  10,                    
                    'C'     =>  150,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  12,
                    'I'     =>  12,
                    'J'     =>  35,
                    'K'     =>  12,
                ));
                //ordenar
                $k=1;
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $i= $key+7;
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
                        $sheet->cell('C'.$i, $value->descripcion); //items  principio_activo
                        $sheet->cell('D'.$i, $value->concentracion); 
                        $sheet->cell('E'.$i, $value->form_farm); 
                        $sheet->cell('F'.$i, $value->presentacion); 
                        if($value->descripcion_unidad_medida=='N/A')
                            $sheet->cell('G'.$i, ''); 
                        else
                            $sheet->cell('G'.$i, $value->descripcion_unidad_medida); 
                        $sheet->cell('H'.$i, $value->descripcion_nivel); 
                        if($value->descripcion_tipo_uso=='N/A')
                            $sheet->cell('I'.$i, ''); 
                        else
                            $sheet->cell('I'.$i, $value->descripcion_tipo_uso); 
                        $sheet->cell('J'.$i, $value->descripcion_tipo_dispositivo);
                        if($value->covid==1)
                            $texto='SI';
                        else
                            $texto='NO';
                        $sheet->cell('K'.$i, $texto); 
                        $k++;
                    }                    
                }                
            });
        })->download($type);
    }

    public function actualiza_petitorio()
    {
        
        $data=DB::table('petitorios')
                    //->where('estado',1)
                    ->where('tipo_dispositivo_medicos_id','<>',1)
                    ->orderby('id','asc')//cambiar desc
                    ->get();

        foreach ($data as $key => $value) {
            $descripcion = $value->principio_activo.'  '.$value->descripcion_unidad_medida;

            DB::table('petitorios')
            ->where('id', $value->id )
            ->update([
                "descripcion"=>$descripcion
                
            ]);

        }
            
        
        Flash::success('Medicamento/Petitorio actualizado.');

        return redirect(route('petitorios.index'));
    }

    public function actualiza_estimacion()
    {
        $data=DB::table('estimacions')
                    //->where('estado',1)
                    ->where('tipo_dispositivo_id','<>',1)
                    ->where('can_id','=',6)
                    ->orderby('id','asc')//cambiar desc
                    ->get();

        foreach ($data as $key => $value) {

            $petitorio = $this->petitorioRepository->findWithoutFail($value->petitorio_id);

            $descripcion = $petitorio->principio_activo.'  '.$petitorio->descripcion_unidad_medida;

            DB::table('estimacions')
            ->where('id', $value->id )
            ->update([
                "descripcion"=>$descripcion
                
            ]);

        }   
        
        Flash::success('Medicamento/Petitorio actualizado.');

        return redirect(route('petitorios.index'));
    }

    public function actualiza_estimacion_servicio()
    {
        $data=DB::table('estimacion_servicio')
                    //->where('estado',1)
                    ->where('tipo_dispositivo_id','<>',1)
                    ->where('can_id','=',6)
                    ->orderby('id','asc')//cambiar desc
                    ->get();

        foreach ($data as $key => $value) {

            $petitorio = $this->petitorioRepository->findWithoutFail($value->petitorio_id);

            $descripcion = $petitorio->principio_activo.'  '.$petitorio->descripcion_unidad_medida;

            DB::table('estimacion_servicio')
            ->where('id', $value->id )
            ->update([
                "descripcion"=>$descripcion
                
            ]);

        }   
        
        Flash::success('Medicamento/Petitorio actualizado.');

        return redirect(route('petitorios.index'));
    }

    

}
