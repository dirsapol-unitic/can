<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Year
 * @package App\Models
 * @version March 22, 2018, 10:56 am UTC
 *
 * @property string descripcion
 */
class Serv extends Model
{
    use SoftDeletes;

    public $table = 'serv';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'dpto_id',
        'servicio_id',
        'codigo',
        'nombre_servicio',
  
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'dpto_id' => 'integer',
        'servicio_id' => 'integer',
        'codigo' => 'string',
        'nombre_servicio' => 'string',

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

}
