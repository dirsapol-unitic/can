<?php

namespace App\Repositories;

use App\Models\TipoDispositivoMedico;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TipoDispositivoMedicoRepository
 * @package App\Repositories
 * @version February 24, 2018, 4:33 pm UTC
 *
 * @method TipoDispositivoMedico findWithoutFail($id, $columns = ['*'])
 * @method TipoDispositivoMedico find($id, $columns = ['*'])
 * @method TipoDispositivoMedico first($columns = ['*'])
*/
class TipoDispositivoMedicoRepository extends BaseRepository
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
        return TipoDispositivoMedico::class;
    }
}
