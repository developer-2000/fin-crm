<?php

namespace App\Models\Api\Measoft;

use App\Models\Model;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class MeasoftSender extends Model
{
    protected $table = 'measoft_senders';

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

}