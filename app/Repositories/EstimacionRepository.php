<?php

namespace App\Repositories;

use App\Models\Estimacion;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EstimacionRepository
 * @package App\Repositories
 * @version June 24, 2018, 12:53 am UTC
 *
 * @method Estimacion findWithoutFail($id, $columns = ['*'])
 * @method Estimacion find($id, $columns = ['*'])
 * @method Estimacion first($columns = ['*'])
*/
class EstimacionRepository extends BaseRepository
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
        return Estimacion::class;
    }
}
