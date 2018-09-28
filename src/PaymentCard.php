<?php

namespace StriderTech\PeachPayments;

use Illuminate\Database\Eloquent\SoftDeletes;
use StriderTech\PeachPayments\Cards\Store;

/**
 * Class PaymentCard
 * @package StriderTech
 */
class PaymentCard extends BasicModel
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
        'cvv',
        'type',
        'is_primary',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_primary' => 'boolean'
    ];

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
            ->setCardCvv($store->getCardCvv());

        return $this;
    }

    /**
     * @param $userId
     * @param $type
     */
    public static function removePrimaryFlagFromCurrentCards($userId, $type)
    {
        self::where('user_id', $userId)
            ->where('type', $type)
            ->update(['is_primary' => false]);
    }

    /**
     * @param $userId
     */
    public function reassignPrimaryFlagToAnotherCard($userId)
    {
        $card = self::where('user_id', $userId)
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->orderBy('is_primary', 'desc')
            ->first();

        if ($card) {
            $card->update(['is_primary' => true]);
        }
    }

    /**
     * @param $isPrimary
     * @param null $type
     */
    public function updatePrimaryFlag($isPrimary, $type = null)
    {
        if ($isPrimary) {
            self::removePrimaryFlagFromCurrentCards($type ?: $this->type);
        }
        $this->is_primary = $isPrimary;
    }

    /**
     * @param null $type
     */
    public function updateType($type = null)
    {
        if ($type && $type !== $this->type) {
            if ($this->is_primary) {
                self::removePrimaryFlagFromCurrentCards($type);
            }

            $this->reassignPrimaryFlagToAnotherCard();

            $this->type = $type;
        }
    }
}
