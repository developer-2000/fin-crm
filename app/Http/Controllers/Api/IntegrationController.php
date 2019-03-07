<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\OrderController;
use App\Models\Api\Posts\Novaposhta;
use App\Models\Api\CodeStatus;
use App\Models\Api\ProjectCodeStatus;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\ProcStatus;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\TargetConfig;
use Illuminate\Validation\Validator;

class IntegrationController extends BaseController
{
    public static $modelNameSpace = 'App\Models\Api\Posts\\';

    public function createDeliveryNote(
        Request $request,
        Order $orderModel,
        OrderProduct $ordersOffersModel,
        OrdersLog $ordersLogModel,
        OrderController $orderController
    ) {
        $errorMessage = $this->errorMessage();

        $phone = $request->get('phone');
        $order = Order::where('id', $request->order_id)->first();
        if (!$order) {
            abort(404);
        }
        $newTarget['target_approve'] = $request->get('target_approve');
        $target = TargetConfig::findOrFail($newTarget['target_approve']);
        $oldTarget = TargetConfig::where('id', $order->target_approve)->first();
        $targetType = 'approve';
        $targetFields = '';
        if ($target) {
            $targetFields = json_decode($target->options, true);
        }
        $proc_status = $order->proc_status;


        //валидация для подтверждения
        $orderController->validateApprove($request, $errorMessage, $targetFields, $targetType, $request->order_id);

        //сохраняем данные клиента
        $dataClient = [
            'name_first'   => $request->get('name'),
            'name_last'    => $request->get('surname'),
            'name_middle'  => $request->get('middle'),
            'phone'        => $phone,
            'geo'          => $request->get('country'),
            'age'          => $request->get('age'),
            'gender'       => $request->get('gender'),
            'proc_status'  => $proc_status,
            'time_changed' => now(),

            'division_id'  => $request->get('division_id') ? $request->get('division_id') : $order->division_id,
        ];


        $dataClient = array_merge($dataClient, $newTarget);

        try {
            $result['contactData'] = $orderModel->saveContactData($dataClient, $request->order_id);
            $orderController->addLogsForData($order, $dataClient);

            if ($oldTarget && $target) {
                if ($oldTarget->id != $target->id) {
                    $ordersLogModel->addOrderLog($order->id, 'Цель была изменена c "' . $oldTarget->name . '" на "' . $target->name . '"');
                }
            }
            $result['messages']['success'][] = trans('alerts.data-successfully-added');
        } catch (\Exception $exception) {
            $result['contactData'] = false;
            $result['messages']['errors'][] = trans('alerts.data-not-added') . $exception->getMessage();
        }

        //сохранение товаров
        if ($request->get('products')) {
            $price = $ordersOffersModel->saveSendingProducts($request->get('products'), $request->order_id, $ordersLogModel);
            if ($price != $request->get('order-price') && $request->get('order-price') > 0) {
                $price = $request->get('order-price');
            }
            $result['products'] = $orderModel->changeAllPriceAndDateChange($request->order_id, $price);
            if ($price != $order->price_total) {
                $ordersLogModel->addOrderLog($order->id, "Стоимость товара была изменена " . $order->price_total . ' -> ' . $price);
            }

            if ($result['products']) {
                $result['messages']['success'][] = trans('alerts.data-successfully-saved');
            } else {
                $result['messages']['errors'][] = trans('alerts.data-not-saved');
            }
        }

        //ставим цель
        if ($request->get('target_status')) {
            try {
                $result['target'] = $orderController->saveTargetSending($request->get('target_status'), $order, $targetFields, $request->get($targetType), $request->get('target_user'));
                if ($result['target']) {
                    $result['messages']['success'][] = trans('alerts.data-successfully-saved');
                } else {
                    $result['messages']['errors'][] = trans('alerts.data-not-saved');
                }
            } catch (\Exception $exception) {
                $result['target'] = false;
                $result['messages']['errors'][] = trans('alerts.data-successfully-saved') . $exception->getMessage();
            }
        }

        $orderModel->getProcessingStatusOrderApi($request->order_id);

        $response['success'] = $result;

        if ($result['contactData'] && $result['products'] && $result['target']) {
            //создание накладной
            $className = self::$modelNameSpace . studly_case($target->alias);
            if (class_exists($className)) {
                $result = $className::createDocument();

                if ($result instanceof Validator) {
                    $response['errors'] = $result->messages();
                    return response()->json($response, 422);
                }
                if (isset($result['errors'])) {
                    $response['errors'] = $result['errors'];
                }

                $response['integration'] = $result;

                if (isset($response['integration']['success'])) {
                    if ($response['integration']['success']) {
                        $response['integration']['message'] = trans('alerts.data-successfully-changed');
                    } else {
                        $response['integration']['message'] = trans('alerts.data-not-changed');
                    }
                }
            }
        }

        return response()->json($response);
    }

