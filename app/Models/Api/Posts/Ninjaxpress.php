<?php

namespace App\Models\Api\Posts;

use App\Models\Api\CodeStatus;
use App\Models\Api\Ninjaxpress\NinjaxpressKey;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\ProcStatus;
use App\Models\Project;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use App\Models\Tracking;
use App\Repositories\Integrations\NinjaxpressRepository;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\BadResponseException;

class Ninjaxpress extends AbstractPost

{
    const CREATE = true;
    const EDIT = false;
    const DELETE = true;

    const PRINT_NOTES = false;
    const PRINT_MARKINGS = false;
    const PRINT_MARKINGS_ZEBRA = false;

    const KEY = '';
    const API = 'https://api.ninjavan.co';
    const TRACKING = true;

    public static function otherFieldsView($params = [])
    {
        return view('integrations.ninjaxpress.otherFields', $params);
    }

    public static function editView(TargetConfig $integration)
    {
        if (auth()->user()->project_id) {
            $subProjects = Project::where('parent_id', auth()->user()->project_id)->get();
        } else {
            $subProjects = Project::where('parent_id', '!=', 0)->get();
        }
        return view('integrations.ninjaxpress.edit', [
            'id'          => $integration->id,
            'subProjects' => $subProjects,
            'keys'        => NinjaxpressKey::all(),
            'countries'   => collect((new Country())->getAllCounties())->keyBy('code')
        ]);
    }

