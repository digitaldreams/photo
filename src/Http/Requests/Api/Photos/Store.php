<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 5/2/2018
 * Time: 9:47 AM
 */

namespace Photo\Http\Requests\Api\Photos;

use Illuminate\Foundation\Http\FormRequest;
use Photo\Models\Photo;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('create', Photo::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file.*' => 'image|required',
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