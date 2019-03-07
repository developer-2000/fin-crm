<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\OrderController;
use App\Models\Api\Posts\Novaposhta;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\TargetValue;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Api\NovaposhtaKey;
use GuzzleHttp\Client as GuzzleHttpClient;
use App\Models\TargetConfig;
use Illuminate\Validation\Validator;

class NovaposhtaController extends BaseController
{
    public function changeKeyStatus($keyId, $status)
    {

        $key = NovaposhtaKey::findOrFail($keyId);
        $key->active = $status;
        $res = $key->save();

        return response()->json([
            'success' => $res,
        ]);
    }

    public function senders()
    {
        $integration = TargetConfig::integration()
            ->where('alias', 'novaposhta')
            ->firstOrFail();

        if (auth()->user()->project_id) {
            $keys = NovaposhtaKey::with('integration')->whereHas('subProject', function ($query) {
                $query->where('id', auth()->user()->project_id);
            })->where('integration_id', $integration->id)->get();
        } else {
            $keys = NovaposhtaKey::with('integration')->where('target_id', $integration->id)->get();
        }

        return view('integrations.novaposhta.senders', [
            'keys'            => $keys,
            'integration'     => $integration,
            'integrationKeys' => $integration->integrationKeys
        ]);
    }

    public function senderEdit($alias, $id, Request $request)
    {
        $city = '';
        $warehouse = '';
        $integration = NovaposhtaKey::where('id', $id)->first();

        $contacts = json_decode($integration->contacts);
        if (!empty($contacts->city) && !empty($contacts->warehouse)) {
            $request->SettlementRef = !empty($contacts->city) ? $contacts->city : NULL;
            $request->warehouseRef = !empty($contacts->warehouse) ? $contacts->warehouse : NULL;;
            $city = json_encode(Novaposhta::settlementFind($request));
            $warehouse = json_encode(Novaposhta::warehouseFind($request));
        }

        return view('integrations.novaposhta.sender-show', [
            'integration' => $integration,
            'city'        => $city,
            'warehouse'   => $warehouse
        ]);
    }

