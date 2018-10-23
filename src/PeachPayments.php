<?php

namespace StriderTech\PeachPayments;

use StriderTech\PeachPayments\Payments\Notification;

class PeachPayments
{
    /**
     * @var Client
     */
    public $client;
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var PaymentCard
     */
    public $card;

    /**
     * PeachPayments constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
        $authConfig = new Configuration(
            $config['user_id'],
            $config['password'],
            $config['entity_id']
        );

        $client = new Client($authConfig);
        $client->setTestMode($config['test_mode']);
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $resourcePath
     * @return ResponseJson
     */
    public function getNotificationStatus($resourcePath)
    {
        $notification = new Notification($this->getClient(), $resourcePath);
        $result = $notification->process();

        return $result;
    }
}