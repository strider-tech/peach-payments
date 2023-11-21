# Peach Payments Integration for Laravel [![stability][0.2]][0.1]
[![release][1]][1.1] [![packagist][3]][3.1] [![downloads][5]][3.1]

## Installation

In Laravel versions >= 5.5 the service provider and facade will automatically be registered and enabled.

Install via composer

```bash
composer require strider-tech/peach-payments
```

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

Add the Billable trait to your model definition. This trait provides various methods to allow you to perform common tasks, such as registration cards, creating payments, applying coupons, and updating credit card information:

```
use StriderTech\PeachPayments\Billable;

class User extends Authenticatable
{
    use Billable;
}
```

## Usage

### Register Card

```
$user = Auth::user();

$card = new PaymentCard();
$card->setCardBrand(CardBrand::MASTERCARD)
    ->setCardNumber('5454545454545454')
    ->setCardHolder('Jane Jones')
    ->setCardExpiryMonth('05')
    ->setCardExpiryYear('2020')
    ->setCardCvv('123');
    
$user->storeCard($card);
```

### Register Card by Token

```
$user->storeCardByToken($token);
```

### Get user cards
```
$cards = $user->cards;
```

### Pay with card
```
$paymentCard = PaymentCard::find($id);
$payment = new Payment();
$payment->fromPaymentCard($paymentCard);
$payment->setCurrency('ZAR')
    ->setAmount(90.9);
    
$user->pay($payment);
```

### Get user payments
```
$payments = $user->payments;
```

### Delete Card

```
$user->deleteCardByToken($token);
```

[0]: https://img.shields.io/badge/stability-experimental-orange.svg?style=flat-square
[0.1]: https://nodejs.org/api/documentation.html#documentation_stability_index
[0.2]: https://img.shields.io/badge/stability-stable-green.svg?style=flat-square
[1]: https://img.shields.io/github/release/strider-tech/peach-payments/all.svg
[1.1]: https://github.com/strider-tech/peach-payments/releases
[2]: https://img.shields.io/github/tag/strider-tech/peach-payments.svg
[3]: https://img.shields.io/packagist/v/strider-tech/peach-payments.svg
[3.1]: https://packagist.org/packages/strider-tech/peach-payments
[4]: https://poser.pugx.org/strider-tech/peach-payments/v/stable.svg
[5]: https://poser.pugx.org/strider-tech/peach-payments/downloads
