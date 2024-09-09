<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Nivel;
use App\Models\TipoDispositivoMedico;
use App\Models\TipoUso;
use App\Models\UnidadMedida;
use App\Models\Petitorio;
use DB;

/**
 * Class petitorio
 * @package App\Models
 * @version February 3, 2018, 3:02 am UTC
 *
 * @property integer tipo_dispositivo_medicos_id
 * @property string codigo
 * @property string principio_activo
 * @property string concentracion
 * @property string form_farm
 * @property string presentacion
 * @property integer unidad_medida
 * @property integer id_nivel
 * @property integer id_tipo_uso
 * @property string descripcion
 */
class Petitorio extends Model
{
    use SoftDeletes;

    public $table = 'petitorios';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo_dispositivo_medicos_id',
        'codigo_petitorio',
        'principio_activo',
        'concentracion',
        'form_farm',
        'presentacion',
        'unidad_medida_id',
        'nivel_id',
        'tipo_uso_id',
        'restriccion_id',
        'descripcion_restriccion',
        'descripcion',
        'precio',

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'tipo_dispositivo_medicos_id' => 'integer',
        'codigo' => 'string',
        'principio_activo' => 'string',
        'concentracion' => 'string',
        'form_farm' => 'string',
        'presentacion' => 'string',
        'unidad_medida_id' => 'integer',
        'nivel_id' => 'integer',
        'tipo_uso_id' => 'integer',
        'descripcion' => 'string',
        'restriccion_id' => 'integer',
        'descripcion_restriccion' => 'string',
        'precio' => 'float',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    


  
    public function pet_nivel(){

        return $this->belongsTo('App\Models\Nivel','nivel_id');
    }
    
    public function pet_unidad_medida(){

        return $this->belongsTo('App\Models\UnidadMedida','unidad_medida_id');
    }

    public function pet_tipo_uso(){

        return $this->belongsTo('App\Models\TipoUso','tipo_uso_id');
    }
    
    public function pet_tipo_dispositivo(){

        return $this->belongsTo('App\Models\TipoDispositivoMedico','tipo_dispositivo_medicos_id');
    }

    public function establecimientos(){

        return $this->belongsToMany('App\Models\Establecimiento');
    }

    public function servicios(){

        return $this->belongsToMany('App\Models\Servicio');
    }    

        public function distribucions(){

        return $this->belongsToMany('App\Models\Distribucion');
    }    

    public function especialidads(){

        return $this->belongsToMany('App\Models\Especialidad');
    }    

    public function mostrar_petitorio(){
        $cad = "select *
from petitorios
order By petitorios.descripcion asc";
        
        $data = DB::select($cad);
        return $data;
    }

