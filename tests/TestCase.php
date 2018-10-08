<?php

namespace StriderTech\PeachPayments\Tests;

use Illuminate\Support\Facades\Schema;
use StriderTech\PeachPayments\Facade\PeachPaymentsFacade;
use StriderTech\PeachPayments\PeachPaymentsServiceProvider;
use Carbon\Carbon;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Add database changes
     */
    protected function setUp()
    {
        parent::setUp();

        Schema::defaultStringLength(191);

        include_once __DIR__ . '/../src/migrations/create_payment_cards_table.php';
        include_once __DIR__ . '/../src/migrations/create_payments_table.php';

        $this->loadLaravelMigrations();

        (new \CreatePaymentCardsTable())->up();
        (new \CreatePaymentsTable())->up();

        $this->createUser();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('peachpayments', [
            'user_id' => '8a8294174e735d0c014e78cf266b1794',
            'password' => 'qyyfHCN83e',
            'entity_id' => '8a8294174e735d0c014e78cf26461790',
            'test_mode' => true,
            'api_uri_test' => 'https://test.oppwa.com/',
            'api_uri_live' => 'https://oppwa.com/',
            'api_uri_version' => 'v1',
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

    /**
     * Create test user
     */
    private function createUser()
    {
        $now = Carbon::now();
        \DB::table('users')->insert([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => \Hash::make('123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

}
