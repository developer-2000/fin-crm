<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class UsersTime extends BaseModel
{
    protected $table = 'users_time';

    public function setTime($data, $userId, $type)
    {
        $timeStart = Carbon::parse('now 00:00:00');
        $timeEnd = Carbon::parse('now 23:59:59');

        $oneTime = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('datetime_start',[$timeStart, $timeEnd])
            ->whereNull('datetime_end')
            ->first();

        if (isset($data['datetime_end']) && $oneTime) {
            $data['duration'] = Carbon::parse($data['datetime_end'])->timestamp - Carbon::parse($oneTime->datetime_start)->timestamp;
            if ($data['duration'] < 0) {
                $lastTime = DB::table($this->table)
                    ->where('user_id', $userId)
                    ->where('type', $type)
                    ->orderBy('datetime_end', 'desc')
                    ->limit(1)
                    ->first();
                $data['duration'] = Carbon::parse($data['datetime_end'])->timestamp - Carbon::parse($lastTime->datetime_start)->timestamp;

                DB::table($this->table)
                    ->where('user_id', $userId)
                    ->where('type', $type)
                    ->where('datetime_start', '>', $data['datetime_end'])
                    ->delete();

                return DB::table($this->table)
                    ->where('user_id', $userId)
                    ->where('type', $type)
                    ->orderBy('datetime_end', 'desc')
                    ->limit(1)
                    ->update($data);

            }

        }

        return DB::table($this->table)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('datetime_start',[$timeStart, $timeEnd])
            ->whereNull('datetime_end')
            ->update($data);
    }

    public function setDataTime($userId , $data, $type)
    {
        return DB::table($this->table)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->whereNull('datetime_end')
            ->update($data);
    }

    public function addTime($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function getUserTime($userId, $type = null, $end = null)
    {
        $time = DB::table($this->table)
            ->where('user_id', $userId);
        if ($type) {
            $time = $time->where('type', $type);
        }
        if (!$end) {
            $time = $time->whereNull('datetime_end');
        }
        return $time->get();
    }

    public function getAllUsersTime($type = null)
    {
        $time = DB::table($this->table);
        if ($type) {
            $time = $time->where('type', $type);
        }
        return $time->get();
    }

    public function getUserWithoutTimeEnd($userId = null, $type = null, $today = false)
    {
        $time = DB::table($this->table)
            ->whereNull('datetime_end');

        if ($userId) {
            $time = $time->where('user_id', $userId);
        }

        if ($type) {
            $time = $time->where('type', $type);
        }

        if ($today) {
            $timeStart = Carbon::parse('now 00:00:00');
            $timeEnd = Carbon::parse('now 23:59:59');
            $time = $time->whereBetween('datetime_start', [$timeStart, $timeEnd]);
        }

        return $time->get();
    }

    /**
     * @param $limit is sec
     * @return mixed
     */
    public function getCheckUsers($limit, $type = null)
    {
        $result = DB::table($this->table . ' AS t')
            ->select('t.user_id')
            ->leftJoin('sessions AS s', 's.user_id', '=', 't.user_id')
            ->where('s.last_activity', '<=', time() - $limit * 60);
        if ($type) {
            $result = $result->where('t.type', $type);
        }
        return $result->get();
    }

    public function getOnlineUsers($limit, $type = null)
    {
        $result = DB::table($this->table . ' AS t')
            ->select('t.user_id')
            ->leftJoin('sessions AS s', 's.user_id', '=', 't.user_id')
            ->where('s.last_activity', '>', time() - $limit * 60);
        if ($type) {
            $result = $result->where('t.type', $type);
        }
        return $result->get();
    }

    public function getAllTime($filter)
    {
        $timeStart = $filter['date_start'] ? Carbon::parse($filter['date_start']) : Carbon::parse('now 00:00:00');
        $timeEnd = $filter['date_end'] ? Carbon::parse($filter['date_end'])->endOfDay() : Carbon::parse('now 23:59:59');

        if ($filter['detail']) {
            $select = [
                't.user_id',
                'u.name',
                'u.surname',
                't.type',
                'datetime_start AS min',
                'datetime_end AS max',
                'duration AS duration'
            ];
        } else {
            $select = [
                't.user_id',
                'u.name',
                'u.surname',
                't.type',
                DB::raw('MIN(datetime_start) AS min'),
                DB::raw('MAX(datetime_end) AS max'),
                DB::raw('SUM(duration) AS duration')
            ];
        }

        $time = DB::table($this->table . ' AS t')
            ->select($select)
            ->leftJoin('users AS u', 'u.id', '=', 't.user_id')
            ->whereBetween('t.datetime_start', [$timeStart, $timeEnd]);

        if (auth()->user()->company_id) {
            $time = $time->where('u.company_id', auth()->user()->company_id);
        }

        if ($filter['id']) {
            $time = $time->where('t.user_id', 'like', $filter['id'] . '%');
        }

        if ($filter['surname']) {
            $time = $time->where('u.surname', 'like', $filter['surname'] . '%');
        }

        if ($filter['name']) {
            $time = $time->where('u.name', 'like', $filter['name'] . '%');
        }

        if ($filter['detail']) {
            $time = $time->groupBy('t.datetime_start');
        } else {
            $time = $time->groupBy('t.user_id');
        }

        $time = $time
            ->orderBy('datetime_start', 'desc')
            ->groupBy('t.type')
            ->get();
        $userID = [];
        $result = [];
        if ($filter['detail']) {
            if ($time) {
                foreach ($time as $userTime) {
                    $userID[] = $userTime->user_id;
                    if (empty($result[$userTime->min])) {
                        $result[$userTime->min] = $userTime;
                        $result[$userTime->min]->talkTime = 0;
                        $result[$userTime->min]->time_crm = 0;
                        $result[$userTime->min]->time_pbx = 0;
                    }

                    if ($userTime->type == 'crm') {
                        $result[$userTime->min]->time_crm = $userTime->duration;
                    }

                    if ($userTime->type == 'pbx' ) {
                        $result[$userTime->min]->time_pbx = $userTime->duration;
                    }

                    if ($userTime->max) {
                        $talkTime = DB::table('call_progress_log')
                            ->select('user_id', DB::raw('SUM(talk_time) AS talkTime'))
                            ->whereIn('user_id', $userID)
                            ->groupBy('user_id')
                            ->whereBetween('date', [ $userTime->min, $userTime->max])
                            ->first();
                        if ($talkTime) {
                            $result[$userTime->min]->talkTime = $talkTime->talkTime;
                        }
                    }
                }
            }
        } else {
            if ($time) {
                foreach ($time as $userTime) {
                    $userID[] = $userTime->user_id;
                    if (empty($result[$userTime->user_id])) {
                        $result[$userTime->user_id] = $userTime;
                        $result[$userTime->user_id]->talkTime = 0;
                        $result[$userTime->user_id]->time_crm = 0;
                        $result[$userTime->user_id]->time_pbx = 0;
                    }

                    if ($userTime->type == 'crm') {
                        $result[$userTime->user_id]->time_crm = $userTime->duration;
                    }

                    if ($userTime->type == 'pbx' ) {
                        $result[$userTime->user_id]->time_pbx = $userTime->duration;
                    }

                }
            }

            $talkTime = collect(DB::table('call_progress_log')
                ->select('user_id', DB::raw('SUM(talk_time) AS talkTime'))
                ->whereIn('user_id', $userID)
                ->groupBy('user_id')
                ->whereBetween('date', [$timeStart, $timeEnd])
                ->get())->keyBy('user_id');

            if ($result) {
                foreach ($result as &$user) {
                    if (isset($talkTime[$user->user_id])) {
                        $user->talkTime = $talkTime[$user->user_id]->talkTime;
                    }
                }
            }
        }

        $sessionModel = new Sessions();
        $userOnline = [];
        if ($userID) {
            if (Carbon::parse('now 00:00:00') <= $timeStart) {
                foreach ($userID as $id) {
                    $online = $sessionModel->getAllSessionOneUser($id);
                    if ($online) {
                        $userOnline[$id] = $id;
                    }
                }
            }
        }

        if ($result) {
            foreach ($result as &$res) {
                if (isset($userOnline[$res->user_id])) {
                    $res->online = true;
                } else {
                    $res->online = false;
                }
            }
        }


        return $result;
    }

    public function getUsersByCompany($yesterday = false)
    {
        $result = DB::table($this->table . ' AS t')
            ->select('t.user_id', 't.duration', 't.type', 'c.type AS company_type', 'c.billing_type', 'u.company_id',
                'c.prices', 'c.billing')
            ->leftJoin('users AS u', 'u.id', '=', 't.user_id')
            ->leftJoin('companies AS c', 'c.id', '=', 'u.company_id')
            ->where(function ($query) {
                $query->where('c.type', 'hour')
                    ->orWhere('c.billing_type', 'hour');
            });

        if ($yesterday) {
            $dayStart = Carbon::yesterday()->subHour(0);
            $dayFinish = Carbon::today()->subHour(0);
            $result = $result->whereBetween('t.datetime_start', [$dayStart, $dayFinish]);
        }

        return $result->get();
    }

}