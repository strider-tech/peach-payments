<?php

namespace StriderTech\PeachPayments;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use StriderTech\PeachPayments\Cards\Store;

/**
 * Class PaymentCard
 * @package StriderTech
 */
class PaymentCard extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be visible for arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'number',
        'brand',
        'holder',
        'last_four',
        'expiry_month',
        'expiry_year',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(config('peachpayments.model'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    /**
     * @return string
     */
    public function getCardBrand()
    {
        return $this->brand;
    }

    /**
     * @param string $cardBrand
     * @return $this
     */
    public function setCardBrand($cardBrand)
    {
        $this->brand = $cardBrand;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->number;
    }

    /**
     * @param string $cardNumber
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->number = $cardNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardHolder()
    {
        return $this->holder;
    }

    /**
     * @param string $cardHolder
     * @return $this
     */
    public function setCardHolder($cardHolder)
    {
        $this->holder = $cardHolder;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardExpiryMonth()
    {
        return $this->expiry_month;
    }

    /**
     * @param string $cardExpiryMonth
     * @return $this
     */
    public function setCardExpiryMonth($cardExpiryMonth)
    {
        $this->expiry_month = $cardExpiryMonth;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardExpiryYear()
    {
        return $this->expiry_year;
    }

    /**
     * @param string $cardExpiryYear
     * @return $this
     */
    public function setCardExpiryYear($cardExpiryYear)
    {
        $this->expiry_year = $cardExpiryYear;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardCvv()
    {
        return $this->cvv;
    }

    /**
     * @param string $cardCvv
     * @return $this
     */
    public function setCardCvv($cardCvv)
    {
        $this->cvv = $cardCvv;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentRemoteId()
    {
        return $this->payment_remote_id;
    }

    /**
     * @param mixed $paymentRemoteId
     * @return $this
     */
    public function setPaymentRemoteId($paymentRemoteId)
    {
        $this->payment_remote_id = $paymentRemoteId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastFour()
    {
        return $this->last_four;
    }

    /**
     * @param mixed $lastFour
     * @return $this
     */
    public function setLastFour($lastFour)
    {
        $this->last_four = $lastFour;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPrimary()
    {
        return $this->is_primary;
    }

    /**
     * @param mixed $isPrimary
     */
    public function setIsPrimary($isPrimary)
    {
        $this->is_primary = $isPrimary;
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
     * @param Store $store
     * @return $this
     */
    public function fromStore(Store $store)
    {
        $this->setCardBrand($store->getCardBrand())
            ->setCardNumber($store->getCardNumber())
            ->setCardHolder($store->getCardHolder())
            ->setCardExpiryMonth($store->getCardExpiryMonth())
            ->setCardExpiryYear($store->getCardExpiryYear())
            ->setCardCvv($store->getCardCvv())
            ->setLastFour($store->getLastFour())
            ->setPaymentRemoteId($store->getPaymentRemoteId())
        ;

        return $this;
    }

    /**
     * @param ResponseJson $responseJson
     * @return $this
     */
    public function fromAPIResponse(ResponseJson $responseJson)
    {
        $this->setCardBrand($responseJson->getPaymentBrand())
            ->setCardHolder($responseJson->getCardHolder())
            ->setCardExpiryMonth($responseJson->getCardExpiryMonth())
            ->setCardExpiryYear($responseJson->getCardExpiryYear())
            ->setLastFour($responseJson->getCardLast4Digits())
            ->setPaymentRemoteId($responseJson->getId())
        ;

        return $this;
    }
}
