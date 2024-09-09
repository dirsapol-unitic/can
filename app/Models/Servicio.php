<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Establecimiento;
use DB;

/**
 * Class Servicio
 * @package App\Models
 * @version June 18, 2018, 3:24 pm UTC
 *
 * @property string codigo
 * @property string nombre_servicio
 */
class Servicio extends Model
{
    use SoftDeletes;

    public $table = 'servicios';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'codigo',
        'nombre_servicio',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'codigo' => 'string',
        'nombre_servicio' => 'string',
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

    public function especialidades(){
        return $this->belongsToMany('App\Models\Especialidad');
    }

    /*public static function servicios($id){
        return Servicio::where('unidad_id','=',$id)
        ->get();
    }
    */
    public function getServicio($id){
        
        $cad="select servicios.id, nombre_servicio from servicios inner join establecimiento_servicio on servicios.id=establecimiento_servicio.servicio_id where establecimiento_id = ".$id;
        $data = DB::select($cad);

        return $data;
    }
    
}
