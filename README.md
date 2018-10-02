# Peach Payments Integration for Laravel

## TODO
- colorful labels (tags)

## Installation

Register package service provider and facade in 'config/app.php':
```php
'providers' => [
    ...
    'StriderTech\PeachPayments\PeachPaymentsServiceProvider',
]

'aliases' => [
    ...
    'PeachPayments' => 'StriderTech\PeachPayments\Facade\PeachPaymentsFacade',
]
```

Add package migrations and vendors with commands:
```
php artisan migrate
php artisan vendor:publish
```

## Examples

### Register Card

```
$card = new Store(\PeachPayments::getClient());
$card->setCardBrand(CardBrand::MASTERCARD)
    ->setCardNumber('5454545454545454')
    ->setCardHolder('Jane Jones')
    ->setCardExpiryMonth('05')
    ->setCardExpiryYear('2020')
    ->setCardCvv('123')
    ->setUserId(Auth::user()->id)
;

\PeachPayments::storeCard($card);
```

### Delete Card

```
$cardDelete = new Delete(\PeachPayments::getClient());
$cardDelete->setPaymentCard($paymentCard);

\PeachPayments::deleteCard($cardDelete);
```