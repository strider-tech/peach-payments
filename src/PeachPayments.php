<?php

namespace StriderTech\PeachPayments;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Cards\Delete;
use StriderTech\PeachPayments\Cards\Store;
use StriderTech\PeachPayments\Payments\Capture;
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
     * @param PaymentCard $paymentCard
     * @return ResponseJson|string
     */
    public function storeCard(PaymentCard $paymentCard)
    {
        $store = new Store($this->getClient());
        $store->fromPaymentCard($paymentCard);
        $result = $store->process();

        $card = new PaymentCard();
        $card->fromStore($store);
        $card->save();

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
     * @param $userId
     * @return mixed|ResponseJson
     */
    public function getCardsByUserId($userId)
    {
        return PaymentCard::where('user_id', $userId)->get();
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
     * @param Payment $payment
     * @return ResponseJson
     */
    public function pay(Payment $payment)
    {
        $capture = new Capture($this->getClient());
        $capture->fromPayment($payment);
        $captureResult = $capture->process();

        $payment->setPaymentType($captureResult->getPaymentType())
            ->setTransactionId($captureResult->getId());
        $payment->save();

        return $captureResult;
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

        $card = PaymentCard::where('payment_remote_id', $cardDelete->getTransactionId())->first();
        $card->delete();

        return $result;
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