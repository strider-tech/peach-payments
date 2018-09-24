<?php

namespace StriderTech\PeachPayments;

use Illuminate\Support\ServiceProvider;

class PeachPaymentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        $this->publishes([
            __DIR__.'/config/peachpayments.php' => config_path('peachpayments.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PeachPayments::class, function () {
            return new PeachPayments(config('peachpayments'));
        });
        $this->app->alias(PeachPayments::class, 'peachpayments');

//        $this->app->bind('peachpayments', function ($app) {
//            return new PeachPayments(config('peachpayments'));
//        });
    }
}
