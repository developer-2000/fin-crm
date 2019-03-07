<?php

namespace App\Repositories;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Clients;
use App\Models\Country;

use App\Models\OrdersLog;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Storehouse;
use App\Models\Tag;
use App\Models\TargetConfig;
use App\Services\PhoneCorrection\PhoneCorrectionService;


use App\Models\Campaign;
use Carbon\Carbon;
use DB;

class OrderApiRepository
{
    protected $intervalRepeatOrder = 600;

    protected $phoneCorrection;

    public function __construct( PhoneCorrectionService $phoneCorrection )
    {
        $this->phoneCorrection = $phoneCorrection;
    }

    public function setOrder( array $data )
    {

        $partner = Partner::where('key', $data['key'])->first();
        $project = $this->firstOrCreateProject($partner->id, $data['project_id'], $data['project_name']);
        $partnerId = $partner->id;
        $projectId = $project->id ?? 0;
        $subProjectId = 0;

        $data['offer_id'] = $this->addOrFindOffer($data['offer_id'], $partnerId, $data['offer_name']);
        if ($project) {
            $subProjectId = $this->firstOrCreateSubproject($partnerId, $projectId, $data['subproject_id'], $data['subproject_name']);
        }
        $tagCampaignId = $this->getOrInsertTag(Tag::TAG_CAMPAIGN, $partnerId, $data['tag_campaign']);
        $tagContentId = $this->getOrInsertTag(Tag::TAG_CONTENT, $partnerId, $data['tag_content']);
        $tagTermId = $this->getOrInsertTag(Tag::TAG_TERM, $partnerId, $data['tag_term']);
        $tagSourceId = $this->getOrInsertTag(Tag::TAG_SOURCE, $partnerId, $data['tag_source']);
        $tagMediumId = $this->getOrInsertTag(Tag::TAG_MEDIUM, $partnerId, $data['tag_medium']);


        $country = Country::where('code', strtoupper($data['geo']))->first();
        $procCampaign = $this->getElastix($data['project_id'], $country->id, $data['offer_id']);
        list($phone, $phoneError) = $this->phoneCorrection->customCorrectionForCountry($data['geo'], $data['phone']);

        $inputData = $data['input_data'];
        $procStatus = $phoneError ? 6 : 1;//6-некоректный номер,1-все норм
        if (!$projectId) {
            $inputData = $this->modifiedInputData($inputData, $data);
            $procStatus = 11; //проект или подпроект не определен
        }
        if (!$subProjectId) {
            $inputData = $this->modifiedInputData($inputData, $data);
            $procStatus = 11; //проект или подпроект не определен
        }

        $order = new Order;
        $order->geo = strtolower($country->code);
        $order->name_first = $data['name_first'];
        $order->name_last = $data['name_last'];
        $order->name_middle = $data['name_middle'];
        $order->phone_input = $data['phone'];
        $order->phone = $phone;
        $order->price_input = $order->price_total = $order->price_products = $data['price_input'];
        $order->input_data = $inputData;
        $order->comments = $data['comment'];
        $order->time_created = $order->time_changed = $order->time_modified = now();
        $order->host = $data['host'];
        $order->entity = 'order';
        $order->offer_id = $data['offer_id'];
        $order->partner_id = $partnerId;
        $order->project_id = $projectId;
        $order->partner_oid = $data['partner_oid'];
        $order->source_url = $data['source_url'];
        $order->service = $data['service'];

        $order->subproject_id = $subProjectId;

        $targetApprove = 'approve';
        $order->target_approve = $this->getTargetID(
            $targetApprove,
            strtolower($country->code),
            $data['offer_id'],
            $projectId,
            $data['tag_campaign'],
            $data['tag_content'],
            $data['tag_medium'],
            $data['tag_source'],
            $data['tag_term'],
            'order'
        );
        $targetRefused = 'refuse';
        $order->target_refuse = $this->getTargetID(
            $targetRefused,
            strtolower($country->code),
            $data['offer_id'],
            $projectId,
            $data['tag_campaign'],
            $data['tag_content'],
            $data['tag_medium'],
            $data['tag_source'],
            $data['tag_term'],
            'order'
        );
        $targetCanceled = 'cancel';
        $order->target_cancel = $this->getTargetID(
            $targetCanceled,
            strtolower($country->code),
            $data['offer_id'],
            $projectId,
            $data['tag_campaign'],
            $data['tag_content'],
            $data['tag_medium'],
            $data['tag_source'],
            $data['tag_term'],
            'order'
        );

        $order->proc_status = $procStatus;
        $repeatId = $this->getRepeatID($phone, $data['host']);
        if ($repeatId) {
            $order->proc_status = 4;                            //4-повтор
        }

        $order->repeat_id = $order->proc_status == 4 ? $repeatId : 0;
        $order->proc_campaign = $procCampaign;
        $order->proc_call_id = 0;
        $order->proc_time = now();
        $order->proc_callback_time = null;
        $order->proc_callback_user = 0;
        $order->proc_priority = 0;
        $order->proc_fails = 0;
        $order->proc_stage = 0;
        $order->target_status = 0;                              //0-нет цели
        $order->target_user = 0;

        $order->tag_source = $tagSourceId ?? null;
        $order->tag_medium = $tagMediumId ?? null;
        $order->tag_content = $tagContentId ?? null;
        $order->tag_term = $tagTermId ?? null;
        $order->tag_campaign = $tagCampaignId ?? null;
        //ата модицикации статуса
        $order->time_status_updated = Carbon::now();

        $order->save();

        try {
            //add log with proc_status
            if (isset($order->procStatus->name)) {
                (new OrdersLog())->addOrderLog($order->id, 'Установлен процессинг статус: "' . $order->procStatus->name, ['status_id' => $order->proc_status, 'status_name' => $order->procStatus->name]);
            }

            if ($projectId && $subProjectId) {
                list($products, $inputDataProducts) = $this->addOrFindProducts($data['products'], $projectId, $subProjectId);
                $this->addOrderProducts($order->id, $products);
                if ($inputDataProducts) {
                    $orderInputData = $order->input_data ? $order->input_data : '';
                    $decode = json_decode($orderInputData, true) ?? [];
                    Order::where('id', $order->id)->update(['input_data' => json_encode(array_merge($decode, $inputDataProducts))]);
                }
            }

            $order->getProcessingStatusOrderApi($order->id);
        } catch (\Exception $exception) {
            \Log::info('-------------' . $exception->getMessage() . '------------');
        }

        return $order->id;
    }

