<?php

namespace StriderTech\PeachPayments\Enums;

class CardException
{
    const EXCEPTION_BAD_CONFIG = 100;
    const EXCEPTION_EMPTY_TID = 300;
    const INVALID = 400;
    const VAR_EMPTY = 400;
    const CVV_INVALID = 400;
    const EXCEPTION_EMPTY_STATUS_TID = 500;
}