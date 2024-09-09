<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UnidadMedida
 * @package App\Models
 * @version January 29, 2018, 9:58 pm UTC
 *
 * @property string descripcion
 */
class UnidadMedida extends Model
{
    use SoftDeletes;

    public $table = 'unidad_medidas';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'descripcion'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'descripcion' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'descripcion' => 'required'
    ];

    /*public function petitorio(){

        return $this->hasMany('App\Models\Petitorio');
    }
    */
    
}
