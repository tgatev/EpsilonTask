<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \App\OAuth2Wrapper\EpsilonApiClient;

class EpsilonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        \App::bind('EpsilonApi', function(){
            return new EpsilonApiClient();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
