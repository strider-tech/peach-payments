<?php

namespace StriderTech\PeachPayments\Tests;

use StriderTech\PeachPayments\Client;

class PeachPaymentsTest extends TestCase
{
    /**
     * Getting facade access
     * @return void
     */
    public function testInitFacade()
    {
        \PeachPayments::shouldReceive('getClient')
            ->once()
        ;

        \PeachPayments::getClient();
    }

    /**
     * Getting facade client
     */
    public function testGetClient()
    {
        $client = \PeachPayments::getClient();

        $this->assertInstanceOf(Client::class, $client);
    }
}