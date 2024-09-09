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
class Dpto extends Model
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

    public function serv(){
        return $this->belongsToMany('App\Models\Serv');
    }
}
