<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Disa
 * @package App\Models
 * @version January 31, 2018, 12:15 pm UTC
 *
 * @property string codigo
 * @property string descripcion
 */
class Disa extends Model
{
    use SoftDeletes;

    public $table = 'disas';
    

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
        'codigo' => 'required',
        'descripcion' => 'required'
    ];

    public function establecimiento(){

        return $this->hasMany('App\Models\Establecimiento');
    }

    
}
