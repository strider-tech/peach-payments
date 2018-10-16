<?php

namespace StriderTech\PeachPayments;

use StriderTech\PeachPayments\Cards\Delete;
use StriderTech\PeachPayments\Cards\Store;
use StriderTech\PeachPayments\Payments\Capture;
use StriderTech\PeachPayments\Payments\Status;

trait Billable
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cards()
    {
        return $this->hasMany(PaymentCard::class, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    /**
     * @param PaymentCard $paymentCard
     * @return ResponseJson|string
     */
    public function storeCard(PaymentCard $paymentCard)
    {
        $store = new Store(\PeachPayments::getClient());
        $store->fromPaymentCard($paymentCard);
        $result = $store->process();

        $paymentCard->fromStore($store);
        $this->cards()->save($paymentCard);

        return $result;
    }

    /**
     * @param string $token
     * @param PaymentCard|null $paymentCard
     * @return ResponseJson|string
     */
    public function storeCardByToken($token, PaymentCard $paymentCard = null)
    {
        $paymentStatus = new Status(\PeachPayments::getClient());
        $paymentStatus->setTransactionId($token);
        $paymentStatusResult = $paymentStatus->process();

        $paymentCard = $paymentCard ?: new PaymentCard();
        $paymentCard->fromAPIResponse($paymentStatusResult);
        $this->cards()->save($paymentCard);

        return $paymentStatusResult;
    }

    /**
     * @param $token
     * @return mixed|ResponseJson
     */
    public function getCardByToken($token)
    {
        return $this->cards()->where('payment_remote_id', $token)->first();
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
        $cardDelete = new Delete(\PeachPayments::getClient());
        $cardDelete->setPaymentCard($paymentCard);
        $result = $cardDelete->process();

        $paymentCard->delete();

        return $result;
    }

    /**
     * @param string $token
     * @return ResponseJson|string
     */
    public function deleteCardByToken($token)
    {
        $cardDelete = new Delete(\PeachPayments::getClient());
        $cardDelete->setTransactionId($token);
        $result = $cardDelete->process();

        $paymentCard = $this->getCardByToken($token);
        $paymentCard->delete();

        return $result;
    }

    /**
     * @param $token
     * @return mixed|ResponseJson
     */
    public function getPaymentStatusByToken($token)
    {
        $paymentStatus = new Status(\PeachPayments::getClient());
        $paymentStatus->setTransactionId($token);
        $paymentStatusResult = $paymentStatus
            ->setTransactionId($token)
            ->process();

        return $paymentStatusResult;
    }

    /**
     * @param Payment $payment
     * @return ResponseJson
     */
    public function pay(Payment $payment)
    {
        $capture = new Capture(\PeachPayments::getClient());
        $capture->fromPayment($payment);
        $captureResult = $capture->process();

        $payment->setPaymentType($captureResult->getPaymentType())
            ->setTransactionId($captureResult->getId());
        $payment->save();

        return $captureResult;
    }

    /**
     * Get the class name of the billable model.
     *
     * @return string
     */
    public static function paymentModel()
    {
        return config('peachpayments.model', 'App\\User');
    }
}