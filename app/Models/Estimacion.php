<?php

namespace App\Models;

use Eloquent as Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
/**
 * Class Estimacion
 * @package App\Models
 * @version June 20, 2018, 6:49 pm UTC
 *
 * @property double necesidad_anual
 * @property integer mes1
 * @property integer mes2
 * @property integer mes3
 * @property integer mes4
 * @property integer mes5
 * @property integer mes6
 * @property integer mes7
 * @property integer mes8
 * @property integer mes9
 * @property integer mes10
 * @property integer mes11
 * @property integer mes12
 * @property string justificacion
 * @property integer cpma
 * @property integer stock
 * @property integer can_id
 * @property integer servicio_id
 * @property integer petitorio_id
 * @property string descripcion_petitorio
 * @property integer tipo_dispositivo_id
 * @property integer cod_establecimiento
 * @property string nombre_establecimiento
 * @property integer cod_petitorio
 * @property integer establecimiento_id
 */
class Estimacion extends Model
{
    use SoftDeletes;

    public $table = 'estimacions';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'necesidad_anual',
        'mes1',
        'mes2',
        'mes3',
        'mes4',
        'mes5',
        'mes6',
        'mes7',
        'mes8',
        'mes9',
        'mes10',
        'mes11',
        'mes12',
        'justificacion',
        'cpma',
        'stock',
        'can_id',
        'servicio_id',
        'petitorio_id',
        'descripcion_petitorio',
        'tipo_dispositivo_id',
        'cod_establecimiento',
        'nombre_establecimiento',
        'cod_petitorio',
        'establecimiento_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'necesidad_anual' => 'double',
        'mes1' => 'integer',
        'mes2' => 'integer',
        'mes3' => 'integer',
        'mes4' => 'integer',
        'mes5' => 'integer',
        'mes6' => 'integer',
        'mes7' => 'integer',
        'mes8' => 'integer',
        'mes9' => 'integer',
        'mes10' => 'integer',
        'mes11' => 'integer',
        'mes12' => 'integer',
        'justificacion' => 'string',
        'cpma' => 'integer',
        'stock' => 'integer',
        'can_id' => 'integer',
        'servicio_id' => 'integer',
        'petitorio_id' => 'integer',
        'descripcion_petitorio' => 'string',
        'tipo_dispositivo_id' => 'integer',
        'cod_establecimiento' => 'integer',
        'nombre_establecimiento' => 'string',
        'cod_petitorio' => 'integer',
        'establecimiento_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public static function ContarProductos($can_id, $establecimiento_id, $tipo_dispositivo) {
        $cad = "select count(*) as total
            from estimacions
            where estado <>2 and can_id ='{$can_id}' and establecimiento_id ='{$establecimiento_id}' ";
        
