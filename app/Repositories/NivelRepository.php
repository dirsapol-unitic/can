<?php

namespace App\Repositories;

use App\Models\Nivel;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NivelRepository
 * @package App\Repositories
 * @version February 24, 2018, 8:15 am UTC
 *
 * @method Nivel findWithoutFail($id, $columns = ['*'])
 * @method Nivel find($id, $columns = ['*'])
 * @method Nivel first($columns = ['*'])
*/
class NivelRepository extends BaseRepository
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
        return Nivel::class;
    }
}
