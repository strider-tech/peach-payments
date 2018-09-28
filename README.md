# Peach Payments Integration for Laravel

## TODO
- colorful labels (tags)

## Installation
```
php artisan migrate
php artisan vendor:publish
```

## How to use
$peachPaymentsService = app()->get('peachpayments');

## Examples

```
$paymentService = new PeachPayments();
$card = new Store($paymentService->client);
$card->setCardBrand(CardBrand::MASTERCARD)
    ->setCardNumber('5454545454545454')
    ->setCardHolder('Jane Jones')
    ->setCardExpiryMonth('05')
    ->setCardExpiryYear('2020')
    ->setCardCvv('123');

$paymentService->storeCard($card);
```