<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScriptDetail extends Model
{

    public $fillable = ['script_id', 'script_category_id', 'block', 'status', 'text', 'img', 'position', 'geo'];

    /**
     * @return BelongsTo
     */
    public function script(){
        return $this->belongsTo(Script::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(){
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'geo', 'code');
    }
}
