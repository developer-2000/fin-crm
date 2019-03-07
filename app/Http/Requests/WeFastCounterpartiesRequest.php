<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Api\WeFast\WeFastKey;
use App\Models\Project;
use Symfony\Component\HttpFoundation\Response;

class WeFastCounterpartiesRequest extends Request
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
            'sub_project_id' => 'required|exists:' . Project::tableName() . ',id',
            'key'            => 'required|exists:' . WeFastKey::tableName() . ',id',
            'sender'         => 'required|min:1',
            'contact'        => 'required|min:1',
            'phone'          => 'required|min:1',
            'address'        => 'required|min:1',
            'warehouse'      => 'required|min:1',
            'province'       => 'required',
            'district'       => 'required',
            'ward'           => 'required',
        ];
    }
}
