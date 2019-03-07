<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Project;
use App\Repositories\FilterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class OperationController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filter = [
            'project'     => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('operations') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $dataFilters = FilterRepository::processFilterData($filter);
        return view('operations.index', [
            'operations' => $this->getOperations($filter),
            'projects'   => Project::where('parent_id', 0)->get(),
        ], $dataFilters);
    }

    /**
     * @param $filter
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOperations($filter)
    {
        $operagtions = Operation::with([
            'user',
            'order'
        ]);

        if ($filter['project']) {
            $filter['project'] = explode(',', $filter['project']);
            $operagtions->whereHas('order', function ($query) use ($filter) {
                $query->whereIn('project_id', $filter['project']);
            });
        }
        if (isset($filter['sub_project'])) {
            $filter['sub_project'] = explode(',', $filter['sub_project']);
            $operagtions->whereHas('order', function ($query) use ($filter) {
                $query->whereIn('subproject_id', $filter['sub_project']);
            });
        }
        return $operagtions->orderBy('created_at')->paginate(50);
    }
}
