<?php

namespace App\Models\Api\Posts;

use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\TargetConfig;
use App\Models\Api\Kazpost\KazpostSender;
use App\Models\TargetValue;
use App\Models\Tracking;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as GuzzleHttpClient;

class Kazpost extends AbstractPost
{
    const PRINT_NOTES = true;
    const PRINT_MARKINGS = false;
    const PRINT_MARKINGS_ZEBRA = false;
    const PRINT_REGISTRY = true;

    const TRACKING = true;

    public static $status_codes = array(
        'warehouse' => array(
            'DLV'       => 1,
            'DLV_POBOX' => 1,
            // 'TRNRPO' => 1,
            'STR'       => 1,
        ),
        'received'  => array(
            'DPAY'     => 1,
            'ISSPAY'   => 1,
            'ISSSC'    => 1,
            'NONDLV_Z' => 1,
        ),
        'return'    => array(
            'RET'      => 1,
            'RETSC'    => 1,
            'RETSCSTR' => 1,
        ),
    );

    public static function track()
    {
        echo "\n\n";
        echo 'time = ' . date('H:i:s d/m/Y', time()) . "\n";
        $target = TargetConfig::where([
            ['alias', 'kazpost'],
        ])->first();

        if ($target) {
//            if ($target->integration_status == TargetConfig::INTEGRATION_INACTIVE) {
//                echo $target->name . " inactive\n";todo нужна ли проверка?
//            }
            $orders = Order::with('getTargetValue')
                ->moderated()
                ->where([
                    ['target_status', 1],
                    ['final_target', 0],
                    ['target_approve', $target->id]
                ])->get();
            echo 'countOrder = ' . $orders->count() . "\n";
            if ($orders->count()) {
                foreach ($orders as $order) {
                    echo "orderId = " . $order->id . "\n";
                    $targetValue = $order->getTargetValue;
                    if (!empty($targetValue) && $targetValue->track) {
                        echo "track = " . $targetValue->track . "\n";
                        try {
                            $client = new GuzzleHttpClient();
                            $response = $client->request('GET', 'http://track.kazpost.kz/api/v2/' . $targetValue->track . '/events');
                            $result = json_decode($response->getBody()->getContents(), true);
                            if (isset($result['error'])) {
                                echo $result['error'] . "\n";
                                continue;
                            }
                            self::processingEvents($result['events'], $order->id, $target->id, $targetValue->track);
                        } catch (RequestException $exception) {
                            echo "request error \n";
                            echo $exception->getMessage();
                            continue;
                        }
                    }
                }
            }
        }
    }

    public static function renderView($params = [])
    {
        // TODO: Implement renderView() method.
    }

