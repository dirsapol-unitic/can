<?php

namespace App\Repositories;

use App\Models\Year;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class YearRepository
 * @package App\Repositories
 * @version March 22, 2018, 10:56 am UTC
 *
 * @method Year findWithoutFail($id, $columns = ['*'])
 * @method Year find($id, $columns = ['*'])
 * @method Year first($columns = ['*'])
*/
class YearRepository extends BaseRepository
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
        return Year::class;
    }
}