    public function setExistOrder( array $data )
    {
        $partner = Partner::where('key', $data['key'])->first();
        $project = $this->firstOrCreateProject($partner->id, $data['project_id'], $data['project_name']);
        $partnerId = $partner->id;
        $projectId = $project->id ?? 0;
        $subProjectId = 0;
        if ($project) {
            $data['offer_id'] = $this->addOrFindOffer($data['offer_id'], $projectId, $data['offer_name']);
            $subProjectId = $this->firstOrCreateSubproject($partnerId, $projectId, $data['subproject_id'], $data['subproject_name']);

            $tagCampaignId = $this->getOrInsertTag(Tag::TAG_CAMPAIGN, $projectId, $data['tag_campaign']);
            $tagContentId = $this->getOrInsertTag(Tag::TAG_CONTENT, $projectId, $data['tag_content']);
            $tagTermId = $this->getOrInsertTag(Tag::TAG_TERM, $projectId, $data['tag_term']);
            $tagSourceId = $this->getOrInsertTag(Tag::TAG_SOURCE, $projectId, $data['tag_source']);
            $tagMediumId = $this->getOrInsertTag(Tag::TAG_MEDIUM, $projectId, $data['tag_medium']);
        }

        $country = Country::where('code', strtoupper($data['geo']))->first();
        $procCampaign = $this->getElastix($data['project_id'], $country->id, $data['offer_id']);
        list($phone, $phoneError) = $this->phoneCorrection->customCorrectionForCountry($data['geo'], $data['phone']);

        $inputData = $data['input_data'];
        $procStatus = $phoneError ? 6 : 1;//6-некоректный номер,1-все норм
        if (!$projectId) {
            $inputData = $this->modifiedInputData($inputData, $data);
            $procStatus = 11; //проект или подпроект не определен
        }
        if (!$subProjectId) {
            $inputData = $this->modifiedInputData($inputData, $data);
            $procStatus = 11; //проект или подпроект не определен
        }

        $order = new Order;
        $order->id = $data['crm_id'];
        $order->geo = strtolower($country->code);
        $order->name_first = $data['name_first'];
        $order->name_last = $data['name_last'];
        $order->name_middle = $data['name_middle'];
        $order->phone_input = $data['phone'];
        $order->phone = $phone;
        $order->price_input = $order->price_total = $order->price_products = $data['price_input'];
        $order->input_data = $inputData;
        $order->comments = $data['comment'];
        $order->time_created = $order->time_changed = $order->time_modified = time();
        $order->host = $data['host'];
        $order->entity = 'order';
        $order->offer_id = $data['offer_id'];
        $order->partner_id = $partnerId;
        $order->project_id = $projectId;
        $order->partner_oid = $data['partner_oid'];
        $order->source_url = $data['source_url'];
        $order->service = $data['service'];

        $order->subproject_id = $subProjectId;

        $targetApprove = 'approve';
        $order->target_approve = $this->getTargetID(
            $targetApprove,
            strtolower($country->code),
            $data['offer_id'],
            $projectId,
            $data['tag_campaign'],
            $data['tag_content'],
            $data['tag_medium'],
            $data['tag_source'],
            $data['tag_term'],
            'order'
        );
        $targetRefused = 'refuse';
        $order->target_refuse = $this->getTargetID(
            $targetRefused,
            strtolower($country->code),
            $data['offer_id'],
            $projectId,
            $data['tag_campaign'],
            $data['tag_content'],
            $data['tag_medium'],
            $data['tag_source'],
            $data['tag_term'],
            'order'
        );
        $targetCanceled = 'cancel';
        $order->target_cancel = $this->getTargetID(
            $targetCanceled,
            strtolower($country->code),
            $data['offer_id'],
            $projectId,
            $data['tag_campaign'],
            $data['tag_content'],
            $data['tag_medium'],
            $data['tag_source'],
            $data['tag_term'],
            'order'
        );

        $order->proc_status = $procStatus;
        $repeatId = $this->getRepeatID($phone, $data['host']);
        if ($repeatId) {
            $order->proc_status = 4;                            //4-повтор
        }

        $order->repeat_id = $order->proc_status == 4 ? $repeatId : 0;
        $order->proc_campaign = $procCampaign;
        $order->proc_call_id = 0;
        $order->proc_time = time();
        $order->proc_callback_time = 0;
        $order->proc_callback_user = 0;
        $order->proc_priority = 0;
        $order->proc_fails = 0;
        $order->proc_stage = 0;
        $order->target_status = 0;                              //0-нет цели
        $order->target_user = 0;

        $order->tag_source = $tagSourceId ?? 0;
        $order->tag_medium = $tagMediumId ?? 0;
        $order->tag_content = $tagContentId ?? 0;
        $order->tag_term = $tagTermId ?? 0;
        $order->tag_campaign = $tagCampaignId ?? 0;
        //ата модицикации статуса
        $order->time_status_updated = Carbon::now();

        $order->save();

        try {
            //add log with proc_status
//            if(isset($order->procStatus->name)){
//                (new OrdersLog())->addOrderLog($order->id, 'Установлен процессинг статус: "' . $order->procStatus->name, ['status_id' => $order->proc_status, 'status_name' => $order->procStatus->name]);
//            }

            if ($projectId && $subProjectId) {
                list($products, $inputDataProducts) = $this->addOrFindProducts($data['products'], $projectId, $subProjectId);
                //$this->addOrderProducts($order->id, $products);

                if ($inputDataProducts) {
                    $orderInputData = $order->input_data ? $order->input_data : '';
                    $decode = json_decode($orderInputData, true) ?? [];
                    Order::where('id', $order->id)->update(['input_data' => json_encode(array_merge($decode, $inputDataProducts))]);
                }
            }

            //$order->getProcessingStatusOrderApi($order->id);
        } catch (\Exception $exception) {
            \Log::info('-------------' . $exception->getMessage() . '------------');
        }

        return $order->id;
    }

