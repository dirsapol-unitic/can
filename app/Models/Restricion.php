<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Restricion
 * @package App\Models
 * @version July 5, 2018, 4:24 pm UTC
 *
 * @property string codigo
 * @property string nombre_restriccion
 */
class Restricion extends Model
{
    use SoftDeletes;

    public $table = 'restricions';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'codigo',
        'descripcion'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'codigo' => 'string',
        'descripcion' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    
}
