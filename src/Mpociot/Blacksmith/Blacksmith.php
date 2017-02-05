<?php

namespace Mpociot\Blacksmith;

use Exception;
use Illuminate\Support\Collection;
use Mpociot\Blacksmith\Models\Circle;
use Mpociot\Blacksmith\Models\Recipe;
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
     * Delete a server by its ID
     *
     * @param $id
     */
    public function deleteServer($id)
    {
        $this->browser->deleteContent('https://forge.laravel.com/servers/'.$id);
    }

    /**
     * Get a single site by its ID or name
     *
     * @param int $identifier
     * @return Server
     */
    public function getSite($identifier)
    {
        $sites = $this->browser->getContent('https://forge.laravel.com/api/servers/sites/list');
        
        if ($identifier > 0) {
            $site = $sites->whereLoose('id', $identifier)->first();
        } else {
            $site = $sites->whereLoose('name', $identifier)->first();
        }

        $siteData = $this->browser->getContent('https://forge.laravel.com/api/servers/'.$site['server_id'].'/sites/'.$site['id'])->toArray();
        return new Site($siteData, $this->browser);
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
     * Get all circles
     *
     * @return Collection
     */
    public function getCircles()
    {
        return $this->browser->getContent('https://forge.laravel.com/api/circles')->transform(function ($data) {
            return new Circle($data, $this->browser);
        });
    }

    /**
     * Get a single circle by its ID
     *
     * @param int $id
     * @return Circle
     */
    public function getCircle($id)
    {
        $circleData = $this->browser->getContent('https://forge.laravel.com/api/circles')->where('id', $id)->first();
        return new Circle($circleData, $this->browser);
    }

    /**
     * Add a new Circle
     *
     * @param string $name
     * @return Circle
     */
    public function addCircle($name)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/circles', ['name' => $name]);
        return new Circle($result, $this->browser);
    }

    /**
     * Delete a Circle
     *
     * @param int $id
     * @return mixed
     */
    public function deleteCircle($id)
    {
        return $this->browser->deleteContent('https://forge.laravel.com/circles/'.$id);
    }

    /**
     * Get all recipes
     *
     * @return Collection
     */
    public function getRecipes()
    {
        return $this->browser->getContent('https://forge.laravel.com/api/recipes')->transform(function ($data) {
            return new Recipe($data, $this->browser);
        });
    }

    /**
     * Get a single recipe by its ID
     *
     * @param int $id
     * @return Recipe
     */
    public function getRecipe($id)
    {
        $recipeData = $this->browser->getContent('https://forge.laravel.com/api/recipes')->where('id', $id)->first();
        return new Recipe($recipeData, $this->browser);
    }

    /**
     * Add a new Recipe
     *
     * @param string $name
     * @return Recipe
     */
    public function addRecipe($name, $user, $script)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/recipes', [
            'name' => $name,
            'user' => $user,
            'script' => $script,
        ]);
        return new Recipe($result, $this->browser);
    }

    /**
     * Delete a Recipe
     *
     * @param int $id
     * @return mixed
     */
    public function deleteRecipe($id)
    {
        return $this->browser->deleteContent('https://forge.laravel.com/recipes/'.$id);
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
     * Add new server to Forge with given configuration
     *
     * @param array $server_configuration
     * @return Server
     * @throws Exception
     */
    public function addServer($server_configuration)
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
