<?php

namespace StriderTech\PeachPayments;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * @package StriderTech
 */
class Payment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be visible for arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'payment_card_id',
        'payment_type',
        'amount',
        'currency',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentCard()
    {
        return $this->belongsTo(PaymentCard::class);
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * @param string $paymentType
     * @return $this
     */
    public function setPaymentType($paymentType)
    {
        $this->payment_type = $paymentType;
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
     * @return mixed
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
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * @param $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        $this->transaction_id = $transactionId;
        return $this;
    }

    /**
     * @param PaymentCard $paymentCard
     * @return $this
     */
    public function fromPaymentCard(PaymentCard $paymentCard)
    {
        $this->setUserId($paymentCard->getUserId())
            ->setPaymentRemoteId($paymentCard->getPaymentRemoteId())
            ->paymentCard()->associate($paymentCard)
        ;

        return $this;
    }
}
