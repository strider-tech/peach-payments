<?php

namespace StriderTech\PeachPayments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model as Eloquent;
use StriderTech\PeachPayments\Billable;

class User extends Eloquent
{
    use Billable;

    protected $guarded = [];
}