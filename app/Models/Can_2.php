<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
/**
 * Class Can
 * @package App\Models
 * @version June 19, 2018, 10:41 pm UTC
 *
 * @property integer mes_id
 * @property string desc_mes
 * @property integer year_id
 * @property string ano
 */
class Can extends Model
{
    use SoftDeletes;

    public $table = 'cans';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'mes_id',
        'desc_mes',
        'year_id',
        'ano'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'mes_id' => 'integer',
        'desc_mes' => 'string',
        'year_id' => 'integer',
        'ano' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function establecimientos(){

        return $this->belongsToMany('App\Models\Establecimiento');
    }

    public function servicios(){

        return $this->belongsToMany('App\Models\Servicio');
    }
    
    public function rubros(){

        return $this->belongsToMany('App\Models\Rubro');
    }

    public function anos(){

        return $this->belongsTo('App\Models\Year','year_id');
    }

    public function meses(){

        return $this->belongsTo('App\Models\Mes','mes_id');
    }
    
    public function petitorios(){
        return $this->belongsToMany('App\Models\Petitorio');
    }

    public static function BuscaCanRepetido($year, $mes) {
        $cad = "select count(*) as total
            from cans
            where active =true and ano ='{$year}' and mes_id='{$mes}' ";
        
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    

    public static function RegistrarCan($mes, $meses, $ano, $nombre_can, $multianual, $tiempo, $id_can, $id_user, $year_id) {

        $cad = "Select sp_insert_can(" . $mes . ",'" . $meses . "'," . $ano . ",'" . $nombre_can . "'," . $multianual . "," . $tiempo . "," . $id_can . "," . $id_user . "," . $year_id . ");";
        $data = DB::select($cad);
        
        return $data[0];

    }

    public static function ContarCanServicio($can_id, $establecimiento_id) {
        $cad = "select count(*) as total
            from can_servicio
            where estado = 1  and can_id='{$tipo_dispositivo}' and establecimiento_id ='{$establecimiento_id}' ";
        
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ContarMedicamentoCerrado($can_id, $establecimiento_id) {
        $cad = "select count(*) as total
            from can_servicio
            where estado = 1  and can_id='{$tipo_dispositivo}' and establecimiento_id ='{$establecimiento_id}' and 
            ( medicamento_cerrado = 2 or medicamento_cerrado = 3)  ";
        
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function ContarDispositivoCerrado($can_id, $establecimiento_id) {
        $cad = "select count(*) as total
            from can_servicio
            where estado = 1  and can_id='{$tipo_dispositivo}' and establecimiento_id ='{$establecimiento_id}' and 
            ( dispositivo_cerrado = 2 or dispositivo_cerrado = 3)  ";
        
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function RegistrarAtencionCanEstablecimientos($mes, $meses, $ano, $nombre_can, $multianual, $tiempo, $id_can, $id_user, $year_id) {

        $cad = "Select sp_insert_atencion_can_establecimiento(" . $mes . ",'" . $meses . "'," . $ano . ",'" . $nombre_can . "'," . $multianual . "," . $tiempo . "," . $id_can . "," . $id_user . "," . $year_id . ");";
        $data = DB::select($cad);
        
        return $data[0];

    }

    public static function BuscaCanEstablecimiento($can_id, $establecimiento_id) {
        $cad = "select count(*) as total
            from can_establecimiento
            where can_id='{$can_id}' and establecimiento_id='{$establecimiento_id}' ";
        
        $data = DB::select($cad);
        if (isset($data[0]))
            return $data[0];
        else
            return $data;
    }

    public static function RegistrarProductosElegidos($can_id, $nivel, $tipo, $establecimiento_id,$codigo_establecimiento, $nombre_establecimiento, $petitorios) {

        $cad = "Select sp_insert_productos_elegidos(?,?,?,?,?,?,?)";
        $data = DB::select($cad, array($can_id, $nivel, $tipo,$establecimiento_id,$codigo_establecimiento,$nombre_establecimiento,$petitorios));
        
        
        return $data[0];

    }

}