    public function editDeliveryNote(
        Request $request,
        Order $orderModel,
        OrderProduct $ordersOffersModel,
        OrdersLog $ordersLogModel,
        OrderController $orderController
    ) {
        $errorMessage = $this->errorMessage();
        $errorMessage['proc_status.required'] = trans('validation.fill-result-call');//

        $phone = $request->get('phone');
        $order = Order::where('id', $request->order_id)->first();
        if (!$order) {
            abort(404);
        }
        $newTarget['target_approve'] = $request->get('target_approve');
        $target = TargetConfig::findOrFail($newTarget['target_approve']);
        $oldTarget = TargetConfig::where('id', $order->target_approve)->first();
        $targetType = 'approve';
        $targetFields = '';
        if ($target) {
            $targetFields = json_decode($target->options, true);
        }
        $proc_status = $order->proc_status;


        //валидация для подтверждения
        $orderController->validateApprove($request, $errorMessage, $targetFields, $targetType, $request->order_id);

        //сохраняем данные клиента
        $dataClient = [
            'name_first'   => $request->get('name'),
            'name_last'    => $request->get('surname'),
            'name_middle'  => $request->get('middle'),
            'phone'        => $phone,
            'geo'          => $request->get('country'),
            'age'          => $request->get('age'),
            'gender'       => $request->get('gender'),
            'proc_status'  => $proc_status,
            'time_changed' => now(),

            'division_id'  => $request->get('division_id') ? $request->get('division_id') : $order->division_id,
        ];


        $dataClient = array_merge($dataClient, $newTarget);

        try {
            $result['contactData'] = $orderModel->saveContactData($dataClient, $request->order_id);
            $orderController->addLogsForData($order, $dataClient);

            if ($oldTarget && $target) {
                if ($oldTarget->id != $target->id) {
                    $ordersLogModel->addOrderLog($order->id, 'Цель была изменена c "' . $oldTarget->name . '" на "' . $target->name . '"');
                }
            }
            $result['messages']['success'][] = trans('alerts.data-successfully-added');
        } catch (\Exception $exception) {
            $result['contactData'] = false;
            $result['messages']['errors'][] = trans('alerts.data-not-added') . $exception->getMessage();
        }

        //сохранение товаров
        if ($request->get('products')) {
            $price = $ordersOffersModel->saveSendingProducts($request->get('products'), $request->order_id, $ordersLogModel);
            $result['products'] = $orderModel->changeAllPriceAndDateChange($request->order_id, $price);

            if ($result['products']) {
                $result['messages']['success'][] = trans('alerts.data-successfully-saved');
            } else {
                $result['messages']['errors'][] = trans('alerts.data-not-saved');
            }
        }

        //ставим цель
        if ($request->get('target_status')) {
            try {
                $result['target'] = $orderController->saveTargetSending($request->get('target_status'), $order, $targetFields, $request->get($targetType), $request->get('target_user'));
                if ($result['target']) {
                    $result['messages']['success'][] = trans('alerts.data-successfully-saved');
                } else {
                    $result['messages']['errors'][] = trans('alerts.data-not-saved');
                }
            } catch (\Exception $exception) {
                $result['target'] = false;
                $result['messages']['errors'][] = trans('alerts.data-not-saved') . $exception->getMessage();
            }
        }

        $orderModel->getProcessingStatusOrderApi($request->order_id);

        $response['success'] = $result;

        if ($result['contactData'] && $result['products'] && $result['target']) {
            //создание накладной
            $className = self::$modelNameSpace . studly_case($target->alias);
            if (class_exists($className)) {
                $result = $className::editDocument();

                if ($result instanceof Validator) {
                    $response['errors'] = $result->messages();
                    return response()->json($response, 422);
                }
                if (isset($result['errors'])) {
                    $response['errors'] = $result['errors'];
                }

                $response['integration'] = $result;

                if (isset($response['integration']['success'])) {
                    if ($response['integration']['success']) {
                        $response['integration']['message'] = trans('alerts.data-successfully-added');
                    } else {
                        $response['integration']['message'] = trans('alerts.track-not-added');
                    }
                }
            }
        }

        return response()->json($response);
    }

