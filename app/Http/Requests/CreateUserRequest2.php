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
              'dni'=>'required|min:8|unique:users',
              'nombres'=>'required',
              'apellidos'=>'required',
              'telefono'=>'required|min:9',
              'grado_id'=>'required',
              'email' => 'required|email|unique:users',
              'establecimiento_id'=>'required'
              'password' => 'required|min:8',

        ];
    }

}