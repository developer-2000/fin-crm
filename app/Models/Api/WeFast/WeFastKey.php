<?php

namespace App\Models\Api\WeFast;

use App\Models\Project;
use App\Models\TargetConfig;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class WeFastKey extends Model
{
    protected $table = 'wefast_keys';

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

    public function target()
    {
        return $this->belongsTo(TargetConfig::class, 'target_id');
    }

    public function counterparties()
    {
        return $this->hasMany(WeFastCounterparty::class, 'key_id');
    }

    public function subProject()
    {
        return $this->belongsTo(Project::class, 'sub_project_id');
    }

    public static function findKey($find, $subProjectId = 0)
    {
        $result = [];

        if (!$subProjectId) {
            return $result;
        }

        $data = self::where('name', 'like', '%' . $find . '%')
            ->where('sub_project_id', $subProjectId)
            ->get();

        if ($data->isNotEmpty()) {
            foreach ($data as $datum) {
                $result[] = [
                    'id' => $datum->id,
                    'text' => $datum->name,
                ];
            }
        }

        return $result;
    }
}