    public function ExisteProducto($codigo) {
        $data = Petitorio::where('codigo_petitorio', '=', $codigo)->first();
        return isset($data) ? true : false;
    }
    
    
    public static function ContarPetitoriosNivel($tipo_dispositivo, $nivel) {
        $cad = "select count(*) as total
            from petitorios
            where estado = 1  and tipo_dispositivo_medicos_id='{$tipo_dispositivo}' ";
        if($nivel == 1):
            $cad .= " and nivel_id ='{$nivel}' "; 
        else:
            $cad .= " and nivel_id <='{$nivel}' ";
        endif;
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ContarPetitoriosNivel2y3($tipo_dispositivo, $nivel, $servicio_id) {
        $cad = "select count(*) as total
            from servicios
            inner join petitorio_servicio on petitorio_servicio.servicio_id = servicios.id
            inner join petitorios on petitorio_servicio.petitorio_id =  petitorios.id
            where petitorios.estado = 1  and petitorio_servicio.servicio_id='{$servicio_id}' and petitorio_servicio.tipo_dispositivo_medico_id='{$tipo_dispositivo}' and nivel_id <'{$nivel}'";
        
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ContarPetitoriosNivel2y3D($tipo_dispositivo, $nivel, $servicio_id) {
        $cad = "select count(*) as total
            from servicios
            inner join petitorio_servicio on petitorio_servicio.servicio_id = servicios.id
            inner join petitorios on petitorio_servicio.petitorio_id =  petitorios.id
            where petitorios.estado = 1  and petitorio_servicio.servicio_id='{$servicio_id}' and petitorio_servicio.tipo_dispositivo_medico_id>'{$tipo_dispositivo}' and nivel_id <'{$nivel}'";
        
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }
    

    public static function ContarPetitoriosNivel1D() {
        $cad = "select count(*) as total
            from petitorios
            where estado = 1  and tipo_dispositivo_medicos_id > 1 and nivel_id < 3 ";
        
        $data = DB::select($cad);
        
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ContarPetitoriosNivel1DA() {
        $cad = "select count(*) as total
            from petitorios
            where estado = 1  and tipo_dispositivo_medicos_id > 1 and nivel_id = 1 ";
        
        $data = DB::select($cad);
        
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ContarPetitoriosN($nivel) {
        $cad = "select count(*) as total
            from petitorios
            where estado = 1  and tipo_dispositivo_medicos_id > 1 and  nivel_id <='{$nivel}' ";
        
        $data = DB::select($cad);
        
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function PetitoriosPorNivel1($tipo_dispositivo, $nivel) {

        $petitorios = DB::table('petitorios')
                    ->where('tipo_dispositivo_medicos_id',$tipo_dispositivo)
                    ->where('nivel_id',$nivel)
                    ->where('estado',1)
                    ->orderby('descripcion','asc')
                    ->pluck('petitorios.descripcion','petitorios.id');
        
        return $petitorios;
    }

    public static function GetPetitoriosPorNivel1($tipo_dispositivo, $nivel) {

        $petitorios = DB::table('petitorios')
                    ->where('tipo_dispositivo_medicos_id',$tipo_dispositivo)
                    ->where('nivel_id',$nivel)
                    ->where('estado',1)
                    ->orderby('descripcion','asc')
                    ->get();
        
        return $petitorios;
    }

    public static function PetitoriosPorNivel2y3($tipo_dispositivo, $nivel) {

        $petitorios = DB::table('petitorios')
                    ->where('tipo_dispositivo_medicos_id',$tipo_dispositivo)
                    ->where('nivel_id','<=',$nivel)
                    ->where('estado',1)
                    ->orderby('descripcion','asc')
                    ->pluck('petitorios.descripcion','petitorios.id');
        
        return $petitorios;
    }

    public static function GetPetitoriosNivel2y3($tipo_dispositivo, $nivel, $servicio_id) {

        $petitorios = DB::table('servicios')
                                  ->select('petitorios.descripcion as descripcion','petitorios.id as id')
                                  ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                  ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                  ->where('petitorio_servicio.servicio_id',$servicio_id)
                                  ->where('petitorios.estado',1)
                                  ->where('petitorios.nivel_id','<',$nivel)
                                  ->where('petitorio_servicio.tipo_dispositivo_medico_id',$tipo_dispositivo)
                                  ->orderby('descripcion','asc')
                                  ->get();
        
        return $petitorios;
    }

    public static function GetPetitoriosNivel2y3D($tipo_dispositivo, $nivel, $servicio_id) {

        $petitorios = DB::table('servicios')
                                  ->select('petitorios.descripcion as descripcion','petitorios.id as id')
                                  ->join('petitorio_servicio','petitorio_servicio.servicio_id','servicios.id')
                                  ->join('petitorios', 'petitorio_servicio.petitorio_id','petitorios.id')
                                  ->where('petitorio_servicio.servicio_id',$servicio_id)
                                  ->where('petitorios.estado',1)
                                  ->where('petitorios.nivel_id','<',$nivel)
                                  ->where('petitorio_servicio.tipo_dispositivo_medico_id','>',$tipo_dispositivo)
                                  ->orderby('descripcion','asc')
                                  
                                  ->get();
        
        return $petitorios;
    }

    public static function GetPetitoriosPorNivel2y3($tipo_dispositivo, $nivel) {

        $petitorios = DB::table('petitorios')
                    ->where('tipo_dispositivo_medicos_id',$tipo_dispositivo)
                    ->where('nivel_id','<=',$nivel)
                    ->where('estado',1)
                    ->orderby('descripcion','asc')
                    ->get();
        
        return $petitorios;
    }

    public static function PetitoriosPorNivel1D() {

        $petitorios = DB::table('petitorios')
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id','<',3)
                        ->where('estado',1)
                        ->orderby('descripcion','asc')
                        ->pluck('petitorios.descripcion','petitorios.id');

        return $petitorios;
    }

    public static function GetPetitoriosPorNivel1D() {

        $petitorios = DB::table('petitorios')
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id','<',3)
                        ->where('estado',1)
                        ->orderby('descripcion','asc')
                        ->get();

        return $petitorios;
    }

    public static function PetitoriosPorNivel1DA() {

        $petitorios = DB::table('petitorios')
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id',1)
                        ->where('estado',1)
                        ->orderby('descripcion','asc')
                        ->pluck('petitorios.descripcion','petitorios.id');

        return $petitorios;
    }

    public static function GetPetitoriosPorNivel1DA() {

        $petitorios = DB::table('petitorios')
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id',1)
                        ->where('estado',1)
                        ->orderby('descripcion','asc')
                        ->get();

        return $petitorios;
    }

    public static function PetitoriosPorNivel($nivel) {

        $petitorios = DB::table('petitorios')
                        ->where('tipo_dispositivo_medicos_id','>',1)
                        ->where('nivel_id','<=',$nivel)
                        ->where('estado',1)
                        ->orderby('descripcion','asc')
                        ->pluck('petitorios.descripcion','petitorios.id');

        return $petitorios;
    }

    

}