    private function addOrderProducts( $orderId, $data )
    {
        if ($data) {
            foreach ($data as $d) {
                DB::table('order_products')->insert([
                    'order_id' => $orderId,
                    'product_id' => $d['product_id'],
                    'price' => $d['price'],
                ]);
            }
        }
    }

    private function getElastix( $projectId, $country, $offer )
    {
        $companyElastxi = new Campaign;
        $id = $companyElastxi->getElastixCompanyByFilter($projectId, $country, 0, $offer);

        return $id;
    }

    private function addOrFindOffer( $offerId, $partnerId, $name )
    {
        $offer = Offer::firstOrCreate([
            'offer_id' => $offerId,
            'partner_id' => $partnerId,
        ], ['name' => $name]);

        return $offer->id;
    }

    private function firstOrCreateProject( $partnerId, $projectId, $projectName )
    {
        $project = Project::where([
            //   ['partner_id', $partnerId],
            ['project_id', $projectId],
        ])->first();

        return $project;
    }

    private function firstOrCreateSubproject( $partnerId, $projectId, $subprojectId, $name )
    {
        $project = Project::where([
            //  'partner_id' => $partnerId,
            'parent_id' => $projectId,
            'subproject_id' => $subprojectId
        ])->first();

        return $project->id ?? 0;
    }

