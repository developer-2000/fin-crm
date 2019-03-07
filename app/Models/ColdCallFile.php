<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColdCallFile extends Model
{
    public $fillable = ['file_name', 'status', 'comment', 'geo', 'company_id', 'campaign_id'];

    /**
     * Получить записи по листам холодных продаж .
     */
    public function coldCallList()
    {
        return $this->hasMany('App\Models\ColdCallList');
    }

    /**
     * Получить записи по листам холодных продаж .
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'geo', 'code');
    }

    /**
     * @return BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Campaign::class);
    }

    /*
    * Scope
    */
    public function scopeSort($query)
    {
      return $query->orderBy('id', 'desc');
    }
}
