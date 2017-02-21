<?php

namespace Mpociot\Blacksmith\Models;

use Illuminate\Support\Collection;

class Database extends ForgeModel
{
    protected $id;
    protected $server_id;
    protected $name;

    /**
     * Get all database users on this server.
     *
     * @return Collection
     */
    public function getDatabaseUsers()
    {
        return collect($this->data->get('database_users'))->transform(
            function ($data) {
                return new DatabaseUser($data, $this->browser);
            }
        );
    }
}
