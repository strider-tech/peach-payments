<?php

namespace StriderTech\PeachPayments\Enums;

class CardBrand
{
    const VISA = 'VISA';
    const MASTERCARD = 'MASTER';
    const AMEX = 'AMEX';
    const DINERSCLUB = 'DINERS';

    /**
     * @param $brand
     * @return string
     */
    public static function mapWithOppwa($brand)
    {
        if ($brand === self::VISA) {
            return 'visa';
        }
        if ($brand === self::MASTERCARD) {
            return 'mastercard';
        }
        if ($brand === self::AMEX) {
            return 'amex';
        }
        if ($brand === self::DINERSCLUB) {
            return 'dinersclub';
        }

        return 'other';
    }
}