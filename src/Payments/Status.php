<?php

namespace StriderTech\PeachPayments\Payments;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\ClientInterface;
use StriderTech\PeachPayments\Enums\CardException;
use StriderTech\PeachPayments\ResponseJson;

/**
 * todo
 * Class Status
 * @package StriderTech\PeachPayments\Payments
 */
class Status implements ClientInterface
{
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
     * @return ResponseJson|string
     * @throws \Exception
     */
    public function process()
    {
        if (empty($this->getTransactionId())) {
            throw new \Exception("Transaction Id can not be empty", CardException::EXCEPTION_EMPTY_STATUS_TID);
        }

        $client = $this->client->getClient();

        try {
            $response = $client->get($this->buildUrl());
            $jsonResponse = $this->handle($response);

            if ($jsonResponse->isSuccess()) {
                $this->dbProcess($jsonResponse);
            }

            return $jsonResponse;
        } catch (RequestException $e) {
            throw new \Exception((string)$e->getResponse()->getBody());
//            return new ResponseJson((string)$e->getResponse()->getBody(), false);
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

    /**
     * @param $response
     * @return bool
     */
    public function dbProcess($response)
    {
        return true;
    }

    /**
     * Handle response from PP API
     *
     * @param $response
     * @return ResponseJson
     */
    public function handle($response)
    {
        $body = (string)$response->getBody();
        $jsonResponse = new ResponseJson($body, true);

        return $jsonResponse;
    }
}
