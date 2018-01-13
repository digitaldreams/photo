<?php

namespace Photo\Http\Requests\Photos;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
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
            'user_id' => 'nullable|numeric',
            'caption' => 'nullable|max:191',
            'title' => 'nullable|max:191',
            'mime_type' => 'nullable|max:100',
            'src' => 'required|max:191',
            'location_id' => 'nullable|exists:photo_locations,id|numeric',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [

        ];
    }

}
