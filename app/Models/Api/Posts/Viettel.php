<?php

namespace App\Models\Api\Posts;

use App\Models\Api\CodeStatus;
use App\Models\Api\ProjectCodeStatus;
use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\TargetConfig;
use App\Models\Project;
use App\Models\Tracking;
use App\Models\VietnamWard;
use App\Models\ViettelSender;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;
use App\Models\Api\ViettelKey;
use App\Models\TargetValue;
use App\Models\Order;
use App\Models\ProcStatus;

class Viettel extends AbstractPost

{
    const CREATE = true;
    const EDIT = false;
    const DELETE = true;

    const PRINT_NOTES = false;
    const PRINT_MARKINGS = false;
    const PRINT_MARKINGS_ZEBRA = false;

    const KEY = '';
    const API = 'https://api.viettelpost.vn';

    const TRACKING = true;

    public static function otherFieldsView($params = [])
    {
        $integration = TargetConfig::integration()
            ->where('alias', 'viettel')
            ->firstOrFail();

        return view('integrations.viettel.otherFields', $params, ['integrationKeys' => $integration->viettelKeys]);
    }

    public static function editView(TargetConfig $integration)
    {
        if (auth()->user()->project_id) {
            $subProjects = Project::where('parent_id', auth()->user()->project_id)->get();
        } else {
            $subProjects = Project::where('parent_id', '!=', 0)->get();
        }
        return view('integrations.viettel.edit', [
            'id'          => $integration->id,
            'subProjects' => $subProjects,
            'keys'        => ViettelKey::all(),
        ]);
    }

    public static function renderView($params = [])
    {
        return view('integrations.viettel.index', $params);
    }

