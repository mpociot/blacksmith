<?php

namespace Mpociot\Blacksmith\Models;

use Mpociot\Blacksmith\Browser;

abstract class ForgeModel
{

    /** @var \Illuminate\Support\Collection */
    protected $data;

    /** @var Browser */
    protected $browser;

    /**
     * ForgeModel constructor.
     * @param array $data
     * @param Browser $browser
     */
    public function __construct(array $data, Browser $browser)
    {
        $this->data = collect($data);
        $this->browser = $browser;

        $this->build();
    }

    protected function build()
    {
        $this->data->each(function ($value, $property) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        });
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data->toArray();
    }
}