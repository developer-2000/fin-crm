<?php

namespace App\Models;

use App\Models\Api\Measoft\MeasoftSender;
use App\Models\Api\NovaposhtaKey;
use App\Models\Api\ViettelKey;
use App\Models\Api\WeFast\WeFastCounterparty;
use App\Models\Api\WeFast\WeFastKey;
use Carbon\Carbon;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use App\Models\Api\Integration;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'partner_id',
        'project_id',
        'parent_id',
        'subproject_id',
        'name',
        'alias'
    ];

    /**
     * get users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function statuses()
    {
        return $this->hasMany(ProcStatus::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function productProjects()
    {
        return $this->hasMany(ProductProject::class);
    }

    public function scopeProject($query)
    {
        return $query->where('parent_id', 0);
    }

    public function scopeSubProject($query)
    {
        return $query->where('parent_id', '>', 0)
                ->whereNull('division_id');
    }

    public function scopeDivision($query, $subProjectId = null)
    {
        $query->where('parent_id', '>', 0)
            ->where('division_id', '>', 0);

        if ($subProjectId) {
            $query->where('parent_id', $subProjectId);
        }

        return $query;
    }

    public function scopeCheckAuth($query)
    {
        if (Auth::user()->project_id || Auth::user()->sub_project_id) {
            $query->where(function ($q) {
                if (Auth::user()->project_id) {
                    $q->where('id', Auth::user()->project_id);
                }
// в условии стоится ИЛИ
                if (Auth::user()->sub_project_id) {
                    $q->orWhere('id', Auth::user()->sub_project_id);
                }
            });
        }

        return $query;
    }

    /**
     * @return BelongsTo
     */
    public function parent() {
        return $this->belongsTo(Project::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function children() {
        return $this->hasMany(Project::class, 'parent_id', 'id');
    }

    /**
     * get integrationKey
     * @return HasMany;
     */
    public function novaposhtaKey()
    {
        return $this->hasMany(NovaposhtaKey::class);
    }

    /**
     * get viettel Post key
     * @return HasMany;
     */
    public function viettelKey()
    {
        return $this->hasMany(ViettelKey::class);
    }

    public function wefastKeys()
    {
        return $this->hasMany(WeFastKey::class, 'sub_project_id');
    }

    public function wefastCounterparties()
    {
        return $this->hasMany(WeFastCounterparty::class, 'sub_project_id');
    }

    public function measoftSenders()
    {
        return $this->hasMany(MeasoftSender::class, 'sub_project_id');
    }

    /**
     * @return HasMany
     */
    // что мы имеем на складе
    public function storagecontents() {
        return $this->hasMany(StorageContent::class, 'project_id', 'id');
    }

    /**
     * @return HasMany
     */
    // то, что отправлено нам
    public function comingmovings() {
        return $this->hasMany(Moving::class, 'receiver_id', 'id');
    }

    /**
     * @return HasMany
     */
    // то, что отправлено нам и находится в движении
    public function actualcomingmovings() {
        return $this->hasMany(Moving::class, 'receiver_id', 'id')
            ->whereNull(Moving::tableName() . '.received_date');
    }

    /**
     * @return HasMany
     */
    // то, что мы отправили
    public function sentmovings() {
        return $this->hasMany(Moving::class, 'sender_id', 'id');
    }

    /**
     * @return HasMany
     */
    // то, что мы отправили и оно сейчас в движении
    public function actualsentmovings() {
        return $this->hasMany(Moving::class, 'sender_id', 'id')
            ->whereNull(Moving::tableName() . '.received_date');
    }

    public static function findByWordProject($word, $partnerId = [])
    {
        $partnerIds = [];

        if (is_array($partnerId)) {
            $partnerIds = $partnerId;
        } elseif (is_string($partnerId) || is_int($partnerId)) {
            $partnerIds[] = $partnerId;
        }

        if (Auth::user()->project_id && Auth::user()->project && Auth::user()->project->partner) {
            $partnerIds = [Auth::user()->project->partner->id];
        }

        $query = self::where('name' , 'LIKE', '%' . $word . '%')
            ->project();

        if ($partnerIds) {
            $query->whereIn('partner_id', $partnerIds);
        }

        return $query->get();
    }

    public static function findByWordSubProject($word, $projectId = [], $partnerId = [])
    {
        $partnerIds = [];
        $projectIds = [];

        if (is_array($partnerId)) {
            $partnerIds = $partnerId;
        } elseif (is_string($partnerId) || is_int($partnerId)) {
            $partnerIds[] = $partnerId;
        }
        if($projectId){
            if (is_array($projectId)) {
                $projectIds = $projectId;
            } elseif (is_string($projectId) || is_int($projectId)) {
                $projectIds[] = $projectId;
            }
        }


        if (Auth::user()->project_id && Auth::user()->project && Auth::user()->project->partner) {
            $partnerIds = [Auth::user()->project->partner->id];
            $projectIds = [Auth::user()->project_id];
        }

        $query = self::where('name' , 'LIKE', '%' . $word . '%')
            ->subProject();


        if ($partnerIds) {
            $query->whereIn('partner_id', $partnerIds);
        }

        if ($projectIds) {
            $query->whereIn('parent_id', $projectIds);
        }
        return $query->get();
    }

    public static function findByWordDivisions($word, $subProjectIds = [], $projectIds = [])
    {
        $query = self::where('name' , 'LIKE', '%' . $word . '%')
            ->division();

        if (!$subProjectIds) {
            $subProjectIds = self::subProject();

            if ($projectIds) {
                $subProjectIds->whereIn('parent_id', $projectIds);
            }
            $subProjectIds = $subProjectIds->pluck('id');
        }

        $query->whereIn('parent_id', $subProjectIds);

        return $query->get();
    }

    /**
     * @return Collection
     */
    /*public static function storagedata_all() {
        $data = DB::table(StorageContent::tableName() . ' as sc')
            ->leftJoin(Product::tableName() . ' as p', 'p.id', '=', 'sc.product_id')
            ->select(DB::raw('count(p.id) names, sum(sc.amount) amount, sum(sc.hold) hold'))
            ->first();
        $MovingProducts = DB::table(MovingProduct::tableName() . ' as sm')
            ->join(Moving::tableName() . ' as sp', 'sm.moving_id', '=', 'sp.id')
            ->select(DB::raw('sum(amount) moves'))
            ->whereNull('received_date')
            ->first();
        $data->moves = $storagemoves->moves;
        foreach ($data as $key=>$value) {
            $data->$key = (int) $value;
        }
        return $data;
    }*/

    /**
     * @return Collection
     */
    public static function storagedata() {
        $data = DB::table(Project::tableName() . ' as pj')
            ->leftJoin(Project::tableName() . ' as sp', 'pj.id', '=', 'sp.parent_id')
            ->leftJoin(StorageContent::tableName() . ' as sc', 'sc.project_id', '=', 'sp.id')
            ->leftJoin(Product::tableName() . ' as p', 'p.id', '=', 'sc.product_id')
            ->select(DB::raw('sp.id id, count(p.id) names, sum(sc.amount) amount, sum(sc.hold) hold'))
            ->where(['pj.parent_id' => 0])
            ->groupBy('id')
            ->get()->keyBy('id');
        $MovingProducts = DB::table(Project::tableName() . ' as pj')
            ->leftJoin(Project::tableName() . ' as sp', 'pj.id', '=', 'sp.parent_id')
            ->leftJoin(Moving::tableName() . ' as comingpack', 'comingpack.receiver_id', '=', 'sp.id')
            ->leftJoin(Moving::tableName() . ' as goingpack', 'goingpack.sender_id', '=', 'sp.id')
            ->leftJoin(MovingProduct::tableName() . ' as coming', 'coming.moving_id', '=', 'comingpack.id')
            ->leftJoin(MovingProduct::tableName() . ' as going', 'going.moving_id', '=', 'goingpack.id')
            ->addSelect(DB::raw('sp.id id, sum(coming.amount) coming_amount, sum(going.amount) going_amount'))
            ->whereNull('comingpack.received_date')->whereNull('goingpack.received_date')
            ->where(['pj.parent_id' => 0])
            ->groupBy('id')
            ->get()->keyBy('id');
        foreach ($data as $key=>$value) {
            $data[$key]->coming_amount = isset($MovingProducts[$key]) ? $MovingProducts[$key]->coming_amount : 0;
            $data[$key]->going_amount = isset($MovingProducts[$key]) ? $MovingProducts[$key]->going_amount : 0;
        }
        foreach ($data as $key=>$value) {foreach ($value as $key2=>$value2) {
            $data[$key]->$key2 = (int) $value2;
        }}

        return $data;
    }

    // если данных будет много, придётся вернуться к загрузке данных только на одну страницу
    public function scopeStorageQuery() {

        $pj_tb = Project::tableName();
        $sc_tb = StorageContent::tableName();
        $p_tb = Product::tableName();
        $sp_tb = Moving::tableName();
        $sm_tb = MovingProduct::tableName();
        $query = $this
            ->leftJoin($pj_tb . ' as spj', $pj_tb . '.id', '=', 'spj.parent_id')
            ->leftJoin($sc_tb . ' as sc', 'sc.project_id', '=', 'spj.id')
            ->leftJoin($p_tb . ' as p', 'p.id', '=', 'sc.product_id')
            ->select(DB::raw($pj_tb . '.id pj_id, ' . $pj_tb .'.name pj_name, spj.id spj_id, spj.name spj_name, '
                . 'if(count(p.id), count(p.id), 0) names, if(sum(sc.amount), sum(sc.amount), 0) amount, '
                . 'if(sum(sc.hold), sum(sc.hold), 0) hold'))

            ->leftJoin($sp_tb . ' as comingpack', 'comingpack.receiver_id', '=', 'spj.id')
            ->leftJoin($sp_tb . ' as goingpack', 'goingpack.sender_id', '=', 'spj.id')
            ->leftJoin($sm_tb . ' as coming', 'coming.moving_id', '=', 'comingpack.id')
            ->leftJoin($sm_tb . ' as going', 'going.moving_id', '=', 'goingpack.id')
            ->addSelect(DB::raw('if(sum(coming.amount), sum(coming.amount), 0) coming_amount, '
                . 'if(sum(going.amount), sum(going.amount), 0) going_amount'))

            ->where([$pj_tb . '.parent_id' => 0])
            ->groupBy($pj_tb . '.id', 'spj.id');

        return $query->orderBy('pj_id', 'asc');
    }

    public static function storagedata_for_project($id) {
        $data = DB::table(Project::tableName() . ' as sp')
            ->leftJoin(StorageContent::tableName() . ' as sc', 'sc.project_id', '=', 'sp.id')
            ->leftJoin(Product::tableName() . ' as p', 'p.id', '=', 'sc.product_id')
            ->select(DB::raw('sp.id id, count(p.id) names, sum(sc.amount) amount, sum(sc.hold) hold'))
            ->where(['sp.parent_id' => $id])
            ->groupBy('sp.id')
            ->get()->keyBy('id');
        $MovingProducts = DB::table(Project::tableName() . ' as sp')
            ->leftJoin(Moving::tableName() . ' as comingpack', 'comingpack.receiver_id', '=', 'sp.id')
            ->leftJoin(Moving::tableName() . ' as goingpack', 'goingpack.sender_id', '=', 'sp.id')
            ->leftJoin(MovingProduct::tableName() . ' as coming', 'coming.moving_id', '=', 'comingpack.id')
            ->leftJoin(MovingProduct::tableName() . ' as going', 'going.moving_id', '=', 'goingpack.id')
            ->select(DB::raw('sp.id id, sum(coming.amount) coming_amount, sum(going.amount) going_amount'))
            ->whereNull('comingpack.received_date')->whereNull('goingpack.received_date')
            ->where(['sp.parent_id' => $id])
            ->groupBy('sp.id')
            ->get()->keyBy('id');

        foreach ($data as $key=>$value) {
            $data[$key]->coming = isset($MovingProducts[$key]) ? $MovingProducts[$key]->coming_amount : 0;
            $data[$key]->going = isset($MovingProducts[$key]) ? $MovingProducts[$key]->going_amount : 0;
        }
        foreach ($data as $key=>$value) {foreach ($value as $key2=>$value2) {
            $data[$key]->$key2 = (int) $value2;
        }}
        return $data;
    }

    // при создании проекта c parent_id=0 должен создаваться ещё один с такими же name и alias и parent_id,
    // как у только что созданного родителя
    // позднее что-то умнее придумается, чтобы alias и name всё же были уникальны
}
