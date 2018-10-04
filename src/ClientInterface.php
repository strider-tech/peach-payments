<?php

namespace StriderTech\PeachPayments;

/**
 * Interface ClientInterface
 * @package StriderTech\PeachPayments
 */
interface ClientInterface
{
    /**
     * Make request to PP API
     *
     * @return array
     */
    public function process();

    /**
     * Build Url to call in api
     *
     * @return string
     */
    public function buildUrl();

    /**
     * Handle response from PP API
     *
     * @param $response
     * @return string
     */
    public function handle($response);

}