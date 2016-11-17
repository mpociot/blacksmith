<?php

namespace Mpociot\Blacksmith\Driver;

use Behat\Mink\Driver\GoutteDriver;

class BlacksmithDriver extends GoutteDriver
{
    /**
     * Perform a POST request
     */
    public function post($url, $postData)
    {
        $this->getClient()->request('POST', $this->prepareUrl($url), [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_X_XSRF_TOKEN' => $this->getClient()->getCookieJar()->get('XSRF-TOKEN')->getValue()
        ], $postData);
    }

    /**
     * Perform a PUT request
     */
    public function put($url, $postData)
    {
        $this->getClient()->request('PUT', $this->prepareUrl($url), [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_X_XSRF_TOKEN' => $this->getClient()->getCookieJar()->get('XSRF-TOKEN')->getValue()
        ], $postData);
    }
}
