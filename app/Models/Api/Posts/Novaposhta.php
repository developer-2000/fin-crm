<?php

namespace App\Models\Api\Posts;

use App\Http\Requests\OrderSendingRequest;
use App\Http\Requests\Request;
use App\Models\Api\CodeStatus;
use App\Models\Api\NovaposhtaKey;
use App\Models\OrdersLog;
use App\Models\ProcStatus;
use App\Models\Tracking;
use App\Models\Project;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use App\Models\Variables;
use Carbon\Carbon;
use function foo\func;
use GuzzleHttp\Client as GuzzleHttpClient;
use App\Models\Order;
use GuzzleHttp\Exception\RequestException;
use App\Models\NP;
use MongoDB\Driver\Exception\ExecutionTimeoutException;

/**
 * Nova Poshta API Class
 */
class Novaposhta extends AbstractPost
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
            ->where('alias', 'novaposhta')
            ->firstOrFail();
        return view('integrations.novaposhta.otherFields', $params, ['integrationKeys' => $integration->integrationKeys]);
    }

    public static function editView(TargetConfig $integration)
    {
        if (auth()->user()->project_id) {
            $subProjects = Project::where('parent_id', auth()->user()->project_id)->get();
        } else {
            $subProjects = Project::where('parent_id', '!=', 0)->get();
        }

        return view('integrations.novaposhta.edit', [
            'id'               => $integration->id,
            'subProjects'      => $subProjects,
            'integrationsKeys' => $integration->integrationKeys,
        ]);
    }

    public static function renderView($params = [])
    {
        return view('integrations.novaposhta.index', $params);
    }

    /**
     * @return array|\Illuminate\Validation\Validator
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createDocument()
    {
        $request = request();
        $order = json_decode($request->order);
        $orderTargetVal = TargetValue::where('order_id', $request->order_id)->first();

        if (!empty($orderTargetVal->track)) {
            $results['errors'] = [0 => 'Накладная уже создана!'];
            return $results;
        }

        if (empty($request->sender)) {
            $results['errors'] = [0 => 'Выберите отправителя'];
            return $results;
        }

        //$senderData = json_decode($request->sender);
        $senderData = NovaposhtaKey::findOrFail($request->sender);


        if (!$senderData) {
            $results['errors'] = [0 => 'Выберите отправителя!'];
            return $results;
        }
        $contacts = json_decode($senderData->contacts);
        // create recipient conterparty
        $recipient = self::saveRecipientCounterParty($request, new GuzzleHttpClient(), $senderData);

        if (!empty($recipient->data[0])) {
            $recipientData = $recipient->data[0];
        } else {
            return ['errors' => $recipient->errors];
        }

        $validator = \Validator::make($request->all(), (new OrderSendingRequest())->rules());

        if ($validator->fails()) {
            return $validator;
        }

        if ($recipientData) {
            $client = new GuzzleHttpClient();
            $orderUpdated = Order::findOrFail($order->id);
            $response = $client->request('POST', self::API, [
                'json' => [
                    "apiKey"           => $senderData->key,
                    "modelName"        => "InternetDocument",
                    "calledMethod"     => "save",
                    "methodProperties" => [
                        "PayerType"     => "Recipient",
                        "PaymentMethod" => "Cash",
                        "CargoType"     => "Cargo",
                        "VolumeGeneral" => $request->volume_general,
                        "Weight"        => $request->weight,
                        "ServiceType"   => "WarehouseWarehouse",
                        "SeatsAmount"   => "1",
                        "Description"   => trim($request->description),
                        "Cost"          => $orderUpdated->price_total,
                        "CitySender"    => $contacts->city,
                        "Sender"        => $senderData->sender_id,
                        "SenderAddress" => $contacts->warehouse, //identifier of sender warehouse
                        "ContactSender" => $contacts->contact_ref,
                        "SendersPhone"  => $contacts->phone,

                        "CityRecipient"    => $request->approve['city'],
                        "Recipient"        => $recipientData->Ref,
                        "RecipientAddress" => $request->approve['warehouse'],
                        "ContactRecipient" => $recipientData->ContactPerson->data[0]->Ref,

                        "RecipientsPhone"       => $request->phone,
                        "DateTime"              => $request->delivery_date,
                        // обратная доставка Ц1П
                        'BackwardDeliveryData'  => [
                            [
                                'PayerType'        => 'Recipient',
                                'CargoType'        => 'Money',
                                'RedeliveryString' => $orderUpdated->price_total,
                                // 'RedeliveryStringRub' => '400', // если доставка в Крым
                            ],
                        ],
                        'AdditionalInformation' => !empty($request->add_information) ? $request->add_information : '',
                    ]
                ]
            ]);

            $result = json_decode($response->getBody()->getContents());
            if (!empty($result->data[0])) {

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
                            $targetField['field_value'] = $result->data[0]->IntDocNumber;
                        }
                        if ($targetField['field_name'] == 'track2') {
                            $targetField['field_value'] = $result->data[0]->Ref;
                        }
                        $targetValue[$targetField['field_name']] = $targetField;
                    }
                }

                $targetValues->values = json_encode($targetValue);
                $targetValues->track = $result->data[0]->IntDocNumber;
                $targetValues->track2 = $result->data[0]->Ref;
                $targetValues->sender_id = $senderData->id;
                if ($targetValues->save()) {
                    (new OrdersLog())->addOrderLog($targetValues->order_id, 'Создан трек НП ' . $result->data[0]->IntDocNumber . ",<br>  Реф.:" . $result->data[0]->Ref);
                    return [
                        'success' => true,
                        'created' => true,
                        'track2'  => $result->data[0]->Ref,
                        'track'   => $result->data[0]->IntDocNumber,
                    ];
                } else {
                    abort(404);
                }
            } elseif (!empty($result->errors)) {
                $results['errors'] = $result->errors;
                return $results;
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function editDocument()
    {
        $request = \request();
        $order = json_decode($request->order);
        $client = new GuzzleHttpClient();
        // $senderData = json_decode($request->sender);
        $senderData = NovaposhtaKey::findOrFail($request->sender);
        // create recipient conterparty
        $recipient = self::saveRecipientCounterParty($request, $client, $senderData);
        if (!empty($recipient->data[0])) {
            $recipientData = $recipient->data[0];
        } else {
            return response()->json(['errors' => $recipient->errors]);
        }

        if ($recipientData) {
            $contacts = json_decode($senderData->contacts);
            $client = new GuzzleHttpClient();
            $orderUpdated = Order::findOrFail($order->id);

            $response = $client->request('POST', Novaposhta::API, [
                'json' => [
                    "apiKey"           => $senderData->key,
                    "modelName"        => "InternetDocument",
                    "calledMethod"     => "update",
                    "methodProperties" => [
                        "Ref"           => $request->approve['track2'],
                        "PayerType"     => "Recipient",
                        "PaymentMethod" => "Cash",
                        "CargoType"     => "Cargo",
                        "VolumeGeneral" => $request->volume_general,
                        "Weight"        => $request->weight,
                        "ServiceType"   => "WarehouseWarehouse",
                        "SeatsAmount"   => "1",
                        "Description"   => $request->description,
                        "Cost"          => $orderUpdated->price_total,
                        "CitySender"    => $contacts->city,
                        "Sender"        => $senderData->sender_id,
                        "SenderAddress" => $contacts->warehouse, //identifier of sender warehouse
                        "ContactSender" => $contacts->contact_ref,
                        "SendersPhone"  => $contacts->phone,

                        "CityRecipient"    => $request->approve['city'],
                        "Recipient"        => $recipientData->Ref,
                        "RecipientAddress" => $request->approve['warehouse'],
                        "ContactRecipient" => $recipientData->ContactPerson->data[0]->Ref,

                        "RecipientsPhone"       => $request->clientPhone,
                        "DateTime"              => $request->delivery_date,
                        // обратная доставка Ц1П
                        'BackwardDeliveryData'  => [
                            [
                                'PayerType'        => 'Recipient',
                                'CargoType'        => 'Money',
                                'RedeliveryString' => $orderUpdated->price_total,
                                // 'RedeliveryStringRub' => '400', // если доставка в Крым
                            ],
                        ],
                        'AdditionalInformation' => !empty($request->add_information) ? $request->add_information : '',
                    ]
                ]
            ]);
            $result = json_decode($response->getBody()->getContents());

            //dd($result);
            if (!empty($result->data[0])) {
                (new OrdersLog())->addOrderLog($orderUpdated->id, 'Данные по треку (' . $result->data[0]->Ref . ') обновлены');
                return ['success' => true, 'updated' => true, 'track2' => $result->data[0]->Ref];
            } elseif (!empty($result->errors)) {
                $results['errors'] = $result->errors;
                return $results;
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function deleteDocument()
    {
        $request = request();

        $target = TargetValue::where('order_id', $request->order_id)->first();
        $senderData = NovaposhtaKey::findOrFail($target->sender_id);

        $client = new GuzzleHttpClient();
        if ($request->approve['track2']) {
            $response = $client->request('POST', Novaposhta::API, [
                'json' => [
                    "apiKey"           => $senderData->key,
                    "modelName"        => "InternetDocument",
                    "calledMethod"     => "delete",
                    "methodProperties" => [
                        "DocumentRefs" => $request->approve['track2'],
                    ]
                ]
            ]);
            $result = json_decode($response->getBody()->getContents());

            if (empty($result->errors)) {

                $targetValue = json_decode($target->values, true);
                foreach ($targetValue as $key => $value) {
                    if ($value['field_name'] == 'track2' || $value['field_name'] == 'track') {
                        unset($targetValue[$key]);
                    }
                }
                $target->values = json_encode($targetValue);
                $target->track = 0;
                $target->track2 = 0;

                if ($target->save()) {
                    (new OrdersLog())->addOrderLog($target->order_id, 'Трек удален');
                    return ['success' => true, 'deleted' => true, 'track2' => $result->data[0]->Ref];
                }
            } elseif (!empty($result->errors)) {
                    $errorsResponse = (Array)$result->errors;
                    if ($errorsResponse["" . $request->approve['track2']] . "" == 'Document is register ' . $request->track) {

                        $response = $client->request('POST', Novaposhta::API, [
                            'json' => [
                                "apiKey"           => $senderData->key,
                                "modelName"        => "ScanSheet",
                                "calledMethod"     => "removeDocuments",
                                "methodProperties" => [
                                    "DocumentRefs" => [$request->approve['track2']],
                                ]
                            ]
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        if (!empty($result->data->DocumentRefs->Success) && empty($result->data->DocumentRefs->Errors)) {
                            $results['errors'] = ['1. Накладная удалена с Реестра. 2. Повторите удаление накладной. '];
                            return $results;
                        } elseif (!empty($result->data->DocumentRefs->Errors)) {
                            $results['errors'] = [$result->data->DocumentRefs->Errors];
                            return $results;
                        } else {
                            $results['errors'] = ['НП deleteDocument --- Произошла ошибка!'];
                            return $results;
                        }
                    } else {
                        $results['errors'] = $result->errors;
                        return $results;
                    }
            }
        }
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function generateKey($key)
    {
        $request = request();

        $client = new GuzzleHttpClient();
        $response = $client->request('POST', self::API, [
            'json' => [
                "apiKey"           => $key,
                "modelName"        => "Counterparty",
                "calledMethod"     => "getCounterparties",
                "methodProperties" => [
                    "CounterpartyProperty" => "Sender",
                    "Page"                 => "1"
                ]
            ]
        ]);

        $getCounterparties = json_decode($response->getBody()->getContents());

        $client = new GuzzleHttpClient();
        $response2 = $client->request('POST', self::API, [
            'json' => [
                "apiKey"           => $key,
                "modelName"        => "Counterparty",
                "calledMethod"     => "getCounterpartyContactPersons",
                "methodProperties" => [
                    "Ref"  => $getCounterparties->data[0]->Ref,
                    "Page" => "1"
                ]
            ]
        ]);
        $contacts = json_decode($response2->getBody()->getContents());


        $responseGetCounterpartyAddresses = $client->request('POST', self::API, [
            'json' => [
                "apiKey"           => $key,
                "modelName"        => "Counterparty",
                "calledMethod"     => "getCounterpartyAddresses",
                "methodProperties" => [
                    "Ref"                  => $getCounterparties->data[0]->Ref,
                    "CounterpartyProperty" => "Sender"
                ]
            ]
        ]);

        $senderAddress = json_decode($responseGetCounterpartyAddresses->getBody()->getContents());

        $newIntegtationKeys = [];
        if (count($contacts->data)) {
            foreach ($contacts->data as $contact) {
                $newIntegtationKeys[] = NovaposhtaKey::create([
                    'key'           => $request->key,
                    'name'          => $request->name,
                    'exp_key_date'  => $request->exp_key_date,
                    'active'        => 1,
                    'sender_id'     => $getCounterparties->data[0]->Ref,
                    'description'   => $request->description,
                    'contacts'      => json_encode([
                        'full_name'   => $contact->Description,
                        'phone'       => $contact->Phones,
                        'address'     => !empty($senderAddress->data->Description) ? $senderAddress->data->Description : NULL,
                        'addressRef'  => !empty($senderAddress->data->Ref) ? $senderAddress->data->Ref : NULL,
                        'city'        => $getCounterparties->data[0]->City,
                        'contact_ref' => $contact->Ref,
                        'email'       => !empty($contact->Email) ? $contact->Email : NULL

                    ]),
                    'size'          => $request->size,
                    'weight'        => $request->weight,
                    'subproject_id' => $request->sub_project_id,
                    'target_id'     => $request->integration_id
                ]);
            }
        }

        return $newIntegtationKeys;
    }

    /**
     * handled by Tracking command
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function track()
    {
        $target = TargetConfig::where([
            ['alias', 'novaposhta'],
        ])->first();

        if ($target) {
            if ($target->integration_status == TargetConfig::INTEGRATION_INACTIVE) {
                echo "Novaposhta inactive\n";
            }
            $ordersArray = Order::with('getTargetValue')->whereHas('getTargetValue', function ($query) use ($target) {
                $query->where('track', '!=', 0)->where('target_id', $target->id);
            })->where([
                ['final_target', 0]
            ])
                ->whereHas('procStatus', function ($query) {
                    $query->whereIn('action', ['sent', 'at_department']);
                });

            //chunk array of orders on 100 positions
            $ordersArray->chunk(100, function ($orders) use ($target) {
                $tracksNumbers = $orders->pluck('getTargetValue.track');
                $deliveryNotes = [];
                foreach ($tracksNumbers as $key => $track) {
                    $deliveryNotes[$key]['DocumentNumber'] = $track;
                    $deliveryNotes[$key]['Phone'] = "";
                }

                $logs['count_orders'] = $orders->count();
                $arrayTracking = [];
                if ($orders->count() && count($deliveryNotes)) {
                    try {
                        $client = new GuzzleHttpClient();
                        $response = $client->request('POST', Novaposhta::API, [
                            'json' => [
                                "apiKey"           => Novaposhta::KEY,
                                "modelName"        => "TrackingDocument",
                                "calledMethod"     => "getStatusDocuments",
                                "methodProperties" => [
                                    "Documents" => $deliveryNotes
                                ]
                            ]
                        ]);


                        $result = json_decode($response->getBody()->getContents(), true);
                        if (!empty($result['data'])) {
                            foreach ($result['data'] as $item) {
                                $targetValue = TargetValue::where('track', $item['Number'])
                                    ->first(['id', 'sender_id', 'order_id', 'target_id']);

                                if ($targetValue) {
                                    $order = Order::with('project')->findOrFail($targetValue->order_id);
                                    $oldStatus = $order->procStatus;
                                    $projectStatusesArray = isset($order->project->statuses) ? $order->project->statuses->pluck('id') : NULL;
                                    $code = CodeStatus::with('projectCodeStatus')
                                        ->where([
                                            ['integration_id', $target->id],
                                            ['status_code', $item['StatusCode']],
                                            ['system_status_id', '!=', 0]
                                        ])
                                        ->first();

                                    if (!empty($code)) {
                                        if ($code->projectCodeStatus->count()) {
                                            $projectCodeStatuses = $code->projectCodeStatus->pluck('proc_status_id')
                                                ->toArray();
                                            foreach ($projectCodeStatuses as $projectCode) {
                                                if (in_array($projectCode, $projectStatusesArray->toArray())) {
                                                    $order->proc_status = $projectCode;
                                                } else {
                                                    $order->proc_status = $code->system_status_id;
                                                }
                                            }
                                        } else {
                                            $order->proc_status = $code->system_status_id;
                                        }

                                        if ($order->proc_status != $oldStatus->id) {
                                            $newProcStatus = ProcStatus::find($order->proc_status);
                                            switch ($newProcStatus->action) {
                                                case 'sent':
                                                    $order->time_sent = Carbon::now();
                                                    break;
                                                case 'at_department':
                                                    $order->time_at_department = Carbon::now();
                                                    break;
                                                case 'received':
                                                    $order->time_received = Carbon::now();
                                                    break;
                                                case 'returned':
                                                    $order->time_returned = Carbon::now();
                                                    break;
                                                case 'paid_up':
                                                    $order->time_paid_up = Carbon::now();
                                                    break;
                                                case 'refused':
                                                    $order->time_refused = Carbon::now();
                                                    break;
                                            }
                                            //update status time
                                            $order->time_status_updated = Carbon::now();

                                            $statusInfo = [
                                                'status_id'   => $newProcStatus->id,
                                                'status_name' => $newProcStatus->name
                                            ];

                                            if ($order->save()) {
                                                echo $order->id . '   status changed, code: ' . $item['StatusCode'] . "\n";
                                                (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус установлен системой(изменен c "' . $oldStatus->name . '" на "' . $statusInfo['status_name'], $statusInfo);
                                            }
                                        }
                                    }

                                    $arrayTracking[$targetValue->order_id] = [
                                        'order_id'    => $targetValue->order_id,
                                        'target_id'   => $targetValue->target_id,
                                        'status_code' => $item['StatusCode'],
                                        'status'      => $item['Status'],
                                        'track'       => $item['Number'],
                                    ];
                                } else {
                                    echo 'нет цели - ' . $item['Number'] . "\n";
                                }
                            }
                        }

                    } catch (RequestException $exception) {
                        echo $exception->getMessage();
                    }
                    if ($arrayTracking) {
                        foreach ($arrayTracking as $tracking) {
                            Tracking::updateOrCreate($tracking, [
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            });
        }
    }

    public static function trackFinal()
    {
        $target = TargetConfig::where([
            ['alias', 'novaposhta'],
        ])->first();

        if ($target) {
            if ($target->integration_status == TargetConfig::INTEGRATION_INACTIVE) {
                echo "Novaposhta inactive\n";
            }
            $ordersArray = Order::with('getTargetValue')->whereHas('getTargetValue', function ($query) use ($target) {
                $query->where('track', '!=', 0)->where('target_id', $target->id);
            })->where([
                ['final_target', 0]
            ])
                ->whereHas('procStatus', function ($query) {
                    $query->whereIn('action', ['received', 'returned']);
                });

            //chunk array of orders on 100 positions
            $ordersArray->chunk(100, function ($orders) use ($target) {
                $tracksNumbers = $orders->pluck('getTargetValue.track');
                $deliveryNotes = [];
                foreach ($tracksNumbers as $key => $track) {
                    $deliveryNotes[$key]['DocumentNumber'] = $track;
                    $deliveryNotes[$key]['Phone'] = "";
                }

                $logs['count_orders'] = $orders->count();
                $arrayTracking = [];
                if ($orders->count() && count($deliveryNotes)) {
                    try {
                        $client = new GuzzleHttpClient();
                        $response = $client->request('POST', Novaposhta::API, [
                            'json' => [
                                "apiKey"           => Novaposhta::KEY,
                                "modelName"        => "TrackingDocument",
                                "calledMethod"     => "getStatusDocuments",
                                "methodProperties" => [
                                    "Documents" => $deliveryNotes
                                ]
                            ]
                        ]);


                        $result = json_decode($response->getBody()->getContents(), true);

                        if (!empty($result['data'])) {
                            foreach ($result['data'] as $item) {
                                if (!in_array($item['StatusCode'], [7, 8])) {
                                    echo $item['Number'] . ' статус:' . $item['StatusCode'];
                                    $targetValue = TargetValue::where('track', $item['Number'])
                                        ->first(['id', 'sender_id', 'order_id', 'target_id']);

                                    $order = Order::with('project')->findOrFail($targetValue->order_id);
                                    $oldStatus = $order->procStatus;
                                    $projectStatusesArray = isset($order->project->statuses) ? $order->project->statuses->pluck('id') : NULL;
                                    $code = CodeStatus::with('projectCodeStatus')
                                        ->where([
                                            ['integration_id', $target->id],
                                            ['status_code', $item['StatusCode']],
                                            ['system_status_id', '!=', 0]
                                        ])
                                        ->first();

                                    if (!empty($code)) {
                                        $statusUpdated = false;
                                        if ($code->projectCodeStatus->count()) {
                                            $projectCodeStatuses = $code->projectCodeStatus->pluck('proc_status_id')
                                                ->toArray();
                                            foreach ($projectCodeStatuses as $projectCode) {
                                                if (in_array($projectCode, $projectStatusesArray->toArray())) {
                                                    if ($projectCode != $order->proc_status) {
                                                        $order->proc_status = $projectCode;
                                                        $statusUpdated = true;
                                                    }
                                                } else {
                                                    if ($code->system_status_id != $order->proc_status) {
                                                        $order->proc_status = $code->system_status_id;
                                                        $statusUpdated = true;
                                                    }
                                                }
                                            }
                                        } else {
                                            if ($code->system_status_id != $order->proc_status) {
                                                $order->proc_status = $code->system_status_id;
                                                $statusUpdated = true;
                                            }
                                        }
                                        if ($statusUpdated) {
                                            if ($order->proc_status != $oldStatus->id) {
                                                $newProcStatus = ProcStatus::find($order->proc_status);
                                                switch ($newProcStatus->action) {
                                                    case 'sent':
                                                        $order->time_sent = Carbon::now();
                                                        break;
                                                    case 'at_department':
                                                        $order->time_at_department = Carbon::now();
                                                        break;
                                                    case 'received':
                                                        $order->time_received = Carbon::now();
                                                        break;
                                                    case 'returned':
                                                        $order->time_returned = Carbon::now();
                                                        break;
                                                    case 'paid_up':
                                                        $order->time_paid_up = Carbon::now();
                                                        break;
                                                    case 'refused':
                                                        $order->time_refused = Carbon::now();
                                                        break;
                                                }
                                                //update status time
                                                $order->time_status_updated = Carbon::now();

                                                $statusInfo = [
                                                    'status_id'   => $newProcStatus->id,
                                                    'status_name' => $newProcStatus->name
                                                ];
                                                if ($order->save()) {
                                                    echo $order->id . '   status changed, code: ' . $item['StatusCode'] . "\n";
                                                    (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус установлен системой(изменен c "' . $oldStatus->name . '" на "' . $statusInfo['status_name'], $statusInfo);
                                                }
                                            }
                                        }
                                    }

                                    $targetValue = TargetValue::where('track', $item['Number'])
                                        ->get(['order_id', 'target_id']);
                                    $arrayTracking[$targetValue[0]->order_id] = [
                                        'order_id'    => $targetValue[0]->order_id,
                                        'target_id'   => $targetValue[0]->target_id,
                                        'status_code' => $item['StatusCode'],
                                        'status'      => $item['Status'],
                                        'track'       => $item['Number'],
                                    ];
                                }
                            }
                        }

                    } catch (RequestException $exception) {
                        echo $exception->getMessage();
                    }
                    if ($arrayTracking) {
                        foreach ($arrayTracking as $tracking) {
                            Tracking::updateOrCreate($tracking, [
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            });
        }
    }

    /**
     * @param $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function settlementFind($request)
    {
        $term = trim($request->q);
        $settlements = [];
        if (isset($request->orderId)) {
            $order = Order::findOrFail($request->orderId);
        }

        if (isset($order) && $order->old) {
            if (!empty($request->SettlementRef)) {
                $city = NP::select('cid', 'city_ru')
                    ->groupBy('cid')
                    ->where('cid', $request->SettlementRef)
                    ->get();
            } else {
                $city = NP::select('cid', 'city_ru')
                    ->groupBy('cid')
                    ->where('city_ru', 'LIKE', '%' . $term . '%')
                    ->get();
            }


            foreach ($city as $item) {
                $settlements[] = [
                    'id'   => "" . $item->cid . "",
                    'text' => $item->city_ru
                ];
            }

        } else {

            try {
                $client = new GuzzleHttpClient();
                if (!empty($term)) {
                    $ref = '';
                } else {
                    $ref = $request->SettlementRef;
                }
                $response = $client->request('POST', Novaposhta::API, [
                    'json' => [
                        "apiKey"           => Novaposhta::KEY,
                        "modelName"        => "AddressGeneral",
                        "calledMethod"     => "getSettlements",
                        "Page"             => "1",
                        "methodProperties" => [
                            "FindByString" => $term,
                            "Ref"          => $ref,
                            "Page"         => "1",
                            "Warehouse"    => "1",
                        ]
                    ]
                ]);


                $getSettlement = json_decode($response->getBody()->getContents());

                foreach ($getSettlement->data as $item) {
                    $settlements[] = [
                        'id'   => "" . $item->Ref . "",
                        'text' => $item->Description . ', ' . $item->RegionsDescription .
                            (isset($item->SettlementTypeDescription) ? ', (' . $item->SettlementTypeDescription . ')' : '') .
                            (isset($item->AreaDescription) ? ', (' . $item->AreaDescription . ')' : '')
                    ];
                }
            } catch (\Exception $exception) {
            }
        }

        return $settlements;
    }

    /**
     * @param $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function warehouseFind($request)
    {
        $ref = '';
        $term = trim($request->q);


        if (isset($request->orderId)) {
            $order = Order::findOrFail($request->orderId);
        }

        if (isset($order) && $order->old) {
            $warehouses = [];
            if (!empty($request->settlementRef)) {
                $warehousesArray = NP::select('wid', 'whs_address_ru')
                    ->groupBy('wid')
                    ->orderBy('whs_number', 'asc')
                    ->where('whs_address_ru', 'LIKE', '%' . $term . '%')
                    ->where('cid', $request->settlementRef)
                    ->get();
            } elseif (!empty($request->warehouseRef)) {
                $warehousesArray = NP::select('wid', 'whs_address_ru')
                    ->groupBy('wid')->orderBy('whs_number', 'asc')
                    ->where('wid', $request->warehouseRef)
                    ->get();
            }
            foreach ($warehousesArray as $item) {
                $warehouses[] = [
                    'id'   => "" . $item->wid . "",
                    'text' => $item->whs_address_ru
                ];
            }
        } else {
            $warehouses = [];
            try {
                $client = new GuzzleHttpClient();
                if (!empty($term)) {
                    $ref = '';
                }
                if (!empty($request->warehouseRef)) {
                    $ref = $request->warehouseRef;
                }

                $response = $client->request('POST', Novaposhta::API, [
                    'json' => [
                        "apiKey"           => Novaposhta::KEY,
                        "modelName"        => "AddressGeneral",
                        "calledMethod"     => "getWarehouses",
                        "Page"             => "1",
                        "methodProperties" => [
                            "Language"      => "ru",
                            "FindByString"  => empty($request->warehouseRef) ? $term : '',
                            "SettlementRef" => !empty($request->settlementRef) ? $request->settlementRef : "",
                            //   "CityRef"       => !empty($request->cityRef) ? $request->cityRef : "",
                            "Ref"           => $ref,
                        ]
                    ]
                ]);

                $getWarehouses = json_decode($response->getBody()->getContents());

                foreach ($getWarehouses->data as $item) {
                    $warehouses[] = [
                        'id'   => "" . $item->Ref . "",
                        'text' => $item->Description
                    ];
                }
            } catch (\Exception $exception) {
            }
        }

        return $warehouses;
    }

    /**
     * @param Request $request
     * @param $client
     * @param $order
     * @return \stdClass
     */
    public static function saveRecipientCounterParty($request, $client, $senderData)
    {
        $responseSaveCounterparty = $client->request('POST', Novaposhta::API, [
            'json' =>
                [
                    "apiKey"           => $senderData->key,
                    "modelName"        => "Counterparty",
                    "calledMethod"     => "save",
                    "methodProperties" => [
                        "CityRef"              => $request->approve['city'],
                        "FirstName"            => $request->name,
                        "MiddleName"           => $request->middle,
                        "LastName"             => $request->surname,
                        "Phone"                => $request->phone,
                        "Email"                => "",
                        "CounterpartyType"     => "PrivatePerson",
                        "CounterpartyProperty" => "Recipient",
                    ]
                ]
        ]);

        $recipient = json_decode($responseSaveCounterparty->getBody()->getContents());
        return $recipient;
    }
}