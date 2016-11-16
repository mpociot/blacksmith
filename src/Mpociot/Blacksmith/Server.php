<?php

namespace Mpociot\Blacksmith;

use Exception;

/**
 * Class Server
 * @package Mpociot\Blacksmith
 */
class Server
{
    /** @var \Illuminate\Support\Collection */
    protected $data;

    /** @var Browser */
    protected $browser;

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /**
     * Server constructor.
     * @param array $data
     * @param Browser $browser
     */
    public function __construct(array $data, Browser $browser)
    {
        $this->data = collect($data);
        $this->browser = $browser;

        $this->id = $this->data->get('id');
        $this->name = $this->data->get('name');
    }

    /**
     * Load advanced server information
     */
    protected function getAdvancedData()
    {
        $this->browser->getContent('https://forge.laravel.com/api/servers/'.$this->id);
    }

    /**
     * Get all available sites on this server.
     *
     * @return $this
     */
    public function getSites()
    {
        $this->getAdvancedData();

        return collect($this->sites)->transform(function ($data) {
            return new Site($data, $this->browser);
        });
    }

    /**
     * Create a new site on this server.
     *
     * @param string $site_name
     * @param string $project_type
     * @param string $directory
     * @param bool $wildcards
     * @return Site
     * @throws Exception
     */
    public function addSite($site_name, $project_type = 'php', $directory = '/public', $wildcards = false)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/sites', [
            'site_name' => $site_name,
            'project_type' => $project_type,
            'directory' => $directory,
            'wildcards' => $wildcards,
        ]);
        
        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new Site($result, $this->browser);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data->toArray();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->data->get($key);
    }
}
