<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Api\WeFast\WeFastKey;
use App\Models\Project;
use Symfony\Component\HttpFoundation\Response;

class KazpostSenderRequest extends Request
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
            'name_last'      => 'required|max:255',
            'name_fm'        => 'required|max:255',
            'city'           => 'required|max:255',
            'address'        => 'required|max:255',
            'index'          => 'required|max:255',
            'code'           => 'max:255',
            'doc'            => 'max:255',
            'doc_num'        => 'max:255',
            'doc_day'        => 'max:255',
            'doc_month'      => 'max:255',
            'doc_year'       => 'max:255',
            'doc_body'       => 'max:255',
            'payment_code'   => 'max:255',
            'document'       => 'max:255',
            'support_phone'  => 'max:255',
        ];
    }
}
