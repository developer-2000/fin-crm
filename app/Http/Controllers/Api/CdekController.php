<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\CdekKey;
use App\Models\Api\Posts\Cdek;
use App\Models\Order;
use App\Models\TargetValue;
use App\Models\TargetConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use GuzzleHttp\Client as GuzzleHttpClient;
use CdekSDK\Requests\CalculationRequest;
use CdekSDK\Requests;
use CdekSDK\Common;

class CdekController extends BaseController
{
    public function editKey($id)
    {
//        return view('integrations.viettel.edit-key', [
//            'senders' => CdekSender::where('viettel_key_id', $id)->get(),
//        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountCreate(Request $request)
    {
        $this->validate($request, [
            'sub_project_id' => 'required|int|bail',
            'name'           => 'required|string|min:3|max:255|bail',
            'account'        => 'required|string|min:3|max:255|bail',
            'secure'         => 'required|max:255|bail',
        ]);

        $key = CdekKey::createAccount($request);
        if (isset($key['success'])) {
            return response()->json(['success' => true]);
        } elseif ($key['error']) {
            return response()->json(['error' => $key['error']]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateSender(Request $request)
    {
        CdekKey::findOrFail($request->key_id)->update(['active' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function changeKeyStatus($keyId, $status)
    {
    }

    public function senders()
    {
    }

    public function senderEdit($alias, $id, Request $request)
    {
    }

    public function senderEditAjax(Request $request)
    {
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function regionsFind(Request $request)
    {
        $term = trim($request->q);

        $regions = Cdek::regionsFind($term, $request);
        $formatted_regions = [];

        if ($regions->hasErrors()) {
            return response()->json(['errors' => $regions->hasErrors()]);
        }

        foreach ($regions->getItems() as $region) {
            if ($term) {
                if (stripos(mb_strtolower($region->getName() . ' ' . $region->getPrefix()), mb_strtolower($term)) !== false) {
                    $formatted_regions[] = [
                        'id'   => $region->getCode(),
                        'text' => $region->getName() . ' ' . $region->getPrefix()
                    ];
                }
            } else {
                $formatted_regions[] = [
                    'id'   => $region->getCode(),
                    'text' => $region->getName() . ' ' . $region->getPrefix()
                ];
            }
        }

        usort($formatted_regions, function ($a, $b) {
            return $a['text'] <=> $b['text'];
        });

        return response()->json($formatted_regions);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function citiesFind(Request $request)
    {
        $term = trim($request->q);

        $cities = Cdek::citiesFind($term, $request);

        $formatted_cities = [];

        if ($cities->hasErrors()) {
            dd($cities->hasErrors());
            return response()->json(['errors' => $cities->hasErrors()]);
        }

        foreach ($cities->getItems() as $city) {
            /** @var \CdekSDK\Common\Location $city */
           // dd($city);
            if ($term) {
                if (stripos(mb_strtolower($city->getCityName()
                        ), mb_strtolower($term)) !== false) {
                    $formatted_cities[] = [
                        'id'   => $city->getCityCode(),
                        'text' => $city->getCityName() . ', ' . $city->getSubRegion()
                    ];
                }
            } else {
                $formatted_cities[] = [
                    'id'   => $city->getCityCode(),
                    'text' => $city->getName() . ', ' . $city->getSubRegion()
                ];
            }
        }
        usort($formatted_cities, function ($a, $b) {
            return $a['text'] <=> $b['text'];
        });
        return response()->json($formatted_cities);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pvzFind(Request $request)
    {
        $term = trim($request->q);

        $pvzlist = Cdek::pvzFind($term, $request);
        $formatted_pvzList = [];
        if ($pvzlist) {


            if ($pvzlist->hasErrors()) {
                // обработка ошибок
            }

            /** @var \CdekSDK\Responses\PvzListResponse $pvzlist */
            foreach ($pvzlist as $item) {
                /** @var \CdekSDK\Common\Pvz $item */
                // всевозможные параметры соответствуют полям из API СДЭК
                $item->Code;
                $item->Name;
                $item->Address;

                foreach ($item->OfficeImages as $image) {
                    $image->getUrl();
                }
            }

            foreach ($pvzlist as $pvzItem) {
                if ($term) {
                    if (stripos(mb_strtolower('' . $pvzItem->Name . ', ' . $pvzItem->Address . ''), mb_strtolower($term)) !== false) {
                        $formatted_pvzList[] = [
                            'id'   => $pvzItem->Code,
                            'text' => $pvzItem->Name . ', ' . $pvzItem->Address
                        ];
                    }
                } else {
                    $formatted_pvzList[] = [
                        'id'   => $pvzItem->Code,
                        'text' => $pvzItem->Name . ', ' . $pvzItem->Address
                    ];
                }
            }
        }
        usort($formatted_pvzList, function ($a, $b) {
            return $a['text'] <=> $b['text'];
        });

        return response()->json($formatted_pvzList);
    }

    public function calculateCostActual(Request $request)
    {
        $sender = CdekKey::find($request->sender);

        $this->validate($request, [
            'approve.city'           => 'required',
            'sender' => 'required'
        ]);
        $senderData = CdekKey::find($request->sender);

        $client = new \CdekSDK\CdekClient($senderData->account, $senderData->secure);

        $order = Order::find($request->order_id);
        $orderTargetValue = TargetValue::whereOrderId($request->order_id)->first();
        $targetValues = json_decode($orderTargetValue->values, true);

        $request =  CalculationRequest::withAuthorization();
            $request->setSenderCityId($senderData->city_id)
            ->setReceiverCityId($request->approve['city'])
            ->setTariffId($request->approve['delivery_mode'])
            ->addPackage([
                'weight' => 5,
                'length' => 1,
                'width'  => 1,
                'height' => 1,
            ]);

        $response = $client->sendCalculationRequest($request);

        if ($response->hasErrors()) {
            $errors = [];
            foreach ($response->getErrors() as $error) {

                $error->getCode();
                $error->getText();
                $errors[] = $error->getText();
            }
            return response()->json(['success' => false, 'message' => $errors]);
        }

        if ($response->getPrice()) {
            if (isset($targetValues['cost_actual'])) {
                $targetValues['cost_actual']['field_value'] = $response->getPrice() ?? '';
            }
            $orderTargetValue->values = json_encode($targetValues);
            $orderTargetValue->cost_actual = $response->getPrice() ?? 0;
            $orderTargetValue->save();
        }

        return [
            'success'      => true,
            'deliveryCost' => $response->getPrice()
        ];
    }

    public function print($trackId)
    {
      $track = TargetValue::where('track', $trackId)->first();

      $userAccaunt = $track->cdekKey;
      $client = new \CdekSDK\CdekClient($userAccaunt->account, $userAccaunt->secure);

      $request = new Requests\PrintReceiptsRequest(['CopyCount' => 2]);
      $request->addOrder(Common\Order::withDispatchNumber($trackId));
      $response = $client->sendPrintReceiptsRequest($request);

      if ($response->hasErrors()) {
        foreach ($response->getMessages() as $message) {
            if ($message->getErrorCode() !== '') {
                $results['errors'][] = $message->getMessage();
            }
        }
        print_r($results);
      }




          $content = $response->getBody();
          header('Content-Type: application/pdf');
          header('Content-Length: ' . strlen($content));
          header('Content-Disposition: inline; filename="YourFileName.pdf"');
          header('Cache-Control: private, max-age=0, must-revalidate');
          header('Pragma: public');
          ini_set('zlib.output_compression','0');

          die($content);
    }
}
