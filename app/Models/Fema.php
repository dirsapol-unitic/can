<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Fema
 * @package App\Models
 * @version June 30, 2018, 3:22 am UTC
 *
 * @property integer can_id
 * @property integer establecimiento_id
 * @property string cod_establecimiento
 */
class Fema extends Model
{
    use SoftDeletes;

    public $table = 'femas';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'can_id',
        'establecimiento_id',
        'cod_establecimiento',
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
        'petitorio_id',
        'descripcion_petitorio',
        'tipo_dispositivo_id',
        'nombre_establecimiento',
        'cod_petitorio',
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

    
    
}
