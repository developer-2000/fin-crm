<?php

namespace App\Models\Api\Russianpost;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Model,
    Project,
    TargetConfig
};

class RussianpostSender extends Model
{
    public $table = 'russianpost_senders';

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
        $this->target_id = TargetConfig::where('alias', 'russianpost')->value('id');
        $this->sub_project_id = Auth::user()->sub_project_id ? Auth::user()->sub_project_id : $request->sub_project_id;
        $this->name_first = $request->name_first;
        $this->name_last = $request->name_last;
        $this->name_middle = $request->name_middle;
        $this->city = $request->city;
        $this->address = $request->address;
        $this->index = $request->index;
    }
}