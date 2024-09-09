<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Establecimientos
 * @package App\Models
 * @version February 1, 2018, 7:57 am UTC
 *
 * @property integer codigo
 * @property string nombre_establecimiento
 * @property integer region_red
 * @property integer nivel
 * @property integer categoria
 * @property integer tipo_ipress
 * @property integer tipo_internamiento
 * @property integer departamento
 * @property integer provincia
 * @property integer distrito
 * @property integer disa
 * @property string norte
 * @property string este
 */
class Indicador extends Model
{
    use SoftDeletes;

    public $table = 'indicadores';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'ici_id',
        'anomes',
        'region_id',
        'nivel_id',
        'categoria_id',
        'tipo_establecimiento_id',
        'tipo_internamiento_id',
        'establecimiento_id',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'normostock_cantidad',
        'normostock_porcentaje',
        'normostock_puntaje',
        'substock_cantidad',
        'substock_porcentaje',
        'substock_puntaje',
        'sobrestock_cantidad',
        'sobrestock_porcentaje',
        'sobrestock_puntaje',
        'sinrotacion_cantidad',
        'sinrotacion_porcentaje',
        'sinrotacion_puntaje',
        'desabastecido_cantidad',
        'desabastecido_porcentaje',
        'desabastecido_puntaje',
        'existente_cantidad',
        'existente_porcentaje',
        'existente_puntaje',
        'disponible_cantidad',
        'disponible_porcentaje',
        'disponible_puntaje',
        'total_items',
        'total_puntaje'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ici_id'=> 'integer',
        'anomes'=> 'string',
        'region_id'=> 'integer',
        'nivel_id'=> 'integer',
        'categoria_id'=> 'integer',
        'tipo_establecimiento_id'=> 'integer',
        'tipo_internamiento_id'=> 'integer',
        'establecimiento_id'=> 'integer',
        'departamento_id'=> 'integer',
        'provincia_id'=> 'integer',
        'distrito_id'=> 'integer',
        'normostock_cantidad'=> 'integer',
        'normostock_porcentaje'=> 'double',
        'normostock_puntaje'=> 'double',
        'substock_cantidad'=> 'integer',
        'substock_porcentaje'=> 'double',
        'substock_puntaje'=> 'double',
        'sobrestock_cantidad'=> 'integer',
        'sobrestock_porcentaje'=> 'double',
        'sobrestock_puntaje'=> 'double',
        'sinrotacion_cantidad'=> 'integer',
        'sinrotacion_porcentaje'=> 'double',
        'sinrotacion_puntaje'=> 'double',
        'desabastecido_cantidad'=> 'integer',
        'desabastecido_porcentaje'=> 'double',
        'desabastecido_puntaje'=> 'double',
        'existente_cantidad'=> 'integer',
        'existente_porcentaje'=> 'double',
        'existente_puntaje'=> 'double',
        'disponible_cantidad'=> 'integer',
        'disponible_porcentaje'=> 'double',
        'disponible_puntaje'=> 'double',
        'total_items'=> 'integer',
        'total_puntaje'=> 'double',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    //devolver valores de 1 a muchos
    public function ind_categoria(){

        return $this->belongsTo('App\Models\Categoria','categoria_id');
    }

    public function ind_establecimiento(){

        return $this->belongsTo('App\Models\Establecimiento','establecimiento_id');
    }

    public function ind_departamento(){

        return $this->belongsTo('App\Models\Departamento','departamento_id');
    }

    public function ind_nivel(){

        return $this->belongsTo('App\Models\Nivel','nivel_id');
    }
    
    public function ind_distrito(){

        return $this->belongsTo('App\Models\Distrito','distrito_id');
    }

    public function ind_provincia(){

        return $this->belongsTo('App\Models\Provincia','provincia_id');
    }

    
    public function ind_region(){
        return $this->belongsTo('App\Models\Region','region_id');
    }

    public function ind_tipo(){

        return $this->belongsTo('App\Models\TipoEstablecimiento','tipo_establecimiento_id');
    }

}
