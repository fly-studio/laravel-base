<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Foundation\Console\PackageDiscoverCommand;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving(PackageDiscoverCommand::class, function() {
            $path = realpath($GLOBALS['loader']->findFile(get_class($this->app)));
            $this->app->make(PackageManifest::class)->vendorPath = substr($path, 0, strrpos($path, 'vendor') + 6).DIRECTORY_SEPARATOR;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