    /**
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function login($data)
    {

        $client = new GuzzleHttpClient();

        $response = $client->request('POST', self::API . '/api/user/Login', [
            'json' => [
                "USERNAME" => $data->account_email,
                "PASSWORD" => $data->account_password,
                "SOURCE"   => 0,
            ]
        ]);

        $account = json_decode($response->getBody()->getContents());

        if (!empty($account) && isset($account->UserName)) {
            $exitingKey = ViettelKey::where('email', $data->account_email)->first();

            if (!empty($exitingKey)) {
                if ($exitingKey->update(['' => $account->TokenKey])) {
                    $keyUpdated = true;
                    return $keyUpdated;
                };
            }
            $target = TargetConfig::where('alias', 'viettel')->first();
            $viettelKey = ViettelKey::create([
                'target_id'     => !empty($target) ? $target->id : 0,
                'subproject_id' => $data->sub_project_id,
                'active'        => 0,
                'email'         => $data->account_email,
                'name'          => $data->name,
                'user_name'     => $account->UserName,
                'user_id'       => $account->UserId,
                'role'          => $account->Role,
                'from_source'   => $account->FromSource,
                'token_key'     => $account->TokenKey,
            ]);

            if ($viettelKey) {
                $responseListInventory = $client->request('POST', self::API . '/api/setting/listInventory', [
                    'headers' => [
                        'Token'        => $viettelKey->token_key,
                        'Content-Type' => 'application/json',
                    ],
                ]);
                $warehousesData = json_decode($responseListInventory->getBody()->getContents());
                if ($warehousesData) {
                    foreach ($warehousesData as $warehouse) {
                        ViettelSender::create([
                            'viettel_key_id' => $viettelKey->id,
                            'warehouse_id'   => $warehouse->GROUPADDRESS_ID ?? 0,
                            'customer_id'    => $warehouse->CUS_ID ?? 0,
                            'name'           => $warehouse->NAME ?? 0,
                            'address'        => $warehouse->ADDRESS ?? 0,
                            'phone'          => $warehouse->PHONE ?? 0,
                            'post_id'        => $warehouse->POST_ID ?? 0,
                            'province_id'    => $warehouse->PROVINCE_ID ?? 0,
                            'district_id'    => $warehouse->DISTRICT_ID ?? 0,
                            'wards_id'       => $warehouse->WARDS_ID ?? 0,
                            'province_name'  => $warehouse->TEN_TINH ?? 0,
                            'district_name'  => $warehouse->TEN_HUYEN ?? 0,
                            'wards_name'     => $warehouse->TEN_XA ?? 0
                        ]);
                    }
                }
            }

            $result = ['accout' => $account, 'key' => $viettelKey];
            return $result;
        } elseif (!empty($account->error)) {
            $result = [];
            $result['account']->message = $account->message;
            return $result;
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createDocument()
    {
        $request = request()->all();
        $order = Order::findOrFail($request['order_id']);
        $productsCount = 0;
        $validator = \Validator::make($request, [
            'sender'           => 'required',
            'sender_warehouse' => 'required',
            'product_weight'   => 'required|int',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $target = TargetValue::where('order_id', $request['order_id'])->first();
        if(!empty($target->track)){
            return ['success' => false,  'errors' =>  'Трек уже создан!'];
        }
        $sender = ViettelSender::where('id', $request['sender_warehouse'])->first();
        $offers = (new OrderProduct())->getProductsByOrderId($order->id, $order->subproject_id ?? 0);

        if (!empty($offers)) {
            foreach ($offers as $offer) {
                if (!$offer->disabled) {
                    $productsList[] = [
                        "PRODUCT_NAME"     => $offer->title,
                        "PRODUCT_PRICE"    => $offer->price,
                        "PRODUCT_WEIGHT"   => $offer->weight,
                        "PRODUCT_QUANTITY" => 1,
                    ];
                }
            }
        }
        $productsCount = count($productsList);

        try {
            // dd( intval($order->price_total));
            $client = new GuzzleHttpClient();

            $response = $client->request('POST', self::API . '/api/tmdt/InsertOrder', [
                'headers' => [
                    'Token'        => $sender->key->token_key,
                    'Content-Type' => 'application/json',
                ],
                'json'    => [
                    "ORDER_NUMBER"        => isset($order->project->alias) ? $order->project->alias . $order->id : "",
                    "GROUPADDRESS_ID"     => $sender->warehouse_id, //sender_warehouse id
                    "CUS_ID"              => $sender->customer_id,
                    "DELIVERY_DATE"       => $request['approve']['date'],
                    "SENDER_FULLNAME"     => $sender->name,
                    "SENDER_ADDRESS"      => $sender->address,
                    "SENDER_PHONE"        => $sender->phone,
                    "SENDER_EMAIL"        => $sender->key->email,
                    "SENDER_WARD"         => intval($sender->wards_id),
                    "SENDER_DISTRICT"     => intval($sender->district_id),
                    "SENDER_PROVINCE"     => intval($sender->province_id),
                    "SENDER_LATITUDE"     => 0,
                    "SENDER_LONGITUDE"    => 0,
                    "RECEIVER_FULLNAME"   => $request['surname'] . ' ' . $request['name'],
                    "RECEIVER_ADDRESS"    => !empty($request['approve']['street']) ? $request['approve']['street'] : "",
                    "RECEIVER_PHONE"      => $request['phone'],
                    "RECEIVER_EMAIL"      => "",
                    "RECEIVER_WARD"       => intval($request['approve']['region']),
                    "RECEIVER_DISTRICT"   => intval($request['approve']['district']),
                    "RECEIVER_PROVINCE"   => intval($request['approve']['warehouse']),
                    "RECEIVER_LATITUDE"   => 0,
                    "RECEIVER_LONGITUDE"  => 0,
                    "PRODUCT_NAME"        => $request['products_description'], //list of all products
                    "PRODUCT_DESCRIPTION" => $request['products_description'], //list of all products
                    "PRODUCT_QUANTITY"    => $productsCount ?? 0,
                    "PRODUCT_PRICE"       => intval($order->price_total),
                    "PRODUCT_WEIGHT"      => !empty($request['product_weight']) ? intval($request['product_weight']) : 0,
                    "PRODUCT_LENGTH"      => !empty($request['product_length']) ? intval($request['product_length']) : 0,
                    "PRODUCT_WIDTH"       => !empty($request['product_width']) ? intval($request['product_width']) : 0,
                    "PRODUCT_HEIGHT"      => !empty($request['product_height']) ? intval($request['product_height']) : 0,
                    "PRODUCT_TYPE"        => "HH", //goods
                    "ORDER_PAYMENT"       => 3, //УТОЧНЕНО
                    "ORDER_SERVICE"       => "VCN",//*Express dilivery*/  //УТОЧНЕНО
                    "ORDER_SERVICE_ADD"   => "", //Уточнено, не нужно!
                    "ORDER_VOUCHER"       => "",
                    "ORDER_NOTE"          => isset($request['order_note']) ? $request['order_note'] : "",
                    "MONEY_COLLECTION"    => intval($order->price_total),//Общая сумма за товары
                    "MONEY_TOTALFEE"      => 0,
                    "MONEY_FEECOD"        => 0,
                    "MONEY_FEEVAS"        => 0,
                    "MONEY_FEEINSURRANCE" => 0,
                    "MONEY_FEE"           => 0,
                    "MONEY_FEEOTHER"      => 0,
                    "MONEY_TOTALVAT"      => 0,
                    "MONEY_TOTAL"         => 0,
                    "LIST_ITEM"           => $productsList
                ]
            ]);
            $result = json_decode($response->getBody()->getContents());
            if ($result->status == 201 && !empty($result->message)) {
                return ['success' => false, 'errors' => $result->message];
            };
            if ($result->status == 200 && !empty($result->data->ORDER_NUMBER)) {
                try {
                    if ($target) {
                        $option = json_decode($target->values, true);
                        if (isset($option['track'])) {
                            $option['track']['field_value'] = $result->data->ORDER_NUMBER;
                        }
                        $target->values = json_encode($option);
                        $target->track = $result->data->ORDER_NUMBER;
                        $target->sender_id = !empty($request['sender']) ? $request['sender'] : 0;
                    }
                    if ($target->save()) {
                        //calculate delivery cost
                        $responseGetPrice = $client->request('POST', self::API . '/api/tmdt/getPrice', [
                            'headers' => [
                                'Token'        => $sender->key->token_key,
                                'Content-Type' => 'application/json',
                            ],
                            'json'    => [
                                "SENDER_PROVINCE"   => intval($sender->province_id),
                                "SENDER_DISTRICT"   => intval($sender->district_id),
                                "RECEIVER_PROVINCE" => intval($request['approve']['warehouse']),
                                "RECEIVER_DISTRICT" => intval($request['approve']['district']),
                                "PRODUCT_TYPE"      => "HH", //Goods
                                "ORDER_SERVICE"     => "VCN", //express delivery
                                "ORDER_SERVICE_ADD" => "",
                                "PRODUCT_WEIGHT"    => !empty($request['product_weight']) ? intval($request['product_weight']) : 0,
                                "PRODUCT_PRICE"     => intval($order->price_total),
                                "MONEY_COLLECTION"  => intval($order->price_total),
                                "PRODUCT_QUANTITY"  => $productsCount ?? 0,
                                "NATIONAL_TYPE"     => 1 //inland
                            ]
                        ]);
                        $resultGetPrice = json_decode($responseGetPrice->getBody()->getContents());
                        $deliveryCost = 0;
                        if (count($resultGetPrice) > 0) {
                            foreach ($resultGetPrice as $row) {
                                if (isset($row->SERVICE_CODE) and $row->SERVICE_CODE == 'ALL') {
                                    $deliveryCost = $row->PRICE;

                                }
                            }
                            $target = TargetValue::where('order_id', $request['order_id'])->first();

                            if (!empty($deliveryCost)) {
                                if ($target) {
                                    $option = json_decode($target->values, true);
                                    if (isset($option['cost_actual'])) {
                                        $option['cost_actual']['field_value'] = $deliveryCost;
                                    }

                                    $target->values = json_encode($option);
                                    $target->sender_id = request()->sender ?? 0;
                                    $target->cost_actual = $deliveryCost;

                                    if ($target->save()) {
                                        (new OrdersLog())->addOrderLog($order->id, 'Создан трек Viettel:  ' . $result->data->ORDER_NUMBER . '  | Сумма доставки: ' . $deliveryCost);
                                        return ['success'      => true,
                                                'created'      => true,
                                                'track'        => $result->data->ORDER_NUMBER,
                                                'deliveryCost' => $deliveryCost
                                        ];
                                    }
                                }
                            }
                        }
                    }
                    (new OrdersLog())->addOrderLog($order->id, 'Создан трек ' . $result->data->ORDER_NUMBER);
                    return ['success' => true, 'created' => true, 'track' => $result->data->ORDER_NUMBER];
                } catch (\Exception $e) {
                    return ['success' => false,  'errors' => $e->getMessage()];
                }
            } elseif (!empty($result->errors)) {
                $results['errors'] = $result->errors;
                return $results;
            }
        } catch (\Exception $exception) {
            return ['success' => false, 'errors' => $exception->getMessage()];
        };
    }

    public static function editDocument()
    {
        // TODO: Implement editDocument() method.
    }

    public static function deleteDocument()
    {
        $request = request();
        if ( $request['approve']['track']) {
            $target = TargetValue::where('order_id', $request->order_id)->first();
            $targetValue = json_decode($target->values, true);
            foreach ($targetValue as $key => $value) {
                if ($value['field_name'] == 'track') {
                    unset($targetValue[$key]);
                }
                if ($value['field_name'] == 'cost_actual') {
                    unset($targetValue[$key]);
                }
            }
            $target->values = json_encode($targetValue);
            $target->track = 0;
            $target->cost_actual = 0;

            if ($target->save()) {
                return ['success' => true, 'deleted' => true];
            } else {
                return ['success' => false, 'deleted' => false];
            }
        }
    }

    /**
     * @param $request
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function generateKey($request)
    {
        $client = new GuzzleHttpClient();

        $response = $client->request('POST', self::API . '/api/user/Register', [
            'json' => [
                "FIRSTNAME"    => $request->first_name,
                "LASTNAME"     => $request->last_name,
                "EMAIL"        => $request->email,
                "PASSWORD"     => $request->password,
                "PHONE"        => $request->phone,
                "DISPLAYNAME"  => $request->first_name . ' ' . $request->last_name,
                "INTRODUCTION" => $request->introduction,
                "DISTRICT_ID"  => $request->district,
                "WARDS_ID"     => $request->ward,
                "ADDRESS"      => $request->address,
            ]

        ]);

        $account = json_decode($response->getBody()->getContents());
        $target = TargetConfig::where('alias', 'viettel')->first();
        if (!empty($account) && isset($account->UserName)) {
            $viettelKey = ViettelKey::create([
                'target_id'     => !empty($target) ? $target->id : 0,
                'subproject_id' => $request->sub_project_id,
                'active'        => 0,
                'user_name'     => $account->UserName,
                'user_id'       => $account->UserId,
                'role'          => $account->Role,
                'from_source'   => $account->FromSource,
                'token_key'     => $account->TokenKey,
            ]);

            if ($viettelKey) {
                $html = view('integrations.viettel.accounts-table', ['keys' => ViettelKey::all()])->render();
                return ['success' => true, 'html' => $html];
            }
        } elseif ($account->error) {
            $error = ['error' => $account->message];
            return $error;
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function track()
    {
        $target = TargetConfig::where([
            ['alias', 'viettel'],
        ])->first();

        if ($target) {
            $logs = [];
            if ($target->integration_status == TargetConfig::INTEGRATION_INACTIVE) {
                echo "Viettel inactive\n";
            }
            $ordersArray = Order::with('getTargetValue', 'project')
                ->whereHas('getTargetValue', function ($query) use ($target) {
                    $query->where([['track', '!=', 0], ['target_id', $target->id]]);
                })->where([
                    ['final_target', 0]
                ])
                ->whereHas('procStatus', function ($query) {
                    $query->whereIn('action', ['sent', 'at_department']);
                });

            //chunk array of orders on 100 positions
            $ordersArray->chunk(100, function ($orders) use ($target) {
                foreach ($orders as $order) {
                    $oldStatus = $order->procStatus;
                    $targetValue = TargetValue::with('viettelKey')->where('order_id', $order->id)
                        ->first(['id', 'sender_id', 'order_id', 'target_id', 'track']);
                    $logs['count_orders'] = $orders->count();
                    $arrayTracking = [];
                    if ($targetValue->track) {
                        try {
                            $projectStatusesArray = isset($order->project->statuses) ? $order->project->statuses->pluck('id') : NULL;
                            if (!empty($targetValue->viettelKey)) {
                                $client = new GuzzleHttpClient();
                                $responseTrack = $client->request('GET', self::API .
                                    '/api/setting/listOrderTracking?OrderNumber=' . $targetValue->track, [
                                    'headers' => [
                                        'Token' => $targetValue->viettelKey->token_key,
                                    ]
                                ]);
                                $resultTrack = json_decode($responseTrack->getBody()->getContents());
                                if (!empty($resultTrack)) {
                                    foreach ($resultTrack as $row) {

                                        $code = CodeStatus::with('projectCodeStatus')
                                            ->where([['integration_id', $target->id], ['status_code', $row->ORDER_STATUS], ['system_status_id', '!=', 0]])
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
                                                    echo $order->id . '   status changed, code: ' . $row->ORDER_STATUS . "\n";
                                                    (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус установлен системой(изменен c "' . $oldStatus->name . '" на "' . $statusInfo['status_name'], $statusInfo);
                                                }
                                            }
                                        }

                                        $arrayTracking[$targetValue->order_id] = [
                                            'order_id'    => $targetValue->order_id,
                                            'status_code' => $row->ORDER_STATUS,
                                            'target_id'   => $targetValue->target_id,
                                            'status'      => $row->STATUS_NAME,
                                            'track'       => $row->ORDER_NUMBER,
                                        ];
                                        Tracking::updateOrCreate($arrayTracking[$targetValue->order_id], [
                                            'updated_at' => now()
                                        ]);
                                    }
                                }
                            }
                        } catch (Exception $exception) {
                            echo $exception->getMessage();
                        }
                    }
                }
            });
        }
    }

    /**
     * Поиск провинции
     * @return array
     */
    public static function provinceFind($term, $request)
    {
        $provinces = '';
        if (isset($request->province)) {
            $provinces = VietnamWard::where('province_id', $request->province)->groupBy('province_id')
                ->get(['province_id', 'province_name', 'district_id']);
        } else {
            $provinces = VietnamWard::where('province_name', 'LIKE', '%' . $term . '%')->groupBy('province_id')
                ->get(['province_id', 'province_name', 'district_id']);
        }

        return $provinces;
    }

    /**
     * @param $term
     * @return mixed
     */
    public static function districtFind($term, $request)
    {
        $districts = '';
        if (!empty($request->district)) {
            $districts = VietnamWard::where('district_name', 'LIKE', '%' . $term . '%')
                ->where('district_id', $request->district)
                ->where('province_id', $request->province)
                ->groupBy('district_id')
                ->get(['district_name', 'district_id']);
        } else {
            $districts = VietnamWard::where('district_name', 'LIKE', '%' . $term . '%')
                ->where('province_id', $request->province_id)
                ->groupBy('district_id')
                ->get(['district_name', 'district_id']);
        }


        return $districts;
    }

    /**
     * Поиск административного подразделения
     *  у ward есть DISTRICT_ID
     * @return array
     */
    public static function wardFind($term, $request)
    {
        $wards = '';
        if (isset($request->ward)) {
            $wards = VietnamWard::where('ward_id', $request->ward)
                ->get(['ward_name', 'ward_id']);
        } else {
            $wards = VietnamWard::where('ward_name', 'LIKE', '%' . $term . '%')
                ->where('district_id', $request->district)
                ->get(['ward_name', 'ward_id']);
        }

        return $wards;
    }
}