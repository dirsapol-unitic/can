<?php

namespace App\Repositories;

use App\Models\Serv;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ServicioRepository
 * @package App\Repositories
 * @version June 18, 2018, 3:24 pm UTC
 *
 * @method Servicio findWithoutFail($id, $columns = ['*'])
 * @method Servicio find($id, $columns = ['*'])
 * @method Servicio first($columns = ['*'])
*/
class ServRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'dpto_id',
        'servicio_id',
        'codigo character',
        'nombre_servicio',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Serv::class;
    }
}
