<?php

namespace App\Http\Controllers;

use App\Models\Variables;
use Illuminate\Http\Request;

class VariableController extends BaseController
{
    public function index()
    {
        return view('variables.index', [
            'variables' => Variables::all()
        ]);
    }

    public function changeVariableAjax(Request $request, Variables $variablesModel)
    {
        $this->validate($request, [
            'key'   => 'required|exists:variables',
            'value' => 'required',
        ]);

        return response()->json([
            'success'   => $variablesModel->setVariable($request->get('key'), $request->get('value'))
        ]);
    }
}
