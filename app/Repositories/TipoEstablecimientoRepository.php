<?php

namespace App\Repositories;

use App\Models\TipoEstablecimiento;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TipoEstablecimientoRepository
 * @package App\Repositories
 * @version February 24, 2018, 8:17 am UTC
 *
 * @method TipoEstablecimiento findWithoutFail($id, $columns = ['*'])
 * @method TipoEstablecimiento find($id, $columns = ['*'])
 * @method TipoEstablecimiento first($columns = ['*'])
*/
class TipoEstablecimientoRepository extends BaseRepository
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
        return TipoEstablecimiento::class;
    }
}
