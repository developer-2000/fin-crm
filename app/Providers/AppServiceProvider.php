<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use \Maatwebsite\Excel\Sheet;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
          $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
      });

// отображение работы базы
//      DB::listen(function ($query){
//          dump($query->sql);
//          dump($query->bindings);
//      });


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (($this->app->environment() !== 'production') && !empty(env('MG'))) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
        //
    }
}
