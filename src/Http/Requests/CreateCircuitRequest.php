<?php

namespace Bishopm\Churchnet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCircuitRequest extends FormRequest
{
    public function rules()
    {
        return [
            'circuit' => 'required',
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
