<?php

namespace App\Http\Controllers;

use App\Models\OperatorMistake;
use function foo\func;
use App\Models\NP;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Company;
use App\Models\Product;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\OrdersLog;
use App\Models\TargetValue;
use App\Models\ColdCallList;
use App\Models\ColdCallFile;
use App\Models\OrderProduct;
use App\Models\OrdersOpened;
use App\Models\TargetConfig;
use App\Models\ColdCallResult;
use App\Models\CallProgressLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\ColdCallList\ColdCallListService;
use App\Services\ColdCallList\ImportColdCallListService;
use App\Repositories\ColdCallRepository;

class ColdCallListController extends BaseController
{
    public function index()
    {
      $coldCallRepository = new ColdCallRepository;

      return view('cold-calls.cold-call-lists')
              ->with('lists', $coldCallRepository->all());
    }

    public function show($id, Request $request)
    {
        if ($request->isMethod('post')) {
            $coldCallList = ColdCallFile::find($id);
            $coldCallList->campaign_id = $request->input('campaign_id');
            $coldCallList->save();
        }

        $listFile = ColdCallFile::where('id', $id)->with('coldCallList', 'company')->first();

        $lists = ColdCallList::where('cold_call_file_id', $id)->paginate(20);
        foreach ($lists as $list) {
            $list->add_info = !empty($list->add_info) ? json_decode($list->add_info) : NULL;
            $list->phone_number = json_decode($list->phone_number);
            $list->call_status = collect(DB::select('SELECT  ccr.call_status
            FROM  cold_call_results AS ccr
             INNER JOIN
           ( SELECT      cold_call_list_id,      MAX(updated_at) AS `updated_at`
            FROM      cold_call_results
            GROUP BY      cold_call_list_id  ) max_time
             ON
            ccr.cold_call_list_id = max_time.cold_call_list_id
             AND
             ccr.updated_at = max_time.updated_at
            WHERE ccr.cold_call_list_id IN (' . $list->id . ')
              '))->first();
        }

        $listFile->country = Country::where('code', $listFile['geo'])->first();
        $listFile['campaign'] = DB::table('company_elastix')->where('id', $listFile->campaign_id)->first();
        $campaigns = DB::table('company_elastix')->where('company_id', $listFile->company_id)
            ->get();

        return view('cold-calls.cold-call-list-edit', ['listFile' => $listFile, 'lists' => $lists, 'companies' => Company::all(),
            'countries' => Country::all(),
            'campaigns' => $campaigns]);
    }

    public function delete(Request $request)
    {
        if ($request->isMethod('POST')) {
            $listId = $request->input('id');
            $listsInProc = DB::table('cold_call_files as ccf')
                ->leftjoin('cold_call_lists as ccl', 'ccl.cold_call_file_id', '=', 'ccf.id')
                ->where([['ccl.proc_status', '!=', '1'], ['ccf.id', $listId]])->get();
            if ($listsInProc) {
                $result['success'] = false;
                return response()->json($result);
            } else {
                $coldCallFile = ColdCallFile::find($listId);
                $coldCallFile->coldCallList()->delete();
                $coldCallFile->delete();
                $result['success'] = true;
                return response()->json($result);
            }
        }
    }

    public function info($id, Request $request)
    {
        $listFile = ColdCallFile::where('id', $id)->with('coldCallList', 'company')->first();
        $listFile->country = Country::where('code', $listFile['geo'])->first();

        $filter = [
            'id' => $request->input('id'),
            'status' => $request->input('status'),
            'phone_number' => $request->input('phone_number'),
            'order_id' => $request->input('order_id'),
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('cold-call-list-info', $id) . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $lists = (new ColdCallList())->getListRows($id, $filter);

        $numbers = ColdCallList::where('cold_call_file_id', $id)->select('phone_number')->get();
        foreach ($numbers as $number) {
            $phone_numbers[] = json_decode($number->phone_number)[0];
        }
        foreach ($lists as $list) {
            $list->add_info = !empty($list->add_info) ? json_decode($list->add_info) : NULL;
            $list->phone_number = json_decode($list->phone_number);
            $list->call_status = collect(DB::select('SELECT  ccr.call_status
            FROM  cold_call_results AS ccr
             INNER JOIN
           ( SELECT      cold_call_list_id,      MAX(updated_at) AS `updated_at`
            FROM      cold_call_results
            GROUP BY      cold_call_list_id  ) max_time
             ON
            ccr.cold_call_list_id = max_time.cold_call_list_id
             AND
             ccr.updated_at = max_time.updated_at
            WHERE ccr.cold_call_list_id IN (' . $list->id . ')
              '))->first();
            $list->progress_log = DB::table('call_progress_log')->where([['order_id', $list->id], ['entity', 'cold_call']])->get();
        }
        $listIds = DB::select('SELECT id FROM cold_call_lists WHERE cold_call_file_id = ' . $id);

        foreach ($listIds as $listid) {
            $ids[] = $listid->id;
        }
        $ids = implode(',', $ids);
        $statuses = collect(DB::select('SELECT
                    count(cpl.order_id) AS listRowQuantity,
                    count(CASE WHEN (cpl.status = \'Success\') THEN 1 END) AS successQuantity,
                    count(CASE WHEN (cpl.status = \'Failure\') THEN 1 END) AS failureQuantity,
                    count(CASE WHEN (cpl.status = \'ShortCall\') THEN 1 END) AS shortCallQuantity,
                    count(CASE WHEN (cpl.status = \'Abandoned\') THEN 1 END) AS abandonedQuantity,
                    count(CASE WHEN (cpl.status = \'NoAnswer\') THEN 1 END) AS noAnswerQuantity
                    FROM  call_progress_log AS cpl
                    LEFT JOIN call_progress_log cpl1  ON (cpl.order_id = cpl1.order_id AND cpl.id < cpl1.id)
                    WHERE   cpl1.ID IS NULL
                    AND cpl.order_id IN (' . $ids . ')
              '))->first();
        $listFile->call_statuses = $statuses;
        if(!empty($statuses->listRowQuantity)){
            $listFile->percentSuccess = round($statuses->successQuantity / $statuses->listRowQuantity * 100, 2);
            $listFile->percentFailure = round($statuses->failureQuantity / $statuses->listRowQuantity * 100, 2);
            $listFile->percentShortCall = round($statuses->shortCallQuantity / $statuses->listRowQuantity * 100, 2);
            $listFile->percentAbandoned = round($statuses->abandonedQuantity / $statuses->listRowQuantity * 100, 2);
            $listFile->percentNoAnswer = round($statuses->noAnswerQuantity / $statuses->listRowQuantity * 100, 2);
        }
        $listFile['campaign'] = DB::table('company_elastix')->where('id', $listFile->campaign_id)->first();
        $campaigns = DB::table('company_elastix')->where('company_id', $listFile->company_id)
            ->get();

        return view('cold-calls.cold-call-list-info', ['listFile' => $listFile, 'lists' => $lists, 'companies' => Company::all(),
            'countries' => Country::all(),
            'campaigns' => $campaigns,
            'phone_numbers' => $phone_numbers]);
    }

    public function getImport()
    {
        return view('cold-calls.import');
    }

    public function parseImport(Request $request)
    {
      $importService = new ImportColdCallListService($request);

      if(!$importService->execute()){
        return redirect()->back();
      }

      return view('cold-calls.import-fields')
              ->with('countries', Country::all())
              ->with('companies', Company::all())
              ->with('header', true)
              ->with($importService->getData());
    }

    public function processImport(Request $request)
    {
      $ccService = new ColdCallListService;

      if($ccService->create($request))
      {
        return response()->json(['success' => true]);
      }

      return response()->json(['success' => false]);
    }

    public function getData($id)
    {
        $list = ColdCallList::find($id);
        return response()->json(['list' => $list]);
    }

    public function getCampaigns(Request $request)
    {
        $userWithoutCompany = DB::table('users')
            ->where([['company_id', 0], ['id', auth()->user()->id]])->get();

        if (empty($userWithoutCompany)) {
            $campaigns = DB::table('company_elastix as ce')
                ->select('ce.id', 'ce.name', 'ce.call_count', 'ce.cron_status',
                    'ce.call_time', 'ce.source', 'ce.content', 'ce.country', 'ce.offer', 'ce.company_id', 'u.id as users')
                ->leftjoin('users as u', 'u.company_id', '=', 'ce.company_id')
                ->where('u.id', auth()->user()->id)
                ->paginate(20);
        } else {
            $companiesIds = DB::table('companies')->select('id')->get();
            $companiesIds = json_encode($companiesIds);
            $companiesIds = json_decode($companiesIds, true);
            $companies = $companiesIds;
            if ($request->isMethod('post')) {
                $companies = explode(',', $request->input('company'));
            }

            $campaigns = DB::table('company_elastix as ce')
                ->select('ce.id', 'ce.name', 'ce.call_count', 'ce.cron_status',
                    'ce.call_time', 'ce.source', 'ce.content', 'ce.country', 'ce.offer', 'ce.company_id', 'u.id as users')
                ->leftjoin('users as u', 'u.company_id', '=', 'ce.company_id')
                ->whereIn('ce.company_id', $companies)
                ->groupBy('ce.id')
                ->paginate(20);
        }
        foreach ($campaigns as $campaign) {
            $campaign->coldCallFile = ColdCallFile::where('campaign_id', $campaign->id)->get();
            $campaign->users = DB::table('users')->where('campaign_id', $campaign->id)->get();
            $campaign->company = Company::find($campaign->company_id);
            $campaign->count_active_rows = collect(DB::select('SELECT COUNT(IF(proc_status = 3, 1, NULL)) AS active_rows,
            COUNT(ccl.id) AS quantity_lists
            FROM cold_call_files AS ccf
            LEFT JOIN cold_call_lists AS ccl ON ccl.cold_call_file_id = ccf.id
            WHERE ccf.campaign_id = ' . $campaign->id . '
            '))->first();
            if (!empty($campaign->count_active_rows->active_rows) && !empty($campaign->count_active_rows->quantity_lists)) {
                $campaign->activePercent = round($campaign->count_active_rows->active_rows / $campaign->count_active_rows->quantity_lists * 100, 2);
            }
        }

        return view('cold-calls.cold-call-campaigns', ['campaigns' => $campaigns,
            'countries' => Country::all(), 'source' => Project::all(), 'offers' => Offer::all(),
            'companies' => Company::all(),]);
    }

    public function createCampaign()
    {
        return view('cold-calls.campaign-create', [
            'countries' => Country::where('use', 1)->orderBy('sequence')->get(),
            'source' => Project::all(),
            'offers' => Offer::all(),
            'companies' => Company::all(),]
        );
    }

    /**
     * страница добавления операторов к группе
     */
    public function setOperators(User $authModel)
    {
        $result['campaigns'] = (new Campaign)->getAllCompanyElastixForColdCalls();
        $result['operators'] = $authModel->getAllOperatorsInCampaigns();
        return view("cold-calls.set-operators", $result);
    }

    public function searchOperatorsForPbxCampaignAjaxColdCalls(Request $request, User $authModel)
    {
        if ($request->isMethod('post')) {
            $result['operators'] = $authModel->searchOperators($request->get('search'));
            $result['campaigns'] = (new Campaign)->getAllCompanyElastixForColdCalls();
            $result['all'] = false;
            if ($request->get('search') == '') {
                $result['all'] = true;
            }
            return response()->json(['html' => view('campaigns.ajax.searchOperatorAjax', $result)->render()]);
        }
        abort(404);
    }


    /*изменение статуса листа*/
    public function changeStatus($id, $status)
    {
        if($status == 'inactive'){
            $coldCallListsIds = ColdCallList::where([['cold_call_file_id', $id], ['proc_status', 2]])->pluck('id')->toArray();

            if(!empty($coldCallListsIds)){
                (new ColdCallList())->deleteCallsFromPBX($coldCallListsIds);
            }
        }
        $list = ColdCallFile::where('id', $id)->first();
        $list->status = $status;
        $list->save();
        return response()->json($list);
    }

    /*создание нового заказа для пзиции листа ХП*/
    public function createOrder($id, $flag, Request $request, Order $orderModel, Comment $commentsModel)
    {
        $existedOrder = ColdCallList::with('order')->where([['id', $id],['order_id', '!=', 0]])->first();

        if ($existedOrder) {
            return redirect()->route('order', ['id' => $existedOrder->order_id, 'flag' => 1]);
        }

        $orderId = NULL;
        $data = [];
        $listRow = ColdCallList::where('id', $id)->with('ColdCallFile')->first();
        if($listRow) {
            $listRow['add_info'] = isset($listRow['add_info']) ? json_decode($listRow['add_info']) : NULL;
            $listRow['phone_number'] = isset($listRow['phone_number']) ? json_decode($listRow['phone_number']) : NULL;

            //костыль partner_id
            $data['offersArray'] = Offer::whereNull('partner_id')->where([['geo', $listRow->ColdCallFile->geo], ['status', 'active'], ['company_id', 9]])
                ->get();

            //костыль partner_id
                $offerFirst = Offer::whereNull('partner_id')->where([['geo', $listRow->ColdCallFile->geo], ['status', 'active'], ['company_id', 9]])
                    ->first();

                $order = new Order;
                $order->geo = $listRow->ColdCallFile->geo;
                $order->name_first = !empty($listRow['add_info']->имя) ? $listRow['add_info']->имя : NULL;
                $order->name_last = !empty($listRow['add_info']->фамилия) ? $listRow['add_info']->фамилия : NULL;
                $order->name_middle = !empty($listRow['add_info']->отчество) ? $listRow['add_info']->отчество : NULL;
                $order->phone_input = "" . $listRow['phone_number'][0] . "";
                $order->phone = "" . $listRow['phone_number'][0] . "";
                $order->time_created = $order->time_changed = $order->time_modified = now();
                $offerId = !empty($offerFirst->id) ? $offerFirst->id : NULL;
                $order->offer_id = $offerId;
                $order->project_id = 3;
                $order->subproject_id = 6;
                $order->input_data = json_encode($listRow['add_info']);
                $order->target_status = 0;                              //0-нет цели
                $order->target_user = 0;
                $order->price_input = 0;
                $order->proc_status = 3;
                $order->proc_campaign = $listRow->ColdCallFile->campaign_id;
                $order->proc_call_id = 0;
                $order->proc_time = now();
                $order->proc_callback_user = 0;
                $order->proc_priority = 0;
                $order->proc_fails = 0;
                $order->proc_stage = 0;
                $order->entity = 'cold_call';
                $order->target_approve = $this->getTargetID('approve', $listRow->ColdCallFile->geo, $offerId, 3, 'cold_call');
                $order->target_refuse = $this->getTargetID('refuse', $listRow->ColdCallFile->geo, $offerId, 3, 'cold_call');
                $order->target_cancel = $this->getTargetID('cancel', $listRow->ColdCallFile->geo, $offerId, 3, 'cold_call');
                $order->save();

                $orderId = $order->id;
                $coldCallListRow = ColdCallList::find($id);
                $coldCallListRow->proc_status = 3;
                $coldCallListRow->order_id = $orderId;
                $coldCallListRow->save();

                if ($flag) {
                    (new OrdersLog)->addOrderLog($orderId, 'Заказ открылся оператору');
                    $learningStatus = DB::table('company_elastix')->where('id', $order->proc_campaign)
                        ->value('learning');
                    (new OrdersOpened)->add([
                        'order_id'     => $orderId,
                        'user_id'      => auth()->user()->id,
                        'date_opening' => now(),
                        'unique_id'    => $flag,
                        'learning'     => $learningStatus
                    ]);
                }

                ColdCallResult::create(['cold_call_list_id' => $id,
                                        'call_status'       => 'Success', 'count_status' => 1]);
        }else{
            abort(404);
        }

        $data['orderOne'] = $orderModel->getOneOrderColdCall($orderId);
        $data['userCalls'] = '';
        $data['userCalls'] = (new CallProgressLog)->getCallProgressLogById($id, $entity = 'cold_call');
        $data['offers'] = (new OrderProduct)->getProductsByOrderId($orderId, $data['orderOne']->subproject_id ?? 0);
        $data['log'] = '';
        $data['log'] = (new OrdersLog)->getOrderLogById($orderId);
        $data['samePhone'] = (new Order)->getCountOrdersByPhone($orderId, $data['orderOne']->phone, $data['orderOne']->host);
        $data['comments'] = (new Comment)->getComments($orderId, 'order', 'comment');
        $data['country'] = collect((new Country)->getAllCounties())->keyBy('code');
        $data['recommended_products'] = (new Product)->getRecommendedProductsGroupByType($data['orderOne']->offer_id, $data['orderOne']->geo);
        $data['processingStatus'] = (new Order)->getOrderFromProcessing($orderId);
        $data['target_value'] = (new TargetValue())->getTargetValue($orderId);

        $data['targets_approve'] = TargetConfig::getConfigsByTarget('approve', $data['orderOne']);
        $data['targets_refuse'] = TargetConfig::getConfigsByTarget('refuse', $data['orderOne']);
        $data['targets_cancel'] = TargetConfig::getConfigsByTarget('cancel', $data['orderOne']);

        $data['target_option']['approve'] = TargetConfig::where('id', $data['orderOne']->target_approve)->where('active', 1)->first();
        $data['target_option']['refuse'] = TargetConfig::where('id', $data['orderOne']->target_refuse)->where('active', 1)->first();
        $data['target_option']['cancel'] = TargetConfig::where('id', $data['orderOne']->target_cancel)->where('active', 1)->first();
        $data['suspicious_comment'] = $commentsModel->getLastComment($orderId, $data['orderOne']->entity, 'suspicious');
        return view('cold-calls.order-one', $data);
    }

    public function offers(Request $request, Order $orderModel, Product $offersModel, Project $projectModel)
    {
        $page = $request->input('page');
        $requestObject = 'query';
        if ($request->isMethod('post')) {
            $requestObject = 'request';
        }
        $filter = [
            'project' => 3,
            'name' => $request->$requestObject->get('name'),
            //  'company' => $request->input('company-select2'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('cold-call-offers') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $result['data'] = $offersModel->getAllOffersByFiltersColdCalls($filter, $page);
        return view('cold-calls.offers', $result);
    }

    public function createOffer(Request $request)
    {
        $data['countries'] = Country::all();
        $data['companies'] = Company::all();

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:20',
                'company' => 'required',
                'country' => 'required',
            ]);

            if ($validator->fails()) {

                return view('cold-calls.offer-create', $data)->withErrors($validator);
            }
            $result['success'] = Offer::create(['name' => $request->input('name'), 'geo' => $request->input('country'),
                'status' => 'inactive',
                'company_id' => $request->input('company'),
                'project_id' => 3]);
            if ($result['success']) {
                return redirect()->route('cold-call-offers');
            } else {
                abort(404);
            }
        }

        return view('cold-calls.offer-create', $data);
    }

    /**
     * Страница одного оффера
     */
    function oneOffer($id, Product $offersModel, Project $projectModel, Country $countriesModel)
    {
        $result['data'] = $offersModel->getOneOffer($id);
        $result['data']->project = Project::find($result['data']->project_id);
        $result['products'] = $offersModel->getAllProductsOfferGroupByType($id);
        $result['countries'] = collect($countriesModel->getAllCounties())->keyBy('code');
        return view('cold-calls.offer_one', $result);
    }

    /**
     * поиск всех товаров по селекту
     */
    public static function findProducts(Request $request, Product $productModel)
    {

        $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $products = $productModel->searchProductByWord($term);
        $formatted_products = [];

        foreach ($products as $product) {
            $prefix = '';
            if ($product->project_id == 1) {
                $prefix = 'UM::';
            } elseif ($product->project_id == 2) {
                $prefix = 'BM::';
            }
            $formatted_products[] = ['id' => $product->id, 'text' => $prefix . '  ' . $product->title];
        }

        return \Response::json($formatted_products);

    }

    /**
     * Получаем ID цели (TEST)
     *
     * @param $country
     *
     * @param $medium
     *
     * @return int
     */
    public function getTargetId(
        $targetType,
        $geo,
        $offerId,
        $projectId,
        $entity
    ) {
        $kEntity = 5;
        $kGeo = 4;
        $kOffer = 3;
        $kProject = 2;
        $max = 0;
        $targetId = 0;
        $targets = TargetConfig::getConfigsByTarget($targetType);
        if ($targets) {
            $targetId = $targets[0]->id;
            foreach ($targets as $target) {
                $sum = 0;
                if ($target->entity == $entity) {
                    $sum += $kEntity;
                }
                if ($target->filter_geo == $geo) {
                    $sum += $kGeo;
                }
                if ($target->filter_offer == $offerId) {
                    $sum += $kOffer;
                }
                if ($target->filter_project == $projectId) {
                    $sum += $kProject;
                }
                if ($sum > $max) {
                    $max = $sum;
                    $targetId = $target->id;
                }
            }
        }

        return $targetId;
    }

    public function changeRecommendedProducts(Request $request, User $authModel)
    {
        if ($request->isMethod('post')) {
            $data['orderOne'] = (new Order)->getOneOrder($request->input('orderId'));
            $order = Order::find($request->input('orderId'));
            $order->offer_id = $request->input('offerId');
            $order->save();

            $data['recommended_products'] = (new Product)->getRecommendedProductsGroupByType($request->input('offerId'));

            return response()->json(['html' => view('cold-calls.recommended-products', $data)->render()]);
        }
        abort(404);
    }

    public static function setOfferStatus(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $offer = Offer::find($id);
            $offer->status = $request->input('status');
            $offer = $offer->save();
            $result['status'] = $request->input('status');

            if ($offer) {
                return response()->json($result);
            }
        }
        abort(404);
    }

    public static function uploadToPbx(Request $request)
    {
        if ($request->isMethod('post')) {
            if (!empty($request->input('status'))) {

                $array = implode(',', $request->input('status'));
                $fileId = $request->input('list_id');
                $lists = ColdCallList::where('cold_call_file_id', $fileId)->get();
                if (!empty($lists)) {
                    foreach ($lists as $list) {
                        $listsIds[] = $list->id;
                    }
                }

                $listsIds = implode(',', $listsIds);
                $listSearched = DB::select('SELECT  ccr.cold_call_list_id, ccr.call_status
            FROM  cold_call_results AS ccr
             INNER JOIN
           ( SELECT      cold_call_list_id,      MAX(updated_at) AS `updated_at`
            FROM      cold_call_results
            GROUP BY      cold_call_list_id  ) max_time
             ON
            ccr.cold_call_list_id = max_time.cold_call_list_id
             AND
             ccr.updated_at = max_time.updated_at
            WHERE ccr.cold_call_list_id IN (' . $listsIds . ')
            AND ccr.call_status IN (' . $array . ')
              ');


                $missedOutIds = ColdCallList::where([['cold_call_file_id', $fileId], ['proc_status', 3], ['order_id' , 0]])->pluck('id');
                if (count($missedOutIds)) {
                    ColdCallFile::where('id', $fileId)->update(['status' => 'active']);
                    ColdCallList::where('cold_call_file_id', $fileId)->whereIn('id', $missedOutIds)->update(['proc_status' => 1]);
                }

                if (!empty($listSearched)) {
                    foreach ($listSearched as $list) {
                        $ids[] = $list->cold_call_list_id;
                    }

                    ColdCallFile::where('id', $fileId)->update(['status' => 'active']);
                    ColdCallList::where('cold_call_file_id', $fileId)->whereIn('id', $ids)
                        ->orWhereHas('order', function ($query){
                        $query->where('target_status', 0);
                    })
                        ->where('order_id', 0)->update(['proc_status' => 1]);
                    $result['success'] = true;
                    return redirect()->route('cold-calls-lists-edit', $request->input('list_id'));
                } else {
                    return redirect()->route('cold-calls-lists-edit', $request->input('list_id'));
                }
            } else {
                return redirect()->route('cold-calls-lists-edit', $request->input('list_id'));
            }
        }
    }

    public function moderation(Request $request, Country $countriesModel)
    {
        $filter = [
            'page'        => $request->get('page'),
            'grouping'    => $request->get('grouping'),
            'country'     => $request->get('country'),
            'project'     => $request->get('project'),
            'sub_project' => $request->get('sub_project'),
            'offer'       => $request->get('offer'),
            'company'     => $request->get('company'),
            'id'          => $request->get('id'),
            'date_start'  => $request->get('date_start') ? $request->get('date_start') : date('d.m.Y'),
            'date_end'    => $request->get('date_end') ? $request->get('date_end') : date('d.m.Y'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('cold-calls-moderation') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $countries = collect($countriesModel->getAllCounties())->keyBy('code');
        list($orders, $count, $targets) = (new ColdCallRepository)->moderationOrder($filter);

        return view('cold-calls.moderation', [
            'projects'            => collect(Project::project()->get())->keyBy('id'),
            'subProjects'         => collect(Project::subProject()->get())->keyBy('id'),
            'offers_filter'       => (new Product)->getOffersNameNoParent(),
            'country'             => (new Country)->getAllCountryArray(),
            'companies'           => Company::all(),
            'campaigns'           => collect((new Campaign)->getAllCompanyElastix())->keyBy('id'),
            'orders'              => $orders,
            'countries'           => $countries,
            'count'               => $count,
            'targets'             => $targets,
            'operatorMistakes'    => OperatorMistake::all(),
            'subprojectsCaldCall' => Project::where('parent_id', '!=', 0)->where('alias', '!=', 'HP')->get()
        ]);
    }

    public function moderationOrderAjax(Request $request, $id, Order $orderModel, OrdersLog $ordersLogModel)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'sender' => 'required|min:1',
            ]);

            $order = Order::find($id);
            $result = ColdCallRepository::setColdCallsModeration($order);

            if (isset($result['success']) && $result['success']) {
                if ($request->sender && $request->sender != $order->subproject_id) {

                    $text = "Склад был изменен ";
                    $text .= $order->subProject ? 'c ' . $order->subProject->name : '';
                    $project_id = Project::find($request->sender)->parent_id;

                    $order->project_id = $project_id;
                    $order->subproject_id = $request->sender;
                    $order->save();

                    $newSubProject = Project::find($request->sender);
                    $text .= $newSubProject ? ' на ' . $newSubProject->name : '';

                    $ordersLogModel->addOrderLog($id, $text);
                }

                $orderModel->getProcessingStatusOrderApi($id);
                $ordersLogModel->addOrderLog($id, 'Заказ ХП промодерирован');
            }
            return response()->json($result);
        }
        abort(404);
    }

    public function countColdCallsOrdersOnModerationAjax(Request $request, Order $orderModel)
    {
        $filter = [
            'page'       => $request->get('page'),
            'grouping'   => $request->get('grouping'),
            'country'    => $request->get('country'),
            'project'    => $request->get('project'),
            'offer'      => $request->get('offer'),
            'company'    => $request->get('company'),
            'id'         => $request->get('id'),
            'date_start' => $request->get('date_start') ? $request->get('date_start') : date('d.m.Y'),
            'date_end'   => $request->get('date_end') ? $request->get('date_end') : date('d.m.Y'),
        ];
        return response()->json(ColdCallRepository::getCountOrderModeration($filter));
    }
}
