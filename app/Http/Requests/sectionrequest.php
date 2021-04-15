<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sectionrequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'section_name' =>'required|unique:sections|max:255',
            'descreption'  =>'required',
        ];
    }
}
