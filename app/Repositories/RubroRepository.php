<?php

namespace App\Repositories;

use App\Models\Rubro;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RubroRepository
 * @package App\Repositories
 * @version July 6, 2018, 1:58 pm UTC
 *
 * @method Rubro findWithoutFail($id, $columns = ['*'])
 * @method Rubro find($id, $columns = ['*'])
 * @method Rubro first($columns = ['*'])
*/
class RubroRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'descripcion',
        'codigo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Rubro::class;
    }
}
