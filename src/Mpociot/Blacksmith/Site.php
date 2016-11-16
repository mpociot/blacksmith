<?php

namespace Mpociot\Blacksmith;

class Site
{
    /** @var \Illuminate\Support\Collection */
    protected $data;

    /** @var Browser */
    protected $browser;

    protected $id;
    protected $name;
    protected $repository;
    protected $directory;
    protected $wildcards;
    protected $displayable_project_type;
    protected $secured;

    /**
     * Site constructor.
     * @param array $data
     * @param Browser $browser
     */
    public function __construct(array $data, Browser $browser)
    {
        $this->data = collect($data);
        $this->browser = $browser;

        $this->id = $this->data->get('id');
        $this->name = $this->data->get('name');
        $this->repository = $this->data->get('repository');
        $this->directory = $this->data->get('directory');
        $this->wildcards = $this->data->get('wildcards');
        $this->displayable_project_type = $this->data->get('displayable_project_type');
        $this->secured = $this->data->get('secured');
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
