# Peach Payments Integration for Laravel

## Installation
```
php artisan migrate
php artisan vendor:publish
```

## How to use
$peachPaymentsService = app()->get('peachpayments');

## Examples

```
$paymentCard = PaymentCard::create([
    'user_id' => $userId,
    'payment_remote_id' => $request->token,
    'brand' => 'VISA',
    'holder' => 'John Doe',
    'last_four' => mt_rand(1000, 9999),
    'expiry_month' => Carbon::now()->addMonth()->format('n'),
    'expiry_year' => Carbon::now()->addYear()->format('Y'),
    'type' => $request->type,
    'is_primary' => $request->is_primary
]);
```