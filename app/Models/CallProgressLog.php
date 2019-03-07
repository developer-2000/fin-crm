<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CallProgressLog extends BaseModel
{
    protected $table = 'call_progress_log';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Добавляем звонок в лог
     * @param int $orderId ID заказа
     * @param string $status Статус звонка
     */
    function addCallProgressLog($orderId, $status, $file, $userId, $talkTime, $trunk, $startTime, $uniqueId, $entity)
    {

        return DB::table($this->table)->insertGetId([
            'order_id'   => $orderId,
            'user_id'    => $userId,
            'status'     => $status,
            'file'       => $file,
            'talk_time'  => $talkTime,
            'trunk'      => $trunk,
            'start_time' => date('Y-m-d H:i:s', $startTime),//todo проверить
            'date'       => now(),
            'unique_id'  => $uniqueId,
            'entity'     => $entity
        ]);
    }

    /**
     * Получаем звонки по одному заказу
     * @param int $orderId ID заказа
     * @return object
     */
    function getCallProgressLogById($orderId, $entity)
    {
        return DB::table($this->table . ' AS ca')
            ->select('ca.status', 'ca.file', 'u.name', 'u.surname', 'u.login', 'ca.date', 'ca.talk_time', 'ca.trunk', 'c.name AS company')
            ->leftJoin('users AS u', 'ca.user_id', '=', 'u.id')
            ->leftJoin('companies AS c', 'c.id', '=', 'u.company_id')
            ->where('ca.order_id', $orderId)
            ->where('entity', $entity)
            ->get();
    }

    public function getCallByName($name)
    {
        $data = $this->getRequestToTheUrl();
        $url = 'https://pbx.badvps.com/recordings.php?key=gxo3v1rxonx85e7&file=' . substr($name, 0, -3) . 'mp3';
        $file = file_get_contents($url, false, $data);
        header("Content-Disposition: attachment; filename=" . substr($name, 0, -3) . 'mp3');
        header("Content-type: application/octet-stream");
        header('Accept-Ranges: bytes');
        header('Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3, audio/mp3, application/octet-stream');
        header('Cache-Control: no-cache');
        header('Content-Transfer-Encoding: chunked');
        return $file;
    }

    public function getTalkTimeByOperator($userId = false, $yesterday = false)
    {
        //todo проверить
        $result = DB::table($this->table)
            ->select(
                'user_id',
                DB::raw('SUM(talk_time) AS talk_time')
            );
        if ($userId) {
            $result = $result->where('user_id', $userId);
        }
        if ($yesterday) {
            $dayStart = Carbon::yesterday()->subHour(0);
            $dayFinish = Carbon::today()->subHour(0);
            $result = $result->whereBetween('date', [$dayStart, $dayFinish]);
        }

        return $result->groupBy('user_id')
            ->get();
    }

    public function getTrunks()
    {
        return DB::table($this->table)
            ->select(DB::raw("DISTINCT(trunk) as trunk"))
            ->whereNotNull('trunk')
            ->pluck('trunk');
    }

    public function getAccountTalkTime($filter)
    {
        switch ($filter['group']) {
            case 'company' :
                {
                    $field = 'c.name';
                    break;
                }
            case 'trunk' :
                {
                    $field = 'cpl.trunk';
                    break;
                }
            case 'user' :
                {
                    $field = 'cpl.user_id';
                    break;
                }
            case 'country' :
                {
                    $field = 'co.name';
                    break;
                }
            default :
                {
                    $field = 'c.name';
                }
        }
        $result = $this->getAccountTalkTimeByField($filter, $field);
        return $result;
    }

    /**
     * @param $filter
     * @param $field = string
     * @return mixed
     */
    public function getAccountTalkTimeByField($filter, $field)
    {
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        $data = DB::table($this->table . ' AS cpl')
            ->select(
                DB::raw('SUM(cpl.talk_time) AS talk_time'),
                DB::raw('COUNT(cpl.talk_time) AS count'),
                DB::raw('COUNT(DISTINCT(cpl.order_id)) as count_order'),
                $field . " AS name"
            )
            ->leftJoin('users AS u', 'u.id', '=', 'cpl.user_id')
            ->leftJoin('companies AS c', 'u.company_id', '=', 'c.id')
            ->leftJoin('orders AS o', 'o.id', '=', 'cpl.order_id')
            ->leftJoin('countries AS co', 'o.geo', '=', 'co.code')
            ->whereNotNull($field)
            ->whereBetween('cpl.date', [$filter['date_start'], $filter['date_end']]);
        if (auth()->user()->company_id) {
            $data = $data->where('c.id', auth()->user()->company_id);
        }
        if ($filter['company']) {
            $data = $data->where('c.id', $filter['company']);
        }
        if ($filter['user']) {
            $data = $data->where('u.id', $filter['user']);
        }
        if ($filter['trunk']) {
            $data = $data->where('cpl.trunk', "like", $filter['trunk'] . "%");
        }
        if ($filter['country']) {
            $data = $data->where('o.geo', mb_strtolower($filter['country']));
        }

        return $data->groupBy($field)
            ->get();
    }
}