<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\NinjaxpressSenderRequest;
use App\Models\Api\Ninjaxpress\NinjaxpressKey;
use App\Models\Api\Posts\Ninjaxpress;
use App\Models\TargetConfig;
use App\Repositories\Integrations\NinjaxpressRepository;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleHttpClient;

class NinjaxpressController extends BaseController
{
    /**
     * @param NinjaxpressKey $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editKey( NinjaxpressKey $key )
    {
        return view('integrations.ninjaxpress.edit-key', [
            'keyData' => $key
        ]);
    }

    /**
     * @param NinjaxpressSenderRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function keyAdd( NinjaxpressSenderRequest $request )
    {
        $target = TargetConfig::where('alias', 'ninjaxpress')->first();
        if ($target) {
            $result = NinjaxpressKey::create([
                'target_id' => $target->id,
                'active' => 0,
                'name' => $request->name,
                'phone' => $request->phone,
                'country' => $request->country,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'email' => $request->email,
                'password' => $request->password,
                'subproject_id' => $request->subproject_id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'size' => $request->size,
                'weight' => $request->weight,
                'volume' => $request->volume,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
            ]);
            if ($result) {
                $html = view('integrations.ninjaxpress.accounts-table', ['keys' => NinjaxpressKey::all()])->render();
                return response()->json(['success' => true, 'html' => $html]);
            }
        } else {
            return response()->json(['errors' => 'Цель для Ninjaxpress еще не создана!']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function keyActivate( Request $request )
    {
        NinjaxpressKey::findOrFail($request->key_id)->update(['active' => $request->status]);
        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAccessToken( Request $request )
    {
        $result = NinjaxpressRepository::generateAccessToken($request);
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateHmac( Request $request )
    {
        $result = NinjaxpressRepository::generateHmac($request);
        return response()->json($result);
    }

    public function printDocument( $key, $deliveryNoteNumber )
    {
        $key = NinjaxpressKey::find($key);
        $client = new GuzzleHttpClient();
        $response = $client->request('GET', 'https://api.ninjavan.co/'. strtoupper($key->country) . '/2.0/reports/waybill?tids=' . $deliveryNoteNumber, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $key->access_token,
            ]
        ]);

        echo '<img src="data:image/png;base64,' . base64_encode($response->getBody()->getContents()) . '"/>';
    }

    /**
     * @param $sender
     * @param $track
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function printNoteHtmlData( $sender, $track )
    {
        $target_value = new \stdClass();
        $target_value->track = $track;
        $target_value->sender_id = $sender;
        return response()->json([
            'html' => view('integrations.ninjaxpress.print', ['target_value' => $target_value])->render(),
        ]);
    }
}
