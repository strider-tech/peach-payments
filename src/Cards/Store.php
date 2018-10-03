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
    public $model;

    /**
     * Oppwa client object.
     *
     * @var Client
     */
    private $client;
    private $type;
    private $isPrimary;
    private $userId;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Process store card procedure.
     * @return ResponseJson|string
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

            if ($jsonResponse->isSuccess()) {
                $this->dbProcess($jsonResponse);
            }

            return $jsonResponse;
        } catch (RequestException $e) {
            return new ResponseJson((string)$e->getResponse()->getBody(), false);
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
//            ->setType($jsonResponse->getPaymentType())
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
     * @param ResponseJson $response
     * @return string
     */
    public function dbProcess($response)
    {
        $card = new PaymentCard();
        $card->fromStore($this);
        $card->save();
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
     * @return mixed
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * @param mixed $isPrimary
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
    }
}
