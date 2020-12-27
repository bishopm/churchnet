<?php

namespace Bishopm\Churchnet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocietyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'society' => 'required',
            'slug' => 'required|unique:societies,id,'.$this->get('id'),
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
