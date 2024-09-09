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
class Year extends Model
{
    use SoftDeletes;

    public $table = 'years';
    

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

    public function ici(){

        return $this->hasMany('App\Models\Ici');
    }
}
