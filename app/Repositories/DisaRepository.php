<?php

namespace App\Repositories;

use App\Models\Disa;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DisaRepository
 * @package App\Repositories
 * @version February 24, 2018, 8:18 am UTC
 *
 * @method Disa findWithoutFail($id, $columns = ['*'])
 * @method Disa find($id, $columns = ['*'])
 * @method Disa first($columns = ['*'])
*/
class DisaRepository extends BaseRepository
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
        return Disa::class;
    }
}
