<?php

namespace App\Repositories;

use App\Models\UnidadMedida;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UnidadMedidaRepository
 * @package App\Repositories
 * @version February 24, 2018, 4:34 pm UTC
 *
 * @method UnidadMedida findWithoutFail($id, $columns = ['*'])
 * @method UnidadMedida find($id, $columns = ['*'])
 * @method UnidadMedida first($columns = ['*'])
*/
class UnidadMedidaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'descripcion'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UnidadMedida::class;
    }
}
