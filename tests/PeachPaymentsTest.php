<?php

namespace StriderTech\PeachPayments\Tests;

use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\Enums\CardBrand;
use StriderTech\PeachPayments\Payment;
use StriderTech\PeachPayments\PaymentCard;

class PeachPaymentsTest extends TestCase
{
    /**
     * Getting facade access
     * @return void
     */
    public function testInitFacade()
    {
        \PeachPayments::shouldReceive('getClient')
            ->once()
        ;

        \PeachPayments::getClient();
    }

    /**
     * Test database data
     */
    public function testDb()
    {
        $this->assertDatabaseHas('users', [
            'id' => 1,
            'email' => 'user@example.com'
        ]);
    }

    /**
     * Getting facade client
     */
    public function testGetClient()
    {
        $client = \PeachPayments::getClient();

        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * Create test card token
     */
    public function testCreateTestCardToken()
    {
        if (config('peachpayments.test_mode') === false) {
            $this->markTestSkipped('No need to create test card token in `live` mode');
        }

        $token = \PeachPayments::getTestCardToken();
        $this->assertNotEmpty($token);
        $this->assertTrue(is_string($token));
    }

    /**
     * Store card by token, get status and pay with card
     */
    public function testRegisterCardByTokenAndPay()
    {
        $token = \PeachPayments::getTestCardToken();
        $result = $this->user->storeCardByToken($token);

        $this->assertDatabaseHas('payment_cards', [
            'id' => 1,
            'payment_remote_id' => $result->getId()
        ]);

        $paymentCard = PaymentCard::find(1);
        $status = $this->user->getPaymentStatusByToken($paymentCard->getPaymentRemoteId());
        $this->assertObjectHasAttribute('json', $status);
        $this->assertTrue($status->isSuccess());

        $payment = new Payment();
        $payment->fromPaymentCard($paymentCard);
        $payment->setCurrency('ZAR')
            ->setAmount('50.90');
        $paymentStatus = $this->user->pay($payment);
        $this->assertObjectHasAttribute('json', $paymentStatus);
        $this->assertTrue($paymentStatus->isSuccess());
    }

    /**
     * Store card, get status and pay with card
     */
    public function testRegisterCardAndPay()
    {
        $card = new PaymentCard();
        $card->setCardBrand(CardBrand::MASTERCARD)
            ->setCardNumber('5454545454545454')
            ->setCardHolder('Jane Jones')
            ->setCardExpiryMonth('05')
            ->setCardExpiryYear('2020')
            ->setCardCvv('123');
        $result = $this->user->storeCard($card);

        $this->assertDatabaseHas('payment_cards', [
            'id' => 1,
            'payment_remote_id' => $result->getId()
        ]);

        $paymentCard = PaymentCard::find(1);
        $status = $this->user->getPaymentStatusByToken($paymentCard->getPaymentRemoteId());
        $this->assertObjectHasAttribute('json', $status);
        $this->assertTrue($status->isSuccess());

        $payment = new Payment();
        $payment->fromPaymentCard($paymentCard);
        $payment->setCurrency('ZAR')
            ->setAmount('50.90');
        $paymentStatus = $this->user->pay($payment);
        $this->assertObjectHasAttribute('json', $paymentStatus);
        $this->assertTrue($paymentStatus->isSuccess());
    }

    /**
     * Store card and delete
     */
    public function testDeleteCard()
    {
        $card = new PaymentCard();
        $card->setCardBrand(CardBrand::MASTERCARD)
            ->setCardNumber('5454545454545454')
            ->setCardHolder('Jane Jones')
            ->setCardExpiryMonth('05')
            ->setCardExpiryYear('2020')
            ->setCardCvv('123');
        $result = $this->user->storeCard($card);

        $this->assertDatabaseHas('payment_cards', [
            'id' => 1,
            'payment_remote_id' => $result->getId()
        ]);

        $paymentCard = PaymentCard::find(1);
        $status = $this->user->deleteCard($paymentCard);
        $this->assertObjectHasAttribute('json', $status);
        $this->assertTrue($status->isSuccess());

        $this->assertSoftDeleted('payment_cards', [
            'id' => 1,
            'payment_remote_id' => $result->getId()
        ]);
    }
}