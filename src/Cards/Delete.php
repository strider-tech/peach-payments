<?php

namespace StriderTech\PeachPayments\Cards;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\ClientInterface;
use StriderTech\PeachPayments\ResponseJson;

/**
 * todo
 * Class Delete
 * @package StriderTech\PeachPayments\Cards
 */
class Delete implements ClientInterface
{
    const EXCEPTION_EMPTY_TID = 300;

    /**
     * Oppwa client object.
     *
     * @var Client
     */
    private $client;

    /**
     * Transaction Id.
     *
     * @var string
     */
    private $transactionId;

    /**
     * Delete constructor.
     * @param Client $client
     * @param string $transactionId
     */
    public function __construct(Client $client, $transactionId = null)
    {
        $this->client = $client;
        if (!empty($transactionId)) {
            $this->transactionId = $transactionId;
        }
    }

    /**
     * Process delete procedure.
     *
     * @return \stdClass
     * @throws \Exception
     */
    public function process()
    {
        if (empty($this->transactionId)) {
            throw new \Exception("Transaction Id can not be empty", self::EXCEPTION_EMPTY_TID);
        }

        $client = $this->client->getClient();

        try {
            $response = $client->delete($this->buildUrl());
            return new ResponseJson((string)$response->getBody(), true);
        } catch (RequestException $e) {
            return new ResponseJson((string)$e->getResponse()->getBody(), false);
        }
    }

    /**
     * Build url to use.
     *
     * @return string
     */
    public function buildUrl()
    {
        return $this->client->getApiUri() . '/registrations/' . $this->getTransactionId() .
        '?authentication.userId=' . $this->client->getConfig()->getUserId() .
        '&authentication.password=' . $this->client->getConfig()->getPassword() .
        '&authentication.entityId=' . $this->client->getConfig()->getEntityId();
    }

    /**
     * Get transaction id.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set transaction id.
     *
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Make process with database by API response
     *
     * @param $response
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dbProcess($response)
    {
        // TODO: Implement dbProcess() method.
    }
}
