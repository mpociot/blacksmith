<?php

namespace Mpociot\Blacksmith;

use Exception;
use Illuminate\Support\Collection;
use Mpociot\Blacksmith\Models\Circle;
use Mpociot\Blacksmith\Models\Database;
use Mpociot\Blacksmith\Models\Recipe;
use Mpociot\Blacksmith\Models\Server;
use Mpociot\Blacksmith\Models\Site;
use Mpociot\Blacksmith\Models\User;

class Blacksmith
{
    const LOGIN_URL = 'https://forge.laravel.com/auth/login';

    /** @var Browser */
    protected $browser;

    /**
     * Blacksmith constructor.
     *
     * @param $email
     * @param $password
     */
    public function __construct($email, $password)
    {
        $this->browser = new Browser($email, $password);
    }

    /**
     * @return Collection A list of servers from /api/servers/active
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
     * @return Collection A collection of servers from /api/servers
     */
    public function getServers()
    {
        return $this->browser->getContent('https://forge.laravel.com/api/servers')->transform(function ($data) {
            return new Server($data, $this->browser);
        });
    }

    /**
     * Get a single server by its ID or name
     *
     * @param $id
     * @return Server A single server from /api/servers/:serverId
     * @throws Exception
     */
    public function getServer($id)
    {
        if(!is_numeric($id)) {
            $servers = $this->getServers();
            $server = $servers->filter(function($server) use ($id){ return $server->name == $id; })->first();
            if($server) {
                $id = $server->id;
            }
        }

        $serverData = $this->browser->getContent('https://forge.laravel.com/api/servers/'.$id)->toArray();

        if(!$serverData) {
            throw new Exception(sprintf('Server "%s" could not be found', $id));
        }

        return new Server($serverData, $this->browser);
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

    /**
     * Delete a server by its ID or name
     *
     * @param $id
     */
    public function deleteServer($id)
    {
        if(!is_numeric($id)) {
            $server = $this->getServer($id);
            $id = $server->id;
        }

        $this->browser->deleteContent('https://forge.laravel.com/servers/'.$id);
    }

    /**
     * Get sites
     *
     * @return Collection of sites from /api/servers/sites/list
     */
    public function getSites(){
        $sites = $this->browser->getContent('https://forge.laravel.com/api/servers/sites/list')->transform(function ($data) {
            return new Site($data, $this->browser);
        });
        return $sites;
    }

    /**
     * Get a single site by its ID or name
     *
     * @param $id
     * @return Site A single site from /api/servers/:serverId/sites/:siteId
     * @throws Exception
     */
    public function getSite($id)
    {
        $sites = $this->getSites();
        $criteria = !is_numeric($id) ? 'name' : 'id';
        $site = $sites->filter(function($site) use ($criteria, $id){
            return $site->{$criteria} == $id;
        })->first();

        $siteData = $this->browser->getContent('https://forge.laravel.com/api/servers/'.$site->server_id.'/sites/'.$site->id)->toArray();

        if(!$siteData) {
            throw new Exception(sprintf('Site "%s" could not be found', $id));
        }

        return new Site($siteData, $this->browser);
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
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getCircle($id)
    {
        $circles = $this->getCircles();
        $criteria = is_numeric($id) ? 'id': 'name';
        $circle = $circles->filter(function($circle) use ($criteria, $id){
            return $circle->$criteria = $id;
        })->first();

        if(!$circle) {
            throw new Exception(sprintf('Circle "%s" could not be found', $id));
        }

        return $circle;
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
        $circle = $this->getCircle($id);
        return $this->browser->deleteContent('https://forge.laravel.com/circles/'.$circle->id);
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
     * Get a single recipe by its ID or name
     *
     * @param int $id
     * @return Recipe
     */
    public function getRecipe($id)
    {
        $recipes = $this->getRecipes();
        $criteria = is_numeric($id) ? 'id': 'name';

        $recipe = $recipes->filter(function($recipe) use ($criteria, $id){
            return $recipe->$criteria = $id;
        })->first();

        return $recipe;
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
        $recipe = $this->getRecipe($id);
        return $this->browser->deleteContent('https://forge.laravel.com/recipes/'.$recipe->id);
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
}