    public static function createDocument()
    {
        $request = request();
        $validator = \Validator::make($request->all(), [
            'sender'              => 'required|exists:' . KazpostSender::tableName() . ',id',
//            'track'               => 'required',
            'approve.cost'        => 'nullable|numeric',
            'approve.cost_actual' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $validator;
        }
        try{
            $res = true;
            TargetValue::updateData($request->order_id, [
                'track'     => $request->approve['track'],
                'sender_id' => $request->sender,
            ]);
        }catch (\Exception $e) {
            $res = false;
        }


        return [
            'success' => $res,
            'orderId' => $request->order_id,
        ];
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
        return view('integrations.kazpost.edit', [
            'integration' => $integration,
            'senders'     => KazpostSender::with('subProject')
                ->checkSubProject()
                ->get()
        ]);
    }

    public static function otherFieldsView($params = [])
    {
        return view('integrations.kazpost.otherFields', $params);
    }

    public static function createSticker2(Order $order)
    {
        $image = self::createImagePngSticker2($order);
        // return
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }

    //create multiple stickers to print
    public static function createStickers2(\Illuminate\Database\Eloquent\Collection $orders)
    {
        $images = [];
        foreach ($orders as $order) {
//            $images[] = self::createImagePngSticker2($order);
            $images[] = self::createImagePngBlank($order);
        }
        if (count($images)) {
            foreach ($images as $image) {
                ob_start();
                imagepng($image);
                $imagedata = ob_get_contents();
                ob_end_clean();
                echo
                '<style>
                .line {
                text-align: center; /* Выравниваем текст по центру */
                border-top: 1px dashed #000; /* Параметры линии  */
                height: 18px; /* Высота блока */
                background: url("/img/scissors.png") no-repeat 10px -18px; /* Параметры фона */
                }

                .line:after {
                content: "Линия отреза";
                font-family: Arial, sans-serif; /* Рубленый шрифт для надписи */
                font-size: 12px; /* Размер шрифта */
                vertical-align: top; /* Выравнивание по верхнему краю */
                }
                </style>';
                echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '"/>';
                echo ' <div class="line"></div>';
            }
        } else {
            abort(404);
        }
    }

    public static function createBlank(Order $order)
    {
        $image = self::createImagePngBlank($order);
        // return
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }

    private static function processingEvents($events, $orderId, $targetId, $track)
    {
        if (count($events)) {
            do {
                self::processingDay(array_pop($events), $orderId, $targetId, $track);
            } while (count($events));
        }
    }

    private static function processingDay($day, $orderId, $targetId, $track)
    {
        if (count($day['activity'])) {
            echo 'day = ' . $day['date'] . "\n";
            do {
                $event = array_pop($day['activity']);
                $text = 'Time : ' . $event['time'] . ' ' . $day['date'] . '<br>';
                if (isset($event['status'][0])) {
                    $status = self::status_mappings($event['status'][0]);
                    $text .= 'Status : ' . ($status ? $status['title_ru'] : $event['status'][0]) . '<br>';
                    $text .= 'City : ' . $event['city'] . '<br>';
                    $text .= 'Name : ' . $event['name'] . '<br>';
                    self::setTarget($event['status'][0], $orderId);
                }

                Tracking::updateOrCreate([
                    'order_id'    => $orderId,
                    'target_id'   => $targetId,
                    'status_code' => $event['status'][0] ?? 'undefined',
                    'status'      => $text,
                    'track'       => $track
                ], [
                    'updated_at' => now(),
                ]);


            } while (count($day['activity']));
        }
    }

    private static function setTarget($status, $orderId)
    {
        echo 'status = ' . $status . "\n";
        $updateData = [];
        $text = '';
        switch ($status) {
            case isset(self::$status_codes['received'][$status]) : { //выкуп
//                $updateData = [
//                    'final_target' => 1
//                ];
//                $text = 'Цель("Выкуп") поставлена автоматически';
                break;
            }
            case isset(self::$status_codes['return'][$status]) : { // не выкуп
//                $updateData = [
//                    'final_target' => 2
//                ];
//                $text = 'Цель("Не выкуп") поставлена автоматически';
                break;
            }
        }

        if ($updateData) {
            $res = Order::where([
                ['id', $orderId],
                ['target_status', 1],
                ['final_target', 0],
                ['moderation_id', '>', 0]
            ])->update($updateData);
            if ($res) {
                echo 'final_target = ' . $updateData['final_target'] . " (updated)\n";
                (new OrdersLog())->addOrderLog($orderId, $text);
            }
        }
    }

    public static function status_mappings($code)
    {

        $status_mappings = <<<EOD
{
	"BOXISS_UNDO":{"title_kz":"Отмена вручения из а-я","title_ru":"Отмена вручения из а-я","onsite":1,"group":"SRTR"},
	"AVIASND":{"title_kz":"Отправка на рейс","title_ru":"Отправка на рейс","onsite":1,"group":"SRTR"},
	"AVIASND_UNDO":{"title_kz":"Отмена отправки на рейс","title_ru":"Отмена отправки на рейс","onsite":1,"group":"SRTR"},
	"BAT":{"title_kz":"Партионный прием","title_ru":"Партионный прием","onsite":1,"group":"SRTR"},
	"CUSTOM_RET":{"title_kz":"Возврат с таможни","title_ru":"Возврат с таможни","onsite":1,"group":"SRTR"},
	"CUSTSRT_SND":{"title_kz":"Выпуск с таможни(с хранения)","title_ru":"Выпуск с таможни(с хранения)","onsite":1,"group":"SRTR"},
	"CUSTSTR_RET":{"title_kz":"Возврат с таможни (с хранения)","title_ru":"Возврат с таможни (с хранения)","onsite":1,"group":"SRTR"},
	"DELAY_RET":{"title_kz":"Возврат с таможни","title_ru":"Возврат с таможни","onsite":1,"group":"SRTR"},
	"DLV":{"title_kz":"Выдача на доставку","title_ru":"Ожидает клиента","onsite":1,"group":"SRTR"},
	"DLV_POBOX":{"title_kz":"Доставка в а/я","title_ru":"Доставка в а/я","onsite":1,"group":"SRTR"},
	"DPAY":{"title_kz":"Вручено","title_ru":"Вручено","onsite":1,"group":"SRTR"},
	"ISSPAY":{"title_kz":"Вручено","title_ru":"Вручено","onsite":1,"group":"SRTR"},
	"ISSSC":{"title_kz":"Вручено","title_ru":"Вручено","onsite":1,"group":"SRTR"},
	"NON":{"title_kz":"Не выдано","title_ru":"Не выдано","onsite":1,"group":"SRTR"},
	"NONDLV":{"title_kz":"Не доставлено","title_ru":"Не доставлено","onsite":1,"group":"SRTR"},
	"NONDLV_S":{"title_kz":"Возврат на хранение","title_ru":"Возврат на хранение","onsite":1,"group":"SRTR"},
	"NONDLV_Z":{"title_kz":"Возврат на хранение","title_ru":"Ожидает клиента, На хранение","onsite":1,"group":"SRTR"},
	"NON_S":{"title_kz":"Не выдано","title_ru":"Не выдано","onsite":1,"group":"SRTR"},
	"PRC":{"title_kz":"Поступление","title_ru":"Поступление","onsite":1,"group":"SRTR"},
	"RCP":{"title_kz":"Прием 1","title_ru":"Прием 1","onsite":1,"group":"SRTR"},
	"RCPOPS":{"title_kz":"Прием","title_ru":"Прием","onsite":1,"group":"SRTR"},
	"RDR":{"title_kz":"Досыл","title_ru":"Досыл","onsite":1,"group":"SRTR"},
	"RDRSC":{"title_kz":"Досыл","title_ru":"Досыл","onsite":1,"group":"SRTR"},
	"RDRSCSTR":{"title_kz":"Досыл","title_ru":"Досыл","onsite":1,"group":"SRTR"},
	"RET":{"title_kz":"Возврат","title_ru":"Возврат","onsite":1,"group":"SRTR"},
	"RETSC":{"title_kz":"Возврат","title_ru":"Возврат","onsite":1,"group":"SRTR"},
	"RETSCSTR":{"title_kz":"Возврат","title_ru":"Возврат","onsite":1,"group":"SRTR"},
	"RPODELAY":{"title_kz":"Задержка на таможенном досмотре","title_ru":"Задержка на таможенном досмотре","onsite":1,"group":"SRTR"},
	"SND":{"title_kz":"Отправка","title_ru":"Отправка","onsite":1,"group":"SRTR"},
	"SNDDELAY":{"title_kz":"Выпуск задержанного  из  таможенного досмотра на возврат","title_ru":"Выпуск задержанного  из  таможенного досмотра на возврат","onsite":1,"group":"SRTR"},
	"SNDZONE":{"title_kz":"Поступление на участок сортировки","title_ru":"Поступление на участок сортировки","onsite":1,"group":"SRTR"},
	"SNDZONE_T":{"title_kz":"Выпущено таможней","title_ru":"Выпущено таможней","onsite":1,"group":"SRTR"},
	"SRTRPOREG":{"title_kz":"Сортировка","title_ru":"Сортировка","onsite":1,"group":"SRTR"},
	"SRTSND":{"title_kz":"Отправка транспорта из сортцентра","title_ru":"Отправка из участка сортировки","onsite":1,"group":"SRTR"},
	"SRTSNDB":{"title_kz":"Отправка из СЦ","title_ru":"Отправка из СЦ","onsite":1,"group":"SRTR"},
	"SRTSNDB_UNDO":{"title_kz":"Отмена отправки","title_ru":"Отмена отправки","onsite":1,"group":"SRTR"},
	"SRTSNDIM":{"title_kz":"Отправка из СЦ","title_ru":"Отправка из СЦ","onsite":1,"group":"SRTR"},
	"SRTSNDIM_UNDO":{"title_kz":"Отмена отправки","title_ru":"Отмена отправки","onsite":1,"group":"SRTR"},
	"SRTSND_UNDO":{"title_kz":"Отмена отправки транспорта","title_ru":"Отмена отправки транспорта","onsite":1,"group":"SRTR"},
	"SRT_CUSTOM":{"title_kz":"Передано таможне","title_ru":"Передано таможне","onsite":1,"group":"SRTR"},
	"STR":{"title_kz":"Хранение","title_ru":"Хранение","onsite":1,"group":"SRTR"},
	"STRSC":{"title_kz":"Возврат с хранения","title_ru":"Возврат с хранения","onsite":1,"group":"SRTR"},
	"TRNRPO":{"title_kz":"Прибытие","title_ru":"Прибытие","onsite":1,"group":"SRTR"},
	"TRNSRT":{"title_kz":"Прибытие транспорта в сортцентр","title_ru":"Прибытие транспорта в сортцентр","onsite":1,"group":"SRTR"},

	"BOXISS":{"title_kz":"Вручение из а/я","title_ru":"Вручение из а/я","onsite":0,"group":"SRTR"},
	"DEL_STR":{"title_kz":"Удаление с хранения","title_ru":"Удаление с хранения","onsite":0,"group":"SRTR"},
	"DLV_POBOX_UNDO":{"title_kz":"Отмена доставки в а/я","title_ru":"Отмена доставки в а/я","onsite":0,"group":"SRTR"},
	"DLV_UNDO":{"title_kz":"Отмена доставки","title_ru":"Отмена доставки","onsite":0,"group":"SRTR"},
	"NONTRNOPS":{"title_kz":"Неприбытие","title_ru":"Неприбытие","onsite":0,"group":"SRTR"},
	"NONTRNSRT":{"title_kz":"Неприбытие","title_ru":"Неприбытие","onsite":0,"group":"SRTR"},
	"PRNNTC2_UNDO":{"title_kz":"Отмена  хранения","title_ru":"Отмена  хранения","onsite":0,"group":"SRTR"},
	"RDRSCSTR_UNDO":{"title_kz":"Отмена досыла с хранения в СЦ","title_ru":"Отмена досыла с хранения в СЦ","onsite":0,"group":"SRTR"},
	"RDRSC_UNDO":{"title_kz":"Отмена досыла в СЦ","title_ru":"Отмена досыла в СЦ","onsite":0,"group":"SRTR"},
	"RDR_UNDO":{"title_kz":"Отмена досыла","title_ru":"Отмена досыла","onsite":0,"group":"SRTR"},
	"REGPBT":{"title_kz":"Регистрация на участке","title_ru":"Регистрация на участке","onsite":0,"group":"SRTR"},
	"REGPBT_UNDO":{"title_kz":"Отмена регистрации","title_ru":"Отмена регистрации","onsite":0,"group":"SRTR"},
	"REGSRT":{"title_kz":"Регистрация на участке","title_ru":"Регистрация на участке","onsite":0,"group":"SRTR"},
	"REGSRT_UNDO":{"title_kz":"Отмена регистрации","title_ru":"Отмена регистрации","onsite":0,"group":"SRTR"},
	"RETSCSTR_UNDO":{"title_kz":"Отмена возврата с хранения в СЦ","title_ru":"Отмена возврата с хранения в СЦ","onsite":0,"group":"SRTR"},
	"RETSC_UNDO":{"title_kz":"Отмена возврата в СЦ","title_ru":"Отмена возврата в СЦ","onsite":0,"group":"SRTR"},
	"RET_UNDO":{"title_kz":"Отмена возврата","title_ru":"Отмена возврата","onsite":0,"group":"SRTR"},
	"RPODELAY_UNDO":{"title_kz":"Отмена задержки РПО","title_ru":"Отмена задержки РПО","onsite":0,"group":"SRTR"},
	"SNDDELAY_UNDO":{"title_kz":"Отмена выпуска задержанного","title_ru":"Отмена выпуска задержанного","onsite":0,"group":"SRTR"},
	"SNDZONE_T_UNDO":{"title_kz":"Отмена выпуска из участка ТК","title_ru":"Отмена выпуска из участка ТК","onsite":0,"group":"SRTR"},
	"SNDZONE_UNDO":{"title_kz":"Отмена передачи в зону сортировки","title_ru":"Отмена передачи в зону сортировки","onsite":0,"group":"SRTR"},
	"SRTRPOREG_UNDO":{"title_kz":"Отмена приписки к емкости (документу)","title_ru":"Отмена приписки к емкости (документу)","onsite":0,"group":"SRTR"},
	"SRT_CUSTOM_UNDO":{"title_kz":"Отмена передачи на таможенный контроль","title_ru":"Отмена передачи на таможенный контроль","onsite":0,"group":"SRTR"},
	"STRCUST":{"title_kz":"Передать на хранение","title_ru":"Передать на хранение","onsite":0,"group":"SRTR"},
	"STRCUST_UNDO":{"title_kz":"Отмена передачи на хранение","title_ru":"Отмена передачи на хранение","onsite":0,"group":"SRTR"},
	"TRN":{"title_kz":"Прибытие транспорта","title_ru":"Прибытие транспорта","onsite":0,"group":"SRTR"},
	"TRNBAG":{"title_kz":"Прибытие емкости","title_ru":"Прибытие емкости","onsite":0,"group":"SRTR"},
	"TRNSRT_UNDO":{"title_kz":"Отмена прибытия","title_ru":"Отмена прибытия","onsite":0,"group":"SRTR"},
	"TRN_UNDO":{"title_kz":"Отмена прибытия транспорта","title_ru":"Отмена прибытия транспорта","onsite":0,"group":"SRTR"},
	"CORRECT":{"title_kz":"Операция корректировки CORRECT","title_ru":"Корректировка данных отправления", "onsite":0,"group":"SRTR"},
	"EME":{"title_kz":"Отправление задержано на таможне", "title_ru":"Отправление задержано на таможне"},
	"EDA":{"title_kz":"Находится на входящем участке обмена", "title_ru":" Находится на входящем участке обмена"},
	"EDB":{"title_kz":"Отправление предъявлено таможне", "title_ru":"Отправление предъявлено таможне"},
	"EDC":{"title_kz":"Отправление возвращено из таможни", "title_ru":"Отправление возвращено из таможни"},
	"EDD":{"title_kz":"Отправление поступило в промежуточный сортировочный центр", "title_ru":"Отправление поступило в промежуточный сортировочный центр"},
	"EDE":{"title_kz":"Отправление покинуло промежуточный сортировочный центр", "title_ru":"Отправление покинуло промежуточный сортировочный центр"},
	"EDF":{"title_kz":"Отправление в пункте доставки на хранении", "title_ru":"Отправление в пункте доставки на хранении"},
	"EDG":{"title_kz":"Отправление передано почтальону/курьеру на доставку", "title_ru":"Отправление передано почтальону/курьеру на доставку"},
	"EDH":{"title_kz":"Отправление поступило в пункт самовывоза", "title_ru":"Отправление поступило в пункт самовывоза"},
	"EDX":{"title_kz":"Отправление задержано контролирующими органами", "title_ru":"Отправление задержано контролирующими органами"},
	"EMA":{"title_kz":"Прием отправления", "title_ru":"Прием отправления"},
	"EMB":{"title_kz":"Отправление прибыло в промежуточный пункт обмена", "title_ru":"Отправление прибыло в промежуточный пункт обмена"},
	"EMC":{"title_kz":"Отправление покинуло промежуточный сортцентр", "title_ru":"Отправление покинуло промежуточный сортцентр"},
	"EMD":{"title_kz":"Отправление прибыло в промежуточный пункт обмена", "title_ru":"Отправление прибыло в промежуточный пункт обмена"},
	"EMF":{"title_kz":"Отправление покинуло пункт обмена в стране получателя", "title_ru":"Отправление покинуло пункт обмена в стране получателя"},
	"EMG":{"title_kz":"Отправление прибыло в пункт выдачи", "title_ru":"Отправление прибыло в пункт выдачи"},
	"EMH":{"title_kz":"Доставка отправления почтальоном/курьером не состоялась", "title_ru":"Доставка отправления почтальоном/курьером не состоялась"},
	"EMI":{"title_kz":"Отправление успешно доставлено", "title_ru":"Отправление успешно доставлено"},
	"EMJ":{"title_kz":"Прибытие в транзитный пункт обмена", "title_ru":"Прибытие в транзитный пункт обмена"},
	"EXA":{"title_kz":"Отправление передано на таможню страны отправителя", "title_ru":"Отправление передано на таможню страны отправителя"},
	"EXB":{"title_kz":"Отправление получено таможней страны отправителя", "title_ru":"Отправление получено таможней страны отправителя"},
	"EXC":{"title_kz":"Отправление успешно прошло таможенный контроль", "title_ru":"Отправление успешно прошло таможенный контроль"},
	"EXD":{"title_kz":"Отправление задержано в пункте обмена", "title_ru":"Отправление задержано в пункте обмена"},
	"EXX":{"title_kz":"Отправка исходящего отправления отменена", "title_ru":"Отправка исходящего отправления отменена"},
	"TRNPST":{"title_kz":"Прибытие в постамат","title_ru": "Прибытие в постамат","onsite":1,"group":"SRTR"},
	"STRPST":{"title_kz":"Хранение в постамате","title_ru": "Хранение в постамате","onsite":1,"group":"SRTR"},
	"RETPST":{"title_kz":"Выемка из постамата","title_ru": "Выемка из постамата","onsite":1,"group":"SRTR"},
	"SND":{"title_kz":"Отправка из постамата","title_ru": "Отправка из постамата","onsite":1,"group":"SRTR"},
	"TRANSITRCV":{"title_kz":"Прибытие в СЦ(транзит)", "title_ru":"Прибытие в СЦ(транзит)", "onsite":1,"group":"SRTR"},
	"TRANSITSND":{"title_kz":"Отправка из СЦ(транзит)", "title_ru":"Отправка из СЦ(транзит)", "onsite":1,"group":"SRTR"}	
}
EOD;

        $array = json_decode($status_mappings, true);

        return $array[$code] ?? [];
    }

    /**
     * @param Order $order
     * @return resource
     */
    public static function createImagePngSticker2(Order $order)
    {
        // blank
        $blankPath = storage_path('app') . '/post/kazpost/sticker2.png';
        $image = imagecreatefrompng($blankPath);

        // vars
        $color = imagecolorallocate($image, 255, 0, 0);
        $font = storage_path('app') . '/post/kazpost/arial.ttf';
        $fontSise = 12;
        $target = $order->getTargetValue;
        if (!$target) {
            abort(404);
        }
        $targetConfig = $target->getTargetConfig;
        if (!$targetConfig || $targetConfig->alias != 'kazpost') {
            abort(404);
        }
        $targetValue = json_decode($target->values, true);
        $sender = KazpostSender::findOrFail($target->sender_id);
        // order_total
        $order_total = $order->price_total;
        if (!empty($targetValue['cost']['field_value'])) {
            $order_total += $targetValue['cost']['field_value'];
        }

        // order_total / str
        $order_total_str = num2str($order_total, ' тенге.');

        /*left*/

        // sender
        ImageTTFText($image, $fontSise, 0, 110, 40, $color, $font, $sender->name_last);
        ImageTTFText($image, $fontSise, 0, 50, 70, $color, $font, $sender->name_fm);

        // sender_address
        $sender_address0 = $sender->city . ', ' . $sender->address;
        ImageTTFText($image, $fontSise, 0, 110, 105, $color, $font, $sender_address0);

        // sender_total0 / number
        ImageTTFText($image, $fontSise, 0, 625, 35, $color, $font, $order_total . ' тенге.');

        // sender_total0 / str
        ImageTTFText($image, 11, 0, 465, 68, $color, $font, $order_total_str);

        // sender_total1 / number
        ImageTTFText($image, $fontSise, 0, 625, 105, $color, $font, $order_total . ' тенге.');

        // sender_total1 / str
        ImageTTFText($image, 11, 0, 465, 137, $color, $font, $order_total_str);


        // payment_code
        if (!empty($sender->payment_code)) {
            ImageTTFText($image, $fontSise, 0, 150, 178, $color, $font, $sender->payment_code);
        }

        if (!empty($sender->support_phone)) {
            // support
            ImageTTFText($image, $fontSise, 0, 110, 255, $color, $font, 'Отдел продаж:');
            ImageTTFText($image, $fontSise, 0, 100, 275, $color, $font, 'тел. ' . $sender->support_phone);
        }

        /*right*/

        // recipient
        $recipient0 = $order->name_last;
        $recipient1 = $order->name_first;
        if (!empty($order->name_middle)) $recipient1 .= ' ' . $order->name_middle;
        ImageTTFText($image, $fontSise, 0, 510, 351, $color, $font, $recipient0);
        ImageTTFText($image, $fontSise, 0, 465, 385, $color, $font, $recipient1);

        // recipient_address0
        if (!empty($targetValue['region']['field_value'])) {
            ImageTTFText($image, $fontSise, 0, 510, 420, $color, $font, $targetValue['region']['field_value']);
        }

        // recipient_address1
        $recipient_address1 = $targetValue['city']['field_value'];
        ImageTTFText($image, $fontSise, 0, 465, 453, $color, $font, $recipient_address1);

        // recipient_address2
        $recipient_address2 = '';
        if (!empty($targetValue['district']['field_value'])) $recipient_address2 .= $targetValue['district']['field_value'] . ', ';
        $recipient_address2 .= $targetValue['street']['field_value'] . ', ' . $targetValue['house']['field_value'];
        if (!empty($targetValue['flat']['field_value'])) $recipient_address2 .= ', ' . $targetValue['flat']['field_value'];
        ImageTTFText($image, $fontSise, 0, 465, 486, $color, $font, $recipient_address2);

        // recipient_phone
        ImageTTFText($image, $fontSise, 0, 465, 523, $color, $font, $order->phone);

        // recipient_index
        ImageTTFText($image, $fontSise, 0, 630, 523, $color, $font, $targetValue['postal_code']['field_value']);

        // barcode
        $barcode = imagecreatefrompng(route('index') . '/barcode.php?text=' . $order->id . '&absolute=true');
        imagecopy($image, $barcode, 446, 215, 0, 0, imagesx($barcode), imagesy($barcode));
        ImageTTFText($image, $fontSise, 0, 455, 255, $color, $font, $order->id);
        return $image;
    }

    public static function createImagePngBlank(Order $order)
    {
        // blank
        $image = imagecreatefrompng(storage_path('app') . '/post/kazpost/blank.png');

        // vars
        $color = imagecolorallocate($image, 255, 0, 0);
        $font = storage_path('app') . '/post/kazpost/arial.ttf';
        $fontSise = 12;

        $target = $order->getTargetValue;
        if (!$target) {
            abort(404);
        }
        $targetConfig = $target->getTargetConfig;
        if (!$targetConfig || $targetConfig->alias != 'kazpost') {
            abort(404);
        }
        $targetValue = json_decode($target->values, true);
        $sender = KazpostSender::findOrFail($target->sender_id);

        // order_total
        $order_total = $order->price_total;
        if (!empty($targetValue['cost']['field_value'])) {
            $order_total += $targetValue['cost']['field_value'];
        }

        // order_total / str
        $order_total_str = num2str($order_total, 'n/a');

        // ----------------------------------------------------- //
        /*left*/
        // ----------------------------------------------------- //

        // sender
        ImageTTFText($image, $fontSise, 0, 115, 250, $color, $font, $sender->name_last . ' ' . $sender->name_fm);

        // sender_address
        $sender_address0 = $sender->city;
        ImageTTFText($image, $fontSise, 0, 115, 300, $color, $font, $sender_address0);

        // support
        ImageTTFText($image, $fontSise, 0, 115, 318, $color, $font, $sender->address);

        // index
        if (!empty($sender->index))
        {
            ImageTTFText($image, 10, 0, 285, 338, $color, $font, $sender->index);
        }


        // ----------------------------------------------------- //
        /*right*/
        // ----------------------------------------------------- //


        // barcode
        $barcode = imagecreatefrompng(route('index') . '/barcode.php?text=' . $order->id . '&absolute=true');

        if (!empty($sender->support_phone)) {
            ImageTTFText($image, $fontSise, 0, 490, 380, $color, $font, 'Тех. поддержка: ' . $sender->support_phone);
        }
        imagecopy($image, $barcode, 530, 390, 0, 0, imagesx($barcode), imagesy($barcode));
        ImageTTFText($image, $fontSise, 0, 560, 430, $color, $font, $order->id);

        // document
        if (!empty($sender->document))
        {
            ImageTTFText($image, $fontSise, 0, 887, 108, $color, $font, $sender->document);
        }

        // payment_code
        if (!empty($sender->payment_code))
        {
            ImageTTFText($image, $fontSise, 0, 910, 205, $color, $font, $sender->payment_code);
        }

        // sender_total0 / number & str
        ImageTTFText($image, $fontSise, 0, 782, 235, $color, $font, $order_total . ' (' . $order_total_str . ')');

        // sender_total1 / number
        ImageTTFText($image, $fontSise, 0, 782, 265, $color, $font, $order_total . ' (' . $order_total_str . ')');


        // recipient
        $recipient0 = $order->name_last;
        $recipient1 = $order->name_first;
        if (!empty($order->name_middle)) $recipient1 .= ' ' . $order->name_middle;
        ImageTTFText($image, $fontSise, 0, 750, 575, $color, $font, $recipient0 . ' ' . $recipient1);

        // recipient_address0
        $recipient_address0 = '';
        if (!empty($targetValue['region']['field_value']))
        {
            // ImageTTFText($image, $fontSise, 0, 510, 420, $color, $font, $shipping['region']);
        }

        // recipient_address1
        $recipient_address1 = $targetValue['city']['field_value'];
        ImageTTFText($image, $fontSise, 0, 750, 626, $color, $font, $recipient_address1);

        // recipient_address2
        $recipient_address2 = '';
        if (!empty($targetValue['district']['field_value'])) $recipient_address2 .= $targetValue['district']['field_value'] . ', ';
        $recipient_address2 .= $targetValue['street']['field_value'] . ', ' . $targetValue['house']['field_value'];
        if (!empty($targetValue['flat']['field_value'])) $recipient_address2 .= ', ' . $targetValue['flat']['field_value'];
        ImageTTFText($image, $fontSise, 0, 750, 654, $color, $font, $recipient_address2);

        // recipient_phone
        ImageTTFText($image, $fontSise, 0, 750, 710, $color, $font, $order->phone);

        // recipient_index
        ImageTTFText($image, $fontSise, 0, 925, 750, $color, $font, $targetValue['postal_code']['field_value']);

        return $image;
    }
}