<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Distribucion
 * @package App\Models
 * @version June 23, 2018, 1:04 pm UTC
 *
 * @property string nombre
 */
class Distribucion extends Model
{
    use SoftDeletes;

    public $table = 'distribucions';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'descripcion',
        'establecimiento_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'descripcion' => 'string',
        'establecimiento_id'=>'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function petitorios(){
        return $this->belongsToMany('App\Models\Petitorio');
    }

    public static function servicios($id){
        return Servicio::where('establecimiento_id','=',$id)
        ->get();
    }    
}
