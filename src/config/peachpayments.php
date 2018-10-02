<?php

return [
    'model' => App\User::class,
    'user_id' => env('PEACH_PAYMENTS_USER_ID'),
    'password' => env('PEACH_PAYMENTS_PASSWORD'),
    'entity_id' => env('PEACH_PAYMENTS_ENTITY_ID'),
    'test_mode' => env('PEACH_PAYMENTS_TEST_MODE')
];
