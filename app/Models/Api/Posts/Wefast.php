<?php

namespace App\Models\Api\Posts;


use App\Models\Api\WeFast\WeFastCounterparty;
use App\Models\Api\WeFast\WeFastKey;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\Product;
use App\Models\Project;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use App\Models\Tracking;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class Wefast extends AbstractPost
{
    const CREATE = true;

    const TRACKING = true;

    const API_URI = 'http://my.wefast.vn/api/public/order';

    public static $statusList = [
        "000" => [
            "name"        => "Chờ gửi vận đơn",
            "description" => "",
            "action"      => "",
        ],
        "001" => [
            "name"        => "Đã gửi vận đơn",
            "description" => "Chờ nhận hàng",
            "action"      => "",
        ],
        "002" => [
            "name"        => "Nhân viên duyệt vận đơn",
            "description" => "Chờ nhận hàng",
            "action"      => "",
        ],
        "003" => [
            "name"        => "Gửi vận đơn thất bại",
            "description" => "Chờ nhận hàng",
            "action"      => "",
        ],
        "100"   => [
            "name"        => "Tiếp nhận vận đơn",
            "description" => "Chờ nhận hàng",
            "action"      => "",
        ],
        "101"   => [
            "name"        => "Hủy theo yêu cầu",
            "description" => "Hủy đơn hàng",
            "action"      => "",
        ],
        "102"   => [
            "name"        => "Trung tâm từ chối vận đơn",
            "description" => "Hủy đơn hàng",
            "action"      => "",
        ],
        "103"   => [
            "name"        => "Giao bưu cục",
            "description" => "Đang nhận hàng",
            "action"      => "",
        ],
        "104"   => [
            "name"        => "Bưu tá đi nhận hàng",
            "description" => "Đang nhận hàng",
            "action"      => "",
        ],
        "105"   => [
            "name"        => "Bưu tá đã nhận hàng",
            "description" => "Xác nhận nhận được hàng",
            "action"      => "",
        ],
        "106"   => [
            "name"        => "Bưu tá không nhận được hàng",
            "description" => "Không nhận được hàng",
            "action"      => "",
        ],
        "109"   => [
            "name"        => "Bưu cục chuyển trả đơn hàng",
            "description" => "Không nhận được hàng",
            "action"      => "",
        ],
        "200"   => [
            "name"        => "Nhận từ bưu tá - Bưu cục gốc",
            "description" => "Xác nhận nhận được hàng",
            "action"      => "",
        ],
        "202"   => [
            "name"        => "Tách tải kiện",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "300"   => [
            "name"        => "Đóng bảng kê đi",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "301"   => [
            "name"        => "Đóng túi gói",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "302"   => [
            "name"        => "Đóng chuyến thư",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "303"   => [
            "name"        => "Gửi chuyến thư đi",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "400"   => [
            "name"        => "Nhận bảng kê đến",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "401"   => [
            "name"        => "Nhận túi thư",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "402"   => [
            "name"        => "Nhận chuyến thư",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "403"   => [
            "name"        => "Nhận chuyến xe",
            "description" => "Đang vận chuyển",
            "action"      => "Отправлен",
        ],
        "500"   => [
            "name"        => "Bưu tá đi phát",
            "description" => "Phân công bưu tá đi phát",
            "action"      => "На отделении",
        ],
        "501"   => [
            "name"        => "Phát thành công",
            "description" => "Phát thành công",
            "action"      => "Забран",
        ],
        "502"   => [
            "name"        => "Đang chuyển hoàn",
            "description" => "Đang chuyển hoàn",
            "action"      => "На отделении",
        ],
        "503"   => [
            "name"        => "Hủy - Theo yêu cầu khách hàng",
            "description" => "Phát không thành công, yêu cầu tiêu hủy hàng hóa",
            "action"      => "",
        ],
        "504"   => [
            "name"        => "Đã chuyển hoàn",
            "description" => "Đã chuyển hoàn về người gửi",
            "action"      => "Возврат",
        ],
        "505"   => [
            "name"        => "Tồn - Sai địa chỉ phát",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "506"   => [
            "name"        => "Tồn - Khách hàng nghỉ không có nhà",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "507"   => [
            "name"        => "Tồn - Khách hàng đến bưu cục nhận",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "512"   => [
            "name"        => "Tồn - Khách hàng từ chối nhận",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "513"   => [
            "name"        => "Tồn - Điện thoại không liên lạc được",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "508"   => [
            "name"        => "Chuyển tiếp",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "509"   => [
            "name"        => "Giữ lại bưu cục phát xử lý",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "510"   => [
            "name"        => "Hủy phân công phát",
            "description" => "Trạng thái tồn đơn hàng",
            "action"      => "На отделении",
        ],
        "600"   => [
            "name"        => "Đang đối soát",
            "description" => "Đang đối soát",
            "action"      => "Забран",
        ],
        "601"   => [
            "name"        => "Đã đối soát",
            "description" => "Đang đối soát",
            "action"      => "Забран",
        ],
        "602"   => [
            "name"        => "Đối soát không thành công",
            "description" => "Đang đối soát",
            "action"      => "Забран",
        ],
        "603"   => [
            "name"        => "Đã thanh toán",
            "description" => "Đã thanh toán",
            "action"      => "Забран",
        ],
        "604"   => [
            "name"        => "Chưa thanh toán",
            "description" => "Đã thanh toán",
            "action"      => "Забран",
        ],
        "605"   => [
            "name"        => "Đã tính cước",
            "description" => "Đã thanh toán",
            "action"      => "Забран",
        ],
        "607"   => [
            "name"        => "Đã tính cước và cước chuyển hoàn",
            "description" => "Đã thanh toán",
            "action"      => "Забран",
        ],
    ];

    public static function track()
    {
        $uri = self::API_URI . '/check/status/v1.0';

        $target = TargetConfig::where([
            ['alias', 'wefast'],
        ])->first();

        if ($target) {
            $logs = [];
            if ($target->integration_status == TargetConfig::INTEGRATION_INACTIVE) {
                echo "WeFast inactive\n";
            }
            $orders = Order::with('getTargetValue')->where([
                ['final_target', 0],
                ['target_approve', $target->id]
            ])->get();

            $senders = WeFastCounterparty::with('key')->get()->keyBy('id');
            $logs['count_orders'] = $orders->count();
            $arrayTracking = [];
            if ($orders->count()) {
                $apiSuccess = 0;
                $target1 = 0;
                $target2 = 0;
                $apiError = [];
                foreach ($orders as $order) {
                    echo $order->id . "\n";
                    if ($order->getTargetValue && isset($senders[$order->getTargetValue->sender_id]) && $senders[$order->getTargetValue->sender_id]->key) {
                        try {
                            $client = new GuzzleHttpClient();
                            $response = $client->request('POST', $uri, [
                                'json'    => [
                                    'tokenCode'   => $senders[$order->getTargetValue->sender_id]->key->token,
                                    'orderNumber' => $order->getTargetValue->track,
                                ],
                                'headers' => [
                                    'Content-Type' => 'application/json',
                                    'Accept'       => 'application/json'
                                ],
                            ]);
                            $result = json_decode($response->getBody()->getContents(), true);
                            if ($result['errorMessage'] == 'Success') {
                                $apiSuccess++;
                                $code = $result['data']['orderStatus'];
                                $arrayTracking[$order->id] = [
                                    'order_id' => $order->id,
                                    'target_id' => $target->id,
                                    'status_code' => $code,
                                    'status' => self::$statusList[$code]['name'] ?? '',
                                    'track' => $order->getTargetValue->track,
                                ];
                                switch ($code)
                                {
                                    case 501:
                                    case 600:
                                    case 601:
                                    case 602:
                                    case 603:
                                    case 604:
                                        // Забран [received] Выкуп
//                                        $order->final_target = 1;
//                                        if ($order->save()) {
//                                            (new OrdersLog())->addOrderLog($order->id, 'Цель("Выкуп") поставлена автоматически');
//                                            $target1++;
//                                        }
                                        break;

                                    case 504:
                                         // Возврат [return] не выкуп
//                                        $order->final_target = 2;
//                                        if ($order->save()) {
//                                            (new OrdersLog())->addOrderLog($order->id, 'Цель("Не выкуп") поставлена автоматически');
//                                            $target2++;
//                                        }
                                        break;


                                }
                            } else {
                                $apiError[$order->id] = $result;
                            }
                        } catch (RequestException $exception) {
                            echo $exception->getMessage();
                        }
                    } else {
                        $apiError[$order->id] = [
                            'targetValue' => isset($order->getTargetValue),
                            'sender_id'   => isset($order->getTargetValue) ? $order->getTargetValue->sender_id : false,
                            'sender'      => isset($senders[$order->getTargetValue->sender_id]),
                            'key'         => isset($senders[$order->getTargetValue->sender_id]->key)
                        ];
                    }
                }
                if ($arrayTracking) {
                    foreach ($arrayTracking as $tracking) {
                        Tracking::updateOrCreate($tracking, [
                            'updated_at' => now()
                        ]);
                    }
                }
                $logs['apiSuccess'] = $apiSuccess;
                $logs['target_1'] = $target1;
                $logs['target_2'] = $target2;
                $logs['apiError'] = $apiError;
            }

            echo json_encode($logs) . "\n";
        } else {

            echo "WeFast not founded\n";
        }
    }

    public static function renderView($params = [])
    {
        //костыль
        $target = TargetConfig::where('alias', 'wefast')
            ->first();
        $params = array_merge($params, [
            'target_config' => json_decode($target->options ?? '')
        ]);

        return view('integrations.wefast.index', $params);
    }

    public static function createDocument()
    {
        $request = request();

        $validator = \Validator::make($request->all(), [
            'sender' => 'required|exists:' . WeFastCounterparty::tableName() . ',id',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $order = Order::with('products:' . OrderProduct::tableName() . '.price,' . Product::tableName() . '.id,title,weight', 'getTargetValue')->findOrFail($request->order_id);
        $sender = WeFastCounterparty::with('key')->findOrFail($request->sender);
        $targetValue = json_decode($order->getTargetValue->values);
        $name = [
            'name_first'  => $order->name_first ?? '',
            'name_last'   => $order->name_last ?? '',
            'name_middle' => $order->name_middle ?? '',
        ];

        $data = [
            'additionalServices' => [
                'cod'       => TRUE,
                'freeFee'   => FALSE,
                'checking'  => TRUE,
                'insurance' => FALSE,
            ],
            'orderItems'         => self::getProductsArray($order),
            'tokenCode'          => $sender->key->token,
            'consigneeName'      => trim(implode(' ', $name)),
            'consigneePhone'     => $request->phone,
            'consigneeAddress'   => isset($targetValue->note->field_value) ? $targetValue->note->field_value : '',
            'districtCode'       => isset($targetValue->district->field_value) ? $targetValue->district->field_value : '',
            'provinceCode'       => isset($targetValue->region->field_value) ? $targetValue->region->field_value : '',
            'description'        => '',
            'sendOrder'          => true,
            'totalMoneyCollect'  => $order->price_total,
            'mainService'        => 'CODN',
            'trackingNumber'     => $order->id,
            'pickupBean'         => [
                'provinceCode'  => $sender->province_code,
                'districtCode'  => $sender->district_code,
                'wardCode'      => $sender->ward_code,
                'contactName'   => $sender->contact,
                'phone'         => $sender->phone,
                'address'       => $sender->address,
                'warehouseName' => $sender->warehouse,
            ]
        ];

        try {
            $client = new GuzzleHttpClient();
            $response = $client->request('POST', self::API_URI . '/create/v1.0', [
                'json'    => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json'
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $result = [
                'errorCode' => $e->getResponse()->getStatusCode(),
                'errorMessage' => $e->getMessage(),
                'data' => [
                    $e->getResponse()->getBody()->getContents()
                    ]
                ];
        }

        if (!$result['errorCode'] && $result['data']['orderNumber']) {
            $result['success'] = TargetValue::updateData($order->id, ['track' => $result['data']['orderNumber']]);
            self::updateTarget($order->id, $result['data']);
            $text = 'Track = ' . $result['data']['orderNumber'] . '<br>' . 'Total shipping = ' . $result['data']['totalPostage'];
            (new OrdersLog())->addOrderLog($order->id, $text);
        }

        return $result;
    }

    public static function editDocument()
    {
        // TODO: Implement editDocument() method.
    }

    public static function deleteDocument()
    {
        // TODO: Implement deleteDocument() method.
    }

    public static function editView(TargetConfig $integration)
    {
        return view('integrations.wefast.edit', [
            'integration' => $integration,
            'keys'        => WeFastKey::with('subProject')->checkSubProject()->get(),
        ]);
    }

    public static function otherFieldsView($params = [])
    {
        return view('integrations.wefast.otherField', $params);
    }

    private static function getProductsArray(Order $order)
    {
        $orderItems = [];
        if ($order->products) {
            $products = [];
            foreach ($order->products as $product) {
                if (isset($products[$product->id])) {
                    $products[$product->id]['price'] += +$product->price;
                    $products[$product->id]['weight'] += $product->weight ? $product->weight : 100;
                    $products[$product->id]['quantity']++;
                } else {
                    $products[$product->id] = [
                        'productName' => $product->title,
                        'price'       => +$product->price,
                        'quantity'    => 1,
                        'weight'      => $product->weight ? $product->weight : 100,
                    ];
                }
            }

            foreach ($products as $product) {
                $orderItems[] = $product;
            }
        }

        return $orderItems;
    }

    private static function updateTarget($orderId, $data)
    {
        $target = TargetValue::where('order_id', $orderId)->first();

        if ($target) {
            $option = json_decode($target->values, true);
            if (isset($option['track'])) {
                $option['track']['field_value'] = $data['orderNumber'];
            }
            if (isset($option['cost_actual'])) {
                $option['cost_actual']['field_value'] = $data['totalPostage'];
            }

            $target->values = json_encode($option);
            $target->sender_id = request()->sender ?? 0;
            $target->save();
        }
    }
}