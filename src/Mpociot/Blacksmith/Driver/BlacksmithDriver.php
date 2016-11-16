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
}
