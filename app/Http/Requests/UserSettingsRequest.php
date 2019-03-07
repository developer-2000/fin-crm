<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserSettingsRequest extends FormRequest
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
            'id'        => 'required|exists:' . User::tableName(),
            'time_zone' => 'timezone',
            'language'  => 'in:' . implode(',', array_keys(TranslationRepository::getLanguages()))
        ];
    }
}
