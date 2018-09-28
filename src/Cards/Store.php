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

    // todo set columns to PaymentCard
    private $userId;
    private $paymentRemoteId;
    private $type;
    private $isPrimary;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process store card procedure.
     * @return \stdClass
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
            $body = (string)$response->getBody();

            if ($response) {
                $this->dbProcess($body);
            }

            return new ResponseJson($body, true);
        } catch (RequestException $e) {
            return new ResponseJson((string)$e->getResponse()->getBody(), false);
        }
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return $this->client->getApiUri() . '/registrations';
    }

    /**
     * @param $response
     * @return string
     */
    public function dbProcess($response)
    {
        $card = new PaymentCard();
        $card->fromStore($this);
        $card->save();
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
}
