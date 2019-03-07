<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Api\Posts\Viettel;
use App\Models\Api\ViettelKey;
use App\Models\ViettelSender;
use Illuminate\Http\Request;

class ViettelController extends BaseController
{

    public function editKey( $id )
    {
        return view('integrations.viettel.edit-key', [
            'senders' => ViettelSender::where('viettel_key_id', $id)->get(),
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function districtFind( Request $request )
    {
        $term = is_string($request->q) ? trim($request->q) : $request->q;
        $districts = Viettel::districtFind($term, $request);

        $formatted_districs = [];

        foreach ($districts as $district) {
            $formatted_districs[] = ['id' => $district->district_id, 'text' => $district->district_name];
        }

        return response()->json($formatted_districs);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function wardFind( Request $request )
    {
        $term = is_string($request->q) ? trim($request->q) : $request->q;
        $wards = Viettel::wardFind($term, $request);

        $formatted_wards = [];

        foreach ($wards as $ward) {
            $formatted_wards[] = ['id' => $ward->ward_id, 'text' => $ward->ward_name];
        }

        return response()->json($formatted_wards);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function provinceFind( Request $request )
    {
        $term = is_string($request->q) ? trim($request->q) : $request->q;
        $provinces = Viettel::provinceFind($term, $request);

        $formatted_provinces = [];

        foreach ($provinces as $province) {
            $formatted_provinces[] = ['id' => $province->province_id, 'text' => $province->province_name];
        }

        return response()->json($formatted_provinces);
    }

    public function accountCreate( Request $request )
    {
        $this->validate($request, [
            'sub_project_id' => 'required|int',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'required|email',
            'password' => 'required|max:255',
            'phone' => 'required', //todo: какие проверки нужжны
            'introduction' => 'required|max:255',
            'district' => 'required|int',
            'ward' => 'required|int',
            'address' => 'required|max:255',
        ]);

        $key = Viettel::generateKey($request);
        if (isset($key['success'])) {
            return response()->json(['success' => true]);
        } elseif ($key['error']) {
            return response()->json(['error' => $key['error']]);
        }
    }

    public function senderAdd( Request $request )
    {
        $this->validate($request, [
            'name' => 'unique:viettel_keys,name|required|max:255|string',
            'account_email' => 'unique:viettel_keys,email|required|email|max:255',
            'account_password' => 'required|max:255',
        ]);

        $result = Viettel::login($request);

        if ($result['key']) {
            $html = view('integrations.viettel.accounts-table', ['keys' => ViettelKey::all()])->render();
            return response()->json(['success' => true, 'html' => $html]);
        } elseif ($result['account']->error) {
            return response()->json(['error' => $result['account']->message]);
        }
    }

    public function activateSender( Request $request )
    {
        ViettelKey::findOrFail($request->key_id)->update(['active' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function signInAjax( Request $request )
    {
        $data = (object)['account_email' => $request['account_email'],
            'account_password' => $request['account_password']];
        $keyUpdated = Viettel::login($data);
        if ($keyUpdated) {
            return response()->json(['keyUpdated' => true]);
        }
    }

    public function loadSenderByKey( Request $request )
    {
        if (!empty($request->key)) {
            $keys = ViettelKey::with('senders')->where('id', $request->key)->get();
            foreach ($keys as $key){
                foreach ($key->senders as $sender){
                    $notes[$sender->id] = $sender->order_note;
                }
                $notes[$sender->id] = $sender->order_note;
            }

            if ($keys->count()) {
                $html = view('integrations.viettel.loaded-senders-data',
                    ['integrationKeys' => $keys])->render();
                return response()->json(['success' => true, 'html' => $html, 'notes' => $notes]);
            } else{
                return response()->json(['success' =>false]);
            }
        }
    }
}
