<?php

namespace StriderTech\PeachPayments;

/**
 * Class Client
 * @package StriderTech\PeachPayments
 */
class Client
{
    // client version
    const CLIENT_VERSION = '1.0.0';

    // api location and version
    const API_URI_TEST = 'https://test.oppwa.com/';
    const API_URI_LIVE = 'https://oppwa.com/';
    const API_URI_VERSION = 'v1';

    // exceptions
    const EXCEPTION_BAD_CONFIG = 100;

    /**
     * Configuration object.
     *
     * @var Configuration $config
     */
    private $config;

    /**
     * Is test mode.
     *
     * Defaults to live mode.
     *
     * @var boolean
     */
    private $isTestMode = false;

    /**
     * Guzzle Http client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Client constructor.
     *
     * @param Configuration|array $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        // config is Configuration object
        if ($config instanceof Configuration) {
            $this->config = $config;
        }

        // config is array
        if (is_array($config) && count($config) === 3) {
            $this->config = new Configuration($config[0], $config[1], $config[2]);
        }

        // config is from function arguments
        $configArgs = func_get_args();
        if (count($configArgs) === 3) {
            $this->config = new Configuration($configArgs[0], $configArgs[1], $configArgs[2]);
        }

        // can not find the configuration
        if (!isset($this->config)) {
            throw new \Exception("Please configure the client correctly", self::EXCEPTION_BAD_CONFIG);
        }

        // Setup client;
        $this->client = new \GuzzleHttp\Client;
    }

    /**
     * Check if we are currently in test mode.
     *
     * @return mixed
     */
    public function isTestMode()
    {
        return $this->isTestMode;
    }

    /**
     * Set the client to use test mode.
     *
     * @param mixed $isTestMode
     * @return $this
     */
    public function setTestMode($isTestMode)
    {
        $this->isTestMode = $isTestMode;
        return $this;
    }

    /**
     * Get configuration object.
     *
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Provide the ability to use an alternative entity id.
     *
     * @param $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        $this->config->setEntityId($entityId);
        return $this;
    }

    /**
     * Get Guzzle Client.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get api uri.
     *
     * @return string
     */
    public function getApiUri()
    {
        if ($this->isTestMode()) {
            return self::API_URI_TEST . self::API_URI_VERSION;
        }
        return self::API_URI_LIVE . self::API_URI_VERSION;
    }
}
