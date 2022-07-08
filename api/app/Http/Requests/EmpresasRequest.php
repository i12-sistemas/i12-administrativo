<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Uppercase;

class EmpresasRequest extends FormRequest
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
            'razao' => 'required|between:2,150',
            'fantasia' => 'between:2,150',
            'situacao' => 'required|numeric',
            'pessoa' => 'required|min:1;max:1',
        ];
    }
    public function messages()
    {
        return [
            'razao.required' => 'Nome ou Razão Social é obrigatório',
            'razao.between' => 'Nome ou Razão Social de conter de :min a :max caracteres',
            'required' => ':attribute é um campo obrigatório.',
        ];
    }
}
