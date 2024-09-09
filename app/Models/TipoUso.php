<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TipoUso
 * @package App\Models
 * @version January 30, 2018, 2:22 am UTC
 *
 * @property string descripcion
 */
class TipoUso extends Model
{
    use SoftDeletes;

    public $table = 'tipo_usos';
    

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
