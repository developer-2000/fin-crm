<?php

namespace App\Listeners;

use App\Events\TranslationCreatedEvent;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class TranslationCreatedEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TranslationCreatedEvent  $event
     * @return void
     */
    public function handle(TranslationCreatedEvent $event)
    {
        Translation::whereNull('value')
            ->where('group', '!=', '_json')
            ->update([
                'value' => DB::raw("`key`")
            ]);
    }
}
