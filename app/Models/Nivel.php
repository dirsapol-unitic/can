<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Nivel
 * @package App\Models
 * @version January 29, 2018, 10:32 pm UTC
 *
 * @property string nivel
 */
class Nivel extends Model
{
    use SoftDeletes;

    public $table = 'nivels';
    

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

    public function petitorio(){

        return $this->hasMany('App\Models\Petitorio');
    }
    
    
    public function establecimiento(){

        return $this->hasMany('App\Models\Establecimientos');
    }
}
