<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use \App\Models\User;

class Feedback extends Model
{
    protected $table = 'feedback';
    public $fillable = ['user_id', 'order_id', 'company_id', 'type', 'mistakes', 'moderator_id', 'status',
        'title', 'activity', 'operator_fault', 'read', 'orders_opened_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function offer()
    {
        //todo возможно не правильно
        return $this->hasManyThrough(Order::class, Offer::class, 'id', 'id');
    }

    public function orderOpened()
    {
        return $this->belongsTo(OrdersOpened::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Получаем все фидбеки
     * @param array $filter Фильтр фидбека
     * @return array
     */
    public static function getFeedbacks($filter)
    {
        $result = Feedback::select('*');
        if (!empty($filter['status']) == NULL) {
            $result = $result->where('status', 'opened');
        }

        if (isset($filter['status']) && $filter['status'] == 'Все') {

            $status = [
                0 => 'opened',
                1 => 'closed',
            ];
            $result = $result->whereIn('status', $status);
        }
        if (isset($filter['status']) && $filter['status'] != 'Все') {
            $status = explode(',', $filter['status']);
            $result = $result->whereIn('status', $status);
        }

        if (isset($filter['id'])) {
            $result = $result->where('id', $filter['id'])
                ->where('id', '>', 0);
        }
        if (isset($filter['company'])) {
            $company = explode(',', $filter['company']);
            $result = $result->whereHas('user', function ($query) use ($company) {
                $query->whereIn('company_id', $company);
            });
        }

        if (isset($filter['oid'])) {
            $orderId = $filter['oid'];
            $result = $result->where('order_id', $orderId);
        }

        if (isset($filter['mistake_type'])) {
            $mistakes = explode(',', $filter['mistake_type']);
            foreach ($mistakes as $mistake) {
                $result = $result->where('mistakes', 'like', '%' . $mistake . '%');
            }
        }

        if ($filter['user']) {
            $user = explode(',', $filter['user']);
            $result = $result->whereIn('user_id', $user);
        }
        if (isset($filter['offers'])) {
            $offers = explode(',', $filter['offers']);
            $result = $result->whereHas('order', function ($query) use ($offers) {
                $query->whereIn('offer_id', $offers);
            });


        }

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {

            $start = Carbon::parse($filter['date_start']);
            $end = Carbon::parse($filter['date_end'])->endOfDay();

            $result = $result->whereBetween('created_at', [$start, $end]);
        }
        $currentUser = User::find(auth()->user()->id);

        if (Route::getFacadeRoot()->current()->uri() == 'success-calls') {
            $result = $result
                ->where('type',
                    'success_call')
                ->with('user', 'user.company')
                ->orderBy('id', 'desc')
                ->paginate(50);
            return $result;
        } else {

            if ($currentUser->role_id == 1) {
                $result = $result
                    ->with('user', 'moderator', 'user.company')
                    ->where(function ($query) use($currentUser){
                        $query->whereIn('type',
                            ['failed_call', 'fault', 'info'])->where([['user_id', $currentUser->id]]);})
                    ->orWhere(function ($query) use($currentUser){
                        $query->whereIn('type',
                            ['failed_call', 'fault', 'info'])->where([['moderator_id', $currentUser->id]]);})
                    ->orderBy('id', 'desc')
                    ->paginate(50);
            } else {
                $result = $result
                    ->whereIn('type',
                        ['failed_call', 'fault', 'info'])
                    ->where(function ($query) use($currentUser){
                        $query->whereIn('type',
                            ['failed_call', 'fault', 'info']);})
                    ->with('user', 'moderator', 'user.company')
                    ->orderBy('id', 'desc')
                    ->paginate(50);
            }

            return $result;
        }
    }
}
