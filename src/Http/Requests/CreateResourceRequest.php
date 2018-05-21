<?php

namespace Bishopm\Churchnet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateResourceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required',
            'url' => 'required',
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
