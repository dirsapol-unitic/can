<?php

namespace App\Repositories;

use App\Models\Restricion;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RestricionRepository
 * @package App\Repositories
 * @version July 5, 2018, 4:24 pm UTC
 *
 * @method Restricion findWithoutFail($id, $columns = ['*'])
 * @method Restricion find($id, $columns = ['*'])
 * @method Restricion first($columns = ['*'])
*/
class RestricionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'codigo',
        'descripcion'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Restricion::class;
    }
}
