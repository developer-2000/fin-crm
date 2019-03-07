<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProcStatus;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class ProcStatusController extends BaseController
{
    public function index()
    {
        return view('procStatus.index', [
            'statuses' => ProcStatus::getAllStatuses(),
            'projects' => Project::where('parent_id', 0)->get(['id', 'name']),
        ]);
    }

    public function create(Request $request)
    {
        \Validator::extend('exists_project', function ($attr, $value) {
            if ($value) {
                $res = Project::where('id', $value)->exists();

                return $res;
            }
            return true;
        }, 'Поле :attribute не корректно.');

        $this->validate($request, [
            'name'         => 'required|max:255|min:2',
            'project'      => 'nullable|int|min:0|exists_project',
            'sub-status'   => 'nullable|array',
            'sub-status.*' => 'string|min:2|max:255',
            'color'        => [
                'required',
                'regex:/^(\#[\da-f]{3}|\#[\da-f]{6}|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)|hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)|hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\))$/'
            ],
        ], [
            'sub-status.*.min' => 'Количество символов в поле "Название подстатуса" должно быть не менее 2.',
            'sub-status.*.max' => 'Количество символов в поле "Название подстатуса" не может превышать 255.',
            'sub-color.regex'  => 'Цвет введен некорректно.',
        ]);

        $projectId = Auth::user()->project_id ? Auth::user()->project_id : 0;

        if ($request->project && !$projectId) {
            $projectId = $request->project;
        }

        $action = !empty($request->action) ? $request->action : 0;
        if ($action) {
            switch ($action) {
                case 'paid_up':
                    $targetFinal = 1;
                    $actionAlias = 'Выкуп';
                    break;
                case 'refused':
                    $targetFinal = 2;
                    $actionAlias = 'Не выкуп';
                    break;
                case 'rejected':
                    $targetFinal = 3;
                    $actionAlias = 'Отклонен';
            }
        }

        $status = new ProcStatus();
        $status->project_id = $projectId;
        $status->name = $request->name;
        $status->locked = ProcStatus::NOT_LOCKED;
        $status->type = ProcStatus::TYPE_SENDERS;
        $status->color = $request->color;
        $status->parent_id = 0;
        $status->action = $action;
        $status->action_alias = isset($actionAlias) ? $actionAlias :0;
        $status->target_final = isset($targetFinal) ? $targetFinal :0;

        $result = $status->save();

        if ($request->get('sub-status') && $result) {
            ProcStatus::createSubStatus($request->get('sub-status'), $status);
        }

        return response()->json([
            'success' => $result,
            'html'    => view('procStatus.table', [
                'statuses' => ProcStatus::getAllStatuses(),
                'projects' => Project::all(),
            ])->render()
        ]);
    }

    public function edit(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string|in:project,name,color',
            'pk'    => 'required|min:0|exists:proc_statuses,id',
            'value' => 'required|min:0'
        ]);

        $result = ProcStatus::editStatus($request->pk, $request->name, $request->value);

        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'pk' => 'required|int|min:0|exists:proc_statuses,id'
        ]);

        $result = ProcStatus::deleteStatus($request->pk);

        return response()->json([
            'success' => $result,
        ]);
    }

    public function addSubStatus(Request $request)
    {
        $this->validate($request, [
            'pk'    => 'required|int|min:1|exists:proc_statuses,id',
            'value' => 'required|string|min:2'
        ]);

        $parent = ProcStatus::where('parent_id', 0)->findOrFail($request->pk);

        $status = new ProcStatus();
        $status->project_id = $parent->project_id;
        $status->name = $request->value;
        $status->type = $parent->type;
        $status->parent_id = $parent->id;
        $status->locked = $parent->locked;
        $result = $status->save();

        return response()->json([
            'success' => $result,
            'html'    => view('procStatus.table', [
                'statuses' => ProcStatus::getAllStatuses(),
                'projects' => Project::all(),
            ])->render()
        ]);
    }

    public function procStatus2Load($procStatus, $orderId)
    {
        $procStatuses2 = ProcStatus::where('parent_id', $procStatus)->get();
        if($procStatuses2->count()){
            return response()->json([
                'html' => view('orders.ajax.proc-statuses2-ajax', [
                    'procStatuses2' => $procStatuses2, 'orderOne' => Order::findOrFail($orderId)
                ])->render()]);
        }
    }

    public function getOrderByStatusAjax()
    {
        $data['statusGroupBy'] = Order::select(\DB::raw('Count(id) AS count'), 'proc_status')
            ->with('procStatus' )
            ->checkAuth()
            ->moderated()
            ->targetApprove()
            ->withoutTargetFinal()
            ->groupBy('proc_status')
            ->get();

        $data['statuses'] = ProcStatus::checkProject()
            ->senderStatuses()
            ->get();

        return response()->json([
            'view' => view('procStatus.rewriteStatus', $data)->render(),
        ]);
    }

    public function rewriteStatus(Request $request)
    {
        $this->validate($request, [
            'status.*' => 'required|numeric|min:1|exists:' . ProcStatus::tableName() . ',id'
        ]);

        $result = [];
        if ($request->status) {
            foreach ($request->status as $oldStatusId => $newStatusId) {
                $result[] = Order::changeProcStatuses($oldStatusId, $newStatusId);
            }
        }

        return response()->json($result);
    }
}
