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
use Illuminate\Http\Request;
use App\Models\Api\Kazpost\KazpostSender;
use App\Models\Api\Posts\Kazpost;
use App\Models\Order;
use App\Models\ProcStatus;
use App\Exports\PostRegistry\KzExport;
use Excel;

class KazpostController extends BaseController
{
    private $viewPath = 'integrations.kazpost.';

    public function addSenderAjax(KazpostSenderRequest $request)
    {
        $sender = new KazpostSender();
        $sender->setData($request);
        $res = $sender->save();

        return response()->json([
            'success' => $res,
            'html'    => view($this->viewPath . 'edit-table', [
                'senders' => KazpostSender::with('subProject')
                    ->checkSubProject()
                    ->get(),
            ])->render(),
        ]);
    }

    public function editSender($id)
    {
        return view($this->viewPath . 'edit-sender', [
            'sender' => KazpostSender::with(['target', 'subProject'])->checkSubProject()->findOrFail($id),
        ]);
    }

    public function editSenderAjax(KazpostSenderRequest $request, $id)
    {
        $sender = KazpostSender::findOrFail($id);
        $sender->setData($request);
        $res = $sender->save();

        return response()->json([
            'success' => $res,
        ]);
    }

    public function sticker2($orderId)
    {
        $order = Order::moderated()->findOrFail($orderId);
        Kazpost::createSticker2($order);
    }

    public function blank($orderId)
    {
        $order = Order::moderated()->findOrFail($orderId);
        Kazpost::createBlank($order);
    }

    public function stickers2($ordersIds)
    {
        if (!empty($ordersIds)) {
            $orders = Order::whereIn('id', explode(',', $ordersIds))->get();
            Kazpost::createStickers2($orders);
        } else {
            abort(404);
        }

    }

    public function getRegistry(Request $request)
    {
        $orders = Order::with('getTargetValue')
            ->moderated()
            ->checkAuth()
            ->targetApprove()
            ->whereIn('id', explode(',', $request->orders))
            ->where('target_approve', 4)
            ->get();

        $fileName = 'registry_' . date("d.m.Y_H:i:s", time());

        return Excel::download(new KzExport($orders),  $fileName . '.xlsx');
    }
}
