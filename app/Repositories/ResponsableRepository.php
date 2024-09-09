<?php

namespace App\Repositories;

use App\Models\Responsable;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ResponsableRepository
 * @package App\Repositories
 * @version June 24, 2018, 6:57 pm UTC
 *
 * @method Responsable findWithoutFail($id, $columns = ['*'])
 * @method Responsable find($id, $columns = ['*'])
 * @method Responsable first($columns = ['*'])
*/
class ResponsableRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'can_id',
        'servicio_id',
        'nombre',
        'descripcion_grado',
        'celular',
        'nombre_establecimiento'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Responsable::class;
    }
}
