<?php

namespace App\Repositories;

use App\Models\Grado;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GradoRepository
 * @package App\Repositories
 * @version March 14, 2018, 5:53 am UTC
 *
 * @method Grado findWithoutFail($id, $columns = ['*'])
 * @method Grado find($id, $columns = ['*'])
 * @method Grado first($columns = ['*'])
*/
class GradoRepository extends BaseRepository
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
        return Grado::class;
    }
}
