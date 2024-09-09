<?php

namespace App\Repositories;

use App\Models\TipoUso;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TipoUsoRepository
 * @package App\Repositories
 * @version February 24, 2018, 4:34 pm UTC
 *
 * @method TipoUso findWithoutFail($id, $columns = ['*'])
 * @method TipoUso find($id, $columns = ['*'])
 * @method TipoUso first($columns = ['*'])
*/
class TipoUsoRepository extends BaseRepository
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
        return TipoUso::class;
    }
}
