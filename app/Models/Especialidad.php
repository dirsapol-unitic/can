<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Especialidad
 * @package App\Models
 * @version July 5, 2018, 7:13 pm UTC
 *
 * @property string codigo
 * @property string nombre_servicio
 */
class Especialidad extends Model
{
    use SoftDeletes;

    public $table = 'especialidads';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'codigo',
        'nombre_servicio'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'codigo' => 'string',
        'nombre_servicio' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function petitorios(){
        return $this->belongsToMany('App\Models\Petitorio');
    }
    
}
