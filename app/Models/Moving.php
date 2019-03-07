<?php

namespace App\Models;

use App\Models\Traits\SearchModel as SearchModelTrait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Moving extends Model
{
    use SearchModelTrait;

    protected $table = 'movings';

    protected $fillable = ['user_id', 'sender_id', 'receiver_id', 'send_date', 'received_date', 'status'];

    protected $dates = [
        'created_at',
        'send_date',
        'received_date'
    ];

    public $timestamps = false;


    const STATUS_NEW = 0;
    const STATUS_SENT = 1;
    const STATUS_RECEIVED = 2;
    const STATUS_CLOSED = 3;

    public static $statuses = [
        self::STATUS_NEW => 'New',
        self::STATUS_SENT => 'Sent',
        self::STATUS_RECEIVED => 'Received',
        self::STATUS_CLOSED => 'Closed'
    ];

    public static function langStatuses() {
        $statuses = [];
        foreach (static::$statuses as $key => $value) {
            $statuses[$key] = $value;
        }
        return $statuses;
    }

    /**
     * @return BelongsTo
     */
    // проект-склад, с которго товар едет
    public function sender()
    {
        return $this->belongsTo(Projects_new::class, 'sender_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    // проект-склад, который ожидает товар
    public function receiver()
    {
        return $this->belongsTo(Projects_new::class, 'receiver_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    // создатель движения
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function movingProducts() {
        return $this->hasMany(MovingProduct::class, 'moving_id', 'id');
    }

    /**
     * @return MorphMany
     */
    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchQuery($query) {
        $mv = $this->table;
        $query
            ->leftJoin(Projects_new::tableName() . ' as sd', 'sd.id', '=', $mv . '.sender_id')
            ->leftJoin(Projects_new::tableName() . ' as sd_papa', 'sd_papa.id', '=', 'sd.parent_id')
            ->leftJoin(Projects_new::tableName() . ' as rc', 'rc.id', '=', $mv . '.receiver_id')
            ->leftJoin(Projects_new::tableName() . ' as rc_papa', 'rc_papa.id', '=', 'rc.parent_id')
            ->join(MovingProduct::tableName() . ' as mp', 'mp.moving_id', '=', $mv . '.id')
            ->groupBy($mv . '.id');

        $users_subproject_id = auth()->user()->sub_project_id;
        $users_project_id = auth()->user()->project_id;

        if ($users_subproject_id) {
            $query->where(function($query) use ($users_subproject_id) {
                $query->where('sd.id', $users_subproject_id)
                    ->orWhere('rc.id', $users_subproject_id);
            });
        } elseif ($users_project_id) {
            $query->where(function($query) use ($users_project_id) {
                $query->where('sd.parent_id', $users_project_id)
                    ->orWhere('rc.parent_id', $users_project_id);
            });
        }

        return $query;
    }

    /**
     * @return array|bool
     */
    public function searchFields() {
        if (!$this->search_fields) {
            $mv = $this->table;
            $this->search_fields = [
                'id'                => $mv . '.id',
                'status'            => $mv . '.status',
                'sender_id'         => $mv . '.sender_id',
                'receiver_id'       => $mv . '.receiver_id',


                'sender_papa_id'    => 'sd.parent_id',
                'receiver_papa_id'  => 'rc.parent_id',

                'sender'            => 'IF(sender_id, concat_ws(\' / \', sd_papa.name, sd.name), \''
                                        . '--space--' . '\')',
                'receiver'          => 'IF(receiver_id, concat_ws(\' / \', rc_papa.name, rc.name), \''
                                        . '--space--' . '\')',
                'send_date'         => $mv . '.send_date',
                'received_date'     => $mv . '.received_date',
                'created_at'        => $mv . '.created_at',
                'amount'            => 'sum(mp.amount)',
                'names'             => 'count(*)',
                'user_id'           => $mv . '.user_id'
            ];
        }
        return $this->search_fields;
    }

    /**
     * @return array|bool
     */
    public function searchWhere() {
        if (!$this->search_where) {
            $this->search_where = [
                'id'                    => '=',
                'sender_id'             => '=',
                'receiver_id'           => '=',
                'status'                => '=',
                'created_at_start'      => ['papa' => 'created_at', 'type' => 'date_from'],
                'created_at_end'        => ['papa' => 'created_at', 'type' => 'date_to'],
                'send_date_start'       => ['papa' => 'send_date', 'type' => 'date_from'],
                'send_date_end'         => ['papa' => 'send_date', 'type' => 'date_to'],
                'received_date_start'   => ['papa' => 'received_date', 'type' => 'date_from'],
                'received_date_end'     => ['papa' => 'received_date', 'type' => 'date_to'],
            ];
        }
        return $this->search_where;
    }

    public $default_sort = ['id', 'desc'];

    public function searchSort() {
        if (!$this->search_sort) {
            $this->search_sort = [
                'id' // MG: когда станет нужна осртировка, можно расширить
            ];
        }
        return $this->search_sort;
    }


}
