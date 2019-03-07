<?php

namespace App\Models;

class Tag extends Model
{
    public $fillable = [
        'name',
        'partner_id',
        'value',
    ];

    public $timestamps = false;

    const TAG_MEDIUM = 'tag_medium';
    const TAG_SOURCE = 'tag_source';
    const TAG_TERM = 'tag_term';
    const TAG_CONTENT = 'tag_content';
    const TAG_CAMPAIGN = 'tag_campaign';

    public static function getTags()
    {
        return [
            self::TAG_MEDIUM   => trans('general.' . self::TAG_MEDIUM),
            self::TAG_SOURCE   => trans('general.' . self::TAG_SOURCE),
            self::TAG_TERM     => trans('general.' . self::TAG_TERM),
            self::TAG_CONTENT  => trans('general.' . self::TAG_CONTENT),
            self::TAG_CAMPAIGN => trans('general.' . self::TAG_CAMPAIGN),
        ];
    }

    public function scopeMedium($query)
    {
        return $query->where('name', self::TAG_MEDIUM);
    }
    public function scopeSource($query)
    {
        return $query->where('name', self::TAG_SOURCE);
    }
    public function scopeTerm($query)
    {
        return $query->where('name', self::TAG_TERM);
    }
    public function scopeContent($query)
    {
        return $query->where('name', self::TAG_CONTENT);
    }
    public function scopeCampaign($query)
    {
        return $query->where('name', self::TAG_CAMPAIGN);
    }
    public function findByWordTag($word, $tagName, $partnerData = []){
        $partnerIds = [];
        if (is_array($partnerData)) {
            $partnerIds = array_unique($partnerData);
        } elseif (is_string($partnerData) || is_int($partnerData)) {
            $partnerIds[] = $partnerData;
        }

        $query = Tag::where('value' , 'LIKE', '%' . $word . '%')->where('name', $tagName);

        if ($partnerIds) {
            $query->whereIn('partner_id', $partnerIds);
        }

        return $query->get();
    }
}
