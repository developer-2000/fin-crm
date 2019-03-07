<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait SearchModel
{
    protected $search_fields = false;

    protected $search_where = false;

    protected $search_sort = false;

    public $default_sort = ['id', 'desc'];

    public function scopeSearchSelect($query) {
        $selects = [];
        foreach ($this->searchFields() as $key => $value) {
            $selects[] = $value . ' as ' . $key;
        }
        return $query->select(DB::raw(implode(', ', $selects)));
    }

    public function scopeSetSearchWhere($query, Request $request) {
        $r = $request->all();
        $defaultWhere = $this->defaultWhere();
        if (!empty($defaultWhere)) {
            foreach ($defaultWhere as $key => $value) {
                if (!isset($r[$key])) {
                    $r[$key] = $value;
                }
            }
        }
        foreach ($r as $key => $value) {
            if (in_array($key, array_keys($this->searchWhere()))) {

                $field = is_array($this->searchWhere()[$key])
                    ? $this->searchFields()[$this->searchWhere()[$key]['papa']]
                    : $this->searchFields()[$key];

                $type =  is_array($this->searchWhere()[$key])
                    ? $this->searchWhere()[$key]['type']
                    : $this->searchWhere()[$key];

                switch ($type) {
                    case '%like%':
                        $query->where($field, 'like', '%' . $value . '%');
                        break;
                    /*case '%concatlike%':
                        $query->where(DB::raw($field . ' like \'%' . $value . '%\''));
                        // проверить, нужно ли экранирование
                        break;*/
                    case '=':
                        $query->where($field, $value);
                        break;
                    /*case '>':
                        $query->where($field, '>', $value);
                        break;*/
                    /*case '<':
                        $query->where($field, '<', $value);
                        break;*/
                    case 'date_from':
                        $validator = Validator::make(['f' => $value],['f' => 'date_format:d.m.Y']);
                        if (!$validator->fails()) {
                            $start = Carbon::createFromFormat('d.m.Y H:i:s', $value . ' 00:00:00');
                            $query->where($field, '>', $start);
                        }

                        break;
                    case 'date_to':
                        $validator = Validator::make(['f' => $value],['f' => 'date_format:d.m.Y']);
                        if (!$validator->fails()) {
                            $end = Carbon::createFromFormat('d.m.Y H:i:s', $value . ' 23:59:59');
                            $query->where($field, '<', $end);
                        }
                        break;
                    case 'date_raw':
                        $validator = Validator::make(['f' => $value],['f' => 'date_format:d.m.Y H:i:s']);
                        if (!$validator->fails()) {
                            $date = Carbon::createFromFormat('d.m.Y H:i:s', $value)
                                ->format('Y-m-d H:i:s');
                            $query->where($field, '=', DB::raw(
                                str_replace($this->searchWhere()[$key]['replace'], $date, $this->searchWhere()[$key]['raw'])
                            ));
                        }
                        break;
                }
            }
        }
        return $query;
    }

    public function defaultWhere() {
        return [];
    }

    public function scopeSetSearchSort($query, Request $request) {
        $sort_asc = $request->get('sort_asc', false);
        $sort_desc = $request->get('sort_desc', false);
        if ($sort_asc && in_array($sort_asc, $this->searchSort())) {
            $sort = [$sort_asc, 'asc'];
        } elseif ($sort_desc && in_array($sort_desc, $this->searchSort())) {
            $sort = [$sort_desc, 'desc'];
        } else {
            $sort = $this->default_sort;
        }
        //return $query->orderBy(DB::raw($this->searchFields()[$sort[0]] . ' ' . $sort[1]));
        return $query->orderBy($sort[0], $sort[1]);
    }

    public function sortLinks(Request $request) {
        $sort_links = [];
        $r = $request->all();
        foreach ($r as $key => $value) {
            if (!in_array($key, array_keys($this->searchWhere()))) {
                unset($r[$key]);
            }
        }
        foreach ($this->searchSort() as $value) {
            $asc = $r; $asc['sort_asc'] = $value;
            $desc = $r; $desc['sort_desc'] = $value;
            $sort_links[$value] = [
                'asc' => route(\Route::currentRouteName(), $asc),
                'desc' => route(\Route::currentRouteName(), $desc),
                'cur_asc' => ($request->get('sort_asc') == $value),
                'cur_desc' => ($request->get('sort_desc') == $value),
            ];
        }
        return $sort_links;
    }

    public function appendPage(Request $request) {
        $r = $request->all();
        foreach ($r as $key => $value) {
            if (!in_array($key, array_keys($this->searchWhere()))) {
                unset($r[$key]);
            }
        }
        if ($request->get('sort_asc')) {
            $r['sort_asc'] = $request->get('sort_asc');
        } elseif ($request->get('sort_desc')) {
            $r['sort_desc'] = $request->get('sort_desc');
        }
        return $r;
    }
}