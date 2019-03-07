<?php

namespace App\Models\Api\Posts;

use App\Models\Api\Russianpost\RussianpostSender;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\TargetConfig;
use App\Models\Api\Kazpost\KazpostSender;
use App\Models\TargetValue;
use App\Models\Tracking;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client as GuzzleHttpClient;

class Russianpost extends AbstractPost
{
    const PRINT_NOTES = true;
    const PRINT_MARKINGS = false;
    const PRINT_MARKINGS_ZEBRA = false;

    const TRACKING = false;

    public static function track()
    {
        // TODO: Implement track() method.
    }

    public static function renderView($params = [])
    {
        // TODO: Implement renderView() method.
    }

    public static function createDocument()
    {
        $request = request();
        $validator = \Validator::make($request->all(), [
            'sender'              => 'required|exists:' . RussianpostSender::tableName() . ',id',
            'approve.cost'        => 'nullable|numeric',
            'approve.cost_actual' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $res = true;


        try {
            TargetValue::updateData($request->order_id, [
                'track'     => $request->approve['track'],
                'sender_id' => $request->sender,
            ]);
        } catch (\Exception $exception) {
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
        return view('integrations.russianpost.edit', [
            'integration' => $integration,
            'senders'     => RussianpostSender::with('subProject')
                ->checkSubProject()
                ->get()
        ]);
    }

    public static function otherFieldsView($params = [])
    {
        return view('integrations.russianpost.otherFields', $params);
    }

    public static function createSticker2(Order $order)
    {
        $image = self::createImagePng($order);

        // return
//        header("Content-type: image/png");
//        imagepng($image);
//        imagedestroy($image);
        ob_start();
        imagepng($image);
        $imagedata = ob_get_contents();
        ob_end_clean();
        echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '"/>';
        echo '<style>
@media print {
  #printPageButton {
    display: none;
  }
}
</style> 
<div>
<button id="printPageButton" onclick="window.print();" style="padding: 15px 40px; position: fixed; right: 0; bottom:0">Отправить на печать</button>
</div>';

    }

    public static function createStickers2(\Illuminate\Database\Eloquent\Collection $orders)
    {
        $images = [];
        foreach ($orders as $order) {
            $images[] = self::createImagePng($order);
        }
        if (count($images)) {
            foreach ($images as $image) {
                ob_start();
                imagepng($image);
                $imagedata = ob_get_contents();
                ob_end_clean();
                echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '"/>';
                echo '<br>';
                echo '<br>';
                echo '<br>';
            }
        } else {
            abort(404);
        }
    }

    /**
     * @param Order $order
     * @return resource
     */
    public static function createImagePng($order)
    {
// blank
        $blankPath = storage_path('app') . '/post/russianpost/sticker2.png';
        $image = imagecreatefrompng($blankPath);

        // vars
        $color = imagecolorallocate($image, 255, 0, 0);
        $font = storage_path('app') . '/post/russianpost/arial.ttf';
        $fontSise = 12;
        $target = $order->getTargetValue;

        if (!$target) {
            abort(404);
        }

        $targetConfig = $target->getTargetConfig;
        if (!$targetConfig || $targetConfig->alias != 'russianpost') {
            abort(404);
        }

        $targetValue = json_decode($target->values, true);
        if (!RussianpostSender::find($target->sender_id)) {
            // sender not found
            ImageTTFText($image, $fontSise, 0, 110, 40, $color, $font, 'Отправитель');
            ImageTTFText($image, $fontSise, 0, 50, 70, $color, $font, implode(' ', ['не сохранен, создайте трек']));
            // barcode
            $barcode = imagecreatefrompng(route('index') . '/barcode.php?text=' . $order->id . '&absolute=true');
            imagecopy($image, $barcode, 446, 215, 0, 0, imagesx($barcode), imagesy($barcode));
            ImageTTFText($image, $fontSise, 0, 455, 255, $color, $font, $order->id);
            return $image;
        }
        $sender = RussianpostSender::findOrFail($target->sender_id);

        // order_total
        $order_total = $order->price_total;
        if (!empty($targetValue['cost']['field_value'])) {
            $order_total += (float)$targetValue['cost']['field_value'];
        }

        // order_total / str
        $order_total_str = num2str($order_total, ' руб.');

        /*left*/

        // sender
        ImageTTFText($image, $fontSise, 0, 110, 40, $color, $font, $sender->name_last);
        ImageTTFText($image, $fontSise, 0, 50, 70, $color, $font, implode(' ', [$sender->name_first, $sender->name_middle]));

        // sender_address
        $sender_address0 = $sender->city . ', ' . $sender->address . ', ' . $sender->index;
        ImageTTFText($image, $fontSise, 0, 110, 105, $color, $font, $sender_address0);

        // sender_total0 / number
        ImageTTFText($image, $fontSise, 0, 625, 35, $color, $font, $order_total . ' руб.');

        // sender_total0 / str
        ImageTTFText($image, 11, 0, 465, 68, $color, $font, $order_total_str);

        // sender_total1 / number
        ImageTTFText($image, $fontSise, 0, 625, 105, $color, $font, $order_total . ' руб.');

        // sender_total1 / str
        ImageTTFText($image, 11, 0, 465, 137, $color, $font, $order_total_str);


        // payment_code
        if (!empty($sender->payment_code)) {
            ImageTTFText($image, $fontSise, 0, 150, 178, $color, $font, $sender->payment_code);
        }
        /*right*/

        // recipient
        $recipient0 = $order->name_last;
        $recipient1 = $order->name_first;
        if (!empty($order->name_middle)) $recipient1 .= ' ' . $order->name_middle;
        ImageTTFText($image, $fontSise, 0, 510, 351, $color, $font, $recipient0);
        ImageTTFText($image, $fontSise, 0, 465, 385, $color, $font, $recipient1);


        $recipient_address0 = '';
        if (!empty($targetValue['region']['field_value'])) $recipient_address0 = $targetValue['region']['field_value'];
        ImageTTFText($image, $fontSise, 0, 510, 420, $color, $font, $recipient_address0);

        // recipient_address1
        $recipient_address1 = '';
        if (!empty($targetValue['district']['field_value'])) $recipient_address1 .= $targetValue['district']['field_value'] . ', ';
        $recipient_address1 .= $targetValue['locality']['field_value'];
        ImageTTFText($image, $fontSise, 0, 465, 453, $color, $font, $recipient_address1);

        // recipient_address2
        $recipient_address2 = $targetValue['street']['field_value'] . ', ' . $targetValue['house']['field_value'];
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

    public static function createBlank107($order)
    {
        $blankPath = storage_path('app') . '/post/russianpost/f107.png';
        $image = imagecreatefrompng($blankPath);

        // vars
        $color = imagecolorallocate($image, 255, 0, 0);
        $font = storage_path('app') . '/post/russianpost/arial.ttf';
        $fontSise = 10;

        $target = $order->getTargetValue;
        if (!$target) {
            abort(404);
        }
        $targetConfig = $target->getTargetConfig;
        if (!$targetConfig || $targetConfig->alias != 'russianpost') {
            abort(404);
        }
        $targetValue = json_decode($target->values, true);
        $sender = RussianpostSender::findOrFail($target->sender_id);
        // order_total
        $order_total = $order->price_total;
        if (!empty($targetValue['cost']['field_value'])) {
            $order_total += (float)$targetValue['cost']['field_value'];
        }

        // sender
        $sender = $order->name_last . ' ' . $order->name_first;
        if (!empty($order->name_middle)) $sender .= ' ' . $order->name_middle;
        ImageTTFText($image, $fontSise, 0, 84, 162, $color, $font, $sender);
        ImageTTFText($image, $fontSise, 0, 637, 162, $color, $font, $sender);

        // sender_address
        $address0 = $targetValue['postal_code']['field_value'] . ', ' . $targetValue['region']['field_value'] . ', ' . $targetValue['district']['field_value'] . ', ' . $targetValue['locality']['field_value'];
        $address1 = $targetValue['street']['field_value'] . ', ' . $targetValue['house']['field_value'];
        if (!empty($targetValue['flat'])) $address1 .= ', ' . $targetValue['flat']['field_value'];
        ImageTTFText($image, $fontSise, 0, 82, 193, $color, $font, $address0);
        ImageTTFText($image, $fontSise, 0, 635, 193, $color, $font, $address0);
        ImageTTFText($image, $fontSise, 0, 82, 225, $color, $font, $address1);
        ImageTTFText($image, $fontSise, 0, 635, 225, $color, $font, $address1);

        // products
        $top = 310;
        $count = 0;
        foreach ($order->products as $product) {
            if (!$product->pivot->disabled) {
                ImageTTFText($image, $fontSise, 0, 90, $top, $color, $font, $product->title);
                ImageTTFText($image, $fontSise, 0, 643, $top, $color, $font, $product->title);

                ImageTTFText($image, $fontSise, 0, 433, $top, $color, $font, 1);
                ImageTTFText($image, $fontSise, 0, 986, $top, $color, $font, 1);

                ImageTTFText($image, $fontSise, 0, 485, $top, $color, $font, abs($product->pivot->price));
                ImageTTFText($image, $fontSise, 0, 1038, $top, $color, $font, abs($product->pivot->price));
                $top += 32;
                $count++;
            }
        }

        // order_total
        $order_total = $count . ' предм., ' . $order->price_total . ' руб.';
        ImageTTFText($image, $fontSise, 0, 340, 606, $color, $font, $order_total);
        ImageTTFText($image, $fontSise, 0, 893, 606, $color, $font, $order_total);

        // barcode

        $barcode = imagecreatefrompng(route('index') . '/barcode.php?text=' . $order->id . '&absolute=true');
        imagecopy($image, $barcode, 600, 615, 0, 0, imagesx($barcode), imagesy($barcode));
        ImageTTFText($image, $fontSise, 0, 610, 650, $color, $font, $order->id);

        // return
//        header("Content-type: image/png");
//        imagepng($image);
//        imagedestroy($image);
        ob_start();
        imagepng($image);
        $imagedata = ob_get_contents();
        ob_end_clean();
        echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '"/>';
        echo '<style>
@media print {
  #printPageButton {
    display: none;
  }
}
</style> 
<div>
<button id="printPageButton" onclick="window.print();" style="padding: 15px 40px; position: fixed; right: 0; bottom:0">Отправить на печать</button>
</div>
';
    }

    public static function createBlank7($order)
    {
        $blankPath = storage_path('app') . '/post/russianpost/f7.png';

        $image = imagecreatefrompng($blankPath);
        // vars
        $color = imagecolorallocate($image, 255, 0, 0);
        $font = storage_path('app') . '/post/russianpost/arial.ttf';
        $fontSise = 12;
        $target = $order->getTargetValue;

        if (!$target) {
            abort(404);
        }
        $targetConfig = $target->getTargetConfig;
        if (!$targetConfig || $targetConfig->alias != 'russianpost') {
            abort(404);
        }

        $targetValue = json_decode($target->values, true);
        $sender = RussianpostSender::findOrFail($target->sender_id);

        /*left*/

        // orderId + barcode

        $barcode = imagecreatefrompng(route('index') . '/barcode.php?text=' . $order->id . '&absolute=true');
        imagecopy($image, $barcode, 250, 120, 0, 0, imagesx($barcode), imagesy($barcode));
        ImageTTFText($image, 17, 0, 267, 165, $color, $font, $order->id);

        // sender
        ImageTTFText($image, $fontSise, 0, 50, 200, $color, $font,  implode(' ', [$sender->name_last, $sender->name_first, $sender->name_middle]));

        // sender_address
        $sender_address = $sender->city . ', ' . $sender->address;

        ImageTTFText($image, $fontSise, 0, 110, 280, $color, $font, $sender_address);
        // recipient_index
        $left = 291;
        $recipient_index = str_split($sender->index);
        foreach ($recipient_index as $num) {
            ImageTTFText($image, $fontSise, 0, $left, 362, $color, $font, $num);
            $left += 21;
        }

        // order_total
        $order_total = $order->price_total;
        if (!empty($targetValue['cost']['field_value'])) {
            $order_total += (float)$targetValue['cost']['field_value'];
        }

        // order_total / str
        $order_total_str = num2str($order_total, ' руб.');
        // sender_total0 / number
        ImageTTFText($image, $fontSise, 0, 425, 175, $color, $font, $order_total . ' руб.');

        // sender_total0 / str
        ImageTTFText($image, 11, 0, 425, 195, $color, $font, $order_total_str);

        // sender_total1 / number
        ImageTTFText($image, $fontSise, 0, 425, 225, $color, $font, $order_total . ' руб.');

        // sender_total1 / str
        ImageTTFText($image, 11, 0, 425, 245, $color, $font, $order_total_str);

        // recipient
        ImageTTFText($image, $fontSise, 0, 480, 320, $color, $font,  implode(' ', [ $order->name_last, $order->name_first, $order->name_middle]));

        $recipient_address0 = '';
        if (!empty($targetValue['region']['field_value'])) $recipient_address0 = $targetValue['region']['field_value'];
        ImageTTFText($image, $fontSise, 0, 510, 400, $color, $font, $recipient_address0);

        // recipient_address1
        $recipient_address1 = '';
        if (!empty($targetValue['district']['field_value'])) $recipient_address1 .= $targetValue['district']['field_value'] . ', ';
        $recipient_address1 .= $targetValue['locality']['field_value'];
        ImageTTFText($image, $fontSise, 0, 465, 423, $color, $font, $recipient_address1);

        // recipient_address2
        $recipient_address2 = $targetValue['street']['field_value'] . ', ' . $targetValue['house']['field_value'];
        if (!empty($targetValue['flat']['field_value'])) $recipient_address2 .= ', ' . $targetValue['flat']['field_value'];
        ImageTTFText($image, $fontSise, 0, 465, 446, $color, $font, $recipient_address2);

        // recipient_index
        $left = 450;
        $recipientPhoneCode= str_split($order->phone);
        $recipientPhoneCodeArray1 = [$recipientPhoneCode[1],$recipientPhoneCode[2], $recipientPhoneCode[3] ];
        foreach ($recipientPhoneCodeArray1 as $num) {
            ImageTTFText($image, $fontSise, 0, $left, 485, $color, $font, $num);
            $left += 20;
        }
        $left2 = 516;
        $recipientPhoneCodeArray2= [$recipientPhoneCode[4],$recipientPhoneCode[5], $recipientPhoneCode[6], $recipientPhoneCode[7], $recipientPhoneCode[8], $recipientPhoneCode[9], $recipientPhoneCode[10]];
        foreach ($recipientPhoneCodeArray2 as $num) {
            ImageTTFText($image, $fontSise, 0, $left2, 485, $color, $font, $num);
            $left2 += 21;
        }

        // recipient_index
        $left = 684;
        $recipient_index = str_split($targetValue['postal_code']['field_value']);
        foreach ($recipient_index as $num) {
            ImageTTFText($image, $fontSise, 0, $left, 485, $color, $font, $num);
            $left += 21;
        }

        $products = $order->products;
        $productsStr = '';
        foreach ($products as $product){
            $productsStr .= $product->title . ',';
        }

        ImageTTFText($image, 10, 0, 45, 567, $color, $font, $productsStr);

        ob_start();
        imagepng($image);
        $imagedata = ob_get_contents();
        ob_end_clean();
        echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '"/>';
        echo '<style>
@media print {
  #printPageButton {
    display: none;
  }
}
</style> 
<div>
<button id="printPageButton" onclick="window.print();" style="padding: 15px 40px; position: fixed; right: 0; bottom:0">Отправить на печать</button>
</div>
';
    }

    public static function createBlank113($order)
    {
        $blankPath = storage_path('app') . '/post/russianpost/f113.png';
        $image = imagecreatefrompng($blankPath);

        // vars
        $color = imagecolorallocate($image, 255, 0, 0);
        $font = storage_path('app') . '/post/russianpost/arial.ttf';
        $fontSise = 10;

        $target = $order->getTargetValue;
        if (!$target) {
            abort(404);
        }
        $targetConfig = $target->getTargetConfig;
        if (!$targetConfig || $targetConfig->alias != 'russianpost') {
            abort(404);
        }
        $targetValue = json_decode($target->values, true);
        $sender = RussianpostSender::findOrFail($target->sender_id);
        // order_total
        $order_total = $order->price_total;
        if (!empty($targetValue['cost']['field_value'])) {
            $order_total += (float)$targetValue['cost']['field_value'];
        }

        $total = explode('.', $order_total);
        ImageTTFText($image, $fontSise, 0, 623, 210, $color, $font, $total[0]);
        ImageTTFText($image, $fontSise, 0, 695, 210, $color, $font, (!empty($total[1]) ? $total[1] : '00'));

        // order_total / str
        ImageTTFText($image, $fontSise, 0, 367, 226, $color, $font, num2str($order_total, ' руб.'));

        // recipient
        $recipient = implode(' ', [$sender->name_last, $sender->name_first, $sender->name_middle]);
        ImageTTFText($image, $fontSise, 0, 367, 250, $color, $font, $recipient);

        // recipient_address
        $recipient_address0 = $sender->city;
        $recipient_address1 = $sender->address;
        ImageTTFText($image, $fontSise, 0, 367, 285, $color, $font, $recipient_address0);
        ImageTTFText($image, $fontSise, 0, 367, 305, $color, $font, $recipient_address1);

        // recipient_index
        $left = 697;
        $recipient_index = str_split($sender->index);
        foreach ($recipient_index as $num) {
            ImageTTFText($image, $fontSise, 0, $left, 332, $color, $font, $num);
            $left += 10.5;
        }

        // sender
        $sender0 = $order->name_last;
        $sender1 = $order->name_first;
        if (!empty($order->name_middle)) $sender1 .= ' ' . $order->name_middle;
        ImageTTFText($image, $fontSise, 0, 392, 514, $color, $font, $sender0);
        ImageTTFText($image, $fontSise, 0, 367, 532, $color, $font, $sender1);

        // sender_address
        $sender_address0 = $targetValue['region']['field_value'] . '' . $targetValue['district']['field_value'] . ' ' . $targetValue['locality']['field_value'];
        $sender_address1 = $targetValue['street']['field_value'] . ' ' . $targetValue['house']['field_value'];
        if (!empty($targetValue['flat'])) $sender_address1 .= ' ' . $targetValue['flat']['field_value'];
        ImageTTFText($image, $fontSise, 0, 457, 550, $color, $font, $sender_address0);
        ImageTTFText($image, $fontSise, 0, 367, 570, $color, $font, $sender_address1);

        // sender_index
        $left = 698;
        $sender_index = str_split($targetValue['postal_code']['field_value']);
        foreach ($sender_index as $num) {
            ImageTTFText($image, $fontSise, 0, $left, 590, $color, $font, $num);
            $left += 10.5;
        }


        // return
//        header("Content-type: image/png");
//        imagepng($image);
//        imagedestroy($image);

        ob_start();
        imagepng($image);
        $imagedata = ob_get_contents();
        ob_end_clean();
        echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '"/>';
        echo '<style>
@media print {
  #printPageButton {
    display: none;
  }
}
</style> 
<div>
<button id="printPageButton" onclick="window.print();" style="padding: 15px 40px; position: fixed; right: 0; bottom:0">Отправить на печать</button>
</div>';
    }
}