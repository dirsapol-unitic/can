<?php

namespace App\Repositories;

use App\Models\Fema;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FemaRepository
 * @package App\Repositories
 * @version June 30, 2018, 3:22 am UTC
 *
 * @method Fema findWithoutFail($id, $columns = ['*'])
 * @method Fema find($id, $columns = ['*'])
 * @method Fema first($columns = ['*'])
*/
class FemaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'can_id',
        'establecimiento_id',
        'cod_establecimiento'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Fema::class;
    }
}
