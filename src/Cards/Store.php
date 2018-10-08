<?php

namespace StriderTech\PeachPayments\Cards;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\ClientInterface;
use StriderTech\PeachPayments\PaymentCard;
use StriderTech\PeachPayments\ResponseJson;

/**
 * Class Store
 * @package StriderTech\PeachPayments\Cards
 */
class Store extends AbstractCard implements ClientInterface
{
    /**
     * Oppwa client object.
     *
     * @var Client
     */
    private $client;
    private $paymentRemoteId;
    private $lastFour;

    /**
     * Store constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getLastFour()
    {
        return $this->lastFour;
    }

    /**
     * @param mixed $lastFour
     * @return $this
     */
    public function setLastFour($lastFour)
    {
        $this->lastFour = $lastFour;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentRemoteId()
    {
        return $this->paymentRemoteId;
    }

    /**
     * @param mixed $paymentRemoteId
     * @return $this
     */
    public function setPaymentRemoteId($paymentRemoteId)
    {
        $this->paymentRemoteId = $paymentRemoteId;

        return $this;
    }

    /**
     * Process store card procedure.
     * @return ResponseJson|string
     * @throws \Exception
     */
    public function process()
    {
        try {
            $this->isCardDetailsValid();
        } catch (\Exception $e) {
            return (object)['result' => ['code' => $e->getCode(), 'message' => $e->getMessage()]];
        }

        $client = $this->client->getClient();

        try {
            $response = $client->post($this->buildUrl(), [
                'form_params' => $this->getParams()
            ]);
            $jsonResponse = $this->handle($response);

            return $jsonResponse;
        } catch (RequestException $e) {
            throw new \Exception((string)$e->getResponse()->getBody());
        }
    }

    /**
     * @param $response
     * @return ResponseJson|string
     */
    public function handle($response)
    {
        $body = (string)$response->getBody();
        $jsonResponse = new ResponseJson($body, true);

        $this->setLastFour($jsonResponse->getCardLast4Digits())
            ->setPaymentRemoteId($jsonResponse->getId())
        ;

        return $jsonResponse;
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return $this->client->getApiUri() . '/registrations';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCard($id)
    {
        return PaymentCard::findOrFail($id);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [
            'authentication.userId' => $this->client->getConfig()->getUserId(),
            'authentication.password' => $this->client->getConfig()->getPassword(),
            'authentication.entityId' => $this->client->getConfig()->getEntityId(),
            'paymentBrand' => $this->getCardBrand(),
            'card.number' => $this->getCardNumber(),
            'card.holder' => $this->getCardHolder(),
            'card.expiryMonth' => $this->getCardExpiryMonth(),
            'card.expiryYear' => $this->getCardExpiryYear(),
            'card.cvv' => $this->getCardCvv()
        ];
    }

    /**
     * @param PaymentCard $paymentCard
     * @return ResponseJson|string
     */
    public function fromPaymentCard(PaymentCard $paymentCard)
    {
        $this->setCardBrand($paymentCard->getCardBrand())
            ->setCardNumber($paymentCard->getCardNumber())
            ->setCardHolder($paymentCard->getCardHolder())
            ->setCardExpiryMonth($paymentCard->getCardExpiryMonth())
            ->setCardExpiryYear($paymentCard->getCardExpiryYear())
            ->setCardCvv($paymentCard->getCardCvv())
        ;

        return $this;
    }
}