        if($tipo_dispositivo ==1):
            $cad .= " and tipo_dispositivo_id=1  "; 
        else:
            $cad .= " and tipo_dispositivo_id>1  ";
        endif;

        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ContarProductosNivel2y3($can_id, $establecimiento_id, $tipo_dispositivo, $servicio_id) {
        $cad = "select count(*) as total
            from estimacion_servicio
            where estado <>2 and can_id ='{$can_id}' and establecimiento_id ='{$establecimiento_id}' and servicio_id ='{$servicio_id}' ";
        
        if($tipo_dispositivo ==1):
            $cad .= " and tipo_dispositivo_id=1  "; 
        else:
            $cad .= " and tipo_dispositivo_id>1  ";
        endif;

        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ProductosIngresados($can_id, $establecimiento_id, $tipo_dispositivo) {

        $data=DB::table('estimacions')
                    ->where('can_id',$can_id) 
                    ->where('tipo_dispositivo_id',$tipo_dispositivo)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('estado','<>',2)
                    ->orderby('descripcion','asc')
                    ->get();
        
        return $data;
    }

    public static function ProductosIngresadosNivel2y3($can_id, $establecimiento_id, $tipo_dispositivo, $servicio_id) {

        $data=DB::table('estimacion_servicio')
                    ->where('can_id',$can_id) 
                    ->where('tipo_dispositivo_id',$tipo_dispositivo)
                    ->where('servicio_id',$servicio_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('estado','<>',2)
                    ->orderby('descripcion','asc')
                    ->get();
        
        return $data;
    }

    public static function ProductosIngresadosNivel2y3D2($can_id, $establecimiento_id, $tipo_dispositivo, $servicio_id) {

        $data=DB::table('estimacion_servicio')
                    ->where('can_id',$can_id) 
                    ->where('tipo_dispositivo_id','>',$tipo_dispositivo)
                    ->where('servicio_id',$servicio_id)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('estado','<>',2)
                    ->orderby('descripcion','asc')
                    ->get();
        
        return $data;
    }



    public static function ProductosIngresados2y3($can_id, $establecimiento_id, $tipo_dispositivo) {

        $data=DB::table('estimacions')
                    ->where('can_id',$can_id) 
                    ->where('tipo_dispositivo_id','>',1)   
                    ->where('establecimiento_id',$establecimiento_id)
                    ->where('estado','<>',2)
                    ->orderby('descripcion','asc')
                    ->get();
        
        return $data;
    }

    public static function ActualizaProducto($id, $necesidad1, $necesidad2, $necesidad3, $cpma,$cpma2, $cpma3, $mes1,$mes1_1, $mes1_2, $mes2,$mes2_1, $mes2_2,$mes3,$mes3_1, $mes3_2,$mes4,$mes4_1, $mes4_2,$mes5,$mes5_1, $mes5_2,$mes6,$mes6_1, $mes6_2,$mes7,$mes7_1, $mes7_2,$mes8,$mes8_1, $mes8_2,$mes9,$mes9_1, $mes9_2,$mes10,$mes10_1, $mes10_2,$mes11,$mes11_1, $mes11_2,$mes12,$mes12_1, $mes12_2) {

        $cad = "Select sp_actualiza_productos_nivel_1(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $data = DB::select($cad, array($id,$necesidad1, $necesidad2, $necesidad3, $cpma,$cpma2, $cpma3, $mes1,$mes1_1, $mes1_2, $mes2,$mes2_1, $mes2_2,$mes3,$mes3_1, $mes3_2,$mes4,$mes4_1, $mes4_2,$mes5,$mes5_1, $mes5_2,$mes6,$mes6_1, $mes6_2,$mes7,$mes7_1, $mes7_2,$mes8,$mes8_1, $mes8_2,$mes9,$mes9_1, $mes9_2,$mes10,$mes10_1, $mes10_2,$mes11,$mes11_1, $mes11_2,$mes12,$mes12_1, $mes12_2));
        
        
        return $data[0];

    }

    public static function ActualizaCPMA_mmulti($id, $cpma,$cpma2, $cpma3) {

        $data = DB::table('estimacions')
                ->where('id', $id)
                ->update([
                            'cpma'=>$cpma,
                            'cpma_1'=>$cpma2,
                            'cpma_2'=>$cpma3
                ]);

        return $data;

    }

    public static function ActualizaCPMA($id, $cpma) {

        $cpma2 = $cpma + $cpma*0.05;
        $cpma3 = $cpma + $cpma*0.10;

        $data = DB::table('estimacions')
                ->where('id', $id)
                ->update([
                            'cpma'=>$cpma,
                            'cpma_1'=>$cpma2,
                            'cpma_2'=>$cpma3
                ]);

        return $data;

    }

    public static function GetProductosRegistrados($can_id, $establecimiento_id) {

        $data = DB::table('estimacions as e')
                    ->select('e.tipo_dispositivo_id')
                    ->where('e.establecimiento_id',$establecimiento_id)
                    ->where('e.can_id',$can_id)
                    ->where('e.estado','<>',2)
                    ->get();
        return $data;
    }

    public static function GetProductosTipoRegistrados($can_id, $establecimiento_id, $tipo) {

        $data = DB::table('estimacions as e')
                    ->select('e.tipo_dispositivo_id')
                    ->where('e.establecimiento_id',$establecimiento_id)
                    ->where('e.can_id',$can_id)
                    ->where('e.tipo_dispositivo_id',$tipo)
                    ->where('e.estado','<>',2)
                    ->get();
        return $data;
    }

    public static function ActualizaProductoNivel2y3($id, $necesidad1, $necesidad2, $necesidad3,$mes1,$mes1_1, $mes1_2, $mes2,$mes2_1, $mes2_2,$mes3,$mes3_1, $mes3_2,$mes4,$mes4_1, $mes4_2,$mes5,$mes5_1, $mes5_2,$mes6,$mes6_1, $mes6_2,$mes7,$mes7_1, $mes7_2,$mes8,$mes8_1, $mes8_2,$mes9,$mes9_1, $mes9_2,$mes10,$mes10_1, $mes10_2,$mes11,$mes11_1, $mes11_2,$mes12,$mes12_1, $mes12_2) {

        $cad = "Select sp_actualiza_productos_nivel_2y3(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $data = DB::select($cad, array($id,$necesidad1, $necesidad2, $necesidad3, $mes1,$mes1_1, $mes1_2, $mes2,$mes2_1, $mes2_2,$mes3,$mes3_1, $mes3_2,$mes4,$mes4_1, $mes4_2,$mes5,$mes5_1, $mes5_2,$mes6,$mes6_1, $mes6_2,$mes7,$mes7_1, $mes7_2,$mes8,$mes8_1, $mes8_2,$mes9,$mes9_1, $mes9_2,$mes10,$mes10_1, $mes10_2,$mes11,$mes11_1, $mes11_2,$mes12,$mes12_1, $mes12_2));
        
        
        return $data[0];

    }

    public static function ActivaServicio($can_id, $servicio_id, $establecimiento_id, $campo, $valor) {

        $data = DB::table('can_servicio')
                ->where('can_id', $can_id)
                ->where('servicio_id', $servicio_id)
                ->where('establecimiento_id', $establecimiento_id)
                ->update([
                            $campo => $valor,   
                            'updated_at'=>Carbon::now()
                ]);

        return $data;
    }

    public static function ConsultaEstimacionFarmaceuticos($can_id, $establecimiento_id, $tipo_dispositivo, $valor) {

        if($valor == 1 ): //avance
            $data=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo_dispositivo)                                 
                        ->where('estado','<>',2)    
                        ->where('petitorio','=',1)                                 
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();
        else:
            $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id',$tipo_dispositivo)                                 
                        ->where('estado','<>',2)                                 
                        ->where('petitorio','=',1)                                 
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();
        endif;


        return $data;
    }        

    public static function ConsultaEstimacionDispositivos($can_id, $establecimiento_id, $tipo_dispositivo, $valor) {

        if($valor == 1 ): //avance
            $data=DB::table('estimacions')
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>=',$tipo_dispositivo) 
                        ->where('estado','<>',2)                   
                        ->where('petitorio','=',1)                                                                      
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();
        else:
            $data=DB::table('estimacions')
                        ->where('necesidad_anual','>',0)
                        ->where('can_id',$can_id) //cambiar 22
                        ->where('tipo_dispositivo_id','>=',$tipo_dispositivo)                                
                        ->where('estado','<>',2)                    
                        ->where('petitorio','=',1)                                              
                        ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                        ->orderby ('tipo_dispositivo_id','asc')  
                        ->orderby ('descripcion','asc')  
                        ->get();
        endif;

        return $data;
    }

    public static function ConsultaEstimacionFarmaceuticosNivel2y3($can_id, $establecimiento_id, $tipo_dispositivo, $valor, $servicio_id) {

        if($valor == 1 ): 
            $data=DB::table('estimacion_servicio')
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) 
                    ->where('tipo_dispositivo_id',$tipo_dispositivo)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    ->where('establecimiento_id',$establecimiento_id)//cambiar 1
                    ->orderby('descripcion','asc')
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get(); 
        else:
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) 
                    ->where('tipo_dispositivo_id',$tipo_dispositivo)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->orderby('descripcion','asc')
                    ->orderby ('tipo_dispositivo_id','asc')  
                    ->get(); 
        endif;


