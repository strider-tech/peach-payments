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
        $this->app->bind('PeachPayments', function ($app) {
            return new PeachPayments(config('peachpayments'));
        });
    }
}
