<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Api\WeFast\WeFastKey;
use App\Models\Project;
use Symfony\Component\HttpFoundation\Response;

class RussianpostSenderRequest extends Request
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
            'name_first'     => 'required|max:255',
            'name_last'      => 'required|max:255',
            'name_middle'    => 'required|max:255',
            'city'           => 'required|max:255',
            'address'        => 'required|max:255',
            'index'          => 'required|max:255',
        ];
    }
}
