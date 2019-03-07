<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class SessionAudit extends BaseModel
{
    protected $table = 'session_audit';

    public function setEvent($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function getEventsUserLastTime($userId, $minutes)
    {
        $time = Carbon::now()->subMinute($minutes);
        return DB::table($this->table)
            ->where('user_id', $userId)
            ->where('datetime', '>', $time)
            ->get();
    }
}