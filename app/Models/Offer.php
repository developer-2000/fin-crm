<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offer extends BaseModel
{
    /**
     * @var array
     */
    public $fillable = ['name', 'partner_id', 'geo', 'company_id', 'status', 'offer_id'];

    /**
     * @return HasMany
     */
    public function scripts()
    {
        return $this->hasMany(Script::class);
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    /**
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany
     */
    public function offerScript()
    {
        return $this->hasMany(OffersScript::class);
    }

    /**
     * @return HasOne
     */
    public function planRateOffer()
    {
        return $this->hasOne(PlanRate::class);
    }

    /**
     * @return BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'geo', 'code');
    }
}