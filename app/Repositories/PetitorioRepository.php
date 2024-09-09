<?php

namespace App\Repositories;

use App\Models\Petitorio;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PetitorioRepository
 * @package App\Repositories
 * @version February 24, 2018, 4:40 pm UTC
 *
 * @method Petitorio findWithoutFail($id, $columns = ['*'])
 * @method Petitorio find($id, $columns = ['*'])
 * @method Petitorio first($columns = ['*'])
*/
class PetitorioRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'codigo',
        'principio_activo',
        'descripcion',
        'precio',
        'concentracion',
        'form_farm',
        'presentacion',
        'tipo_dispositivo_medicos_id',
        'unidad_medida_id',
        'nivel_id',
        'tipo_uso_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Petitorio::class;
    }
}
