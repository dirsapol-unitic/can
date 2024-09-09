<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

/**
 * Class Provincia
 * @package App\Models
 * @version February 7, 2018, 1:17 am UTC
 *
 * @property string nombre_prov
 * @property integer departamento_id
 */
class Provincia extends Model
{
    use SoftDeletes;

    public $table = 'provincias';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'nombre_prov',
        'departamento_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nombre_prov' => 'string',
        'departamento_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function departamento(){

        return $this->belongsTo('App\Models\Departamento','departamento_id');
    }

    public static function provincias($id){
        return Provincia::where('departamento_id','=',$id)
        ->get();
    }

    public function getProvincia($id){
        
        $cad="select provincias.id, provincias.nombre_prov nombre from provincias inner join departamentos on departamentos.id=provincias.departamento_id where provincias.departamento_id = ".$id;
        $data = DB::select($cad);

        return $data;
    }

}
