<?php

namespace Mpociot\Blacksmith\Models;

class Recipe extends ForgeModel
{
    protected $id;
    protected $name;
    protected $script;
    protected $user;

    public function update($name, $user, $script)
    {
        $result = $this->browser->putContent('https://forge.laravel.com/recipes/'.$this->id, [
            'name' => $name,
            'user' => $user,
            'script' => $script,
        ]);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        $this->data = $this->data->merge($result);

        return $this;
    }
}
