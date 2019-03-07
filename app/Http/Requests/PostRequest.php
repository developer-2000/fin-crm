<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostRequest extends FormRequest
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
          'title' => "required|string|min:3|max:100|regex:/^[a-zA-Zа-яА-Я0-9]+$/ui|bail",
          'body' => "required|string|bail",
          'category_id' => "required|integer|bail",
          'priority' => "required|string|bail",
        //  'publish_at' => "nullable|date|bail",
        ];
    }


    /**
     * Throws error response when validation is failed.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'field'    => $validator->errors()->keys()[0]
        ])
        );
    }
}
