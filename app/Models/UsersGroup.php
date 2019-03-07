<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use App\Models\Pagination;
use App\Models\Role;

class UsersGroup extends BaseModel
{
    protected $table = 'users_group';

    public function searchGroupsByWord($term)
    {
        $groups = DB::table('users_group')
            ->select('id', 'name')
            ->where('name', 'LIKE', '%' . $term . '%')->get();
        return $groups;
    }

    public function updateCpl()
    {
        try {
            $wrongOrders = $this->apiElastixProcessing2('getWrongCalls');
            $ordersArray = [];
            foreach ($wrongOrders->data as $item) {
                if ($item->uniqueid) {
                    $uniqueIds[] = $item->uniqueid;
                    $ordersArray[] = $item->crm_id;
                }
            }

            $cpls = CallProgressLog::whereIn('unique_id', $uniqueIds)->pluck('order_id')->toArray();



        } catch (\Exception $exception) {
            echo $exception;
        }
    }

}
