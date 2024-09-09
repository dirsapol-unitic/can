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
class Div extends Model
{
    use SoftDeletes;

    public $table = 'division_establecimiento';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'nombre_division', 
        'establecimiento_id',       
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nombre_division' => 'string',
        'establecimiento_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function unidads(){
        return $this->belongsToMany('App\Models\Unidad');
    }
}
