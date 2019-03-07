<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.06.18
 * Time: 17:16
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Requests\WeFastCounterpartiesRequest;
use App\Jobs\WeFastOffices;
use App\Models\Api\WeFast\WeFastCounterparty;
use App\Models\Api\WeFast\WeFastKey;
use App\Models\Api\WeFast\WeFastOffice;
use App\Models\Project;
use App\Models\TargetConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;

class WeFastController extends BaseController
{

    public function wefastCreateTokenAjax(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|unique:' . WeFastKey::tableName() . ',token',
            'sub_project_id' => 'required|exists:' . Project::tableName() . ',id',
            'name'  => 'required|max:255'
        ]);

        $target = TargetConfig::where('alias', 'wefast')->firstOrFail();
        $token = new WeFastKey();
        $token->target_id = $target->id;
        $token->sub_project_id = Auth::user()->sub_project_id ? Auth::user()->sub_project_id : $request->sub_project_id;
        $token->name = $request->name;
        $token->token = $request->token;
        $res = $token->save();

        return response()->json([
            'success' => $res,
            'html'    => view('integrations.wefast.edit-table', [
                'keys' => WeFastKey::with('subProject')->checkSubProject()->get(),
            ])->render(),
        ]);
    }

    public function wefastCounterparties()
    {
        return view('integrations.wefast.counterparties', [
            'integration' => TargetConfig::where('alias', 'wefast')->first(),
            'keys' => WeFastKey::active()->get(),
            'counterparties' => WeFastCounterparty::with('key')->checkSubProject()->get(),
        ]);
    }

    public function createCounterpartyAjax(WeFastCounterpartiesRequest $request)
    {
        $counterparty = new WeFastCounterparty();
        $counterparty->setValues($request);
        $res = $counterparty->save();

        return response()->json([
            'success' => $res,
            'html' => view('integrations.wefast.counterparties-table', [
                'counterparties' => WeFastCounterparty::with('key')->checkSubProject()->get()
            ])->render(),
        ]);
    }

    public function counterpartiesEdit($id)
    {
        return view('integrations.wefast.counterparties-edit', [
            'counterparty' => WeFastCounterparty::checkSubProject()->findOrFail($id),
        ]);
    }

    public function counterpartiesEditAjax(WeFastCounterpartiesRequest $request)
    {
        $counterparty = WeFastCounterparty::checkSubProject()->findOrFail($request->id);
        $counterparty->setValues($request);
        $res = $counterparty->save();

        return response()->json([
            'success' => $res
        ]);
    }

    public function counterpartiesDeleteAjax(Request $request)
    {
        $this->validate($request, [
            'pk' => 'required|exists:' . WeFastCounterparty::tableName() . ',id'
        ]);

        return response()->json([
            'success' => WeFastCounterparty::checkSubProject()->where('id', $request->pk)->delete(),
            'id' => $request->pk,
        ]);
    }


    public function changeStatusKeyAjax(Request $request)
    {
        $this->validate($request, [
            'id'     => 'required|exists:' . WeFastKey::tableName(),
            'status' => 'required|int|min:0'
        ]);

        $key = WeFastKey::checkSubProject()->findOrFail($request->id);
        $key->active = $request->status;
        $res = $key->save();

        return response()->json([
            'success' => $res,
        ]);
    }

    public function offices()
    {
        return view('integrations.wefast.offices', [
            'integration' => TargetConfig::where('alias', 'wefast')->first(),
            'offices'     => WeFastOffice::paginate(50),
        ]);
    }

    public function importOffices(Request $request)
    {
        $file = $request->file('file');
        $content = Excel::load($file->getRealPath(), function ($reader) {
        })->get()->toArray();

        $errors = [];
        $data = collect();
        if (isset($content[0]) && count($content[0])) {
            foreach ($content[0] as $item => $row) {
                try {
                    $data[] = [
                        'province_code' => array_shift($row),
                        'province_name' => array_shift($row),
                        'district_code' => array_shift($row),
                        'district_name' => array_shift($row),
                        'pickup'        => array_shift($row),
                        'delivery'      => array_shift($row),
                        'ward_code'     => array_shift($row),
                        'ward_name'     => array_shift($row),
                        'active'        => 1,
                    ];
                } catch (\Exception $exception) {
                    $errors[$item + 1] = $exception->getMessage();
                }
            }
        }
        if (count($data)) {
            WeFastOffice::updateAll();
            $oldOffices = WeFastOffice::all();

            if ($oldOffices->isNotEmpty()) {
                $offices = [];
                foreach ($oldOffices as $office) {
                    $offices[$office->ward_code] = $office;
                }
                foreach ($data as $item) {
                    if (isset($offices[$item['ward_code']])) {
                        WeFastOffice::where('ward_code', $item['ward_code'])->update(['active' => 1]);
                    } else {
                        WeFastOffice::create($item);
                    }
                }


            } else {
                $take = 1000;
                $skip = 0;
                do {

                    $rows = $data->slice($skip)->take($take);
                    $skip += $rows->count();

                    WeFastOffice::insert($rows->toArray());

                } while ($skip < count($data));
            }
        }

        $res['errors'] = $errors;
        return response()->json($res);
    }

    public function changeStatusCounterpartyAjax(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:' . WeFastCounterparty::tableName(),
            'status' => 'required|int'
        ]);

        $counterparty = WeFastCounterparty::checkSubProject()->findOrFail($request->id);
        $counterparty->active = $request->status;
        $res = $counterparty->save();

        return response()->json([
            'success' => $res
        ]);
    }

    public function editKeyAjax(Request $request)
    {
        $this->validate($request, [
            'pk' => 'required|exists:'. WeFastKey::tableName(). ',id',
            'name' => 'required|in:name,token,sub_project_id',
            'value' => 'required|min:1'
        ]);

        $key = WeFastKey::checkSubProject()->findOrFail($request->pk);
        $fieldName = $request->name;
        $key->$fieldName = $request->value;
        $res = $key->save();

        return response()->json([
            'success' => $res,
        ]);
    }

    public function deleteKeyAjax(Request $request)
    {
        $this->validate($request, [
            'pk' => 'required|exists:'. WeFastKey::tableName(). ',id',
        ]);

        $key = WeFastKey::checkSubProject()->findOrFail($request->pk);

        if ($key->counterparties->isNotEmpty()) {
            return response()->json([
                'counterparties' => true
            ], 422);
        }

        $res = WeFastKey::where('id', $request->pk)->delete();

        return response()->json([
            'success' => $res,
            'id' => $request->pk,
        ]);
    }

    public function findProvince(Request $request)
    {
        return response()->json(WeFastOffice::findProvince($request->q));
    }

    public function findDistrict(Request $request)
    {
        return response()->json(WeFastOffice::findDistrict($request->q, $request->province));
    }

    public function findWard(Request $request)
    {
        return response()->json(WeFastOffice::findWard($request->q, $request->province, $request->district));
    }

    public function findKey(Request $request)
    {
        return response()->json(WeFastKey::findKey($request->input('query'), $request->sub_project_id));
    }
}