<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Ninjaxpress\NinjaxpressKey;
use App\Models\Api\Posts\Ninjaxpress;
use Illuminate\Http\Request;

class NinjaxpressApiController extends Controller
{
    public function track(Request $request)
    {
        try {
            $hmacs = NinjaxpressKey::all()->pluck('hmac')->toArray();
            $hmac_header = $request->header('X-NINJAVAN-HMAC-SHA256');
            if (in_array($hmac_header, $hmacs)) {
                $result = Ninjaxpress::getWebhooks($request);
                return response()->json($result['response'], $result['code']);
            } else {
                return \response("X-NINJAVAN-HMAC-SHA256 is wrong", 404);
            }
        } catch (\Exception  $exception) {
            return response()->json('System Error', 500);
        }
    }
    public function setTimezoneHtml(Request $request){
        if ($request->timezone){
            return response()->json([
                'html' => view('integrations.ninjaxpress.ajax.set-timezone', [
                    'timezone' => $request->timezone,
                ])->render()
            ]);
        }
    }
}
