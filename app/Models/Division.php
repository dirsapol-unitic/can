<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Division
 * @package App\Models
 * @version June 25, 2018, 7:16 pm UTC
 *
 * @property string nombre_division
 * @property integer establecimiento_id
 * @property integer region_id
 */
class Division extends Model
{
    use SoftDeletes;

    public $table = 'divisions';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'nombre_division',        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nombre_division' => 'string',        
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
