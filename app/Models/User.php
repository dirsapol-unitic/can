<?php

namespace App\Models;

use Caffeinated\Shinobi\Traits\ShinobiTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    use Notifiable, ShinobiTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','grado','nombre_establecimiento','dni','grado_id','establecimiento_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static $rules = [
        
    ];

    public function establecimientos(){

        return $this->belongsToMany('App\Models\Establecimiento');
    }

    public function grados(){

        return $this->belongsToMany('App\Models\Grado');
    }

    public function buscar_responsable($establecimiento_id) {
        
        $num_responsable=DB::table('users')
                    ->where('establecimiento_id',$establecimiento_id)
                    ->count();

        return $num_responsable;
    }  
}

