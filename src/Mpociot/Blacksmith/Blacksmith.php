<?php

namespace Mpociot\Blacksmith;

use Exception;
use Illuminate\Support\Collection;
use Mpociot\Blacksmith\Models\Server;
use Mpociot\Blacksmith\Models\Site;

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
}
