<?php

namespace Mpociot\Blacksmith;

use Exception;
use Illuminate\Support\Collection;
use Mpociot\Blacksmith\Models\Server;
use Mpociot\Blacksmith\Models\Site;
use Mpociot\Blacksmith\Models\User;

class Blacksmith
{
    const LOGIN_URL = 'https://forge.laravel.com/auth/login';

    /** @var Browser */
    protected $browser;

    public function __construct($email, $password)
    {
        $this->browser = new Browser($email, $password);
    }

    /**
     * Get all active servers
     *
     * @return Collection
     */
    public function getActiveServers()
    {
        return $this->browser->getContent('https://forge.laravel.com/api/servers/active')->transform(function ($data) {
            return new Server($data, $this->browser);
        });
    }

    /**
     * Get all available servers
     *
     * @return Collection
     */
    public function getServers()
    {
        return $this->browser->getContent('https://forge.laravel.com/api/servers')->transform(function ($data) {
            return new Server($data, $this->browser);
        });
    }

    /**
     * Get a single server by its ID
     *
     * @param int $id
     * @return Server
     */
    public function getServer($id)
    {
        $serverData = $this->browser->getContent('https://forge.laravel.com/api/servers/'.$id)->toArray();
        return new Server($serverData, $this->browser);
    }

    /**
     * Get a single site by its ID
     *
     * @param int $id
     * @return Server
     */
    public function getSite($id)
    {
        $site = $this->browser->getContent('https://forge.laravel.com/api/servers/sites/list')->where('id', $id)->first();
        $siteData = $this->browser->getContent('https://forge.laravel.com/api/servers/'.$site['server_id'].'/sites/'.$id)->toArray();
        return new Server($siteData, $this->browser);
    }

    /**
     * Get all available sites
     *
     * @return Collection
     */
    public function getSites()
    {
        return $this->browser->getContent('https://forge.laravel.com/api/servers/sites/list')->transform(function ($data) {
            return new Site($data, $this->browser);
        });
    }

    /**
     * Get the logged in User
     *
     * @return User
     */
    public function getUser()
    {
        $user = $this->browser->getContent('https://forge.laravel.com/api/user')->toArray();
        return new User($user, $this->browser);
    }

    /**
     * Create a new server with given configuration
     *
     * @param array $server_configuration
     * @return Server
     * @throws Exception
     */
    public function createServer($server_configuration)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/servers', $server_configuration);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        // Add the provision URL
        $result['provision_url'] = 'wget -O forge.sh https://forge.laravel.com/servers/'.$result['id'].'/vps?forge_token='.$this->getUser()->forge_token.'; bash forge.sh';

        return new Server($result, $this->browser);
    }
}
