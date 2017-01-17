<?php

namespace Mpociot\Blacksmith\Models;

use Illuminate\Support\Collection;

class DatabaseUser extends ForgeModel
{
    protected $id;
    protected $server_id;
    protected $name;
    protected $status;

    /**
     * Get user databases
     *
     * @return Collection
     */
    public function getDatabases() {
        return collect($this->data->get('databases'))->transform(function ($data) {
            return new Database($data, $this->browser);
        });
    }

    /**
     * Get a single user database
     *
     * @param $identifier
     * @return mixed
     */
    public function getDatabase($identifier) {
        $criteria = (is_numeric($identifier)) ? 'id' : 'name';
        /** @var Collection $databases */
        $databases = $this->getDatabases();
        $database = $databases->filter(function($database) use ($criteria, $identifier){
            return $database->$criteria == $identifier;
        }, $databases)->first();
        return $database;
    }

    /**
     * Add a database to the user
     *
     * @param Database $database
     * @return array
     */
    public function addDatabase(Database $database){
        /** @var Collection $databases */
        $databases = $this->getDatabases();

        // Add the database and get the ids
        $databaseIds = $databases
            ->push($database)
            ->map(function($value){
                return $value->id;
            })
            ->toArray();

        $data = [
            'databases' => $databaseIds
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->server_id.'/database-user/'.$this->id, $data);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        $response = [
            'responseCode' => $this->browser->getSession()->getStatusCode(),
            'response' => $result
        ];

        return $response;
    }

    /**
     * Remove a database to the user
     *
     * @param Database $database
     * @return array
     */
    public function removeDatabase(Database $database){
        /** @var Collection $databases */
        $databases = $this->getDatabases();
        $databaseId = $database->id;

        // Remove the database and get the ids
        $databaseIds = $databases
            ->filter(function($value) use ($databaseId){
                return $databaseId != $value->id;
            })
            ->map(function($value){
                return $value->id;
            })
            ->toArray();

        $data = [
            'databases' => $databaseIds
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->server_id.'/database-user/'.$this->id, $data);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        $response = [
            'responseCode' => $this->browser->getSession()->getStatusCode(),
            'response' => $result
        ];

        return $response;
    }

}
