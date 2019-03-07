<?php

namespace App\Models\Api\Kazpost;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Model,
    Project,
    TargetConfig
};

class KazpostSender extends Model
{
    public $table = 'kazpost_senders';

    public function target()
    {
        return $this->belongsTo(TargetConfig::class);
    }

    public function subProject()
    {
        return $this->belongsTo(Project::class, 'sub_project_id');
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

    public function setData(Request $request)
    {
        $this->target_id = TargetConfig::where('alias', 'kazpost')->value('id');
        $this->sub_project_id = Auth::user()->sub_project_id ? Auth::user()->sub_project_id : $request->sub_project_id;
        $this->name_last = $request->name_last;
        $this->name_fm = $request->name_fm;
        $this->city = $request->city;
        $this->address = $request->address;
        $this->index = $request->index;
        $this->code = $request->code;
        $this->doc = $request->doc;
        $this->doc_num = $request->doc_num;
        $this->doc_day = $request->doc_day;
        $this->doc_month = $request->doc_month;
        $this->doc_year = $request->doc_year;
        $this->doc_body = $request->doc_body;
        $this->payment_code = $request->payment_code;
        $this->document = $request->document;
        $this->support_phone = $request->support_phone;
    }
}