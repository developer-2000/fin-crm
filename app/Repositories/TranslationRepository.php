<?php

namespace App\Repositories;


use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Facades\Config;

class TranslationRepository
{
    public static function getLanguages()
    {
        return Config::get('translation-manager.available_langs', []);
    }

    public static function getLocales()
    {
        $translations = Translation::select('locale')
            ->groupBy( 'locale' )
            ->where('status', 0)
            ->get()
            ->keyBy('locale')
            ->toArray();

        return array_intersect_key(static::getLanguages(), $translations);
    }
}