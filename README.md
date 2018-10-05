# Peach Payments Integration for Laravel [![stability][0]][1]

## Installation

In Laravel versions >= 5.5 the service provider and facade will automatically be registered and enabled. 

In older versions of the framework just add the package service provider and facade in 'config/app.php' file:
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
php artisan vendor:publish --provider="StriderTech\PeachPayments\PeachPaymentsServiceProvider"
```
After publishing of vendors edit config file: `app/config/peachpayments.php` and run migrations: 
```
php artisan migrate
```

## Examples

### Register Card

```
$card = new PaymentCard();
$card->setCardBrand(CardBrand::MASTERCARD)
    ->setCardNumber('5454545454545454')
    ->setCardHolder('Jane Jones')
    ->setCardExpiryMonth('05')
    ->setCardExpiryYear('2020')
    ->setCardCvv('123')
    ->setUserId(Auth::user()->id);
    
\PeachPayments::storeCard($card);
```

### Register Card by Token

```
\PeachPayments::storeCardByToken($token, Auth::user()->id);
```

### Get user cards
```
$cards = PaymentCard::where('user_id', $userId)->get();
```

### Pay with card
```
$paymentCard = PaymentCard::find($id);
$payment = new Payment();
$payment->fromPaymentCard($paymentCard);
$payment->setCurrency('ZAR')
    ->setAmount('90.9');
    
\PeachPayments::pay($payment);
```

### Get user payments
```
$cards = Payment::where('user_id', $userId)->get();
```

### Delete Card

```
$paymentCard = PaymentCard::find($paymentCardId);

\PeachPayments::deleteCard($paymentCard);
```

[0]: https://img.shields.io/badge/stability-experimental-orange.svg?style=flat-square
[1]: https://nodejs.org/api/documentation.html#documentation_stability_index
[2]: https://img.shields.io/github/tag/strider-tech/peach-payments.svg