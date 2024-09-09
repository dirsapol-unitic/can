<?php

namespace App\Repositories;

use App\Models\Div;
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
class DivRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'division_id',
        'nombre_division',
        'establecimiento_id',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Div::class;
    }
}