    /**
     * $tableName = 'tag_source', 'tag_content', 'tag_campaign', 'tag_term', 'tag_medium'
     * @param $tableName
     * @param $projectId
     * @param $name
     * @return mixed
     */
    private function getOrInsertTag( $tagName, $partnerId, $value )
    {
        if (!empty($value)) {
            $tag = Tag::firstOrCreate([
                'name' => $tagName,
                'partner_id' => $partnerId,
                'value' => $value
            ]);
            return $tag->id;
        }
    }

    private function addOrFindProducts( $products, $projectId, $subProjectId )
    {
        $data = [];
        $inputData = [];
        if ($products) {
            foreach ($products as $key => $product) {
                if (isset($product['id']) &&
                    isset($product['name']) &&
                    isset($product['price'])) {
                    $id = DB::table('products as p')
                        ->leftJoin('product_projects as pp', 'p.id', '=', 'pp.product_id')
                        ->where('p.product_id', $product['id'])
                        ->where('pp.project_id', $projectId)
                        ->where('pp.subproject_id', $subProjectId)
                        ->value('p.id');
                    if ($id) {
                        $data[] = [
                            'product_id' => $id,
                            'price' => $product['price'],
                            'comment' => isset($product['comment']) ? $product['comment'] : NULL
                        ];
                    } else {
                        $inputData['products'][] = $product['id'] . '::' . $product['name'] . '(' . $product['price'] . ')' . ' - ' . (isset($product['comment']) ? $product['comment'] : '');
                    }
                }
            }
        } else {
            $inputData['products']['message'] = 'Нет товаров';
        }

        return [$data, $inputData];
    }

    /**
     * Получаем ID повторяющегося заказа
     *
     * @param $phone
     *
     * @param $ip
     *
     * @return int
     */
    private function getRepeatID( $phone, $ip )
    {
        return Order::where('phone', $phone)
            ->where('host', $ip)
//            ->where('time_created', '>', time() - $this->intervalRepeatOrder)
            ->orderBy('id', 'desc')
            ->value('id');
    }

    public function getTargetId(
        $targetType,
        $geo,
        $offerId,
        $projectId,
        $tagCampaign,
        $tagContent,
        $tagMedium,
        $tagSource,
        $tagTerm,
        $entity
    )
    {
        $kEntity = 5;
        $kGeo = 4;
        $kOffer = 3;
        $kProject = 2;
        $kTag = 1;
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
                if ($target->tag_campaign == $tagCampaign) {
                    $sum += $kTag;
                }
                if ($target->tag_content == $tagContent) {
                    $sum += $kTag;
                }
                if ($target->tag_medium == $tagMedium) {
                    $sum += $kTag;
                }
                if ($target->tag_source == $tagSource) {
                    $sum += $kTag;
                }
                if ($target->tag_term == $tagTerm) {
                    $sum += $kTag;
                }
                if ($sum > $max) {
                    $max = $sum;
                    $targetId = $target->id;
                }
            }
        }

        return $targetId;
    }

    protected function modifiedInputData( $inputData, $requestData )
    {
        $data = json_decode($inputData, true);
        $data['input_project_id'] = $requestData['project_id'] ?? 0;
        $data['input_project_name'] = $requestData['project_name'] ?? 0;
        $data['input_sub_project_id'] = $requestData['subproject_id'] ?? 0;
        $data['input_sub_project_name'] = $requestData['subproject_name'] ?? 0;
        if (isset($requestData['products'])) {
            foreach ($requestData['products'] as $product) {
                if (isset($product['id']) &&
                    isset($product['name']) &&
                    isset($product['price'])
                ) {
                    $data['products'][] = $product['id'] . '::' . $product['name'] . '(' . $product['price'] . ')';
                }
            }
        }
        return json_encode($data);
    }
}
    