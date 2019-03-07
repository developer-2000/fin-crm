<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateRequest;
use App\Models\Comment;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\Template;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleHttpClient;

class SmsController extends BaseController
{
    private $sender = 'MultiShop'; // InetShop
    private $login = 'sadasss';
    private $password = 'happyass';
    private $api = 'https://smsc.ru/sys/send.php';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $templates = Template::where('type', 'sms')->paginate(50);

        return view('sms.index', ['templates' => $templates]);
    }

    /**
     * @param TemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(TemplateRequest $request)
    {
        $template = Template::create(
          [
            'name' => $request->name,
            'body' => $request->message,
            'type' => 'sms'
           ]
         );

        if ($template) {
            return response()->json(
              [
                'success' => true,
                'html'    => view(
                    'sms.ajax.templates-table',
                          [
                            'templates' => Template::where('type', 'sms')
                                         ->paginate(50)
                          ]
                )->render()
              ]
            );
        }
    }

    public function getTemplate(int $templateId)
    {
        $template = Template::findOrFail($templateId);

        return response()->json(['text' => $template->body]);
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initiateSmsGuzzle(Request $request)
    {
        $order = Order::findOrFail($request->orderId);
        $client = new GuzzleHttpClient();
        $result = $client->request(
            'POST',
            $this->api,
        [
            'form_params' => [
                'login'   => $this->login,
                'psw'     => $this->password,
                'phones'  => $request->phone_number,
                'mes'     => $request->message,
                'sender'  => $this->sender, // InetShop
                'charset' => 'utf-8',
            ]
        ]
        );
        if ($result->getStatusCode() == 200) {
            (new OrdersLog)->addOrderLog(
              $request->orderId,
              $result->getBody()->getContents().'   Message text:' .$request->message
            );
            (new Comment)->addComment(
              $request->orderId,
              $request->message,
              $order->entity,
             'sms'
            );
            $message = $result->getBody()->getContents();

            return response()->json(
              [
                'success' => true,
                'message' => 'Статус отправки: ' . $message
              ]
            );
        } else {
            (new OrdersLog)->addOrderLog(
              $request->orderId,
              'Смс не отправлено. ' .$result->getBody()->getContents()
            );

            return response()->json(
              [
                'not_sended' => true,
                'message' => $result->getBody()->getContents()
              ]
            );
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function editTemplateName(Request $request)
    {
        return Template::where('id', $request->pk)->update(['name' => $request->value]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function editTemplateBody(Request $request)
    {
        return Template::where('id', $request->pk)->update(['body' => $request->value]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        if (Template::find($request->pk)->delete()) {
            return response()->json(['success' => true, 'pk' => $request->pk]);
        }
    }
}
