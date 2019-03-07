<?php

namespace App\Models;

use App\IntegrationCodeStatus;
use App\Models\Api\Ninjaxpress\NinjaxpressKey;
use App\Models\Api\CdekKey;
use App\Models\Api\NovaposhtaKey;
use App\Models\Api\Russianpost\RussianpostSender;
use App\Models\Api\ViettelKey;
use App\Models\Api\WeFast\WeFastKey;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use App\Models\UserCalls;
use Psy\Util\Json;
use App\Models\Api\Kazpost\KazpostSender;

class TargetConfig extends BaseModel
{
    protected $table = 'target_configs';

    public $timestamps = false;

    const INTEGRATION_ACTIVE = 'active';
    const INTEGRATION_INACTIVE = 'inactive';

    public function getTargetValues()
    {
        return $this->hasMany(TargetValue::class, 'target_id');
    }

    public function getOrdersByApprove()
    {
        return $this->hasMany(Order::class, 'target_approve');
    }

    public function getOrdersByCancel()
    {
        return $this->hasMany(Order::class, 'target_cancel');
    }

    public function getOrdersByRefuse()
    {
        return $this->hasMany(Order::class, 'target_refuse');
    }

    public function integrationKeys()
    {
        return $this->hasMany(NovaposhtaKey::class, 'target_id');
    }

    public function wefastKeys()
    {
        return $this->hasMany(WeFastKey::class, 'target_id');
    }

    public function viettelKeys()
    {
        return $this->hasMany(ViettelKey::class, 'target_id');
    }

    public function ninjaxpressKeys()
    {
        return $this->hasMany(NinjaxpressKey::class, 'target_id');
    }

    public function cdekKeys()
    {
        return $this->hasMany(CdekKey::class, 'target_id');
    }

    public function kazpostSenders()
    {
        return $this->hasMany(KazpostSender::class);
    }

    public function russianpostSenders()
    {
        return $this->hasMany(RussianpostSender::class);
    }

   public function integrationCodeStatus()
    {
        return $this->hasMany(IntegrationCodeStatus::class, 'integration_id');
    }

    public function scopeIntegration($query)
    {
        return $query->where('integration', 1);
    }

    public function addData($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateDataTarget($data, $targetId)
    {
        return DB::table($this->table)->where('id', $targetId)->update($data);
    }

    /**
     * @param $targetType : approve, refuse, cancel
     * @param Order $order
     */
    public static function getConfigsByTarget($targetType, $order = false)
    {
        $result = [];
        $targets =  TargetConfig::where('target_type', $targetType)
            ->where('active', 1)
            ->get();
        if ($targets) {
            if ($order) {
                foreach ($targets as $target) {
                    try {
                        if ($target->entity && $target->entity == $order->entity) {
                            $result[$target->id] = $target;
                        } else if ($target->filter_geo && $target->filter_geo == $order->geo) {
                            $result[$target->id] = $target;
                        } else if ($target->filter_offer && $target->filter_offer == $order->offer) {
                            $result[$target->id] = $target;
                        } else if ($target->filter_project && $target->filter_project == $order->project_id) {
                            $result[$target->id] = $target;
                        } else if (!$target->filter_project && !$target->filter_offer && !$target->filter_geo && !$target->entity) {
                            $result[$target->id] = $target;
                        }
                    } catch (\Exception $exception) {
                        continue;
                    }
                }
            } else {
                $result = $targets;
            }
        }

        return $result;
    }

    public function getAllTargets()
    {
        return DB::table($this->table . ' AS t')
            ->select('t.id', 't.name', 't.alias', 't.entity', 't.target_type', 't.filter_geo', 'o.name as offer_name', 'p.name AS project',
                't.tag_campaign', 't.tag_content', 't.tag_medium', 't.tag_source', 't.tag_term')
            ->leftJoin('offers AS o', 'o.id', '=', 't.filter_offer')
            ->leftJoin('projects AS p', 'p.id', '=', 't.filter_project')
            ->get();
    }

    public static function findTarget($findName, $countryCode = [])
    {
        $query = self::select('id', 'name')
            ->where('name', 'like', '%' . $findName . '%');

        if ($countryCode) {
            if (is_array($countryCode)) {
                $query->whereIn('filter_geo', $countryCode);
            } else {
                $query->where('filter_geo', $countryCode);
            }
        }
        $targetConfigs = $query->get();

        if (!$targetConfigs->count()) {
            $targetConfigs = self::select('id', 'name')
                ->where('name', 'like', '%' . $findName . '%')->where('filter_geo','')->where('target_type', 'approve')->get();

        }

        return $targetConfigs;
    }

}