    public function deleteDeliveryNote()
    {
        $request = request();
        $newTarget['target_approve'] = $request->get('target_approve');
        $target = TargetConfig::findOrFail($newTarget['target_approve']);

        //удаление накладной
        $className = self::$modelNameSpace . studly_case($target->alias);
        if (class_exists($className)) {
            $result = $className::deleteDocument();
            if (isset($result['errors'])) {
                $response['errors'] = $result['errors'];
            }
            $response['integration'] = $result;

            if (isset($response['integration']['success'])) {
                if ($response['integration']['success']) {
                    $response['integration']['message'] = trans('alerts.record-successfully-deleted');
                } else {
                    $response['integration']['message'] = trans('alerts.record-not-deleted');
                }
            }
        }
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param $client
     * @param $order
     * @return \stdClass
     */
    public static function saveRecipientCounterParty(Request $request, $client, $senderData): \stdClass
    {
        $responseSaveCounterparty = $client->request('POST', Novaposhta::API, ['json' =>
                                                                                   ["apiKey"           => $senderData->key,
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

    public function index()
    {
        return view('integrations.index', [
            'integrations' => TargetConfig::integration()->paginate(20),
        ]);
    }

    public function edit($alias)
    {
        $integration = TargetConfig::integration()
            ->where('alias', $alias)
            ->firstOrFail();

        $className = self::$modelNameSpace . studly_case($integration->alias);
        if (class_exists($className)) {
            return $className::editView($integration);
        }

        abort(404);
    }

    public function codesStatuses($alias)
    {
        $targetConfig = TargetConfig::where('alias', $alias)->first();
        $systemStatuses = ProcStatus::senderStatuses()->systemStatuses()->get();
        $procStatuses = collect(ProcStatus::senderStatuses()->checkProject()->get())->groupBy('project_id');
        $codesStatuses = CodeStatus::where('integration_id', $targetConfig->id)->get();
        if (view('integrations.' . $alias . '.codes-statuses')) {
            return view('integrations.' . $alias . '.codes-statuses',
                [
                    'integrationId'  => $targetConfig->id,
                    'codesStatuses'  => $codesStatuses,
                    'systemStatuses' => $systemStatuses,
                    'procStatuses'   => $procStatuses
                ]);
        } else {
            abort(404);
        }
    }

    public function saveCodeStatusAjax(Request $request)
    {
        $codeStatus = CodeStatus::where([['integration_id', $request->integrationId], ['status_code', $request->code]])
            ->first();
        $codeStatus->system_status_id = $request->status;
        $codeStatus->save();

        if ($request->procStatus) {
            $newProjectCodeStatus = ProjectCodeStatus::create(['codes_statuses_id' => $codeStatus->id, 'proc_status_id' => $request->procStatus]);

            if ($newProjectCodeStatus) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
        return response()->json(['success' => true]);
    }

    private function errorMessage()
    {
        return [
            'approve.required_if'            => trans('validation.fill-details-confirm'), //
            'refuse.required_if'             => trans('validation.fill-details-refuse'), //
            'cancel.required_if'             => trans('validation.fill-details-cancel'), //
            'suspicious_comment.required_if' => trans('validation.fill-comment-suspicious-order'), //
            'suspicious_comment.min'         => trans('validation.suspicious-must-be-more'), //
            'callback_time.required_if'      => trans('validation.select-time'), //Select time
            'now.required_if'                => trans('validation.select-time'),
            'products.*.price.min'           => trans('validation.price-must-be-more'), //price must be more
            'products.*.price.numeric'       => trans('validation.price-must-be-numeric'), //price must be numeric value.
            'products.*.price.required_if'   => trans('validation.price-required'), //price is required
            'products.required_if'           => trans('validation.cant-confirm-order-products'), //
            'target_approve.required_if'     => trans('validation.cant-confirm-order-target'), //
            'target_refuse.required_if'      => trans('validation.cant-refuse-order-target'), //
            'target_cancel.required_if'      => trans('validation.cant-cancel-order-target'), //You can not cancel an order without target.
        ];
    }
}
