<?php

namespace App\Models;

use App\Models\Traits\SearchModel as SearchModelTrait;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Builder;

class Remainder extends Model
{
    use SearchModelTrait;

    protected $table = 'storage_transactions';

    /**
     * @param $query
     * @return Builder
     */
    public function scopeSearchQuery($query) {
        $st = $this->table;
        $pj = Project::tableName();
        $p = Product::tableName();
        $u = User::tableName();
        $query
            ->join($pj . ' as sp',  'sp.id', '=', $st . '.project_id')
            ->join($pj . ' as pj', 'pj.id', '=', 'sp.parent_id')
            ->join($p . ' as p', 'p.id', '=', $st . '.product_id')
            ->join($u . ' as u', 'u.id', '=', $st . '.user_id');

        $users_project_id = (int) auth()->user()->project_id;
        $users_subproject_id = (int) auth()->user()->sub_project_id;
      
        if ($users_project_id) {
            $query->where('pj.id', $users_project_id);
            if ($users_subproject_id) {
                $query->where('sp.id', $users_subproject_id);
            }
        }

        return $query;
    }

    /**
     * @return array|bool
     */
    public function searchFields() {
        if (!$this->search_fields) {
            $st = $this->table;
            $this->search_fields = [
                'id'                => $st . '.id',
                'pj_name'           => 'pj.name',
                'pj_id'             => 'pj.id', // чтобы не путать с родным project_id в этой же таблице
                'sp_name'           => 'sp.name',
                'sp_id'             => $st . '.project_id',
                'moving_id'         => 'moving_id',
                'product_name'      => 'p.title',
                'product_id'        => 'p.id',
                'user_name'         => "concat_ws(' ', u.name,u.surname)",
                'user_id'           => 'u.id',
                'amount'            => 'amount2',
                'hold'              => 'hold2',
                'date'              => $st . '.created_at',
            ];
        }
        return $this->search_fields;
    }

    /**
     * @return array|bool
     */
    public function searchWhere() {
        if (!$this->search_where) {
            $st = static::tableName();
            $this->search_where = [
                'id'            => '=',
                'pj_id'         => '=',
                'sp_id'         => '=',
                'moving_id'     => '=',
                'product_id'    => '=',
                'user_id'       => '=',
                'amount1'       => '=',
                'amount2'       => '=',
                'hold1'         => '=',
                'hold2'         => '=',
                'type'          => '=',
                'date'          => [
                    'type' => 'date_raw',
                    'papa' => 'date',
                    'replace' => '{date}',
                    'raw' => '(select max(st2.created_at) from ' . $st . " as st2 where st2.created_at <= '{date}' "
                        . ' and st2.product_id = ' . $st . '.product_id and st2.project_id = ' . $st . '.project_id)'
                ],
            ];
        }
        return $this->search_where;
    }

    public function defaultWhere() {
        return [
            // если не ввели дату - подразумеваем дату прямо сейчас
            'date' => date('d.m.Y H:i:s')
        ];
    }

    public $default_sort = ['id', 'desc'];

    /**
     * @return array|bool
     */
    public function searchSort() {
        if (!$this->search_sort) {
            $this->search_sort = ['id', 'moving_id',];
        }
        return $this->search_sort;
    }
}