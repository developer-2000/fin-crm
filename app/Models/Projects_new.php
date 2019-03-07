<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


use App\Models\Api\Integration;

class Projects_new extends Model
{

    protected $table = 'projects_new';
    protected $guarded = [];

    /**
     * @return string
     */
    public static function tableName()
    {
        return with(new static)->getTable();
    }


// 1 выбрать название отношения к проекту
    public static function StatusName($value){

        if((int)$value > 0 && (int)$value <= 3){
            $Data = [
                1 => 'Project',
                2 => 'SubProject',
                3 => 'Division'
            ];
        return $Data[$value];
        }
    return null;
    }

// 2 взвращает значение поля negative указанного id проекта
    public static function CheckValNeg($id)
    {
        return Projects_new::where('id', (int)$id)->pluck('negative')->all()[0];
    }


//     =============================================================
//     =============================================================
//     ==================         SCOPE            =================
//     =============================================================
//     =============================================================


    public function scopeCheckProj($query)
    {
        if (Auth::user()->project_id) {
            $query->where(function ($q) {
                if (Auth::user()->project_id) {
                    $q->where('id', Auth::user()->project_id);
                }
            });
        }

        return $query;
    }

    public function scopeCheckSub($query)
    {
        if (Auth::user()->sub_project_id) {
            $query->where(function ($q) {
                if (Auth::user()->sub_project_id) {
                    $q->orWhere('id', Auth::user()->sub_project_id);
                }
            });
        }

        return $query;
    }

// 2 =========================
    public function scopeprojW1($query, $elem, $project_id)
    {
        return $query->where($elem, $project_id);
    }

// 3 ==========================

    public function scopeprojW3($query, $elem, $project_id, $id)
    {
        return $query->where($elem, $project_id)->orderBy($id, 'asc')->pluck('name', 'id');
    }

// 4 ==========================
    public function scopeprojW4($query, $project_id, $id)
    {
        return $query->whereIn('parent_id', $project_id)->orderBy($id, 'asc')->pluck('name', 'id');
    }

// 4 ==========================
    public function scopeprojW5($query, $project_id)
    {
        return $query->whereIn('parent_id', $project_id)->pluck('id');
    }



//     =============================================================
//     =============================================================
//     ================== Связи таблиц             =================
//     =============================================================
//     =============================================================


    /**
//     * @return HasMany
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


    /**
     * @return BelongsTo
     */
    public function parent() {
        return $this->belongsTo(Projects_new::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function children() {
        return $this->hasMany(Projects_new::class, 'parent_id', 'id');
    }



































}
