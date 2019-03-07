<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class ProcStatus extends Model
{
    protected $table = 'proc_statuses';

    protected $fillable = [
        'project_id',
        'name',
        'locked',
        'type',
        'parent_id'
    ];

    const LOCKED = 1;
    const NOT_LOCKED = 0;

    const TYPE_CALL_CENTER = 'call_center';
    const TYPE_SENDERS = 'senders';

    public function orders()
    {
        return $this->hasMany(Order::class, 'proc_status');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function subStatuses()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function scopeCallCenterStatuses($query)
    {
        return $query->where('type', self::TYPE_CALL_CENTER)
            ->status();
    }

    public function scopeSenderStatuses($query)
    {
        return $query->where('type', self::TYPE_SENDERS)
            ->status();
    }

    public function scopeStatus($query)
    {
        return $query->where('parent_id', 0);
    }

    public function scopeProjectQuery($query)
    {
        return $query->where('project_id', Auth::user()->project_id);
    }

    public function scopeCheckProject($query)
    {
        if (Auth::user()->project_id) {
            $query->projectQuery();
        } else {
            $query->where('project_id', '=', 0);
        }

        return $query;
    }

    public function scopeStatusesByProject($query)
    {
        $query->where('project_id', '>', 0);
        return $query;
    }

    public function scopeSystemStatuses($query)
    {
        $query->where('project_id', 0);
        return $query;
    }

    public function scopeStatusesUser($query)
    {
        return $query->where(function ($query) {
            $query->checkProject()
                ->orWhere('project_id', 0);
        });
    }

    public static function getAllStatuses()
    {
        return self::with('project', 'subStatuses')
            ->status()
            ->statusesUser()
            ->paginate(50);
    }

    public static function getAllStatusesForOrder($projectId)
    {
        $statuses = self::with('subStatuses')
            ->where('project_id', $projectId)
            ->where('type', self::TYPE_SENDERS)
            ->where('parent_id', 0)
            ->get();

        if ($statuses->isEmpty()) {
            $statuses = self::with('subStatuses')
                ->where('type', self::TYPE_SENDERS)
                ->where('project_id', 0)
                ->where('parent_id', 0)
                ->get();
        }

        return $statuses;
    }

    public static function updateProjectIdSubStatuses(ProcStatus $status)
    {
        return self::where('parent_id', $status->id)
            ->update(['project_id' => $status->project_id]);
    }

    public static function deleteStatus($id)
    {
        $status = self::where('id', $id);


        if (Auth::user()->project_id) {
            $status->where('project_id', Auth::user()->project_id);
        }

        $status = $status->first();

        $orders = $status->orders;

        if ($orders->isNotEmpty() || $status->locked) {
            return false;
        }

        $res = $status->delete();

        self::where('parent_id', $id)->delete();

        return $res;
    }

    public static function editStatus($statusId, $fieldName, $value)
    {
        $result = [
            'success' => false,
            'message' => false,
        ];

        $status = self::find($statusId);

        if ($fieldName == 'project') {
            $project = Project::find($value);

            if (!$project) {
                $result['message'] = 'Проект не найден';
            }

            $status->project_id = $value;
        } elseif ($fieldName == 'name') {
            $status->name = $value;
        } elseif ($fieldName == 'color') {
            $status->color = $value;
        }

        if (!$result['message']) {
            $result['success'] = $status->save();

            self::updateProjectIdSubStatuses($status);
        }

        return $result;
    }

    public static function createSubStatus($subStatuses, ProcStatus $parent)
    {
        if ($subStatuses) {
            $data = [];
            foreach ($subStatuses as $status) {
                if ($status) {
                    $data[] = [
                        'project_id' => $parent->project_id,
                        'name'       => $status,
                        'locked'     => $parent->locked,
                        'type'       => $parent->type,
                        'parent_id'  => $parent->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            self::insert($data);
        }
    }
}
