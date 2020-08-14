<?php

namespace Photo\Http\Requests\Albums;

use Illuminate\Foundation\Http\FormRequest;
use Photo\Models\Album;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('create', Album::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|max:150',
            'description' => 'nullable|max:191',
        ];
    }
}
