<?php

namespace App\Repositories;

use App\Models\Division;
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
class DivisionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nombre_division',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Division::class;
    }
}
