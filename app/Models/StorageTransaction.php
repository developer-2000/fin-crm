<?php

namespace App\Models;

use App\Models\Traits\SearchModel as SearchModelTrait;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use \Illuminate\Database\Eloquent\Builder;

class StorageTransaction extends Model
{
    use SearchModelTrait;

    protected $table = 'storage_transactions';

    protected $fillable = ['product_id', 'project_id', 'moving_id', 'user_id', 'amount1', 'amount2', 'hold1', 'hold2',
                           'type', 'created_at', 'order_id'];

    // updated_at здесь не нужно, выключаю
    public function setUpdatedAt($value)
    {
        return $this;
    }

    const TYPE_UNDEFINED = 0;
    const TYPE_SYSTEM_SENT = 1;
    const TYPE_SYSTEM_RELEASED = 2;
    const TYPE_SYSTEM_RECEIVED = 3;
    const TYPE_MANUAL = 4;
    const TYPE_SENT = 5;
    const TYPE_RECEIVED = 6;
    const TYPE_RETURN = 7;
    const TYPE_CANCEL = 8;
    const TYPE_WRITE_OFF = 9;
    const TYPE_REPAIR = 10;
    const TYPE_INCOMPLETE = 11;
    const TYPE_REVERSAL = 12;

    public static $types = [
        self::TYPE_UNDEFINED => 'undefined',
        self::TYPE_SYSTEM_SENT => 'sent in the system',
        self::TYPE_SYSTEM_RELEASED => 'hold written off on the system',
        self::TYPE_SYSTEM_RECEIVED => 'received in the system',
        self::TYPE_MANUAL => 'manual correction',
        self::TYPE_SENT => 'sent to the client',
        self::TYPE_RECEIVED => 'received by the client',
        self::TYPE_RETURN => 'return from customer',
        self::TYPE_CANCEL => 'cancel',
        self::TYPE_WRITE_OFF => 'written off',
        self::TYPE_REPAIR => 'repair',
        self::TYPE_INCOMPLETE => 'incomplete',
    ];

    public static function langTypes() {
        $types = [];
        foreach (static::$types as $key => $value) {
            $types[$key] = $value;
        }
        return $types;
    }

    /**
     * @return BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */

    public function project() {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return MorphMany
     */
    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @param $project
     * @param $datetime
     * @return StorageTransaction[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    // найти содержимое всех складов проекта в заданный момент времени
    public static function remainder($project, $datetime) {

        // если пришёл подпроект - показываем его остатки, если пришёл проект - показываем остатки его подпроектов
        $ids = $project->parent_id ? [$project->id] : $project->children()->pluck('id');
        $st = static::tableName();
        $storageTransactions = static::with(['project', 'product'])
            //->select(DB::RAW('id, product_id, project_id, amount2, hold2, max(created_at) as created_at'))
            //->where('created_at', '<=', Carbon::createFromTimeString($datetime))
            //->groupBy('project_id', 'product_id')
            // так когда-то давно работало, но сейчас не хочет
            ->whereIn('project_id', $ids)
            ->where('created_at', '=', DB::raw('(select max(created_at) from ' . $st . ' as st2 where st2.product_id='
                . $st . '.product_id and st2.project_id=' . $st . '.project_id)'))
            ->orderBy('project_id')
            ->get();
        return $storageTransactions;
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeSearchQuery($query) {
        $st = $this->table;
        $query
            ->join(Project::tableName() . ' as sp', 'sp.id', '=', $st . '.project_id')
            ->join(Project::tableName() . ' as pj', 'pj.id', '=', 'sp.parent_id')
            ->join(Product::tableName() . ' as p', 'p.id', '=', $st . '.product_id')
            ->join(User::tableName() . ' as u', 'u.id', '=', $st . '.user_id')
            ->leftJoin(Comment::tableName(). ' as c', function($join) use ($st) {
                $join->on(['c.commentable_id' => $st . '.id'])
                    ->where(['c.commentable_type' => StorageTransaction::class]);
            })// из расчёта, что у транзакции один коммент (поэтому тут без group by)
            ->leftJoin(User::TableName() . ' as u2', 'u2.id', '=', 'c.user_id');

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
            $c = Comment::tableName();
            $this->search_fields = [
                'id'                => $st . '.id',
                'project_name'      => 'pj.name',
                'pj_id'             => 'pj.id', // чтобы не путать с родным project_id в этой же таблице
                'subproject_name'   => 'sp.name',
                'subproject_id'     => $st . '.project_id',
                'moving_id'         => 'moving_id',
                'product_name'      => 'p.title',
                'product_id'        => 'p.id',
                'user_name'         => 'concat_ws(\' \', u.name,u.surname)',
                'user_id'           => 'u.id',
                'amount1'           => 'amount1',
                'amount2'           => 'amount2',
                'hold1'             => 'hold1',
                'hold2'             => 'hold2',
                'type'              => $st . '.type',
                'created_at'        => $st . '.created_at',
                /*'comments'          => '(select count(*) from ' . $c . ' where ' . $c . '.commentable_id = ' . $st . '.id '
                    . 'and ' . $c . '.commentable_type = \'' . StorageTransaction::class . '\')'*/
                'comment'           => 'c.text',
                'comment_user'      => 'concat_ws(\' \', u2.name, u2.surname)',
                'comment_user_photo'=> 'u2.photo',
                'comment_date'      => 'c.date',
            ];
        }
        return $this->search_fields;
    }

    public function searchWhere() {
        if (!$this->search_where) {
            $this->search_where = [
                'id'                => '=',
                'pj_id'             => '=',
                'subproject_id'     => '=',
                'moving_id'         => '=',
                'product_id'        => '=',
                'user_id'           => '=',
                'amount1'           => '=',
                'amount2'           => '=',
                'hold1'             => '=',
                'hold2'             => '=',
                'type'              => '=',
                'created_at_start'  => ['papa' => 'created_at', 'type' => 'date_from'],
                'created_at_end'    => ['papa' => 'created_at', 'type' => 'date_to']
            ];
        }
        return $this->search_where;
    }

    public $default_sort = ['id', 'desc'];

    public function searchSort() {
        if (!$this->search_sort) {
            $this->search_sort = [
                'id', 'project_name', 'subproject_name', 'moving_id', 'product_name', 'user_name',
                'amount1', 'amount2', 'hold1', 'hold2', 'type', 'created_at'
            ];
        }
        return $this->search_sort;
    }

    public function defaultWhere() {
        return [
            // если не ввели дату - подразумеваем дату прямо сейчас
            'date' => date('d.m.Y H:i:s')
        ];
    }

}
