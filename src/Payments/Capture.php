<?php

namespace StriderTech\PeachPayments\Payments;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\ClientInterface;
use StriderTech\PeachPayments\Enums\PaymentType;
use StriderTech\PeachPayments\Payment;
use StriderTech\PeachPayments\ResponseJson;

/**
 * todo
 * Class Capture
 * @package StriderTech\PeachPayments\Payments
 */
class Capture implements ClientInterface
{
    /**
     * @var Client
     */
    private $captureClient;
    /**
     * @var string
     */
    private $transactionId;
    /**
     * @var string
     */
    private $currency = 'ZAR';
    /**
     * @var float
     */
    private $amount;

    /**
     * Capture constructor.
     * @param Client $captureClient
     * @param string $transactionId
     * @param float $amount
     * @param string $currency
     */
    public function __construct(Client $captureClient, $transactionId = null, $amount = null, $currency = null)
    {
        $this->captureClient = $captureClient;

        if (!empty($transactionId)) {
            $this->setTransactionId($transactionId);
        }

        if (!empty($amount)) {
            $this->amount = $amount;
        }

        if (!empty($currency)) {
            $this->currency = $currency;
        }
    }

    /**
     * @return ResponseJson
     * @throws \Exception
     */
    public function process()
    {
        $captureClient = $this->captureClient->getClient();
        try {
            $response = $captureClient->post($this->buildUrl(), [
                'form_params' => $this->getCaptureParams()
            ]);
            $jsonResponse = $this->handle($response);

            return $jsonResponse;
        } catch (RequestException $e) {
            throw new \Exception((string)$e->getResponse()->getBody());
        }
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return $this->captureClient->getApiUri() . '/payments/' . $this->getTransactionId();
    }

    /**
     * @return array
     */
    public function getCaptureParams()
    {
        return [
            'authentication.userId' => $this->captureClient->getConfig()->getUserId(),
            'authentication.password' => $this->captureClient->getConfig()->getPassword(),
            'authentication.entityId' => $this->captureClient->getConfig()->getEntityId(),
            'paymentType' => PaymentType::CAPTURE,
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
        ];
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
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
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

    /**
     * @param Payment $payment
     * @return $this
     */
    public function fromPayment(Payment $payment)
    {
        $this->setTransactionId($payment->getPaymentRemoteId())
            ->setAmount($payment->getAmount())
            ->setCurrency($payment->getCurrency())
        ;

        return $this;
    }
}
