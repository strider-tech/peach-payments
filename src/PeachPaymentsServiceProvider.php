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
        $this->publishes([
            __DIR__.'/config/peachpayments.php' => config_path('peachpayments.php'),
        ]);

        $timestamp = date('Y_m_d_His', time());

        if (! class_exists('CreatePaymentCardsTable')) {
            $this->publishes([
                __DIR__ . '/migrations/create_payment_cards_table.php' => $this->app->databasePath()."/migrations/{$timestamp}_create_payment_cards_table.php",
            ], 'migrations');
        }

        if (! class_exists('CreatePaymentsTable')) {
            $this->publishes([
                __DIR__ . '/migrations/create_payments_table.php' => $this->app->databasePath()."/migrations/{$timestamp}_create_payments_table.php",
            ], 'migrations');
        }
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
