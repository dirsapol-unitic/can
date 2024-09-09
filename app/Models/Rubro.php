<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

/**
 * Class Rubro
 * @package App\Models
 * @version July 6, 2018, 1:58 pm UTC
 *
 * @property string descripcion
 * @property string codigo
 */
class Rubro extends Model
{
    use SoftDeletes;

    public $table = 'rubros';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'descripcion',
        'codigo',
        'consolidado',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'descripcion' => 'string',
        'codigo' => 'string',
        'consolidado' => 'integer',
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

    public function getRubro($id){
        
        $cad="select id, descripcion from rubros inner join establecimiento_rubro on rubros.id=establecimiento_rubro.rubro_id where establecimiento_id = ".$id;
        $data = DB::select($cad);

        return $data;
    }
}
