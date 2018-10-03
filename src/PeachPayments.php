<?php

namespace StriderTech\PeachPayments;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Cards\Delete;
use StriderTech\PeachPayments\Cards\Store;
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
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Store $store
     * @return ResponseJson|string
     */
    public function storeCard(Store $store)
    {
        $result = $store->process();

        return $result;
    }

    /**
     * @param string $token
     * @param $userId
     * @return ResponseJson|string
     */
    public function storeCardByToken($token, $userId)
    {
        $paymentStatus = new Status($this->getClient());
        $paymentStatus->setTransactionId($token);
        $paymentStatusResult = $paymentStatus->process();

        $card = new PaymentCard();
        $card->fromAPIResponse($paymentStatusResult);
        $card->setUserId($userId);
        $card->save();

        return $paymentStatusResult;
    }

    /**
     * @param $token
     * @return mixed|ResponseJson
     */
    public function getPaymentStatusByToken($token)
    {
        $paymentStatus = new Status($this->getClient());
        $paymentStatus->setTransactionId($token);
        $paymentStatusResult = $paymentStatus
            ->setTransactionId($token)
            ->process();

        return $paymentStatusResult;
    }

    /**
     * @param $token
     * @return mixed|ResponseJson
     */
    public function getCardByToken($token)
    {
        return PaymentCard::where('payment_remote_id', $token)->first();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getCardById($id)
    {
        return PaymentCard::find($id);
    }

    /**
     * @param PaymentCard $paymentCard
     * @return ResponseJson|string
     */
    public function deleteCard(PaymentCard $paymentCard)
    {
        $cardDelete = new Delete($this->getClient());
        $cardDelete->setPaymentCard($paymentCard);
        $result = $cardDelete->process();

        return $result;
    }

    /**
     * @param PaymentCard $paymentCard
     * @return ResponseJson|string
     */
    public function fromPaymentCard(PaymentCard $paymentCard)
    {
        $storeCard = new Store($this->getClient());

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
            $url = $this->getClient()->getApiUri() . $resourcePath .
                '?authentication.userId=' . $this->getClient()->getConfig()->getUserId() .
                '&authentication.password=' . $this->getClient()->getConfig()->getPassword() .
                '&authentication.entityId=' . $this->getClient()->getConfig()->getEntityId();

            $response = $this->getClient()->getClient()->get($url);

            return new ResponseJson((string)$response->getBody(), true);
        } catch (RequestException $e) {
            return new ResponseJson((string)$e->getResponse()->getBody(), false);
        }
    }
}