<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderSendingRequest extends FormRequest
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
         // 'proc_status' => 'required|int',
          'volume_general' => 'required',
          'description' => 'required|max:50',
          'add_information' => 'max:50',
          'weight' => 'required',
          'approve.city' => 'required',
          'approve.warehouse' => 'required'
        ];
    }
}
