<?php

namespace App\Repositories;

use App\Models\Distrito;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DistritoRepository
 * @package App\Repositories
 * @version February 24, 2018, 8:23 am UTC
 *
 * @method Distrito findWithoutFail($id, $columns = ['*'])
 * @method Distrito find($id, $columns = ['*'])
 * @method Distrito first($columns = ['*'])
*/
class DistritoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nombre_dist',
        'provincia_id',
        'departamento_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Distrito::class;
    }
}
