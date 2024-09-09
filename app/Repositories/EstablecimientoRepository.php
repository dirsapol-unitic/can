<?php

namespace App\Repositories;

use App\Models\Establecimiento;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EstablecimientoRepository
 * @package App\Repositories
 * @version February 24, 2018, 8:10 am UTC
 *
 * @method Establecimiento findWithoutFail($id, $columns = ['*'])
 * @method Establecimiento find($id, $columns = ['*'])
 * @method Establecimiento first($columns = ['*'])
*/
class EstablecimientoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'codigo_establecimiento',
        'nombre_establecimiento',
        'region_id',
        'nivel_id',
        'categoria_id',
        'tipo_establecimiento_id',
        'tipo_internamiento_id',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'disa_id',
        'norte',
        'este',
        'estado'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Establecimiento::class;
    }
}
