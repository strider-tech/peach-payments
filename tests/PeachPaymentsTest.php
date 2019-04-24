<?php

namespace StriderTech\PeachPayments\Tests;

use StriderTech\PeachPayments\Cards\Store;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\Enums\CardBrand;
use StriderTech\PeachPayments\Payment;
use StriderTech\PeachPayments\PaymentCard;
use StriderTech\PeachPayments\Payments\Debit;

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

        $token = $this->getTestCardToken();
        $this->assertNotEmpty($token);
        $this->assertTrue(is_string($token));
    }

    /**
     * Store card by token, get status and pay with card
     */
    public function testRegisterCardByTokenAndPay()
    {
        $token = $this->getTestCardToken();
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
            ->setAmount(50.9)
            ->setMerchantTransactionId('123');
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
            ->setCardExpiryYear(date('Y', strtotime('+1 year')))
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
            ->setAmount(50.9)
            ->setMerchantTransactionId('123');
        $paymentStatus = $this->user->pay($payment);
        $this->assertObjectHasAttribute('json', $paymentStatus);
        $this->assertTrue($paymentStatus->isSuccess());
    }

    /**
     * Store 3DS card
     */
    public function testRegister3DSCard()
    {
        $result = $this->getTestCard3DSToken();

        $this->assertObjectHasAttribute('json', $result);
        $this->assertTrue($result->is3DS());
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
            ->setCardExpiryYear(date('Y', strtotime('+1 year')))
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

    /**
     * @return string
     */
    private function getTestCardToken()
    {
        $store = new Store(\PeachPayments::getClient());
        $store->setCardBrand(CardBrand::MASTERCARD)
            ->setCardNumber('5454545454545454')
            ->setCardHolder('Jane Jones')
            ->setCardExpiryMonth('05')
            ->setCardExpiryYear(date('Y', strtotime('+1 year')))
            ->setCardCvv('123');

        $result = $store->process();

        return $result->getId();
    }

    /**
     * @return object|\StriderTech\PeachPayments\ResponseJson|string
     */
    private function getTestCard3DSToken()
    {
        $debit = new Debit(\PeachPayments::getClient());
        $debit->setCreateRegistration(true)
            ->setAuthOnly(true)
            ->setCardBrand(CardBrand::VISA)
            ->setCardNumber('4012888888881881')
            ->setCardHolder('Jane Jones')
            ->setCardExpiryMonth('05')
            ->setCardExpiryYear(date('Y', strtotime('+1 year')))
            ->setCardCvv('123')
            ->setShopperResultUrl(config('peachpayments.notification_url'))
        ;

        $result = $debit->process();

        return $result;
    }
}