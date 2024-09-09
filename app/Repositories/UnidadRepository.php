<?php

namespace App\Repositories;

//use App\Models\Unidad;
use App\Models\Dpto;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UnidadRepository
 * @package App\Repositories
 * @version June 25, 2018, 7:18 pm UTC
 *
 * @method Unidad findWithoutFail($id, $columns = ['*'])
 * @method Unidad find($id, $columns = ['*'])
 * @method Unidad first($columns = ['*'])
*/
class UnidadRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        /*'nombre_unidad',
        'division_id',
        'establecimiento_id',
        'region_id'*/
        'nombre_unidad',
        'division_establecimiento_id',
        'unidad_id',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Dpto::class;
        //return Unidad::class;
    }
}
