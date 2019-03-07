<?php

namespace App\Models;

use function foo\func;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Pass extends Model
{
    protected $table = 'passes';
    protected $fillable = ['sub_project_id', 'user_id', 'type', 'comment', 'active', 'origin_id'];

    const TYPE_REDEMPTION = 'redemption';
    const TYPE_NO_REDEMPTION = 'no-redemption';
    const TYPE_TO_PRINT = 'to_print';
    const TYPE_SENDING = 'sending';
    const TYPE_REVERSAL = 'reversal';

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ordersToPrint()
    {
        return $this->hasMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function ordersToSend()
    {
        return $this->hasMany(Order::class, 'pass_send_id');
    }

    public function ordersPass()
    {
        return $this->hasMany(OrdersPass::class);
    }

    public function reversalPass()
    {
        return $this->hasMany(Pass::class, 'origin_id');
    }

    public function scopeCheckSubProject($query)
    {
        if (Auth::user()->sub_project_id) {
            $query->where('sub_project_id', Auth::user()->sub_project_id);
        }

        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', 0);
    }

    public function scopeByTypeRedemption($query)
    {
        return $query->whereIn('type', [
            self::TYPE_REDEMPTION,
            self::TYPE_NO_REDEMPTION,
            self::TYPE_SENDING,
            self::TYPE_REVERSAL
        ]);
    }

    public function scopeRedemption($query)
    {
        return $query->where('type', self::TYPE_REDEMPTION);
    }

    public function scopeNoRedemption($query)
    {
        return $query->where('type', self::TYPE_NO_REDEMPTION);
    }

    public function scopeSending($query)
    {
        return $query->where('type', self::TYPE_SENDING);
    }

    public static function getPassByFilter($filter)
    {
        $query = self::with('user', 'orders.getTargetValue', 'ordersToSend.getTargetValue', 'reversalPass')
            ->withCount([
                'ordersPass',
                'orders AS price_total'            => function ($q) {
                    $q->select(DB::raw('SUM(price_total) as price_total'));
                },
                'ordersToSend AS price_total_send' => function ($q) {
                    $q->select(DB::raw('SUM(price_total) as price_total_send'));
                },
                'ordersPass AS cost_actual'        => function ($q) {
                    $q->select(DB::raw('SUM(cost_actual) as cost_actual'));
                },
                'ordersPass AS cost_return'        => function ($q) {
                    $q->select(DB::raw('SUM(cost_return) as cost_return'));
                },
            ])
            ->checkSubProject()
            ->byTypeRedemption()
            ->inactive();
        if ($filter['project']) {
            $projects = explode(',', $filter['project']);
            //  $projectIds = Project::subProject()->where('parent_id', $filter['project'])->pluck('id');
            //  $query->whereIn('passes.sub_project_id', $subProjectIds);

            $query->where(function ($q) use ($projects){
                $q->whereHas('orders', function ($query) use ($projects) {
                    $query->whereIn('project_id', $projects);
                })->orwhereHas(
                    'ordersToSend', function ($query) use ($projects) {
                    $query->whereIn('project_id', $projects);
                });
            });
        }
        if ($filter['pass_id']) {
            $query->where('id', $filter['pass_id']);
        }
        if ($filter['initiator']) {
            $initiators = explode(',', $filter['initiator']);

            $query->whereIn('user_id', $initiators);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            
            $query->where(function ($q) use ($subProject) {
                $q->whereHas('orders', function ($query) use ($subProject) {
                    $query->whereIn('subproject_id', $subProject);
                })->orwhereHas(
                    'ordersToSend', function ($query) use ($subProject) {
                    $query->whereIn('subproject_id', $subProject);
                });
            }
            );
        }

        if ($filter['date_start']) {
            $query->where('updated_at', '>=', date('Y-m-d' . ' 00:00:00', strtotime($filter['date_start'])));
        }
        if ($filter['date_end']) {
            $query->where('updated_at', '<=', date('Y-m-d' . ' 23:59:59', strtotime($filter['date_end'])));
        }

        return $query->orderBy('updated_at', 'desc')->paginate(50)->appends(Input::except('page'));
    }
}
