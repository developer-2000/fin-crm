<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuditOwner extends BaseModel
{
    protected $table = 'audit_owners';

    public function addTime($date = false)
    {
        if ($date) {
            $dateStart = Carbon::parse($date . ' 00:00:00');
            $dateEnd = Carbon::parse($date . ' 23:59:59');
        } else {
            $dateStart = Carbon::parse('now 00:00:00');
            $dateEnd = Carbon::parse('now 23:59:59');
        }
        $audit = $this->apiElastixProcessing2('getAudit', [
            'date' => $dateStart->format('Y-m-d'),
        ]);

        if ($audit->status == 200) {
            foreach ($audit->data as $a) {

                $userId = DB::table('users')->where('login_sip', $a->id)->value('id');
                if(isset($userId)){
                    DB::insert('
                    INSERT INTO report_time (
                    `user_id`,
                    `login_time_elastix`,
                    `talk_time`,
                    `pause_time`,
                    `order_time`,
                    `date`
                    ) VALUES (?,?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE
                    `login_time_elastix` = ?,
                    `talk_time` = ?,
                    `pause_time` = ?,
                    `order_time` = ?', [
                        $userId,
                        $a->onlineTime,
                        $a->talkTime,
                        $a->pause,
                        $a->order,
                        $dateStart,
                        $a->onlineTime,
                        $a->talkTime,
                        $a->pause,
                        $a->order
                    ]);
                }

            }
        }
        $timeCrm = DB::table('online')->where(function ($query) use ($dateStart, $dateEnd) {
            $query->where('date_start', '>=', $dateStart)
                ->where('date_start', '<=', $dateEnd);
        })->orWhere(function ($query) use ($dateStart, $dateEnd) {
            $query->where('date_end', '>=', $dateStart)
                ->where('date_end', '<=', $dateEnd);
        })->get();
        $timeArray = [];
        if ($timeCrm) {
            foreach ($timeCrm as $tc) {
                if (!isset($timeArray[$tc->user_id])) {
                    $timeArray[$tc->user_id] = 0;
                }
                $durationStart = $tc->date_start;
                $durationEnd = $tc->date_end;
                if ($tc->date_start < $dateStart) {
                    $durationStart = $dateStart;
                } elseif ($tc->date_end > $dateEnd) {
                    $durationEnd = $dateStart;
                }
                $timeArray[$tc->user_id] += Carbon::parse($durationEnd)->timestamp - Carbon::parse($durationStart)->timestamp;
            }
            foreach ($timeArray as $keyTa => $valueTa) {
                DB::insert('
                INSERT INTO report_time (
                    `user_id`,
                    `login_time_crm`,
                    `date`
                ) VALUES (?,?,?)
                ON DUPLICATE KEY UPDATE
                  `login_time_crm` = ?', [
                    $keyTa,
                    $valueTa,
                    $dateStart,
                    $valueTa
                ]);
            }
        }
    }
}
