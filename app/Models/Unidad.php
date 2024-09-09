<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Unidad
 * @package App\Models
 * @version June 25, 2018, 7:18 pm UTC
 *
 * @property string nombre_unidad
 * @property integer division_id
 * @property integer establecimiento_id
 * @property integer region_id
 */
class Unidad extends Model
{
    use SoftDeletes;

    public $table = 'unidads';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'nombre_unidad',
             
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nombre_unidad' => 'string',
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function servicios(){
        return $this->belongsToMany('App\Models\Servicio');
    }
    
}
