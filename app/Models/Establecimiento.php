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
class Establecimiento extends Model
{
    use SoftDeletes;

    public $table = 'establecimientos';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'codigo_establecimiento',
        'nombre_establecimiento',
        'region_id',
        'nivel_id',
        'categoria_id',
        'tipo_establecimiento_id',
        'tipo_internamiento_id',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'disa_id',
        'norte',
        'este',
        'estado'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'codigo_establecimiento' => 'string',
        'nombre_establecimiento' => 'string',
        'region_id' => 'integer',
        'nivel_id' => 'integer',
        'categoria_id' => 'integer',
        'tipo_establecimiento_id' => 'integer',
        'tipo_internamiento_id' => 'integer',
        'departamento_id' => 'integer',
        'provincia_id' => 'integer',
        'distrito_id' => 'integer',
        'disa_id' => 'integer',
        'norte' => 'string',
        'este' => 'string',
        'estado' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    //devolver valores de 1 a muchos
    public function est_categoria(){

        return $this->belongsTo('App\Models\Categoria','categoria_id');
    }

    public function est_departamento(){

        return $this->belongsTo('App\Models\Departamento','departamento_id');
    }

    public function est_nivel(){

        return $this->belongsTo('App\Models\Nivel','nivel_id');
    }

    public function est_usuario(){

        return $this->belongsTo('App\Models\User','establecimiento_id');
    }

    public function est_disa(){

        return $this->belongsTo('App\Models\Disa','disa_id');
    }
    public function est_distrito(){

        return $this->belongsTo('App\Models\Distrito','distrito_id');
    }

    public function est_provincia(){

        return $this->belongsTo('App\Models\Provincia','provincia_id');
    }

    
    public function est_region(){
        return $this->belongsTo('App\Models\Region','region_id');
    }

    public function est_tipo(){

        return $this->belongsTo('App\Models\TipoEstablecimiento','tipo_establecimiento_id');
    }

    public function est_internamiento(){

        return $this->belongsTo('App\Models\TipoInternamiento','tipo_internamiento_id');
    }

    
    public function user(){

        return $this->hasMany('App\Models\Users');

    }

    public function petitorios(){
        return $this->belongsToMany('App\Models\Petitorio');
    }

    public function rubros(){
        return $this->belongsToMany('App\Models\Rubro');
    }

    public function servicios(){
        return $this->belongsToMany('App\Models\Servicio');
    }

    public function divisions(){
        return $this->belongsToMany('App\Models\Division');
    }
    
    public function est_unidad_medida(){

        return $this->belongsTo('App\Models\UnidadMedida','unidad_medida_id');
    }

    public function est_tipo_uso(){

        return $this->belongsTo('App\Models\TipoUso','tipo_uso_id');
    }
    
    public function est_tipo_dispositivo(){

        return $this->belongsTo('App\Models\TipoDispositivoMedico','tipo_dispositivo_medico_id');
    }

    public function femas(){
        return $this->belongsToMany('App\Models\Fema');
    }

}
