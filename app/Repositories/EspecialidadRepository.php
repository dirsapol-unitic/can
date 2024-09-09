<?php

namespace App\Repositories;

use App\Models\Especialidad;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EspecialidadRepository
 * @package App\Repositories
 * @version July 5, 2018, 7:13 pm UTC
 *
 * @method Especialidad findWithoutFail($id, $columns = ['*'])
 * @method Especialidad find($id, $columns = ['*'])
 * @method Especialidad first($columns = ['*'])
*/
class EspecialidadRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'codigo',
        'nombre_servicio'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Especialidad::class;
    }
}
