<?php

namespace Mpociot\Blacksmith;

use Behat\Mink\Session;
use Exception;
use Mpociot\Blacksmith\Driver\BlacksmithDriver;

/**
 * Class Browser
 * @package Mpociot\Blacksmith
 */
class Browser
{
    const LOGIN_URL = 'https://forge.laravel.com/auth/login';

    /** @var Session */
    protected $session;

    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /** @var bool */
    protected $logged_in = false;

    /**
     * Browser constructor.
     * @param $email
     * @param $password
     */
    public function __construct($email, $password)
    {
        $this->session = new Session(new BlacksmithDriver());
        $this->session->start();

        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @throws Exception
     */
    protected function login()
    {
        $this->session->visit(self::LOGIN_URL);
        $page = $this->session->getPage();
        $page->find('css', '#email')->setValue($this->email);
        $page->find('css', 'input[type="password"]')->setValue($this->password);
        $page->find('css', 'form')->submit();

        if ($this->session->getCurrentUrl() === self::LOGIN_URL) {
            throw new Exception('Invalid login');
        }
        $this->logged_in = true;
    }

    /**
     * @param $url
     * @param bool $raw
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public function getContent($url, $raw = false)
    {
        if ($this->logged_in === false) {
            $this->login();
        }

        $this->session->visit($url);
        $content = $this->session->getPage()->getContent();

        if ($raw) {
            return $content;
        }
        return collect(json_decode($content, true));
    }

    /**
     * @param $url
     * @param $payload
     * @param $raw
     * @return mixed
     * @throws Exception
     */
    public function postContent($url, $payload, $raw = false)
    {
        if ($this->logged_in === false) {
            $this->login();
        }

        $this->session->getDriver()->post($url, json_encode($payload));
        $content = $this->session->getPage()->getContent();

        if ($raw) {
            return $content;
        }
        return json_decode($content, true);
    }

    /**
     * @param $url
     * @param $payload
     * @return mixed
     * @throws Exception
     */
    public function putContent($url, $payload)
    {
        if ($this->logged_in === false) {
            $this->login();
        }

        $this->session->getDriver()->put($url, json_encode($payload));
        return json_decode($this->session->getPage()->getContent(), true);
    }

    /**
     * @param $url
     * @return mixed
     * @throws Exception
     */
    public function deleteContent($url)
    {
        if ($this->logged_in === false) {
            $this->login();
        }

        return $this->session->getDriver()->delete($url);
    }

    /**
     * Gets the value of session.
     *
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }
}
