<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.06.18
 * Time: 17:16
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\KazpostSenderRequest;
use App\Http\Requests\RussianpostSenderRequest;
use App\Models\Api\Kazpost\KazpostSender;
use App\Models\Api\Posts\Kazpost;
use App\Models\Api\Posts\Russianpost;
use App\Models\Api\Russianpost\RussianpostSender;
use App\Models\Order;
use App\Models\OrdersLog;

class RussianpostController extends BaseController
{
    private $viewPath = 'integrations.russianpost.';

    public function addSenderAjax(RussianpostSenderRequest $request)
    {
        $sender = new RussianpostSender();
        $sender->setData($request);
        $res = $sender->save();

        return response()->json([
            'success' => $res,
            'html'    => view($this->viewPath . 'edit-table', [
                'senders' => RussianpostSender::with('subProject')
                    ->checkSubProject()
                    ->get(),
            ])->render(),
        ]);
    }

    public function editSender($id)
    {
        return view($this->viewPath . 'edit-sender', [
            'sender' => RussianpostSender::with(['target', 'subProject'])
                ->checkSubProject()
                ->findOrFail($id),
        ]);
    }

    public function editSenderAjax(RussianpostSenderRequest $request, $id)
    {
        $sender = RussianpostSender::findOrFail($id);
        $sender->setData($request);
        $res = $sender->save();

        return response()->json([
            'success' => $res,
        ]);
    }

    public function sticker2($orderId, $senderId)
    {
        try {
            $order = Order::moderated()->findOrFail($orderId);
            if ($order->getTargetValue->sender_id != $senderId && $senderId > 0) {
                $oldSenderId = $order->getTargetValue->sender_id;
                $order->getTargetValue->sender_id = $senderId;
                $order->getTargetValue->save();
                (new OrdersLog())->addOrderLog($order->id, 'Отправитель был изменен "[' . $oldSenderId . ',' . $senderId . ']"');
            }
            Russianpost::createSticker2($order);
        } catch (\Exception $exception) {
            return redirect()->back();
        }
    }

    public function stickers2($ordersIds)
    {
        if (!empty($ordersIds)) {
            $orders = Order::whereIn('id', explode(',', $ordersIds))->get();
            Russianpost::createStickers2($orders);
        } else {
            abort(404);
        }
    }

    public function blank_113($orderId, $senderId)
    {
        try {
            $order = Order::moderated()->findOrFail($orderId);
            if ($order->getTargetValue->sender_id != $senderId && $senderId > 0) {
                $oldSenderId = $order->getTargetValue->sender_id;
                $order->getTargetValue->sender_id = $senderId;
                $order->getTargetValue->save();
                (new OrdersLog())->addOrderLog($order->id, 'Отправитель был изменен "[' . $oldSenderId . ',' . $senderId . ']"');
            }
            Russianpost::createBlank113($order);
        } catch (\Exception $exception) {
            return redirect()->back();
        }
    }

    public function blank_107($orderId, $senderId)
    {
        try {
            $order = Order::moderated()->findOrFail($orderId);
            if ($order->getTargetValue->sender_id != $senderId && $senderId > 0) {
                $oldSenderId = $order->getTargetValue->sender_id;
                $order->getTargetValue->sender_id = $senderId;
                $order->getTargetValue->save();
                (new OrdersLog())->addOrderLog($order->id, 'Отправитель был изменен "[' . $oldSenderId . ',' . $senderId . ']"');
            }
            Russianpost::createBlank107($order);
        } catch (\Exception $exception) {
            return redirect()->back();
        }
    }

    public function blank_7($orderId, $senderId)
    {
        try {
            $order = Order::moderated()->findOrFail($orderId);
            if ($order->getTargetValue->sender_id != $senderId && $senderId > 0) {
                $oldSenderId = $order->getTargetValue->sender_id;
                $order->getTargetValue->sender_id = $senderId;
                $order->getTargetValue->save();
                (new OrdersLog())->addOrderLog($order->id, 'Отправитель был изменен "[' . $oldSenderId . ',' . $senderId . ']"');
            }
            Russianpost::createBlank7($order);
        } catch (\Exception $exception) {
            return redirect()->back();
        }
    }
}