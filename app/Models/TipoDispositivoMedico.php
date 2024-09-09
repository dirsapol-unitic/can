<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TipoDispositivoMedico
 * @package App\Models
 * @version January 28, 2018, 9:37 pm UTC
 *
 * @property string descripcion
 */
class TipoDispositivoMedico extends Model
{
    use SoftDeletes;

    public $table = 'tipo_dispositivo_medicos';
    

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
    
}
