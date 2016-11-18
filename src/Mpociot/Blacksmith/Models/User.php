<?php

namespace Mpociot\Blacksmith\Models;

class User extends ForgeModel
{
    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data->toArray();
    }
}
