<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class Estimacion2 extends Model
{
    use SoftDeletes;

    public $table = 'estimacion_rubro';
    

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
        'rubro_id',
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
        'rubro_id' => 'integer',
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

    public function tipo_dispositivo(){

        return $this->belongsTo('App\Models\TipoDispositivoMedico','tipo_dispositivo_medico_id');
    }
    
}

