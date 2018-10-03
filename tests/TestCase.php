<?php

namespace StriderTech\PeachPayments\Tests;

use StriderTech\PeachPayments\Facade\PeachPaymentsFacade;
use StriderTech\PeachPayments\PeachPaymentsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('peachpayments', [
            'model' => App\User::class,
            'user_id' => '8a8294174e735d0c014e78cf266b1794',
            'password' => 'qyyfHCN83e',
            'entity_id' => '8a8294174e735d0c014e78cf26461790',
            'test_mode' => true
        ]);
    }

    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return array|PeachPaymentsServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [PeachPaymentsServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return ['PeachPayments' => PeachPaymentsFacade::class];
    }
}
