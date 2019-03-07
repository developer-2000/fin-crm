<?php

namespace App\Providers;

use App\Extensions\SessionHandler;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $connection = $this->app['config']['session.connection'];
        $table = $this->app['config']['session.table'];
        $lifetime = $this->app['config']['session.lifetime'];
        $thisApp = $this->app;
        $this->app['session']->extend('database', function($app) use ($connection, $table,$lifetime, $thisApp){
            return new SessionHandler(
                $this->app['db']->connection($connection),
                $table,
                $lifetime,
                $thisApp
            );
        });
    }
}
