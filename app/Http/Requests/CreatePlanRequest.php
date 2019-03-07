<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Symfony\Component\HttpFoundation\Response;
class CreatePlanRequest extends Request
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
            'name' => 'required|max:10',
        ];
    }

//    // OPTIONAL OVERRIDE
//    public function forbiddenResponse()
//    {
//        // Optionally, send a custom response on authorize failure
//        // (default is to just redirect to initial page with errors)
//        //
//        // Can return a response, a view, a redirect, or whatever else
//        return Response::make('Permission denied foo!', 403);
//    }
//
//    // OPTIONAL OVERRIDE
//    public function response()
//    {
//        // If you want to customize what happens on a failed validation,
//        // override this method.
//        // See what it does natively here:
//        // https://github.com/laravel/framework/blob/master/src/Illuminate/Foundation/Http/FormRequest.php
//    }
}
