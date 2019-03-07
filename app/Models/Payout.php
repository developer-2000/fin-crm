<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Payout extends Model
{
    protected $table = 'finance_payouts';

   public function getAllPayoutsCompanies()
   {
       $countOnePage = 100;

       $payouts = DB::table($this->table . ' AS p')
           ->select('p.id', 'u.name AS initiatorName', 'u.surname AS initiatorSurname', 'cc.name AS company', 'p.comment',
               'p.valuation', 'p.time_created', 'p.period_start', 'p.period_end')
           ->leftJoin('companies as cc', 'cc.id', '=', 'p.entity_id')
           ->leftJoin('users as u', 'u.id', '=', 'p.initiator_id')
           ->where('p.entity', 'company');

       if (auth()->user()->company_id) {
           $payouts = $payouts->where('p.entity_id', auth()->user()->company_id);
       }

       $payouts = $payouts->orderBy('id', 'desc')
           ->paginate($countOnePage);
       return [
           'payouts'        => $payouts->appends(Input::except('page')),
       ];
   }

    public function getAllPayoutsUsers()
    {
        $countOnePage = 100;

        $payouts = DB::table($this->table . ' AS p')
            ->select('p.id', 'u.name AS initiatorName', 'u.surname AS initiatorSurname', 'oper.name AS operName', 'oper.surname AS operSurname',
                'p.comment', 'p.valuation', 'p.time_created', 'p.period_start', 'p.period_end')
            ->leftJoin('users as oper', 'oper.id', '=', 'p.entity_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.initiator_id')
            ->where('p.entity', 'user');

        if (auth()->user()->company_id) {
            $payouts = $payouts->where('oper.company_id', auth()->user()->company_id);
        }

        $payouts = $payouts->orderBy('id', 'desc')
            ->paginate($countOnePage);
        return [
            'payouts' => $payouts->appends(Input::except('page')),
        ];
    }

   public function addNewPayoutForCompany($data)
   {
       $result = [
           'errors'    => 0,
           'status'    => 0,
       ];
       if (!$data['period_start'] || !$data['period_end']) {
           $result['errors'] = [
               'period_start'   => true,
               'period_end'   => true,
           ];
           return $result;
       }
       $data['period_start'] = strtotime($data['period_start'] . ' 00:00:00');
       $data['period_end'] = strtotime($data['period_end'] . ' 23:59:59');
       $validator = \Validator::make($data, [
           'entity_id'      => 'required|numeric',
           'comment'        => '',
           'valuation'      => 'required|numeric',
           'period_start'   => 'required|numeric',
           'period_end'     => 'required|numeric',
       ]);
       if ($validator->fails()) {
           $result['errors'] = $validator->errors();
           return $result;
       }
       if (Carbon::today()->timestamp < $data['period_end']) {
           $result['errors'] = ['period'    => true];
           return $result;
       }

       if ($data['period_start'] > $data['period_end']) {
           $result['errors'] = ['period'    => true];
           return $result;
       }
       $comment = "Выплатили за период <br><b>" . date('d/m/Y', $data['period_start']) . "</b> по <b>" . date('d/m/Y', $data['period_end']) . "</b><br> Сумма " . $data['valuation'] . ' грн';
       $insert = [
           'initiator_id'   => auth()->user()->id,
           'entity'         => 'company',
           'entity_id'      => $data['entity_id'],
           'comment'        => $data['comment'] ? $data['comment'] : $comment,
           'valuation'      => $data['valuation'],
           'period_start'   => $data['period_start'],
           'period_end'     => $data['period_end'],
           'time_created'   => now()
       ];
       $result['status'] = DB::table($this->table)->insertGetId($insert);

       return $result;
   }

    public function addNewPayoutForUser($data)
    {
        $result = [
            'errors'    => 0,
            'status'    => 0,
        ];
        if (!$data['period_start'] || !$data['period_end']) {
            $result['errors'] = [
                'period_start'   => true,
                'period_end'   => true,
            ];
            return $result;
        }
        $data['period_start'] = strtotime($data['period_start'] . ' 00:00:00');
        $data['period_end'] = strtotime($data['period_end'] . ' 23:59:59');
        $validator = \Validator::make($data, [
            'entity_id'      => 'required|numeric',
            'comment'        => '',
            'valuation'      => 'required|numeric',
            'period_start'   => 'required|numeric',
            'period_end'     => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $result['errors'] = $validator->errors();
            return $result;
        }
        if (Carbon::today()->timestamp < $data['period_end']) {
            $result['errors'] = ['period'    => true];
            return $result;
        }

        if ($data['period_start'] > $data['period_end']) {
            $result['errors'] = ['period'    => true];
            return $result;
        }
        $comment = "Выплатили за период <br><b>" . date('d/m/Y', $data['period_start']) . "</b> по <b>" . date('d/m/Y', $data['period_end']) . "</b><br> Сумма " . $data['valuation'] . ' грн';
        $insert = [
            'initiator_id'   => auth()->user()->id,
            'entity'         => 'user',
            'entity_id'      => $data['entity_id'],
            'comment'        => $data['comment'] ? $data['comment'] : $comment,
            'valuation'      => $data['valuation'],
            'period_start'   => $data['period_start'],
            'period_end'     => $data['period_end'],
            'time_created'   => now()
        ];
        $result['status'] = DB::table($this->table)->insertGetId($insert);

        return $result;
    }

    public function getAllPayoutOneUser($id, $page)
    {
        $skip = 0;
        $countOnePage = 100;
        if ($page) {
            $skip = ($page - 1) * $countOnePage;
        }
        $payouts = DB::table($this->table . ' AS p')
            ->select('p.id', 'u.name AS initiatorName', 'u.surname AS initiatorSurname', 'oper.name AS operName', 'oper.surname AS operSurname',
                'p.comment', 'p.valuation', 'p.time_created', 'p.period_start', 'p.period_end')
            ->leftJoin('users as oper', 'oper.id', '=', 'p.entity_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.initiator_id')
            ->where('p.entity', 'user')
            ->where('p.entity_id', $id)
            ->skip($skip)
            ->orderBy('id', 'desc')
            ->take($countOnePage)
            ->get();
        $count = count($payouts);
        $paginationModle = new Pagination;
        return [
            'payouts'        => $payouts,
            'pagination'     => $paginationModle->getPagination($page, $count, $countOnePage),
        ];
    }
}
