<?php

namespace Mpociot\Blacksmith\Models;

use Exception;

class Circle extends ForgeModel
{
    protected $id;

    public function inviteMember($email)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/circles/'.$this->id.'/invite/', [
            'email' => $email,
        ]);

        if ($this->browser->getSession()->getStatusCode() === 422) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new Circle($result, $this->browser);
    }
}
