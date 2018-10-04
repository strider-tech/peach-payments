<?php

namespace StriderTech\PeachPayments\Enums;

class PaymentType
{
    const CAPTURE = 'CP';
    const DEBIT = 'DB';
    const PREAUTHORISATION = 'PA';
    const REFUND = 'RF';
    const REVERSAL = 'RV';
}