<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
/**
 * Class Responsable
 * @package App\Models
 * @version June 24, 2018, 6:57 pm UTC
 *
 * @property integer user_id
 * @property integer can_id
 * @property integer servicio_id
 * @property string nombre
 * @property string descripcion_grado
 * @property string celular
 * @property string nombre_establecimiento
 */
class Responsable extends Model
{
    use SoftDeletes;

    public $table = 'responsables';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'can_id',
        'servicio_id',
        'nombre',
        'nombre_servicio',
        'descripcion_grado',
        'celular',
        'nombre_establecimiento'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'can_id' => 'integer',
        'servicio_id' => 'integer',
        'nombre' => 'string',
        'nombre_servicio' => 'string',
        'descripcion_grado' => 'string',
        'celular' => 'string',
        'nombre_establecimiento' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'servicio_id' => 'required'
    ];

    public static function GetJefeResponsable($can_id, $establecimiento_id, $servicio_id,$rol) {

        $data = DB::table('responsables')
                        ->where('can_id',$can_id)
                        ->where('rol',$rol)
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('servicio_id',$servicio_id)
                        ->where('estado',1)
                        ->get();

        return $data;
    }

    public static function GetBuscaResponsable($can_id, $establecimiento_id) {

        $data = DB::table('responsables as r')
                        ->select('r.servicio_id as tipo_dispositivo_id','r.id')
                        ->where('establecimiento_id',$establecimiento_id)
                        ->where('can_id',$can_id)
                        ->where('estado',1)
                        ->where('servicio_id','>',0)
                        ->where('servicio_id','<',6)
                        ->orderby('rol','asc')
                        ->get();

        return $data;
    }



    
}
