<?php

namespace StriderTech\PeachPayments;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Cards\Brands;
use StriderTech\PeachPayments\Cards\Delete;
use StriderTech\PeachPayments\Cards\Store;
use StriderTech\PeachPayments\Enums\CardBrand;
use StriderTech\PeachPayments\Payments\Status;

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
     * @param Store $store
     * @return Store
     */
    public function storeCard(Store $store)
    {
        $store->process();

        return $store;
    }

    /**
     * @param Store $storeCardResult
     * @return \stdClass
     */
    public function deleteCard(Store $storeCardResult)
    {
        $cardDelete = new Delete($this->client);
        $cardDelete->setTransactionId($storeCardResult->getId());
        $cardDeleteResult = $cardDelete->process();

        return $cardDeleteResult;
    }

    /**
     * @param $transactionId
     * @return mixed|ResponseJson
     */
    public function getPaymentStatus($transactionId)
    {
        $paymentStatus = new Status($this->client);
        $paymentStatusResult = $paymentStatus
            ->setTransactionId($transactionId)
            ->process();

        return $paymentStatusResult;
    }

    /**
     * @param PaymentCard $paymentCard
     * @return \stdClass
     */
    public function fromPaymentCard(PaymentCard $paymentCard)
    {
        $storeCard = new Store($this->client);

        $storeCardResult = $storeCard->setCardBrand($paymentCard->getCardBrand())
            ->setCardNumber($paymentCard->getCardNumber())
            ->setCardHolder($paymentCard->getCardHolder())
            ->setCardExpiryMonth($paymentCard->getCardExpiryMonth())
            ->setCardExpiryYear($paymentCard->getCardExpiryYear())
            ->setCardCvv($paymentCard->getCardCvv())
            ->process();

        return $storeCardResult;
    }

    /**
     * @param $resourcePath
     * @return ResponseJson
     */
    public function getNotificationStatus($resourcePath)
    {
        try {
            $url = $this->client->getApiUri() . $resourcePath .
                '?authentication.userId=' . $this->client->getConfig()->getUserId() .
                '&authentication.password=' . $this->client->getConfig()->getPassword() .
                '&authentication.entityId=' . $this->client->getConfig()->getEntityId();

            $response = $this->client->getClient()->get($url);

            return new ResponseJson((string)$response->getBody(), true);
        } catch (RequestException $e) {
            return new ResponseJson((string)$e->getResponse()->getBody(), false);
        }
    }
}