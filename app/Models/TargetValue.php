<?php

namespace App\Models;

use App\Models\Api\ViettelKey;
use App\Models\Api\CdekKey;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\UserCalls;
use App\Models\Api\Posts\Novaposhta;
use GuzzleHttp\Client as GuzzleHttpClient;

class TargetValue extends BaseModel
{
    protected $table = 'target_values';

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTargetConfig()
    {
        return $this->belongsTo(TargetConfig::class, 'target_id', 'id');
    }

    /**
     * get novaposhtaTracking
     * @return HasMany
     */
    public function novaposhtaTracks()
    {
        return $this->hasMany(Tracking::class, 'order_id', 'order_id');
    }

    public function addData($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function getTargetValue($orderId)
    {
        return self::where('order_id', $orderId)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function viettelKey()
    {
        return $this->belongsTo(ViettelKey::class, 'sender_id','id');
    }

    public function cdekKey()
    {
        return $this->belongsTo(CdekKey::class, 'sender_id','id');
    }

    public function deleteValue($orderId)
    {
        return DB::table($this->table)->where('order_id', $orderId)->delete();
    }

    public function getValuesForApi($orderId)
    {
        $targets = [];
        $source = [];

        $target = $this::where('order_id', $orderId)->first();

        if ($target) {
            try {
                $fields = json_decode($target->values);
                $source['name'] = $target->getTargetConfig->alias;
                $targets['name'] = $target->getTargetConfig->name;
                $functionName = 'getData' . mb_strtoupper($target->getTargetConfig->alias[0]) . mb_substr($target->getTargetConfig->alias, 1);
                if (method_exists($this, $functionName)) {
                    $targets = array_merge($this->$functionName($fields), $targets);
                } else {
                    foreach ($fields as $field) {
                        $targets[$field->field_name] = $this->getValueField($field);
                    }
                }
                foreach ($fields as $field) {
                    if (is_object($field->field_value)) {//для checkbox
                        foreach ($field->field_value AS $value) {
                            $source[$field->field_name][] = $value;
                        }
                    } else {
                        $source[$field->field_name] = $field->field_value;
                    }
                }

            } catch (\Exception $exception) {
                return [$targets, $source];
            }
        }

        return [$targets, $source];
    }

    protected function getDataNovaposhta($fields)
    {
        $result = [];

        if ($fields) {
//            foreach ($fields as $field) {
//                switch ($field->field_name) {
//                    case 'warehouse' :
//                        {
//                            $client = new GuzzleHttpClient();
//                            $response = $client->request('POST', Novaposhta::API, [
//                                'json' => [
//                                    "apiKey"           => Novaposhta::KEY,
//                                    "modelName"        => "AddressGeneral",
//                                    "calledMethod"     => "getWarehouses",
//                                    "Page"             => "1",
//                                    "methodProperties" => [
//                                        "Language"      => "ru",
//                                        "SettlementRef" => "",
//                                        "Ref"           => !empty($field->field_value) ? $field->field_value : "",
//                                    ]
//                                ]]);
//
//                            $getWarehouses = json_decode($response->getBody()->getContents());
//                            $warehouse= [];
//                            if(!empty($getWarehouses->data)){
//                                foreach ($getWarehouses->data as $item) {
//                                    $warehouse[] = $item->Description;
//                                }
//
//                                $result[$field->field_name] = !empty($warehouse[0]) ? $warehouse[0] : "";
//                            }else{
//                                $result[$field->field_name] = "";
//                            }
//
//                            break;
//                        }
//                    case 'city' :
//                        {
//                            $client = new GuzzleHttpClient();
//                            $response = $client->request('POST', Novaposhta::API, [
//                                'json' => [
//                                    "apiKey" => Novaposhta::KEY,
//                                    "modelName" => "AddressGeneral",
//                                    "calledMethod" => "getSettlements",
//                                    "Page" => "1",
//                                    "methodProperties" => [
//                                        "Ref" =>  !empty($field->field_value) ? $field->field_value : "",
//                                        "Page" => "1",
//                                        "Warehouse" => "1",
//                                    ]
//                                ]]);
//
//
//                            $getSettlement = json_decode($response->getBody()->getContents());
//                            $settlement = [];
//                            if(!empty($getSettlement->data)) {
//                                foreach ($getSettlement->data as $item) {
//                                    if ($item->RegionsDescription) {
//                                        $settlement[] =
//                                            $item->Description . ', ' . $item->RegionsDescription . ', ' . ' (' . $item->SettlementTypeDescription . ')';
//                                    } else {
//                                        $settlement[] = $item->Description . ', ' . ' (' . $item->SettlementTypeDescription . ')';
//                                    }
//
//                                }
//                                $result[$field->field_name] =  !empty($settlement[0]) ? $settlement[0] : "";
//                            }else{
//                                $result[$field->field_name] =  "";
//                            }
//                        }
//                }
//            }
            foreach ($fields as $field) {
                switch ($field->field_name) {
                    case 'warehouse' :
                        {
                            $result[$field->field_name] = NP::where('wid', $field->field_value)
                                ->value('whs_address_ru');
                            break;
                        }
                    case 'city' :
                        {
                            $result[$field->field_name] = NP::where('cid', $field->field_value)->value('city_ru');
                        }
                }
            }
        }

        return $result;
    }

    protected function getValueField($field)
    {
        $val = $field->field_value;
        if (is_object($val)) {//для checkbox
            $values = $val;
            $val = [];
            foreach ($values as $v) {
                if (isset($field->options->$v)) {
                    $val[] = $field->options->$v;
                } else {
                    $val[] = $v;
                }
            }
        } else {
            if (isset($field->options->$val)) {
                $val = $field->options->$val;
            }
        }

        return $val;
    }

    public function setTargetAsRepeat($orderIds)
    {
        if ($orderIds) {
            foreach ($orderIds as $id) {
                $jsonOption = DB::table('orders AS o')->select('o.target_cancel', 'tc.options')
                    ->leftJoin('target_configs AS tc', 'tc.id', '=', 'o.target_cancel')->where('o.id', $id)->first();
                if ($jsonOption->options) {
                    $options = json_decode($jsonOption->options, true);
                    $options['cause']['field_value'] = 5;
                    DB::table($this->table)->insert([
                        'order_id'  => $id,
                        'target_id' => $jsonOption->target_cancel,
                        'values'    => json_encode($options)
                    ]);
                }
            }
            return true;
        }
        return false;
    }

    public static function setTargetValues(TargetConfig $target, $values, $orderId)
    {
        $targetConfig = json_decode($target->options, true);

        $targetValue = [];
        if ($targetConfig) {
            foreach ($targetConfig as $targetField) {
                if (isset($values[$targetField['field_name']])) {
                    $targetField['field_value'] = $values[$targetField['field_name']];
                }
                $targetValue[$targetField['field_name']] = $targetField;
            }
        }

        return DB::table('target_values')->insert([
            'target_id'    => $target->id,
            'order_id'     => $orderId,
            'values'       => json_encode($targetValue),
            'time_created' => time()
        ]);
    }

    public static function updateData($orderId, $data)
    {
        return self::where('order_id', $orderId)->update($data);
    }
    public static function findTrack($term)
    {
        $tracks = DB::table('target_values')
            ->select('track')
            ->where('track', 'LIKE', '%' . $term . '%');
        return $tracks->get();
    }

    public function setTrack($track)
    {
        $values = json_decode($this->values, true);
        $log = 'Создан Track = "' . $track. '"';
        if (!$track) {
            $log = 'Track = "' . $this->track . '" был удален';
        }
        $this->track = $track;

        if (isset($values['track'])) {
            $values['track']['field_value'] = $this->track;
        }
        $this->values = json_encode($values);
        $this->save();
        (new OrdersLog())->addOrderLog($this->order_id, $log);
    }
    public function setData(array $data)
    {
        if ($data) {
            $jsonValues = json_decode($this->values, true);
            foreach ($data as $property => $value) {
                if (method_exists(static::class, 'set' . title_case($property))) {
                    $method = 'set' . title_case($property);
                    $this->$method($value);
                    continue;
                }
                if (isset($this->$property)) {
                    $this->$property = $value;
                }
                if (isset($jsonValues[$property])) {
                    $jsonValues[$property]['field_value'] = $value;
                }
            }
            $this->values = json_encode($jsonValues);
        }

        return false;
    }
}
