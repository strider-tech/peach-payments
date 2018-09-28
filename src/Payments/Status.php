<?php

namespace StriderTech\PeachPayments\Payments;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\ClientInterface;
use StriderTech\PeachPayments\ResponseJson;

/**
 * todo
 * Class Status
 * @package StriderTech\PeachPayments\Payments
 */
class Status implements ClientInterface
{
    const EXCEPTION_EMPTY_STATUS_TID = 500;

    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var null|string
     */
    private $transactionId = '';

    /**
     * Status constructor.
     * @param Client $client
     * @param null $transactionId
     */
    public function __construct(Client $client, $transactionId = null)
    {
        $this->client = $client;
        if (!empty($transactionId)) {
            $this->transactionId = $transactionId;
        }
    }

    /**
     * @return ResponseJson
     * @throws \Exception
     */
    public function process()
    {
        if (empty($this->getTransactionId())) {
            throw new \Exception("Transaction Id can not be empty", self::EXCEPTION_EMPTY_STATUS_TID);
        }

        $client = $this->client->getClient();

        try {
            $response = $client->get($this->buildUrl());
            return new ResponseJson((string)$response->getBody(), true);
        } catch (RequestException $e) {
            return new ResponseJson((string)$e->getResponse()->getBody(), false);
        }
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return $this->client->getApiUri() . '/payments/' . $this->getTransactionId() .
        '?authentication.userId=' . $this->client->getConfig()->getUserId() .
        '&authentication.password=' . $this->client->getConfig()->getPassword() .
        '&authentication.entityId=' . $this->client->getConfig()->getEntityId();
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function dbProcess($response)
    {
        // TODO: Implement dbProcess() method.
    }
}
