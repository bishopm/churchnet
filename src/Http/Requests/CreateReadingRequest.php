<?php

namespace Bishopm\Churchnet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReadingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'description' => 'required',
            'a' => 'required',
            'b' => 'required',
            'c' => 'required'
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
