<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AccessRequest;
use Illuminate\Support\Facades\DB;

class AccessController extends Controller
{

  /*
  * Show panel in access modal when add rule
  */
  public function addRule(Request $request)
  {
    $request->count++;

    return response()->json([
      'id' => '#' . $request->type . '-collapse-' . $request->count,
      'html' => view('modal_access.panel')
            ->with('type', $request->type)
            ->with('number', $request->count)
            ->render()
          ]);
  }
  /*
  * Save access
  */
  public function store(AccessRequest $request)
  {
    //$access = $request->input('access');
    $access = [
        'project_id' => 12,
        'user_id' => 7,
    ];

    $result = DB::table('test_table_access')
                ->where('access->project_id', 12)
                ->get();
                print_r($result);exit();
  }
}
