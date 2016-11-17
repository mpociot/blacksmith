<?php

namespace Mpociot\Blacksmith\Models;

class Site extends ForgeModel
{
    /** @var \Illuminate\Support\Collection */
    protected $data;

    /** @var \Mpociot\Blacksmith\Browser */
    protected $browser;

    protected $id;
    protected $server_id;
    protected $name;
    protected $repository;
    protected $directory;
    protected $wildcards;
    protected $displayable_project_type;
    protected $secured;

    /**
     * Return the configured .env data
     * @return string
     */
    public function getEnvironment()
    {
        return $this->browser->getContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/environment', true);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data->toArray();
    }
}
