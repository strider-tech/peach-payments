<?php

namespace StriderTech\PeachPayments\Payments;

use GuzzleHttp\Exception\RequestException;
use StriderTech\PeachPayments\Client;
use StriderTech\PeachPayments\ClientInterface;
use StriderTech\PeachPayments\Enums\Exception;
use StriderTech\PeachPayments\ResponseJson;

/**
 * Class Notification
 * @package StriderTech\PeachPayments\Payments
 */
class Notification implements ClientInterface
{
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var null|string
     */
    private $resourcePath = '';

    /**
     * Status constructor.
     * @param Client $client
     * @param null $resourcePath
     */
    public function __construct(Client $client, $resourcePath = null)
    {
        $this->client = $client;

        if (!empty($resourcePath)) {
            $this->resourcePath = $resourcePath;
        }
    }

    /**
     * @return ResponseJson|string
     * @throws \Exception
     */
    public function process()
    {
        if (empty($this->getResourcePath())) {
            throw new \Exception("Resource path can not be empty", Exception::EMPTY_STATUS_TID);
        }

        $client = $this->client->getClient();

        try {
            $response = $client->get($this->buildUrl());
            $jsonResponse = $this->handle($response);

            return $jsonResponse;
        } catch (RequestException $e) {
            throw new \Exception((string)$e->getResponse()->getBody());
        }
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return $this->client->getApiUri() . $this->resourcePath .
            '?authentication.userId=' . $this->client->getConfig()->getUserId() .
            '&authentication.password=' . $this->client->getConfig()->getPassword() .
            '&authentication.entityId=' . $this->client->getConfig()->getEntityId();
    }

    /**
     * @return string
     */
    public function getResourcePath()
    {
        return $this->resourcePath;
    }

    /**
     * @param $resourcePath
     * @return $this
     */
    public function setResourcePath($resourcePath)
    {
        $this->resourcePath = $resourcePath;
        return $this;
    }

    /**
     * Handle response from PP API
     *
     * @param $response
     * @return ResponseJson
     */
    public function handle($response)
    {
        $body = (string)$response->getBody();
        $jsonResponse = new ResponseJson($body, true);

        return $jsonResponse;
    }
}
