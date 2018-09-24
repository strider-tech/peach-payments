<?php

namespace StriderTech\PeachPayments\Test;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use StriderTech\PeachPayments\Facade\PeachPaymentsFacade;
use StriderTech\PeachPayments\PeachPaymentsServiceProvider;

abstract class TestCase extends BaseTestCase
{
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
        return [
            'peachpayments' => PeachPaymentsFacade::class,
        ];
    }
}
