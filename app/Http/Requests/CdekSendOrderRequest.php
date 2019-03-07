<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CdekSendOrderRequest extends FormRequest
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
          'approve.house' => "required|string|min:1|max:30|bail" ,
          'approve.street' => "required|string|min:1|max:30|bail",
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
