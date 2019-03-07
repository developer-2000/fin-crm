<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.06.18
 * Time: 17:16
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Api\Measoft\MeasoftSender;
use App\Models\Api\Posts\Measoft;
use App\Models\Project;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use Illuminate\Http\Request;

class MeasoftController extends BaseController
{
    private $viePath;

    public function __construct()
    {
        parent::__construct();
        $this->viePath = Measoft::getViewPath();
    }

    public function addSenderAjax(Request $request)
    {
        $this->validate($request, [
            'sub_project_id' => 'required|exists:' . Project::tableName() . ',id',
            'name'           => 'required|string|max:255',
            'extra'          => 'required|string|max:255',
            'login'          => 'required|string|max:255',
            'password'       => 'required|string|max:255',
        ]);

        $sender = new MeasoftSender();
        $sender->target_id = TargetConfig::where('alias', 'measoft')->value('id');
        $sender->sub_project_id = $request->sub_project_id;
        $sender->name = $request->name;
        $sender->extra = $request->extra;
        $sender->login = $request->login;
        $sender->password = $request->password;

        return response()->json([
            'success' => $sender->save(),
            'html'    => view($this->viePath . 'edit-table', [
                'senders' =>MeasoftSender::with('subProject')
                    ->checkSubProject()
                    ->get(),
            ])->render(),
        ]);
    }

    public function editSenderAjax(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|in:sub_project_id,name,extra,login,password',
            'value' => 'required',
            'pk'   => 'required|exists:' . MeasoftSender::tableName() . ',id'
        ]);

        $sender = MeasoftSender::find($request->pk);
        $field = $request->name;

        $sender->$field = $request->value;

        return response()->json([
            'success' => $sender->save(),
        ]);
    }

    public function deleteSenderAjax(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|in:delete',
            'pk'   => 'required|exists:' . MeasoftSender::tableName() . ',id'
        ]);

        try {
            $target = TargetConfig::where('alias', 'measoft')->first();
            $sender = MeasoftSender::find($request->pk);
            $ordersExist = TargetValue::where('target_id', $target->id)->where('sender_id', $sender->id)->exists();

            if ($ordersExist) {
                return response()->json([
                    'message' => "Нельзя удалить отправителя, у которого есть заказы!",
                ], 422);
            }

            return response()->json([
                'success' => $sender->delete(),
                'id'      => $sender->id,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function findTowns(Request $request)
    {
        $result = [];

        $towns = Measoft::findTown($request->get('query'), $request->get('code'));

        if ($towns) {
            foreach ($towns as $town) {
                $result[] = [
                    'id'   => $town['code'],
                    'text' => $town['name'] . ' | ' . $town['region']
                ];
            }
        }

        return response()->json($result);
    }

    public function findStreets(Request $request)
    {
        $result = [];

        $streets = Measoft::findStreet($request->get('query'), $request->get('code'));

        if ($streets) {
            foreach ($streets as $street) {
                $result[] = $street['name'];
            }
        }

        return response()->json($result);
    }
}