<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TipoInternamiento
 * @package App\Models
 * @version January 31, 2018, 11:03 am UTC
 *
 * @property string descripcion
 */
class TipoInternamiento extends Model
{
    use SoftDeletes;

    public $table = 'tipo_internamientos';
    

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
        
    ];

    public function establecimiento(){

        return $this->hasMany('App\Models\Establecimiento');
    }
    
}