    public function senderEditAjax(Request $request)
    {
        $sender = NovaposhtaKey::where('id', $request->senderId)->first();
        $contacts = json_decode($sender->contacts);
        $contacts->city = $request->city;
        $contacts->warehouse = $request->warehouse;
        $sender->contacts = json_encode($contacts);
        if ($sender->save()) {
            return response()->json(['success' => true]);
        } else {
            abort(404);
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function findCityByWord(Request $request)
    {
        $term = trim($request->q);

        $client = new GuzzleHttpClient();
        $response = $client->request('POST', Novaposhta::API, [
            'json' => [
                "apiKey"           => Novaposhta::KEY,
                "modelName"        => "Address",
                "calledMethod"     => "getCities",
                "methodProperties" => [
                    "FindByString" => $term
                ]
            ]
        ]);

        $getCities = json_decode($response->getBody()->getContents());
        $cities = [];

        foreach ($getCities->data as $item) {
            $cities[] = ['id'   => "" . $item->Ref . "",
                         'text' => $item->DescriptionRu
            ];
        }

        return $cities;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function keyCreateAjax(Request $request)
    {
        $this->validate($request, [
            'key'            => 'required',
            'name'           => 'required',
            'sub_project_id' => 'required',
            'integration_id' => 'required|exists:' . TargetConfig::tableName() . ',id'
        ]);

        $result = Novaposhta::generateKey($request->key);

        return response()->json([
            'success'   => $result,
            'tableHtml' => view('integrations.novaposhta.integrations-keys-table', [
                'integrationsKeys' => NovaposhtaKey::all(),
            ])->render()
        ]);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function novaposhtaSettlementsFind(Request $request)
    {
        return Novaposhta::settlementFind($request);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function novaposhtaWarehousesFind(Request $request)
    {
        return Novaposhta::warehouseFind($request);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function novaposhtaStreetFind(Request $request)
    {
        $client = new GuzzleHttpClient();
        $term = trim($request->q);
        $response = $client->request('POST', Novaposhta::API, [
            'json' => [
                "apiKey"           => Novaposhta::KEY,
                "modelName"        => "Address",
                "calledMethod"     => "getStreet",
                "methodProperties" => [
                    "FindByString" => $term,
                    "CityRef"      => $request->city,
                ]
            ]]);

        $getWarehouses = json_decode($response->getBody()->getContents());
        $settlements = [];

        foreach ($getWarehouses->data as $item) {
            $settlements[] = ['id'   => "" . $item->Ref . "",
                              'text' => $item->StreetsType . ' ' . $item->Description];
        }

        return $settlements;
    }

    public function printNoteHtmlData($track)
    {
        $target_value = new \stdClass();
        $target_value->track = $track;
        return response()->json([
            'html' => view('integrations.novaposhta.print', ['target_value' => $target_value])->render(),
        ]);
    }

    public function printDocument($deliveryNoteNumber)
    {
        $senderId = TargetValue::where('track', $deliveryNoteNumber)->first()->sender_id;
        $sender = NovaposhtaKey::find($senderId);
        if($sender){
            return redirect('https://my.novaposhta.ua/orders/printDocument/orders[]/' . $deliveryNoteNumber . '/type/html/apiKey/' . $sender->key);
        }

    }

    public function printMarkings($deliveryNoteNumber)
    {
        $senderId = TargetValue::where('track', $deliveryNoteNumber)->first()->sender_id;
        $sender = NovaposhtaKey::find($senderId);
        if($sender) {
            return redirect('https://my.novaposhta.ua/orders/printMarkings/orders[]/' . $deliveryNoteNumber . '/type/html/apiKey/' . $sender->key);
        }
    }

    public function printMarkingsZebra($deliveryNoteNumber)
    {
        $targetVal= TargetValue::where('track', $deliveryNoteNumber)->first();
        if($targetVal){
            $senderId = $targetVal->sender_id;
            $sender = NovaposhtaKey::find($senderId);
            if($sender) {
                return redirect('https://my.novaposhta.ua/orders/printMarkings/orders[]/' . $deliveryNoteNumber . '/type/html/apiKey/' . $sender->key . '/zebra/zebra');
            }
            }else{
            abort(404);
        }
    }

    public function printAllDocuments($tracks, $alias, $orderIds)
    {
        switch ($alias) {
            case 'novaposhta':
                $tracksNumbers = explode(',',$tracks );
                if(!empty($tracksNumbers)){
                    $targetVal= TargetValue::where('track', $tracksNumbers[0])->first();
                    if($targetVal) {
                        $senderId = $targetVal->sender_id;
                        $sender = NovaposhtaKey::find($senderId);
                        return redirect('https://my.novaposhta.ua/orders/printDocument/orders/' . $tracks . '/type/html/apiKey/' . $sender->key);
                    }
                    else{
                        abort(404);
                    }
                }
                else{
                    abort(404);
                }

                break;
            case 'kazpost':
                $orderIds = TargetValue::whereIn('track', explode(',', $tracks))->get()->pluck('order_id')->toArray();
                return redirect('/integrations/kazpost/stickers2/' . implode(',', $orderIds));
                break;

            case 'russianpost':
                if(!empty($orderIds)){
                    $orderIdsArray = explode(',',$orderIds);
                }
                //$orderIds = TargetValue::whereIn('track', explode(',', $tracks))->get()->pluck('order_id')->toArray();
                return redirect('/integrations/russianpost/stickers2/' . implode(',', $orderIdsArray));
                break;
        }

    }

    public function printAllMarkings($tracks, $alias)
    {
        switch ($alias) {
            case 'novaposhta':
                $tracksNumbers = explode(',',$tracks );
                if(!empty($tracksNumbers)){
                    $targetVal= TargetValue::where('track', $tracksNumbers[0])->first();
                    if($targetVal) {
                        $senderId = $targetVal->sender_id;
                        $sender = NovaposhtaKey::find($senderId);
                        return redirect('https://my.novaposhta.ua/orders/printMarkings/orders/' . $tracks . '/type/html/apiKey/' . $sender->key);
                    }
                    else{
                        abort(404);
                    }
                }
                else{
                    abort(404);
                }

                break;
        }

    }

    public function printAllMarkingsZebra($tracks, $alias)
    {
        switch ($alias) {
            case 'novaposhta':

                $tracksNumbers = explode(',',$tracks );
                if(!empty($tracksNumbers)){
                    $targetVal= TargetValue::where('track', $tracksNumbers[0])->first();
                    if($targetVal) {
                        $senderId = $targetVal->sender_id;
                        $sender = NovaposhtaKey::find($senderId);
                        return redirect('https://my.novaposhta.ua/orders/printMarkings/orders/' . $tracks . '/type/html/apiKey/' .  $sender->key . '/zebra/zebra');
                    }
                    else{
                        abort(404);
                    }
                }
                else{
                    abort(404);
                }

                break;
        }

    }
}
