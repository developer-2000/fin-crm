<?php

namespace App\Models\Api\Posts;

use App\Models\Api\CdekKey;
use App\Models\Order;
use App\Models\Project;
use App\Models\TargetConfig;
use CdekSDK\Requests\PvzListRequest;
use GuzzleHttp\Client as GuzzleHttpClient;
use function GuzzleHttp\Promise\all;
use CdekSDK\Requests;
use CdekSDK\Common;
use App\Models\TargetValue;
use App\Models\OrdersLog;
use App\Models\Tracking;

/**
 * CDEK API Class
 */
class Cdek extends AbstractPost
{
    const CREATE = true;
    const EDIT = true;
    const DELETE = true;

    const PRINT_NOTES = true;
    const PRINT_MARKINGS = true;
    const PRINT_MARKINGS_ZEBRA = true;

    const TRACKING = true;

    const KEY = '1e74cff5be30456c8482d464a98b43aa';
    const API = 'https://api.novaposhta.ua/v2.0/json/';

    public static function otherFieldsView($params = [])
    {
        $integration = TargetConfig::integration()
            ->where('alias', 'cdek')
            ->firstOrFail();
        return view('integrations.cdek.otherFields', $params, ['integrationKeys' => $integration->cdekKeys]);
    }

    public static function editView(TargetConfig $integration)
    {
        if (auth()->user()->project_id) {
            $subProjects = Project::where('parent_id', auth()->user()->project_id)->get();
        } else {
            $subProjects = Project::where('parent_id', '!=', 0)->get();
        }

        return view('integrations.cdek.edit', [
            'id'          => $integration->id,
            'subProjects' => $subProjects,
            'keys'        => $integration->cdekKeys,
        ]);
    }

    public static function renderView($params = [])
    {
        return view('integrations.cdek.index', $params);
    }