    public static function renderView($params = [])
    {
        return view('integrations.ninjaxpress.index', $params);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createDocument()
    {
        $request = request()->all();
        $key = NinjaxpressKey::findOrFail($request['sender']);
        $order = Order::findOrFail($request['order_id']);
        $validator = \Validator::make($request, [
            'sender'      => 'required',
            'postal_code' => 'int',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $target = TargetValue::where('order_id', $request['order_id'])->first();
        $targetValues = json_decode($target->values);

        if (!empty($target->track)) {
            return ['success' => false, 'errors' => 'Track has been already created!'];
        }

        $client = new GuzzleHttpClient();
  
        try {
               $response = $client->request('POST', self::API.'/'. strtoupper($key->country).'/4.1/orders', [
          //  $response = $client->request('POST', 'https://api-sandbox.ninjavan.co/SG/4.1/orders', [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $key->access_token,
                ],
                "json"    => [
                    "service_type"              => "Parcel", //required
                    "service_level"             => "Standard",   //required
                    "requested_tracking_number" => !empty($target->track2) ? $target->track2 + 1 : $order->id . '00',
                    "from"                      =>
                        [
                            "name"         => $key->name,
                            "phone_number" => $key->phone,
                            "email"        => $key->email,
                            "address"      => [
                                "address1"  => $key->address,
                                "address2"  => "",
                                "country"   => strtoupper($key->country),
                                "postcode"  => $key->postcode,
                                'kelurahan' => $key->kelurahan,
                                'kecamatan' => $key->kecamatan,
                                'city'      => $key->city,
                                'province'  => $key->province
//                                "latitude"  => '-6.14960589185359',
//                                "longitude"  => '106.89020812511444'
                            ],
                        ],
                    "to"                        => [
                        "name"         => $order->name_last . ' ' . $order->name_first,
                        "phone_number" => "+" . $order->phone,
                        "email"        => "", //empty
                        "address"      => [
                            "address1"  => $targetValues->street->field_value,
                            "address2"  => "",
                            "country"   => strtoupper($key->country),
                            "postcode"  => $targetValues->postal_code->field_value,
                            "kelurahan" => $targetValues->district->field_value,
                            "kecamatan" => $targetValues->region->field_value,
                            "city"      => $targetValues->city->field_value,
                            "province"  => $targetValues->warehouse->field_value
//                            "latitude"  => $targetValues->latitude->field_value,
//                            "longitude"  => $targetValues->longitude->field_value
                        ]
                    ],
                    "parcel_job"                => [
                        "is_pickup_required"     => true,
                        'pickup_service_type'    => "Scheduled", //Scheduled pickups.
                        'pickup_service_level'   => "Standard",//Scheduled pickups.
                        'pickup_date'            => $targetValues->pickup_date->field_value ?? "",
                        //Specifies the date for which the pickup will occur.
                        'pickup_timeslot'        => [
                            'start_time' => $targetValues->pickup_time_min->field_value ?? "",
                            'end_time'   => $targetValues->pickup_time_max->field_value ?? "",
                            'timezone'   => "Asia/Jakarta" // change to Indonesia
                        ],
                        'pickup_instruction'     => "",
                        "delivery_start_date"    => $targetValues->delivery_date->field_value ?? "",
                        "delivery_timeslot"      => [
                            "start_time" => $targetValues->delivery_time_min->field_value ?? "",
                            "end_time"   => $targetValues->delivery_time_max->field_value ?? "",
                            "timezone"   => $targetValues->timezone->field_value
                        ],
                        "delivery_instructions"  => $targetValues->delivery_instructions->field_value ?? "",
                        "allow_weekend_delivery" => false, //казали пока не нужно
                        // Specifies whether or not deliveries should be attempted on weekends.
                        'cash_on_delivery'       => intval($order->price_total),
                        //Specifies the cash that should be picked up from recipient of the parcel.
                        "insured_value"          => !empty($key->insurance) ? intval($order->price_total) : 0,
                        "dimensions"             => [
                            "size"   => $key->size,
                            "weight" => (float)$key->weight,//The weight of the parcel, in kilograms (k.g.). number
                            "length" => (float)$key->length,//number The length of the parcel, in centimeters (c.m.).
                            "width"  => (float)$key->width, // number The width of the parcel, in centimeters (c.m.).
                            "height" => (float)$key->height, //number The height of the parcel, in centimeters (c.m.).
                        ]
                    ]
                ]
            ]);
            $responseContent = json_decode($response->getBody()->getContents());
            $statusCode = json_decode($response->getStatusCode());

            if ($statusCode == 200) {
                $targetFields = json_decode($target->values, true);

                $targetData = $request['approve'];
                if ($targetFields) {
                    foreach ($targetFields as $targetField) {
                        if (isset($targetData[$targetField['field_name']])) {
                            $targetField['field_value'] = $targetData[$targetField['field_name']];
                        }
                        if ($targetField['field_name'] == 'track') {
                            $targetField['field_value'] = $responseContent->tracking_number;
                        }
                        if ($targetField['field_name'] == 'track2') {
                            $targetField['field_value'] = !empty($target->track2) ? $target->track2 + 1 : $order->id . '00';
                        }
                        $targetValue[$targetField['field_name']] = $targetField;
                    }
                }

                $target->values = json_encode($targetValue);
                $target->track = $responseContent->tracking_number;
                $target->track2 = !empty($target->track2) ? $target->track2 + 1 : $order->id . '00';
                $target->sender_id = $key->id;
                if ($target->save()) {
                    (new OrdersLog)->addOrderLog($target->order_id, 'Создан трек  ' . $responseContent->tracking_number);
                    return [
                        'success' => true,
                        'created' => true,
                        'track'   => $responseContent->tracking_number,
                        'sender'  => $key->id
                    ];
                }
            } else {
                return ['success' => false, 'errors' => $statusCode . ' ' . $responseContent];
            }
        } catch (BadResponseException $exception) {
           // $response = $exception->getResponse();
           // $jsonBody = (string)$response->getBody();

            if ($exception->getCode() == 401) {
                $keydata = new \stdClass();
                $keydata->key_id = $key->id;
                $generateAccessTokenResult = NinjaxpressRepository::generateAccessToken($keydata);
                if ($generateAccessTokenResult['success']) {
                    return [
                        'success' => false,
                        'errors'  => trans('alerts.access-token-has-been-updated')
                    ];
                } else {
                    return ['success' => false, 'errors' => 'Ошибка на сервере!'];
                }
            } elseif ($exception->getCode() == 400) {
                $response = $exception->getResponse();

                $jsonBody = (string)$response->getBody();
                $details = json_decode($jsonBody)->error;
                if (isset($details->message)) {
                    $errors = [];
                    foreach ($details as $detail) {
                        $errors[] = $detail->message;
                    }
                    return ['success' => false, 'errors' => $errors];
                }elseif(isset($details)){
                    return ['success' => false, 'errors' => $details];
                }
                return ['success' => false, 'errors' => $jsonBody];
            }
        }
    }

    public static function editDocument()
    {
        // TODO: Implement editDocument() method.
    }

    public static function deleteDocument()
    {
        $request = request();
        if ($request->sender) {
            $target = TargetValue::where('order_id', $request->order_id)->first();
            $key = NinjaxpressKey::findOrFail($request->sender);
            try {
                $client = new GuzzleHttpClient();
                $response = $client->request('DELETE', self::API . '/' . $key->country . '/2.2/orders/' . $target->track, [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $key->access_token,
                    ]
                ]);
                $statusCode = json_decode($response->getStatusCode());

                if ($statusCode == 200) {
                    $targetFields = json_decode($target->values, true);

                    $targetData = $request['approve'];
                    if ($targetFields) {
                        foreach ($targetFields as $targetField) {
                            if (isset($targetData[$targetField['field_name']])) {
                                $targetField['field_value'] = $targetData[$targetField['field_name']];
                            }
                            if ($targetField['field_name'] == 'track') {
                                $targetField['field_value'] = "";
                            }
                            $targetValue[$targetField['field_name']] = $targetField;
                        }
                    }

                    $target->values = json_encode($targetValue);
                    $target->track = 0;
                    if ($target->save()) {
                        (new OrdersLog)->addOrderLog($target->order_id, 'Трек удален.');
                        return [
                            'success' => true,
                            'deleted' => true
                        ];
                    }
                }
            } catch (BadResponseException $exception) {
                if ($exception->getCode() == 401) {
                    $keydata = new \stdClass();
                    $keydata->key_id = $key->id;
                    $generateAccessTokenResult = NinjaxpressRepository::generateAccessToken($keydata);
                    if ($generateAccessTokenResult['success']) {
                        return [
                            'success' => false,
                            'errors'  => trans('alerts.access-token-has-been-updated')
                        ];
                    } else {
                        return ['success' => false, 'errors' => 'Ошибка на сервере!'];
                    }
                }
                elseif ($exception->getCode() == 400) {
                    $response = $exception->getResponse();
                    $jsonBody = (string)$response->getBody();
                    $details = json_decode($jsonBody)->error->details;
                    if ($details) {
                        $errors = [];
                        foreach ($details as $detail) {
                            $errors = $detail->message;
                        }
                        return ['success' => false, 'errors' => $errors];
                    }
                    return ['success' => false, 'errors' => $jsonBody];
                } else {
                    return ['success' => false, 'errors' => $exception->getMessage()];
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function track()
    {
        $request = request();
        $target = TargetConfig::where([
            ['alias', 'ninjaxpress'],
        ])->first();
        $targetValue = TargetValue::where('track', $request->tracking_id)->first();
        if ($targetValue) {
            $order = Order::find($targetValue->order_id);
            $oldStatus = $order->procStatus;

            if (!empty($request->tracking_id)) {
                $projectStatusesArray = isset($order->project->statuses) ? $order->project->statuses->pluck('id') : NULL;
                $code = CodeStatus::with('projectCodeStatus')
                    ->where([
                        ['integration_id', $target->id],
                        ['status_code', $request->status],
                        ['system_status_id', '!=', 0]
                    ])
                    ->first();

                if (!empty($code)) {
                    if ($code->projectCodeStatus->count()) {
                        $projectCodeStatuses = $code->projectCodeStatus->pluck('proc_status_id')
                            ->toArray();
                        foreach ($projectCodeStatuses as $projectCode) {
                            if (!empty($projectStatusesArray->toArray()) && in_array($projectCode, $projectStatusesArray->toArray())) {
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
                            echo $order->id . '   status changed, code: ' . $request->status . "\n";
                            (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус установлен системой(изменен c "' . $oldStatus->name . '" на "' . $statusInfo['name'], $statusInfo);
                        }
                    }
                }
            }
            $arrayTracking[$targetValue->order_id] = [
                'order_id'      => $targetValue->order_id,
                'status_code'   => $request->status,
                'target_id'     => $targetValue->target_id,
                'status'        => $request->status,
                'track'         => $request->tracking_id,
                'comment'       => $request->comments ?? NUll,
                'delivery_info' => isset($data->pod) ? json_encode($request->pod) : NUll,
            ];
            Tracking::updateOrCreate($arrayTracking[$targetValue->order_id], [
                'updated_at' => now()
            ]);
            return ['response' => 'Ok', 'code' => 200];
        } else {
            return ['response' => 'Track is not found!', 'code' => 404];
        }
    }
}