<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class LogErrosSaveRequest extends FormRequest
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
            'id' => 'required|numeric'
            // 'tipo' => 'required|numeric',
            // 'problema' => 'required|min:1',
            // 'versaobd' => 'required|min:1',
            // 'cnpj' => 'required|min:1'
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute - campo requerido.',
        ];
    } 

    
    
}
    