    /**
     * create new odelivery order
     *
     * @return array|\Illuminate\Validation\Validator
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createDocument()
    {
        $request = request();
        $order = json_decode($request->order);
        $orderTargetVal = TargetValue::where('order_id', $request->order_id)->first();
        // self::deleteDocumentTest(1409563);
        if (!empty($orderTargetVal->track)) {
            $results['errors'] = [0 => 'Накладная уже создана!'];
            return $results;
        }

        if (empty($request->sender)) {
            $results['errors'] = [0 => 'Выберите отправителя'];
            return $results;
        }

        $senderData = CdekKey::find($request->sender);

        $client = new \CdekSDK\CdekClient($senderData->account, $senderData->secure);
        if ($client) {
          $crmOrder = Order::findOrFail($request->order_id);

            $order = new Common\Order(
          [
              'Number'   => $crmOrder->id,
              'SendCity' => Common\City::create([
                  'Code' => $senderData->city_id,
              ]),
              'RecCity' => Common\City::create([
                  'Code' => $request->approve['city'],
              ]),
              'RecipientName'  => $request->name . ' ' . $request->surname,
              'RecipientEmail' => '',
              'Phone'          => $request->phone,
              'TariffTypeCode' => $request->approve['delivery_mode'],
          ]
        );

            $order->setAddress(Common\Address::create([
            'Street' => $request->approve['street'],
            'House'  => $request->approve['house'],
            'Flat'   => $request->approve['flat'],
        ]));

            $package = Common\Package::create([
            'Number'  => $crmOrder->id,
            'BarCode' => $crmOrder->id,
            'Weight'  => $request->weight,
        ]);

            $productIds = self::getFromedProducts($crmOrder);



            foreach ($productIds as $productId) {
              $package->addItem(new Common\Item([
                'WareKey' => $productId->WareKey, // Идентификатор/артикул товара/вложения
                'Cost'    => $productId->Cost, // Объявленная стоимость товара (за единицу товара)
                'Weight'  => $productId->Weight,
                'Payment' => $productId->Payment, // Оплата за товар при получении (за единицу товара)
                'Amount'  => $productId->Amount, // Количество единиц одноименного товара (в штуках)
            ]));
            }

            $package->addItem(new Common\Item([
              'WareKey' => 'ДОСТАВКА', // Идентификатор/артикул товара/вложения
              'Cost'    => $request->approve['cost_actual'], // Объявленная стоимость товара (за единицу товара)
              'Weight'  => 1,
              'Payment' => 0, // Оплата за товар при получении (за единицу товара)
              'Amount'  => 1, // Количество единиц одноименного товара (в штуках)
          ]));

          $order->addPackage($package);

          if($request->additional_service_30){
            $order->addService(Common\AdditionalService::create(30));
          }
          if($request->additional_service_36){
            $order->addService(Common\AdditionalService::create(36));
          }
          if($request->additional_service_37){
            $order->addService(Common\AdditionalService::create(37));
          }


            $deliveryRequest = new Requests\DeliveryRequest(
          [
            'Number' => $crmOrder->id,
          ]
        );
            $deliveryRequest->addOrder($order);
            $response = $client->sendDeliveryRequest($deliveryRequest);

            if ($response->hasErrors()) {
                foreach ($response->getMessages() as $message) {
                    if ($message->getErrorCode() !== '') {
                        $results['errors'][] = $message->getMessage();
                    }
                }
                return $results;
            } else {
                foreach ($response->getOrders() as $order) {
                    $trakId = $order->getDispatchNumber();

                    return self::saveResponse($request, $trakId, $senderData);
                }
            }
        }
    }

    public static function getFromedProducts($crmOrder)
    {
      $productIds = [];
      foreach ($crmOrder->products as $product) {

        if(! isset($productIds[$product->id])){
          $productIds[$product->id] = (object) [
            'WareKey' => (string)$product->id,
            'Cost'    => $product->pivot->price,
            'Weight'  => 10,
            'Payment' => 0,
            'Amount'  => 1,
          ];

        }else{
        $productIds[$product->id]->Amount +=1;
        }
      }
      return $productIds;
    }

    public static function saveResponse($request, $trakId, $senderData)
    {
        $target = TargetConfig::where('id', $request->target_approve)->first();
        if ($target) {
            $targetFields = json_decode($target->options, true);
        }
        $targetValues = TargetValue::where('order_id', $request->order_id)->first();

        $targetData = $request->get('approve');
        if ($targetFields) {
            foreach ($targetFields as $targetField) {
                if (isset($targetData[$targetField['field_name']])) {
                    $targetField['field_value'] = $targetData[$targetField['field_name']];
                }
                if ($targetField['field_name'] == 'track') {
                    $targetField['field_value'] = $trakId;
                }

                $targetValue[$targetField['field_name']] = $targetField;
            }
        }

        $targetValues->values = json_encode($targetValue);
        $targetValues->track = $trakId;
        $targetValues->sender_id = $senderData->id;
        if ($targetValues->save()) {
            (new OrdersLog())->addOrderLog($targetValues->order_id, 'Создан трек НП ' . $trakId . ",<br>  Реф.:" . $trakId);
            return [
              'success' => true,
              'created' => true,
              'track'   => $trakId,
          ];
        } else {
            abort(404);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function editDocument()
    {
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function deleteDocument()
    {

    }

    public static function regionsFind($term, $requestData)
    {
      $request = new Requests\RegionsRequest();
      $client = new \CdekSDK\CdekClient();
      $response = $client->sendRegionsRequest($request);



        return $response;
    }

    public static function citiesFind($term, $requestData)
    {
        $client = new \CdekSDK\CdekClient();

        $request = new Requests\CitiesRequest();

        if ($requestData->regionCode) {
            $request->setRegionCode($requestData->regionCode);
        }

        $response = $client->sendCitiesRequest($request);

        if ($response->hasErrors()) {
            dd($response->hasErrors());
        }

        return $response;
    }

    public static function pvzFind($term, $requestData)
    {
        $client = new \CdekSDK\CdekClient('', '');

        $request = new Requests\PvzListRequest();
        if ($requestData->cityCode) {
            $request->setCityId($requestData->cityCode);
        }
        if ($requestData->regionCode) {
            $request->setRegionId($requestData->regionCode);
        }
        $request->setType(PvzListRequest::TYPE_PVZ);
        //ПВЗ с наложенным платежем - обязательно!!!
        $request->setCodAllowed(true);

        $response = $client->sendPvzListRequest($request);
        return $response;
    }

    /**
     * handled by Tracking command
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function track()
    {
      $target = TargetConfig::where([
          ['alias', 'cdek'],
      ])->first();

      if ($target) {
          if ($target->integration_status == TargetConfig::INTEGRATION_INACTIVE) {
              echo "Cdek inactive\n";
          }

          $orders =  TargetValue::where('target_id', $target->id)
                      ->where('sender_id','<>',0)->get();

          foreach ($orders as $order) {
            $userAccaunt = $order->cdekKey;

            $client = new \CdekSDK\CdekClient($userAccaunt->account, $userAccaunt->secure);

            $request = new Requests\StatusReportRequest();
            // можно указывать или всё сразу, или только диапазоны дат, или только конкретные заказы
            $request->setChangePeriod(new Common\ChangePeriod(new \DateTime('-1 day'), new \DateTime('+1 day')));
            $request->addOrder(Common\Order::withDispatchNumber($order->track));

            $response = $client->sendStatusReportRequest($request);

            if ($response->hasErrors()) {
              foreach ($response->getMessages() as $message) {
                  if ($message->getErrorCode() !== '') {
                      $results['errors'][] = $message->getMessage();
                  }
              }
              print_r($results);
            }

            if(! $response->hasErrors()){
              foreach ($response as $resp) {
                self::setTrack($order->order_id, $order->target_id, $resp);
              }
            }
          }
        }
    }

    public static function setTrack($orderId, $targetId, $response)
    {
      $text = '';
      if ($status = $response->getStatus()) {
          $text = 'Status: '. $status->getDescription();
          $statusCode = $status->getCode();
      }

      Tracking::updateOrCreate([
          'order_id'    => $orderId,
          'target_id'   => $targetId,
          'status_code' => $statusCode ?? 'undefined',
          'status'      => $text,
          'track'       => $response->getDispatchNumber(),
          // 'comment' => $response->getReason()->getDescription()
      ], [
          'updated_at' => now(),
      ]);
    }
}
