<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.09.18
 * Time: 13:40
 */

namespace App\Models\Api\Posts;

use App\Http\Controllers\ActionController;
use App\Models\Api\CodeStatus;
use App\Models\Api\Measoft\MeasoftSender;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\OrdersPass;
use App\Models\Pass;
use App\Models\ProcStatus;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use App\Models\Tracking;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;

class Measoft extends AbstractPost
{
    private static $viewPath = 'integrations.measoft.';

    const API_PATH = 'https://home.courierexe.ru/api/';

    public static function getViewPath()
    {
        return static::$viewPath;
    }

    public static function track()
    {
        $measoft = TargetConfig::where('alias', 'measoft')->first();

        if (!$measoft) {
            echo date('Y-m-d H:i:s', time()) . " - Measoft not founded \n";
        }

        $statusReadySend = ProcStatus::where('action_alias', 'ready_send')->get();
        $allProcStatus = ProcStatus::all()->keyBy('id');

        $apiStatuses = CodeStatus::with('projectCodeStatus')->where('integration_id', $measoft->id)->get()->keyBy('status_code');
//todo доделать projectCodeStatus
        $orders = Order::with('getTargetValue', 'procStatus')
            ->moderated()
            ->targetApprove()
            ->withoutTargetFinal()
            ->where('moderation_time', ">=", '2018-08-01 00:00:00')
//            ->whereIn('proc_status', $statusReadySend->pluck('id'))
            ->whereHas('getTargetValue', function ($q) use ($measoft) {
                $q->where(function ($query) {
                    $query->whereNotNull('track')
                        ->where('track', '!=', 0)
                        ->where('track', '!=', '');
                })->where('target_id', $measoft->id)
                ->where('sender_id', '>', 0);
            })
            ->whereHas('procStatus', function ($query) {
                $query->whereNotIn('action', ['received', 'returned'])
                ->whereNotIn('proc_statuses.id', [22]);
            })
            ->get();

        if ($orders->count()) {
            echo date('Y-m-d H:i:s', time()) . ' orders count ' . $orders->count() . "\n";
            $ordersBySender = $orders->groupBy('getTargetValue.sender_id');
            $senders = MeasoftSender::whereIn('id', $ordersBySender->keys())->get()->keyBy('id');

            $orderPasses = [];
            $passStatus = null;
            foreach ($ordersBySender as $senderId => $orders) {
                if (isset($senders[$senderId])) {
                    foreach ($orders as $order) {
                        try {
                            echo date('Y-m-d H:i:s', time()) . ' - order id = ' . $order->id . "\n";
                            $track = $order->getTargetValue->track;
                            $xml = static::createTrackXml($track, $senders[$senderId]);
                            $response = static::send($xml);
                            $errors = static::getRequestErrors($response);

                            //обработка ошибок при создании трека
                            static::processingErrors($errors);

                            //было
//                            if (!isset($response->order->statushistory->status)) {
//                                echo date('Y-m-d H:i:s', time()) . ' - track = ' . $track . " response errors\n";
//                                continue;
//                            }

                            if (!isset($response->order->status[0])) {
                                echo date('Y-m-d H:i:s', time()) . ' - track = ' . $track . " response errors\n";
                                continue;
                            }
                            $comment = '';

                            if(isset($response->order->deliveredto ) && !empty((string)$response->order->deliveredto[0])){
                                $comment = (string)$response->order->deliveredto[0];
                            }
                            foreach ($response->order->status as $status) {
                                $apiStatus = $apiStatuses[(string)$status];

                                $attributes = $status->attributes();
                                $tracking = Tracking::where([
                                    ['order_id', $order->id],
                                    ['target_id', $measoft->id],
                                    ['status_code', (string)$status],
                                    ['track', $track],
                                    ['comment', $comment],
                                ])->first();
                                
                                //создание трекинга
                                if (!$tracking) {
                                    $tracking = new Tracking();
                                    $tracking->order_id = $order->id;
                                    $tracking->target_id = $measoft->id;
                                    $tracking->status_code = (string)$status;
                                    $tracking->status = $apiStatus->status ?? (string)$attributes->title;
                                    $tracking->track = $track;
                                    $tracking->comment = $comment ?? '';

                                    if ((string)$attributes->eventtime) {
                                        $tracking->status .= '<br>Event time - ' . $attributes->eventtime;
                                    }
                                    if ((string)$attributes->eventstore) {
                                        $tracking->status .= '<br>Event store - ' . $attributes->eventstore;
                                    }

                                    $tracking->save();
                                }

                                //изменения статуса
                                if ($apiStatus && $apiStatus->system_status_id && $order->proc_status != $apiStatus->system_status_id) {
                                    //добавдение заказов для проводки

                                    $newStatus = $allProcStatus[$apiStatus->system_status_id];

                                    if (isset($newStatus) && $newStatus->action == 'sent' && !$order->pass_send_id) {
                                        $orderPasses[$order->subproject_id][$order->id] = $order;
                                        $passStatus = $newStatus->id;
                                        continue;
                                    }

                                    $apiStatusInfo = [
                                        'status_id'   => $newStatus->id ?? 0,
                                        'status_name' => $newStatus->name ?? ' - ',
                                    ];
                                    $oldStatus = $order->procStatus;

                                    $order->proc_status = $apiStatus->system_status_id;
                                    $order->setTimeByStatus($newStatus->id ?? $newStatus);

                                    if ($order->save()) {
                                        (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус установлен системой(изменен c "' . $oldStatus->name . '" на "' . $apiStatusInfo['status_name'], $apiStatusInfo);
                                    }
                                }
                            }
                        } catch (\Exception $exception) {
                            echo date('Y-m-d H:i:s', time()) . ' - sender_id = ' . $senderId . ' ' . $exception->getMessage() . ' line ' . $exception->getLine() . "\n";
                        }
                    }
                }
            }

            //создние проводки
            $errorsPass = static::createSendPass($orderPasses, $passStatus);
            if ($errorsPass) {
                foreach ($errorsPass as $errorPass) {
                    echo date('Y-m-d H:i:s', time()) . $errorPass . "\n";
                }
            }
        } else {
            echo date('Y-m-d H:i:s', time()) . " - orders not founded \n";
        }
    }

    public static function renderView($params = [])
    {
        //костыль
        $target = TargetConfig::where('alias', 'measoft')
            ->first();
        $params = array_merge($params, [
            'target_config' => json_decode($target->options ?? '')
        ]);

        return view(static::$viewPath . 'index', $params);
    }

    public static function createDocument()
    {
        $request = request();
        $validator = \Validator::make($request->all(), [
            'sender' => 'required|exists:' . MeasoftSender::tableName() . ',id',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        TargetValue::updateData($request->order_id, [
            'sender_id' => $request->sender,
        ]);
        $order = Order::with('getTargetValue')->where('id', $request->order_id)->get();
        $result = [
            'success'  => false,
            'messages' => [],
        ];
        try {
            $xml = self::createXMLOrders($order, $request->sender);

            $response = static::send($xml);
            $result['messages'] = static::getRequestErrors($response);
            $attributes = $response->createorder->attributes() ?? [];
            $result['success'] = $attributes && $attributes['error'] == 0;

            if ($result['success']) {
                $order->first()->getTargetValue->setTrack($attributes['orderno']);
                $result['track'] = (string)$attributes['orderno'];
            }
        } catch (\Exception $exception) {
            $result['messages'][] = $exception->getMessage();
        }

        return $result;
    }

    public static function editDocument()
    {
        // TODO: Implement editDocument() method.
    }

    public static function deleteDocument()
    {
        $request = request();
        $result = [
            'success'  => false,
            'errors' => []
        ];

        try {
            $order = Order::where('id', $request->order_id)->get();
            $senderId = $order->first()->getTargetValue->sender_id ?? 0;
            $xml = static::cancelOrderXml($order, $senderId);

            $response = static::send($xml);
            $result['errors'] = static::getRequestErrors($response);
            $attributes = $response->order->attributes() ?? [];
            $result['success'] = $attributes && $attributes['error'] == 0;

            if ($result['success']) {
                $order->first()->getTargetValue->setTrack('');
            }
        } catch (\Exception $exception) {
            $result['errors'][] = $exception->getMessage();
        }

        return $result;
    }

    public static function editView(TargetConfig $integration)
    {
        return view(self::$viewPath . 'edit', [
            'integration' => $integration,
            'senders'     => MeasoftSender::with('subProject')
                ->checkSubProject()
                ->get(),
        ]);
    }

    public static function otherFieldsView($params = [])
    {
        return view(self::$viewPath . 'otherFields', $params);
    }

    /**
     * Подготавливаем данные для запроса
     */
    protected static function createXMLOrders($orders, $sender)
    {
        $level = 0;
        $result = '';

        if (!($sender instanceof MeasoftSender)) {
            $sender = MeasoftSender::find($sender);
        }

        if (count($orders) && $sender) {
            $result .= static::startXML();

            $result .= static::makeXMLNode('neworder', '', $level++, '', 1);
            $result .= static::makeXMLNode('auth', '', $level, 'extra="'.$sender->extra.'" login="'.$sender->login.'" pass="'.$sender->password.'"');
            foreach ($orders as $order) {
                if (is_int($order)) {
                    $order = Order::find($order);
                }
                if (!($order instanceof Order) || !$order) {
                    continue;
                }
                $targetValue = $order->getTargetValue;
                $target = json_decode($targetValue->values ?? '', true);

                $products = $order->products()->where('disabled', 0)->get();

                if (!$targetValue || !$target || $products->isEmpty()) {
                    continue;
                }

                $result .= static::makeXMLNode('order', '', $level++, 'orderno="'.$order->id.'"', 1);

                //receiver
                $person = trim(implode(' ', [$order->name_last, $order->name_first, $order->name_middle]));
                $date = date('Y-m-d', strtotime($target['date']['field_value'] ?? 0));
                $result .= static::makeXMLNode('receiver', '', $level++, '', 1);
                $result .= static::makeXMLNode('person', $person, $level);
                $result .= static::makeXMLNode('phone', $order->phone, $level);
                $result .= static::makeXMLNode('town', $target['city']['field_value'] ?? '-', $level);
                $result .= static::makeXMLNode('address', $target['street']['field_value'] ?? '-', $level);
                $result .= static::makeXMLNode('date', $date, $level);
                $result .= static::makeXMLNode('time_min', $target['time_min']['field_value'] ?? '-', $level);
                $result .= static::makeXMLNode('time_max', $target['time_max']['field_value'] ?? '-', $level);
                $result .= static::makeXMLNode('receiver', '', --$level, '', 2);

                $result .= static::makeXMLNode('return', 'NO', $level);
                $result .= static::makeXMLNode('return_service', 1, $level);
                $result .= static::makeXMLNode('weight', '0.1', $level);
                $result .= static::makeXMLNode('quantity', $products->count(), $level);
                $result .= static::makeXMLNode('service', 1, $level);
                $result .= static::makeXMLNode('type', 1, $level);
                $result .= static::makeXMLNode('paytype', 'CASH', $level);
                $result .= static::makeXMLNode('price', $order->price_total, $level);
                $result .= static::makeXMLNode('deliveryprice', (int)$target['cost']['field_value'] ?? '-', $level);
                $result .= static::makeXMLNode('discount', 0, $level);
                $result .= static::makeXMLNode('inshprice', $order->price_total, $level);
                $result .= static::makeXMLNode('enclosure', $target['note']['field_value'] ?? '-', $level);

                $result .= static::makeXMLNode('items', '', $level++, '', 1);
                foreach ($products as $product) {
                    $result .= static::makeXMLNode('item', $product->title, $level, 'quantity="1" retprice="'. $product->pivot->price.'" VATrate="0"');
                }
                $result .= static::makeXMLNode('items', '', --$level, '', 2);

                $result .= static::makeXMLNode('order', '', --$level, '', 2);
            }

            $result .= static::makeXMLNode('neworder', '', --$level, '', 2);
        }

        return $result;
    }

    protected static function startXML()
    {
        return ('<?xml version="1.0" encoding="UTF-8"?>');
    }

    protected static function stripTagsHTML($s)
    {
        $s = str_replace('&', '&amp;', $s);
        $s = str_replace("'", '&apos;', $s);
        $s = str_replace('<', '&lt;', $s);
        $s = str_replace('>', '&gt;', $s);
        $s = str_replace('"', '&quot;', $s);

        return $s;
    }

    protected static function makeXMLNode($nodename, $nodetext, $level = 0, $attr = '', $justopen = 0)
    {
        $result = "\r\n";
        for ($i = 0; $i < $level; $i++) $result .= '    ';

        $emptytag = ($nodetext === '') && ($justopen == 0);
        $nodetext = static::stripTagsHTML($nodetext);

        if ($justopen < 2) $result .= '<'.$nodename.($attr ? $attr = ' '.$attr : '').($emptytag ? ' /' : '').'>'.$nodetext;
        if ((($justopen == 0) && !$emptytag) || ($justopen == 2)) $result .= '</'.$nodename.'>';

        return ($result);
    }

    public static function findTown($word, $code = false)
    {
        $towns = [];

        if (trim($word)) {
            $xml = static::startXML();
            $xml .= static::makeXMLNode('townlist', '', 0, '', 1);

            if (trim($code)) {
                $xml .= static::makeXMLNode('codesearch', '', 1, '', 1);
                $xml .= static::makeXMLNode('code', trim($code),2);
                $xml .= static::makeXMLNode('codesearch', '', 1, '', 2);
            }

            $xml .= static::makeXMLNode('conditions', '', 1, '', 1);
            $xml .= static::makeXMLNode('namestarts', trim($word), 2);
            $xml .= static::makeXMLNode('country', 1, 2);
            $xml .= static::makeXMLNode('conditions', '', 1, '', 2);
            $xml .= static::makeXMLNode('limit', '', 1, '', 1);
            $xml .= static::makeXMLNode('limitcount', '5', 2);
            $xml .= static::makeXMLNode('limit', '', 1, '', 2);
            $xml .= static::makeXMLNode('townlist', '', 0, '', 2);

            $response = static::send($xml);

            if (!empty($response->town)) {
                foreach ($response->town as $town) {
                    $towns[] = [
                        'code'        => (int)$town->code,
                        'name'        => (string)$town->name,
                        'region'      => (string)$town->city->name,
                        'region_code' => (int)$town->city->code,
                    ];
                }
            }
        }

        return $towns;
    }

    public static function findStreet($word, $code)
    {
        $streets = [];

        if (trim($word) && trim($code)) {
            $xml = static::startXML();
            $xml .= static::makeXMLNode('streetlist', '', 0, '', 1);
            $xml .= static::makeXMLNode('conditions', '', 1, '', 1);
            $xml .= static::makeXMLNode('town', trim($code), 2);
            $xml .= static::makeXMLNode('namestarts', trim($word), 2);
            $xml .= static::makeXMLNode('conditions', '', 1, '', 2);
            $xml .= static::makeXMLNode('limit', '', 1, '', 1);
            $xml .= static::makeXMLNode('limitcount', '5', 2);
            $xml .= static::makeXMLNode('limit', '', 1, '', 2);
            $xml .= static::makeXMLNode('streetlist', '', 0, '', 2);

            $response = static::send($xml);

            if (!empty($response->street)) {
                foreach ($response->street as $street) {
                    $streets[] = [
                        'name'      => (string)$street->name,
                        'shortname' => (string)$street->shortname,
                        'typename'  => (string)$street->typename,
                    ];
                }
            }
        }

        return $streets;
    }

    /**
     * Проверяем ошибки возвращаемые АПИ
     */
    protected static function getRequestErrors($response, $array = true)
    {
        $errorsText = [
            'Ошибок нет',
            'Ошибка авторизации',
            'Отправлен пустой запрос',
            'Некорректно указана сумма заказа',
            'Некорректный общий вес заказа',
            'Не найден город получатель',
            'Не найден город отправитель',
            'Не заполнен адрес получателя',
            'Не заполнен телефон получателя',
            'Не заполнено контактное имя получателя',
            'Не заполнено название компании получателя',
            'Некорректная сумма объявленной ценности',
            'Артикул не найден',
            'Не заполнено название компании отправителя',
            'Не заполнено контактное имя отправителя',
            'Не заполнен телефон отправителя',
            'Не заполнен адрес отправителя',
            'Заказ с таким номером уже существует'
        ];
        $errors = [];

        if (!$response || !isset($response)) {
            $errors[] = 'Ответ не пришел';
            return $errors;
        }

        if ($attributes = $response->attributes()) {
            if (isset($attributes['count']) && $attributes['count'] == 0) {
                $errors[] = 'Заказ с таким номером не найден';
            }
            if (isset($attributes['error']) && $attributes['error'] > 0) {
                $errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : (string) $response;
            }
        }

        if (isset($response->createorder)) {
            foreach($response->createorder as $order) {
                if ($attributes = $order->attributes()) {
                    if (isset($attributes['error']) && $attributes['error'] > 0) {
                        $errors[(int)$attributes['orderno']] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : (string)$attributes['errormsg'];

                        if (!isset($errorsText[(int) $attributes['error']])) {
                            $errors[(int)$attributes['orderno']] = (string)$attributes['errormsgru'];
                        }
                    }
                }
            }
        }

        if (isset($response->error)) {
            foreach($response->error as $error) {
                if ($attributes = $error->attributes()) {
                    if (isset($attributes['error']) && $attributes['error'] > 0) {
                        $errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : (string)$attributes['errormsg'];

                        if (!isset($errorsText[(int) $attributes['error']])) {
                            $errors[] = (string)$attributes['errormsgru'];
                        }
                    }
                } else {
                    $errors[] = 'Ошибка синтаксиса XML: '.(string) $error;
                }
            }
        }

        if (isset($response->order)) {
            foreach($response->order as $error) {
                if ($attributes = $error->attributes()) {
                    if (isset($attributes['error']) && $attributes['error'] > 0) {
                        $errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : (string)$attributes['errormsg'];

                        if (!isset($errorsText[(int) $attributes['error']])) {
                            $errors[] = (string)$attributes['errormsgru'];
                        }
                    }
                } else {
                    $errors[] = 'Ошибка синтаксиса XML: '.(string) $error;
                }
            }
        }

        if ($errors && !$array) {
            $errors = implode(';<br>', $errors);
        }

        return $errors;
    }

    private static function send($xml)
    {
        $client = new GuzzleHttpClient();
        $response = $client->post( self::API_PATH,['body' => $xml]);

        return simplexml_load_string($response->getBody()->getContents());
    }

    protected static function cancelOrderXml($orders, $sender)
    {
        $xml = '';
        $level = 0;
        if (!($sender instanceof MeasoftSender)) {
            $sender = MeasoftSender::find($sender);
        }

        if (count($orders) && $sender) {
            $xml .= static::startXML();
            $xml .= static::makeXMLNode('cancelorder','', $level++, '', 1);
            $xml .= static::makeXMLNode('auth', '', $level, 'extra="'.$sender->extra.'" login="'.$sender->login.'" pass="'.$sender->password.'"');

            foreach ($orders as $order) {
                if (is_int($order)) {
                    $order = Order::find($order);
                }
                if (!($order instanceof Order) || !$order) {
                    continue;
                }
                $targetValue = $order->getTargetValue;

                if (!$targetValue) {
                    continue;
                }

                $xml .= static::makeXMLNode('order', '', $level, 'orderno="' . $targetValue->track . '"');
            }
            $xml .= static::makeXMLNode('cancelorder', '', --$level, '', 2);
        }

        return $xml;
    }

    public static function cronCreateOrders()
    {
        $measoft = TargetConfig::where('alias', 'measoft')->first();

        if (!$measoft) {
            echo date('Y-m-d H:i:s', time()) . " - Measoft not founded \n";
        }

        $orders = Order::with('getTargetValue')
            ->moderated()
            ->targetApprove()
            ->withoutTargetFinal()
            ->where('moderation_time', ">=", '2018-08-01 00:00:00')
            ->where('proc_status', 3)
            ->whereHas('getTargetValue', function ($q) use ($measoft) {
                $q->where(function ($query) {
                    $query->whereNull('track')
                        ->orWhere('track', 0)
                        ->orWhere('track', '');
                })->where('target_id', $measoft->id);
            })
            ->get();

        if ($orders->count()) {
            $errorStatus = ProcStatus::where('action_alias', 'api_errors')->first();

            echo date('Y-m-d H:i:s', time()) . " - count orders " . $orders->count() . "\n";

            $ordersBySender = $orders->groupBy('getTargetValue.sender_id');

            if (!empty($ordersBySender[0])) { //выбор отправителя, тем заказам у которых его нет
                echo date('Y-m-d H:i:s', time()) . " - " . count($ordersBySender[0]) . " orders without sender\n";

                $subProjectIds = $ordersBySender[0]->pluck('subproject_id');
                $errorsAutoSend = self::autoSenders($ordersBySender[0], $subProjectIds, $errorStatus);
                if ($errorsAutoSend) {
                    foreach ($errorsAutoSend as $id => $message) {
                        echo date('Y-m-d H:i:s', time()) . ' ERROR: (order_id = ' . $id . ') ' . $message . "\n";
                    }
                }

                $ordersBySender->forget(0);// удаление заказов без отправтеля из массива
            }

            $senders = MeasoftSender::whereIn('id', $ordersBySender->keys())->get()->keyBy('id');

            foreach ($ordersBySender as $senderId => $orders) {
                echo date('Y-m-d H:i:s', time()) . ' sender_id = ' . $senderId . ' - count orders = ' . count($orders) . "\n";
                try {
                    if (isset($senders[$senderId])) {
                        $xml = static::createXMLOrders($orders, $senders[$senderId]);

                        $orders = $orders->keyBy('id');
                        $response = static::send($xml);
                        $errors = static::getRequestErrors($response);

                        //обработка ошибок при создании трека
                        echo date('Y-m-d H:i:s', time()) . ' sender_id = ' . $senderId . ' - count errors orders = ' . count($errors) . "\n";
                        static::processingErrors($errors, $errorStatus);

                        //обработка успешных заказов
                        if (isset($response->createorder)) {
                            $successOrders = [];
                            foreach ($response->createorder as $orderApi) {
                                try {
                                    $attributes = $orderApi->attributes();
                                    if ($attributes && (int)$attributes['error'] == 0 && (int)$attributes['orderno']) {
                                        $order = $orders[(int)$attributes['orderno']] ?? Order::find((int)$attributes['orderno']);
                                        if (static::setTrack($order, (string)$attributes['orderno'])) {
                                            $successOrders[] = $order->id;
                                        }
                                    }
                                } catch (\Exception $exception) {
                                    echo date('Y-m-d H:i:s', time()) . " track error " . $exception->getMessage() . ' line ' . $exception->getLine() .  "\n";
                                    continue;
                                }
                            }
                            echo date('Y-m-d H:i:s', time()) . ' sender_id = ' . $senderId . ' - count success orders = ' . count($successOrders) . "\n";
                        }
                    }
                } catch (\Exception $exception) {
                    echo date('Y-m-d H:i:s', time()) . " sender_id = " . $senderId . " " . $exception->getMessage() . ' line ' . $exception->getLine() . "\n";
                }
            }
        } else {
            echo date('Y-m-d H:i:s', time()) . " - Orders not founded\n";
        }
    }

    protected static function autoSenders($orders, $subProjectIds, $updateErrorStatus = false)
    {
        $errors = [];
        $measoftSenders = MeasoftSender::whereIn('sub_project_id', $subProjectIds)->get()->groupBy('sub_project_id');

        foreach ($orders as $order) {
            try {
                $subProjectId = $order->subproject_id;
                if (!empty($measoftSenders[$subProjectId]) && count($measoftSenders[$subProjectId]) == 1) {
                    $sender = $measoftSenders[$subProjectId]->first();

                    $order->getTargetValue->sender_id = $sender->id;
                    $order->getTargetValue->save();

                    (new OrdersLog())->addOrderLog($order->id, 'Отправитель "' . $sender->name . '" был выбран системой');

                } elseif (empty($measoftSenders[$subProjectId])) {
                    (new OrdersLog())->addOrderLog($order->id, 'Отправитель не найден');

                    if ($updateErrorStatus) {
                        ActionController::updateOrderProcStatus([$order->id], $updateErrorStatus);
                    }
                } elseif (count($measoftSenders[$subProjectId]) > 1) {
                    (new OrdersLog())->addOrderLog($order->id, count($measoftSenders[$subProjectId]) . ' - кол-во отправителей.Отправитель не выбран системой');

                    if ($updateErrorStatus) {
                        ActionController::updateOrderProcStatus([$order->id], $updateErrorStatus);
                    }
                }
            } catch (\Exception $exception) {
                $errors[$order->id] = $exception->getMessage();
                continue;
            }
        }

        return $errors;
    }

    private static function processingErrors($errors, $errorStatus = false)
    {
        if ($errors) {
            foreach ($errors as $orderId => $message) {
                try {
                    if ($errorStatus) {
                        ActionController::updateOrderProcStatus([$orderId], $errorStatus);
                    }
                    (new OrdersLog())->addOrderLog($orderId, $message);
                } catch (\Exception $exception) {
                    echo date('Y-m-d H:i:s', time()) . " request errors order_id = " . $orderId . " - " . $message . "\n";
                    echo date('Y-m-d H:i:s', time()) . $exception->getMessage() . ' line ' . $exception->getLine() .  "\n";
                    continue;
                }
            }
        }
    }

    private static function setTrack($order, $track)
    {
        $targetValues = $order->getTargetValue;
        $targetValues->setData(['track' => $track]);
        $result = $targetValues->save();

        $procStatus = ProcStatus::where('action_alias', 'ready_send')
            ->where(function ($q) use ($order) {
                $q->where('project_id', 0)
                    ->orWhere('project_id', $order->project_id);
            })->first();//статус "готов к отпарвке"
        if ($procStatus) {
            ActionController::updateOrderProcStatus([$order->id], $procStatus);
        }

        return $result;
    }

    protected static function createTrackXml($orderNo, $sender)
    {
        $level = 0;
        $result = '';

        if (!($sender instanceof MeasoftSender)) {
            $sender = MeasoftSender::find($sender);
        }

        if ($orderNo && $sender) {
            $result .= static::startXML();

            $result .= static::makeXMLNode('statusreq', '', $level++, '', 1);
            $result .= static::makeXMLNode('auth', '', $level, 'extra="' . $sender->extra . '" login="' . $sender->login . '" pass="' . $sender->password . '"');
            $result .= static::makeXMLNode('orderno', $orderNo, $level);
            $result .= static::makeXMLNode('statusreq', '', --$level, '', 2);
        }

        return $result;
    }

    //todo перенести в pass
    protected static function createSendPass($orderPasses, $passStatus)
    {
        $errors = [];

        if ($orderPasses) {
            foreach ($orderPasses as $subProjectId => $orders) {
                try {
                    $pass = new Pass();
                    $pass->active = 0;
                    $pass->type = Pass::TYPE_SENDING;
                    $pass->user_id = 0;
                    $pass->sub_project_id = $subProjectId;
                    $pass->comment = 'Автоматическая проводка';
                    $pass->save();

                    $insertOrderPass = [];
                    $ids = [];
                    foreach ($orders as $order) {
                        $insertOrderPass[] = [
                            'pass_id'    => $pass->id,
                            'order_id'   => $order->id,
                            'track'      => $order->getTargetValue->track ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $ids[] = $order->id;
                    }

                    OrdersPass::insert($insertOrderPass);
                    Order::whereIn('id', $ids)->update([
                        'pass_send_id' => $pass->id,
                    ]);
                    $text = 'Заказ добавлен в проводку <a href="' . route('pass-one', $pass->id) . '">' . $pass->id . '</a> системой';
                    OrdersLog::addOrdersLog($ids, $text);

                    ActionController::runSentAction([
                        'orders' => $ids,
                        'status' => $passStatus ?? ProcStatus::where('action', 'sent')->first()
                    ]);
                } catch (\Exception $exception) {
                    $errors[] = $subProjectId . ' : ' . $exception->getMessage() . ' line - ' . $exception->getLine();
                }
            }
        }

        return $errors;
    }
}