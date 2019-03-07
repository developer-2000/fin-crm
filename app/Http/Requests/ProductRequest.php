<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'title'   => 'required|max:255',
            //  'project_id' => 'required|int',
            //  'product_id' => 'required',
            // 'category_id' => 'required|int',
            //'weight' => 'required|int',
            //'price_cost' => 'required|int',
           // 'status' => 'required|string|max:3',
        ];
    }
}
