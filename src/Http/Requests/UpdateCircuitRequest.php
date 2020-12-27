<?php

namespace Bishopm\Churchnet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCircuitRequest extends FormRequest
{
    public function rules()
    {
        return [
            'circuit' => 'required',
            'slug' => 'required|unique:circuits,id,'.$this->get('id'),
            'district_id' => 'required',
            'circuitnumber' => 'required'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
        ];
    }
}
