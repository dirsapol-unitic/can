<?php

namespace App\Repositories;

use App\Models\Dpto;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DivisionRepository
 * @package App\Repositories
 * @version June 25, 2018, 7:16 pm UTC
 *
 * @method Division findWithoutFail($id, $columns = ['*'])
 * @method Division find($id, $columns = ['*'])
 * @method Division first($columns = ['*'])
*/
class DptoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nombre_unidad',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Dpto::class;
    }
}
