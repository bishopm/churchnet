<?php

namespace Bishopm\Churchnet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLabelRequest extends FormRequest
{
    public function rules()
    {
        return [
            'label' => 'required',
            'description' => 'required'
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
