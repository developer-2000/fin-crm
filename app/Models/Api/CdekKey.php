<?php

namespace App\Models\Api;

use App\Models\TargetValue;
use App\Models\ViettelSender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;
use App\Models\TargetConfig;

class CdekKey extends Model
{
    protected $fillable = ['target_id', 'active', 'name', 'subproject_id', 'account', 'pass'];
    protected $table = 'cdek_keys';

    /**
     * get subproject
     * @return BelongsTo;
     */
    public function subProject()
    {
        return $this->belongsTo(Project::class, 'subproject_id');
    }

    public function senders()
    {
        return $this->hasMany(ViettelSender::class);
    }

    public function target()
    {
        return $this->belongsTo(TargetConfig::class, 'target_id');
    }

    public function targetValue()
    {
        return $this->hasMany(TargetValue::class, 'sender_id');
    }

    public static function createAccount($request)
    {
        $target = TargetConfig::where('alias', 'cdek')->first();
        if ($request) {
            $viettelKey = CdekKey::create([
                'target_id'     => !empty($target) ? $target->id : 0,
                'subproject_id' => $request->sub_project_id,
                'active'        => 0,
                'name'          => $request->name,
                'account'       => $request->account,
                'secure'        => $request->secure,
            ]);

            if ($viettelKey) {
                $html = view('integrations.cdek.accounts-table', ['keys' => CdekKey::all()])->render();
                return ['success' => true, 'html' => $html];
            }
        } else {
            $error = ['error' => true];
            return $error;
        }
    }
}
