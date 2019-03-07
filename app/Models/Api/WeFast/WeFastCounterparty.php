<?php

namespace App\Models\Api\WeFast;

use App\Http\Requests\Request;
use App\Models\Project;
use App\Models\TargetConfig;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class WeFastCounterparty extends Model
{
    protected $table = 'wefast_counterparties';

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeCheckSubProject($query)
    {
        if (Auth::user()->sub_project_id) {
            $query->where('sub_project_id', Auth::user()->sub_project_id);
        }

        return $query;
    }

    public function scopeOrderSubProject($query, $order)
    {
        if ($order && $order->subproject_id) {
            $query->where('sub_project_id', $order->subproject_id);
        }

        return $query;
    }

    public function key()
    {
        return $this->belongsTo(WeFastKey::class, 'key_id');
    }

    public function subProject()
    {
        return $this->belongsTo(Project::class,'sub_project_id');
    }

    public function setValues(Request $request)
    {
        $this->key_id = $request->key;
        $this->sub_project_id = Auth::user()->sub_project_id ? Auth::user()->sub_project_id : $request->sub_project_id;
        $this->sender = $request->sender;
        $this->contact = $request->contact;
        $this->phone = $request->phone;
        $this->province_code = $request->province;
        $this->district_code = $request->district;
        $this->ward_code = $request->ward;
        $this->address = $request->address;
        $this->warehouse = $request->warehouse;
        $this->active = (bool)$request->active;
    }
}
