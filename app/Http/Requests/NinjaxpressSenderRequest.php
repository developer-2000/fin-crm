<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Api\Ninjaxpress\NinjaxpressKey;
use App\Models\Api\WeFast\WeFastKey;
use App\Models\Project;
use Symfony\Component\HttpFoundation\Response;

class NinjaxpressSenderRequest extends Request
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
         //   'target_id'      => 'required|int',
            'name'           => 'required|max:255|unique:' . (new NinjaxpressKey)->getTable(),
            'email'          => 'required|max:255|unique:' . (new NinjaxpressKey)->getTable(),
            'password'       => 'required|max:255s',
            'subproject_id' => 'required|int',
            'client_id'      => 'required|max:255|unique:' . (new NinjaxpressKey)->getTable(),
            'client_secret'  => 'required|max:255|unique:' . (new NinjaxpressKey)->getTable(),
        ];
    }
}
