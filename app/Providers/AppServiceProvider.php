<?php

namespace App\Providers;

use Agent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*if (config('app.debug'))
        {
            switch (strtolower(Agent::browser())) {
                case 'chrome':
                    app('log')->getMonolog()->pushHandler(new \Monolog\Handler\ChromePHPHandler()); //chrome-php
                    break;
                case 'firefox':
                    app('log')->getMonolog()->pushHandler(new \Monolog\Handler\FirePHPHandler()); //firebug-php
                    break;
                default:
                    app('log')->getMonolog()->pushHandler(new \Monolog\Handler\BrowserConsoleHandler()); //console.log
                    break;
            }
        }*/
        //app('log')->getMonolog()->pushHandler(new \Monolog\Handler\PHPConsoleHandler()); //chrome-php-console
        //instead with DatabaseAuditor
        app(\OwenIt\Auditing\AuditorManager::class)->extend('database', function(){
            return new \App\Drivers\DatabaseAuditor();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