        return $data;
    } 

    public static function ConsultaEstimacionDispositivosNivel2y3($can_id, $establecimiento_id, $tipo_dispositivo, $valor, $servicio_id) {

        if($valor == 1 ): //avance
            $data=DB::table('estimacion_servicio')
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id) 
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->orderby('descripcion','asc')
                    ->orderby ('tipo_dispositivo_id','asc')                    
                    ->get();  
        else:
            $data=DB::table('estimacion_servicio')
                    ->where('necesidad_anual','>',0)
                    ->where('servicio_id',$servicio_id)
                    ->where('can_id',$can_id)
                    ->where('tipo_dispositivo_id','>',1)
                    ->where('estado','<>',2)
                    ->where('petitorio','=',1)
                    ->where('establecimiento_id',$establecimiento_id)
                    ->orderby('descripcion','asc')
                    ->orderby ('tipo_dispositivo_id','asc')                    
                    ->get();  
        endif;

        return $data;
    }

    public static function EliminaProducto($id) {

        $cad = "Select sp_elimina_productos_nivel_2y3(?)";
        $data = DB::select($cad, array($id));
        
        return $data[0];

    }

    public static function ActualizaNewProducto($id, $necesidad1, $cpma,$mes1,$mes2,$mes3,$mes4,$mes5,$mes6,$mes7,$mes8,$mes9,$mes10,$mes11,$mes12) {

        $cad = "Select sp_actualiza_productos_new_nivel_1(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $data = DB::select($cad, array($id, $necesidad1, $cpma,$mes1,$mes2,$mes3,$mes4,$mes5,$mes6,$mes7,$mes8,$mes9,$mes10,$mes11,$mes12));
        
        return $data[0];

    }

    public static function ActualizaNewProductoNivel2y3($id, $necesidad1, $mes1,$mes2,$mes3,$mes4,$mes5,$mes6,$mes7,$mes8,$mes9,$mes10,$mes11,$mes12) {

        $cad = "Select sp_actualiza_productos_new_nivel_2y3(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $data = DB::select($cad, array($id, $necesidad1, $mes1,$mes2,$mes3,$mes4,$mes5,$mes6,$mes7,$mes8,$mes9,$mes10,$mes11,$mes12));
        
        return $data[0];

    }

        public static function ActualizaFinalProductoNivel2y3($petitorio_id, $can_id, $establecimiento_id) {

        $cad = "Select sp_actualiza_productos_final_nivel_2y3(?,?,?)";
        $data = DB::select($cad, array($petitorio_id, $can_id, $establecimiento_id));
        
        return $data[0];

    }


}
