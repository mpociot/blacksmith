<?php

namespace Mpociot\Blacksmith\Models;

class Site extends ForgeModel
{
    /** @var \Illuminate\Support\Collection */
    protected $data;

    /** @var \Mpociot\Blacksmith\Browser */
    protected $browser;

    protected $id;
    protected $name;
    protected $repository;
    protected $directory;
    protected $wildcards;
    protected $displayable_project_type;
    protected $secured;

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data->toArray();
    }
}
