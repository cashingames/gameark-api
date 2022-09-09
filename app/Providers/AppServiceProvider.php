<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;

use App\Services\SMS\TermiiService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\SMS\SMSProviderInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::listen(function ($query) {
            $query = $query->sql;
            // $query->time;
        });

        $this->app->bind(SMSProviderInterface::class, function($app){
            $api_key = config('services.termii.api_key');
            return new TermiiService($api_key);
        });
    }
}
