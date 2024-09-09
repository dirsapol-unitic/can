<?php

namespace App\Repositories;

use App\Models\Distribucion;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DistribucionRepository
 * @package App\Repositories
 * @version June 24, 2018, 12:57 am UTC
 *
 * @method Distribucion findWithoutFail($id, $columns = ['*'])
 * @method Distribucion find($id, $columns = ['*'])
 * @method Distribucion first($columns = ['*'])
*/
class DistribucionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nombre'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Distribucion::class;
    }
}
