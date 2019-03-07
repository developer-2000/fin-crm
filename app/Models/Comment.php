<?php

namespace App\Models;

use \App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Comment extends Model
{
    protected $table = 'comments';
    public $timestamps = false;

    public $fillable = ['order_id', 'user_id', 'text', 'date', 'entity', 'type',
        'commentable_type', 'commentable_id'];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get post
     * @return belongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Получаем все комментарии
     * @param int $orderId ID заказа
     * @return object
     */
    function getComments($orderId, $entity = false, $type = false)
    {
        $comments = DB::table($this->table . ' AS c')->select('c.id', 'c.user_id', 'c.text', 'c.date', 'u.login', 'u.name', 'u.surname', 'c.entity', 'c.type', 'u.photo', 'companies.name AS company')
            ->where('order_id', $orderId)
            ->leftJoin('users AS u', 'c.user_id', '=', 'u.id')
            ->leftJoin('companies', 'u.company_id', '=', 'companies.id');
        if ($entity) {
            $comments = $comments->where('c.entity', $entity);
        }
        if ($type) {
            $comments = $comments->where('c.type', $type);
        }
        return $comments->get();
    }

    /**
     * Добавляем комментарий к заказу
     * @param int $orderId ID заказа
     * @param string $text Текс комментария
     * @return bool
     */
    function addComment($orderId, $text, $entity = 'order', $type = 'comment')
    {
        if (!$text && $type == 'comment') {
            exit;
        }

        return DB::table($this->table)->insert([
            'order_id' => $orderId,
            'user_id' => auth()->user()->id,
            'text' => $text,
            'date' => now(),
            'entity' => $entity,
            'type' => $type,
        ]);
    }

    function getLastComment($orderId, $entity = false, $type = false)
    {
        $comments = DB::table($this->table . ' AS c')->select('c.id', 'c.user_id', 'c.text', 'c.date', 'u.login', 'u.name', 'u.surname', 'c.entity', 'c.type')
            ->where('order_id', $orderId)
            ->leftJoin('users AS u', 'c.user_id', '=', 'u.id');
        if ($entity) {
            $comments = $comments->where('c.entity', $entity);
        }
        if ($type) {
            $comments = $comments->where('c.type', $type);
        }
        return $comments->limit(1)->orderBy('c.id', 'desc')->first();
    }
}