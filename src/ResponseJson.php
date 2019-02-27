<?php

namespace StriderTech\PeachPayments;

/**
 * Class ResponseJson
 * @package StriderTech\PeachPayments
 */
class ResponseJson
{
    /**
     * @var string
     */
    private $jsonString;
    /**
     * @var \stdClass
     */
    private $json;

    /**
     * @var bool
     */
    private $success = false;

    /**
     * ResponseJson constructor.
     * @param string $json
     * @param boolean $success
     */
    public function __construct($json, $success)
    {
        $this->jsonString = $json;
        $this->json = \GuzzleHttp\json_decode($json);
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->jsonString;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->getResultCode() === $this->getSuccessCode() && $this->success;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getPropertyIfExists('id');
    }

    /**
     * @return string
     */
    public function getRegistrationId()
    {
        return $this->getPropertyIfExists('registrationId');
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getPropertyIfExists('paymentType');
    }

    /**
     * @return string
     */
    public function getPaymentBrand()
    {
        return $this->getPropertyIfExists('paymentBrand');
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->getPropertyIfExists('amount');
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getPropertyIfExists('currency');
    }

    /**
     * @return string
     */
    public function getDescriptor()
    {
        return $this->getPropertyIfExists('descriptor');
    }

    /**
     * @return \stdClass
     */
    public function getResult()
    {
        return $this->getPropertyIfExists('result');
    }

    /**
     * @return string
     */
    public function getResultCode()
    {
        return $this->getPropertyIfExists('code', $this->getResult());
    }

    /**
     * @return string
     */
    public function getResultMessage()
    {
        return $this->getPropertyIfExists('message', $this->getResult()) ?: $this->getResultDescription();
    }

    /**
     * @return string
     */
    public function getResultDescription()
    {
        return $this->getPropertyIfExists('description', $this->getResult());
    }

    /**
     * @return \stdClass
     */
    public function getCard()
    {
        return $this->getPropertyIfExists('card');
    }

    /**
     * @return \stdClass|string
     */
    public function getCardBin()
    {
        return $this->getPropertyIfExists('bin', $this->getCard());
    }

    /**
     * @return \stdClass|string
     */
    public function getCardLast4Digits()
    {
        return $this->getPropertyIfExists('last4Digits', $this->getCard());
    }

    /**
     * @return \stdClass|string
     */
    public function getCardHolder()
    {
        return $this->getPropertyIfExists('holder', $this->getCard());
    }

    /**
     * @return \stdClass|string
     */
    public function getCardExpiryMonth()
    {
        return $this->getPropertyIfExists('expiryMonth', $this->getCard());
    }

    /**
     * @return \stdClass|string
     */
    public function getCardExpiryYear()
    {
        return $this->getPropertyIfExists('expiryYear', $this->getCard());
    }

    /**
     * @return \stdClass|string
     */
    public function getRisk()
    {
        return $this->getPropertyIfExists('risk');
    }

    /**
     * @return \stdClass|string
     */
    public function getRiskScore()
    {
        return $this->getPropertyIfExists('score', $this->getRisk());
    }

    /**
     * @return \stdClass|string
     */
    public function getBuildNumber()
    {
        return $this->getPropertyIfExists('buildNumber');
    }

    /**
     * @return \stdClass|string
     */
    public function getTimestamp()
    {
        return $this->getPropertyIfExists('timestamp');
    }

    /**
     * @return \stdClass|string
     */
    public function getNdc()
    {
        return $this->getPropertyIfExists('ndc');
    }

    /**
     * @return \stdClass|string
     */
    public function getThreeDSecure()
    {
        return $this->getPropertyIfExists('threeDSecure');
    }

    /**
     * @return \stdClass|string
     */
    public function getThreeDSecureEci()
    {
        return $this->getPropertyIfExists('eci', $this->getThreeDSecure());
    }

    /**
     * @param $propertyName
     * @param $tmpProperty
     * @return string|\stdClass
     */
    protected function getPropertyIfExists($propertyName, $tmpProperty = null)
    {
        // use tmp stdObject
        if ($tmpProperty !== null) {
            if (property_exists($tmpProperty, $propertyName)) {
                return $tmpProperty->{$propertyName};
            }
        }
        
        // use main stdObject
        if (property_exists($this->json, $propertyName)) {
            return $this->json->{$propertyName};
        }

        // nothing found lets return an empty string
        return '';
    }

    /**
     * @return string
     */
    public function getSuccessCode()
    {
        $code = '000.000.000';

        if (config('peachpayments.test_mode') === true) {
            $code = '000.100.110';
        }

        return $code;
    }
}
