<?php

namespace StriderTech\PeachPayments\Cards;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\ClientInterface;
use StriderTech\PeachPayments\Enums\Exception;
use StriderTech\PeachPayments\PaymentCard;
use StriderTech\PeachPayments\ResponseJson;

/**
 * Class Delete
 * @package StriderTech\PeachPayments\Cards
 */
class Delete implements ClientInterface
{
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
     * @var PaymentCard
     */
    private $paymentCard;

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
     * @return mixed
     */
    public function getPaymentCard()
    {
        return $this->paymentCard;
    }

    /**
     * @param PaymentCard $paymentCard
     */
    public function setPaymentCard(PaymentCard $paymentCard)
    {
        $this->paymentCard = $paymentCard;
        $this->setTransactionId($this->paymentCard->getPaymentRemoteId());
    }

    /**
     * Process delete procedure.
     *
     * @return ResponseJson|string
     * @throws \Exception
     */
    public function process()
    {
        if (empty($this->transactionId)) {
            throw new \Exception("Transaction Id can not be empty", Exception::EMPTY_TID);
        }

        $client = $this->client->getClient();

        try {
            $response = $client->delete($this->buildUrl());
            $jsonResponse = $this->handle($response);

            return $jsonResponse;
        } catch (RequestException $e) {
            throw new \Exception((string)$e->getResponse()->getBody());
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
     * @param $response
     * @return ResponseJson|string
     * @throws \Exception
     */
    public function handle($response)
    {
        $body = (string)$response->getBody();
        $jsonResponse = new ResponseJson($body, true);

        if (!$jsonResponse->isSuccess()) {
            throw new \Exception($jsonResponse->getResultMessage());
        }

        return $jsonResponse;
    }
}
