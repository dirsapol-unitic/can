<?php

namespace App\Repositories;

use App\Models\TipoInternamiento;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TipoInternamientoRepository
 * @package App\Repositories
 * @version February 24, 2018, 8:18 am UTC
 *
 * @method TipoInternamiento findWithoutFail($id, $columns = ['*'])
 * @method TipoInternamiento find($id, $columns = ['*'])
 * @method TipoInternamiento first($columns = ['*'])
*/
class TipoInternamientoRepository extends BaseRepository
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
        return TipoInternamiento::class;
    }
}
