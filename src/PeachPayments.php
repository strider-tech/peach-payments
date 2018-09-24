<?php

namespace StriderTech\PeachPayments;

use App\User;
use GuzzleHttp\Exception\RequestException;
use Peach\Oppwa\Cards\Brands;
use Peach\Oppwa\Cards\Delete;
use Peach\Oppwa\Cards\Store;
use Peach\Oppwa\Client;
use Peach\Oppwa\Configuration;
use Peach\Oppwa\Payments\Status;
use Peach\Oppwa\ResponseJson;

/**
 * Class PeachPayments
 * @package App\Helpers
 */
class PeachPayments
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array
     */
    private $config = [];

    /**
     * PeachPayments constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
        $authConfig = new Configuration(
            $config['user_id'],
            $config['password'],
            $config['entity_id']
        );

        $client = new Client($authConfig);
        $client->setTestMode($config['test_mode']);
        $this->client = $client;
    }

    /**
     * todo not using for now, keep for future
     *
     * @return \stdClass
     */
    public function storeCard()
    {
        $storeCard = new Store($this->client);

        $storeCardResult = $storeCard->setCardBrand(Brands::MASTERCARD)
            ->setCardNumber('5454545454545454')
            ->setCardHolder('Jane Jones')
            ->setCardExpiryMonth('05')
            ->setCardExpiryYear('2020')
            ->setCardCvv('123')
            ->process();

        return $storeCardResult;
    }

    /**
     * @param Store $storeCardResult
     * @return \stdClass
     */
    public function deleteCard(Store $storeCardResult)
    {
        $cardDelete = new Delete($this->client);
        $cardDelete->setTransactionId($storeCardResult->getId());
        $cardDeleteResult = $cardDelete->process();

        return $cardDeleteResult;
    }

    /**
     * @param $transactionId
     * @return mixed|\Peach\Oppwa\ResponseJson
     */
    public function getPaymentStatus($transactionId)
    {
        $paymentStatus = new Status($this->client);
        $paymentStatusResult = $paymentStatus
            ->setTransactionId($transactionId)
            ->process();

        return $paymentStatusResult;
    }

    /**
     * @param User $user
     * @param mixed|ResponseJson $paymentData
     * @return User
     */
    public function fromPayment(User $user, $paymentData)
    {
        $user->payment_remote_id = $paymentData->getId();
        $user->card_brand = $paymentData->getPaymentBrand();
        $user->card_holder = $paymentData->getCardHolder();
        $user->card_last_four = $paymentData->getCardLast4Digits();
        $user->card_expiry_month = $paymentData->getCardExpiryMonth();
        $user->card_expiry_year = $paymentData->getCardExpiryYear();

        return $user;
    }

    /**
     * @param $resourcePath
     * @return ResponseJson
     */
    public function getNotificationStatus($resourcePath)
    {
        try {
            $url = $this->client->getApiUri() . $resourcePath .
                '?authentication.userId=' . $this->client->getConfig()->getUserId() .
                '&authentication.password=' . $this->client->getConfig()->getPassword() .
                '&authentication.entityId=' . $this->client->getConfig()->getEntityId();

            $response = $this->client->getClient()->get($url);

            return new ResponseJson((string)$response->getBody(), true);
        } catch (RequestException $e) {
            return new ResponseJson((string)$e->getResponse()->getBody(), false);
        }
    }
}