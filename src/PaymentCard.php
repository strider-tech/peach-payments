<?php

namespace StriderTech\PeachPayments;

use Illuminate\Database\Eloquent\SoftDeletes;

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
        'brand',
        'holder',
        'last_four',
        'expiry_month',
        'expiry_year',
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
