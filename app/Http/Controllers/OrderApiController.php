<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Partner;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Validator;

use Illuminate\Http\Request;
use App\Repositories\OrderApiRepository;

class OrderApiController extends BaseController
{
    protected $orderApi;

    public function __construct(OrderApiRepository $orderApi)
    {
        $this->orderApi = $orderApi;
    }

    public function setOrder(Request $request)
    {
        $json = [
            'id' => 0,
            'errors' => $this->validationOrder($request)
        ];

        if (empty($json['errors'])) {
            $data['key'] = $request->input('key');
            $data['partner_oid'] = $request->input('id');
            $data['comment'] = $request->input('comment', '');
            $data['input_data'] = $request->input('input_data', '');
            $data['name_first'] = $request->input('name', '');
            $data['name_last'] = $request->input('surname', '');
            $data['name_middle'] = $request->input('middle', '');
            $data['phone'] = $request->input('phone', '');
            $data['geo'] = $request->input('country');
            $data['host'] = $request->input('ip');
            $data['project_id'] = $request->input('project_id');
            $data['project_name'] = $request->input('project_name');
            $data['offer_id'] = $request->input('offer_id');
            $data['offer_name'] = $request->input('offer_name');
            $data['price_input'] = $request->input('all_price');
            $data['products'] = json_decode($request->input('products'), true);
            $data['source_url'] = $request->input('source_url', '');

            $data['service'] = $request->input('service', '');
            $data['subproject_id'] = $request->input('subproject_id','');
            $data['subproject_name'] = $request->input('subproject_name','');
            $data['category_id'] = $request->input('category_id', '');
            $data['category_name'] = $request->input('category_name', '');

            $data['tag_source'] = $request->input('tag_source', '');
            $data['tag_medium'] = $request->input('tag_medium', '');
            $data['tag_content'] = $request->input('tag_content', '');
            $data['tag_term'] = $request->input('tag_term', '');
            $data['tag_campaign'] = $request->input('tag_campaign', '');

            try {
                $json['id'] = $this->orderApi->setOrder($data);
            } catch (\Exception $exception) {
                $json['errors'][] = $exception->getMessage();
                Log::error("ERROR API : LINE " . $exception->getLine() . "\n MESSAGE : " . $exception->getMessage());
            }
        }

        return response()->json($json);
    }

    public function setExistOrder(Request $request)
    {
        $json = [
            'id' => 0,
            'errors' => $this->validationOrder($request)
        ];

        if (empty($json['errors'])) {
            $data['crm_id'] = $request->input('crm_id');
            $data['key'] = $request->input('key');
            $data['partner_oid'] = $request->input('id');
            $data['comment'] = $request->input('comment', '');
            $data['input_data'] = $request->input('input_data', '');
            $data['name_first'] = $request->input('name', '');
            $data['name_last'] = $request->input('surname', '');
            $data['name_middle'] = $request->input('middle', '');
            $data['phone'] = $request->input('phone', '');
            $data['geo'] = $request->input('country');
            $data['host'] = $request->input('ip');
            $data['project_id'] = $request->input('project_id');
            $data['project_name'] = $request->input('project_name');
            $data['offer_id'] = $request->input('offer_id');
            $data['offer_name'] = $request->input('offer_name');
            $data['price_input'] = $request->input('all_price');
            $data['products'] = json_decode($request->input('products') ?? '', true);
            $data['source_url'] = $request->input('source_url', '');

            $data['service'] = $request->input('service', '');
            $data['subproject_id'] = $request->input('subproject_id','');
            $data['subproject_name'] = $request->input('subproject_name','');
            $data['category_id'] = $request->input('category_id', '');
            $data['category_name'] = $request->input('category_name', '');

            $data['tag_source'] = $request->input('tag_source', '');
            $data['tag_medium'] = $request->input('tag_medium', '');
            $data['tag_content'] = $request->input('tag_content', '');
            $data['tag_term'] = $request->input('tag_term', '');
            $data['tag_campaign'] = $request->input('tag_campaign', '');

            try {
                $json['id'] = $this->orderApi->setExistOrder($data);
            } catch (\Exception $exception) {
                $json['errors'][] = $exception->getMessage();
                Log::error("ERROR API : LINE " . $exception->getLine() . "\n MESSAGE : " . $exception->getMessage());
            }
        }

        return response()->json($json);
    }

    protected function validationOrder(Request $request)
    {
        $services = Order::SERVICE_ALL . ',' . Order::SERVICE_CALL_CENTER . ','. Order::SERVICE_SENDING;
        $errors = Validator::make($request->all(), [
            'key'             => 'required|exists:partners',
            'id'              => 'required|int|min:1',
            'phone'           => 'max:25',
            'country'         => 'required|exists:countries,code',
            'ip'              => 'required|ip',
            'project_id'      => 'min:1',
            'project_name'    => 'max:255',
            'all_price'       => 'required',
            'offer_id'        => 'required|int|min:1',
            'offer_name'      => 'required|max:255',

            'service'         => 'required|string|in:' . $services,
            'subproject_id'   => 'min:1',
            'subproject_name' => 'max:255',
            'products'        => 'json',

            'name'            => 'max:255',
            'surname'         => 'max:255',
            'middle'          => 'max:255',
            'input_data'      => 'json',
            'comment'         => 'max:255',
            'tag_source'      => 'max:255',
            'tag_medium'      => 'max:255',
            'tag_content'     => 'max:255',
            'tag_term'        => 'max:255',
            'tag_campaign'    => 'max:255',
            'source_url'      => 'url',
        ])->messages()->messages();

        return $errors;
    }

    function getStatusOrder4(Request $request, Order $orderModel)
    {
        if ($request->input('key') != '2sNYIn8RUlxNQRgUDYqH') {
            die('Incorrect key');
        }
        echo $orderModel->getStatusOrderApi4($request->input('ids'));
    }

    public function getResultOrder(Request $request, Order $orderModel)
    {
        try {
            $partner = Partner::where('key', $request->key)->first();

            if ($partner) {
                return response()->json($orderModel->getResultOrder($request->ids));
            }

        } catch (\Exception $exception) {
            return response()->json([
                'errors' => $exception->getMessage(),
            ]);
        }

        abort(404);
    }
}
