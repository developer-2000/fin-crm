<?php

namespace App\Models;

class Script extends Model
{
    public $fillable = ['offer_id', 'name', 'status', 'comment', 'company_id'];

    /**
     * get offer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo;
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * get scriptDetails
     * @return \Illuminate\Database\Eloquent\Relations\HasMany;
     */
    public function scriptDetails()
    {
        return $this->hasMany(ScriptDetail::class);
    }

    /**
     * get offerScript
     * @return \Illuminate\Database\Eloquent\Relations\HasMany;
     */
    public function offerScript()
    {
        return $this->hasMany(OffersScript::class);
    }

    /**
     * get category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo;
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
