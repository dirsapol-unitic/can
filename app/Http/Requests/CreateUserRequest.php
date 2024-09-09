<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [ 
              'dni'=>'required|numeric|min:8',
              'nombres'=>'required',
              'apellido_paterno'=>'required',
              'apellido_materno'=>'required',
              'telefono'=>'required|numeric|min:9',
              'grado'=>'required',
              'email' => 'required|email|unique:users',
              'establecimiento_id'=>'required',
              'password' => 'required|min:8',

        ];
    }
}
