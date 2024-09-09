<?php

namespace App\Repositories;

use App\Models\Can;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CanRepository
 * @package App\Repositories
 * @version June 19, 2018, 10:41 pm UTC
 *
 * @method Can findWithoutFail($id, $columns = ['*'])
 * @method Can find($id, $columns = ['*'])
 * @method Can first($columns = ['*'])
*/
class CanRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mes_id',
        'desc_mes',
        'year_id',
        'ano'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Can::class;
    }
}
