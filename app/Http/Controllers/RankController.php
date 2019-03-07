<?php

namespace App\Http\Controllers;

use App\Repositories\RankRepository;
use Illuminate\Http\Request;
use App\Models\Rank;
use App\Models\Role;

class RankController extends BaseController
{

    public function usersRanks()
    {
        return view('ranks.ranks', [
            'ranks' => Rank::with('role')->get(),
            'roles' => Role::all()
        ]);
    }

    public function usersRanksCreateAjax(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:1|max:255',
            'role' => 'required|numeric'
        ]);
        $rank = new Rank;
        $rank->name = $request->get('name');
        $rank->role_id = $request->get('role');
        $result = $rank->save();
        return response()->json([
            'success' => $result,
            'tableHtml' => view('ranks.ranks-table', [
                'ranks' => Rank::with('role')->get(),
                'roles' => Role::all()
            ])->render()
        ]);
    }

    public function usersRanksUpdateAjax(Request $request)
    {
        $rules = [
            'pk' => 'required|numeric',
            'name' => 'required|min:1|max:255|in:name,role_id',
            'value' => 'required|min:1'
        ];

        if ($request->get('name') == 'role_id') {
            $rules['value'] .= '|numeric';
        }

        $this->validate($request, $rules);
        $rank = Rank::find($request->get('pk'));
        if ($rank) {
            $nameMethod = $request->get('name');
            $rank->$nameMethod = $request->get('value');
            $result = $rank->save();
            return response()->json([
                'success' => $result,
            ]);
        }
        abort(404);
    }

    public function usersRanksDeleteAjax(Request $request)
    {
        $this->validate($request, [
            'pk' => 'required|numeric|exists:ranks,id'
        ]);

        $rank = Rank::find($request->get('pk'));
        $result['success'] = $rank->delete();
        $result['id'] = $request->get('pk');
        return response()->json($result);
    }

    public static function findByName(Request $request)
    {
        $term = trim($request->input('query'));
        $rolesIds = $request->rolesIds;

        $ranks = RankRepository::findByName($term, $rolesIds);
        $formatted_ranks = [];

        foreach ($ranks as $rank) {
            $formatted_ranks[] = ['id' => $rank->id, 'text' => $rank->name];
        }

        return \Response::json($formatted_ranks);
    }
}
