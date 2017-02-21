<?php

namespace Mpociot\Blacksmith\Models;

use Exception;
use Illuminate\Support\Collection;

/**
 * Class Server
 * @package Mpociot\Blacksmith
 */
class Server extends ForgeModel
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** ------------------------------------------------------------------------
     * Sites
     */

    /**
     * Get all available sites on this server.
     *
     * @return Collection
     */
    public function getSites(){
        return collect($this->data->get('sites'))->transform(function ($data) {
            return new Site($data, $this->browser);
        });
    }

    /**
     * Get a site on this server
     *
     * @param $id
     * @return Site
     * @throws Exception
     */
    public function getSite($id) {

        if(!is_numeric($id)) {
            $sites = $this->getSites();
            $site = $sites->filter(function($site) use ($id){ return $site->name === $id; })->first();
            if($site) {
                $id = $site->id;
            }
        }

        $siteData = $this->browser->getContent('https://forge.laravel.com/api/servers/'.$this->id.'/sites/'.$id)->toArray();

        if(!$siteData) {
            throw new Exception(sprintf('Site "%s" could not be found', $id));
        }

        return new Site($siteData, $this->browser);
    }

    /**
     * Create a new site on this server.
     *
     * @param string $siteName
     * @param string $projectType
     * @param string $directory
     * @param bool $wildcards
     * @return Site
     * @throws Exception
     */
    public function addSite($siteName, $projectType = 'php', $directory = '/public', $wildcards = false)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/sites', [
            'site_name' => $siteName,
            'project_type' => $projectType,
            'directory' => $directory,
            'wildcards' => $wildcards,
        ]);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new Site($result, $this->browser);
    }

    /**
     * Remove site
     *
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function removeSite($id)
    {
        if(!is_numeric($id)) {
            $sites = $this->getSites();
            $site = $sites->filter(function($site) use ($id){ return $site->name === $id; })->first();
            if($site) {
                $id = $site->id;
            }
        }

        $this->browser->deleteContent('https://forge.laravel.com/api/servers/'.$this->id.'/sites/'.$id);
        $response = [
            'responseCode' => $this->browser->getSession()->getStatusCode()
        ];

        return $response;
    }

    /** ------------------------------------------------------------------------
     * Databases
     */

    /**
     * Get all databases on this server.
     *
     * @return Collection
     */
    public function getDatabases(){
        return collect($this->data->get('databases'))->transform(function ($data) {
            return new Database($data, $this->browser);
        });
    }

    /**
     * Get a database on this server
     *
     * @param $id
     * @return Database
     * @throws Exception
     */
    public function getDatabase($id) {

        $criteria = !is_numeric($id) ? 'name' : 'id';
        $databases = $this->getDatabases();

        $database = $databases->filter(function($database) use ($criteria, $id){
            return $database->{$criteria} == $id;
        })->first();

        if(!$database) {
            throw new Exception(sprintf('Database "%s" could not be found', $id));
        }

        return $database;
    }

    /**
     * Create a new database on this server.
     *
     * @param $databaseName
     * @param bool $username
     * @param bool $password
     * @return Database
     * @throws Exception
     */
    public function addDatabase($databaseName, $username = false, $password = false)
    {
        $data = array_filter([
            'name' => $databaseName,
            'user' => $username,
            'password' => $password,
        ]);

        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/database', $data);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new Database($result, $this->browser);
    }

    /**
     * Delete Database
     *
     * @param $id
     * @return mixed
     */
    public function removeDatabase($id)
    {
        if(!is_numeric($id)) {
            $databases = $this->getDatabases();
            $database = $databases->filter(function($database) use ($id){ return $database->name === $id; })->first();
            if($database) {
                $id = $database->id;
            }
        }

        $this->browser->deleteContent('https://forge.laravel.com/servers/'.$this->id.'/database/'.$id);
        $response = [
            'responseCode' => $this->browser->getSession()->getStatusCode()
        ];
        return $response;
    }


    /** ------------------------------------------------------------------------
     * Database Users
     */

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

    /**
     * Get a database on this server
     *
     * @param $id
     * @return Database
     * @throws Exception
     */
    public function getDatabaseUser($id)
    {
        $criteria = !is_numeric($id) ? 'name' : 'id';
        $databaseUsers = $this->getDatabaseUsers();

        $databaseUser = $databaseUsers->filter(function($databaseUser) use ($criteria, $id){
            return $databaseUser->{$criteria} == $id;
        })->first();

        if(!$databaseUser) {
            throw new Exception(sprintf('Database User "%s" could not be found', $id));
        }

        return $databaseUser;
    }

    /**
     * Create a new database user on this server.
     *
     * @param bool $username
     * @param bool $password
     * @param array $canAccess
     * @return DatabaseUser
     * @throws Exception
     */
    public function addDatabaseUser($username = false, $password = false, $canAccess = array())
    {
        $databases = $this->getDatabases();
        /** @var Collection $canAccess */
        $canAccess = collect($canAccess);
        $canAccess = $canAccess->transform(function($databaseId) use ($databases) {
            $database = $this->getDatabase($databaseId);
            return $database->id;
        });

        $data = [
            'name' => $username,
            'password' => $password,
            'databases' => $canAccess
        ];

        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/database-user', $data);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new DatabaseUser($result, $this->browser);
    }

    /**
     * Delete Database User
     *
     * @param $id
     * @return mixed
     */
    public function removeDatabaseUser($id)
    {
        if(!is_numeric($id)) {
            $databaseUsers = $this->getDatabaseUsers();
            $databaseUser = $databaseUsers->filter(function($databaseUser) use ($id){ return $databaseUser->name === $id; })->first();
            if($databaseUser) {
                $id = $databaseUser->id;
            }
        }

        $this->browser->deleteContent('https://forge.laravel.com/servers/'.$this->id.'/database-user/'.$id);
        $response = [
            'responseCode' => $this->browser->getSession()->getStatusCode()
        ];
        return $response;
    }

    /**
     * Update an existing database user on this server.
     *
     * @param bool $id
     * @param array $canAccess
     * @return DatabaseUser
     * @throws Exception
     */
    public function updateDatabaseUser($id, $canAccess = array())
    {
        $databaseUser = $this->getDatabaseUser($id);

        $databases = $this->getDatabases();
        /** @var Collection $canAccess */
        $canAccess = collect($canAccess);
        $canAccess = $canAccess->transform(function($databaseId) use ($databases) {
            $database = $this->getDatabase($databaseId);
            return $database->id;
        });

        $data = [
            'databases' => $canAccess
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->id.'/database-user/'.$databaseUser->id, $data);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new DatabaseUser($result, $this->browser);
    }

    /** ------------------------------------------------------------------------
     * SSH Keys
     */

    /**
     * Get all ssh keys on this server.
     *
     * @return Collection
     */
    public function getSSHKeys(){
        return collect($this->data->get('keys'))->transform(function ($data) {
            return new SSHKey($data, $this->browser);
        });
    }

    /**
     * Add an SSH key to server.
     *
     * @param $name
     * @param $key
     * @return SSHKey
     * @throws Exception
     */
    public function addSSHKey($name, $key)
    {
        $data = [
            'name' => $name,
            'key' => $key,
        ];
        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/key', $data);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new SSHKey($result, $this->browser);
    }

    /**
     * Delete SSH Key from this server
     *
     * @param $id
     * @return mixed
     */
    public function removeSSHKey($id)
    {
        if(!is_numeric($id)) {
            $sshKeys = $this->getSSHKeys();
            $sshKey = $sshKeys->filter(function($sshKey) use ($id){ return $sshKey->name === $id; })->first();
            if($sshKey) {
                $id = $sshKey->id;
            }
        }

        $this->browser->deleteContent('https://forge.laravel.com/servers/'.$this->id.'/key/'.$id);
        $response = [
            'responseCode' => $this->browser->getSession()->getStatusCode()
        ];
        return $response;
    }

    /** ------------------------------------------------------------------------
     * Scheduled Jobs
     */

    /**
     * Get all scheduled jobs on this server.
     *
     * @return Collection
     */
    public function getScheduledJobs(){
        return collect($this->data->get('jobs'))->transform(function ($data) {
            return new ScheduledJob($data, $this->browser);
        });
    }

    /**
     * Add an Scheduled Job to server.
     *
     * @param $command
     * @param $user
     * @param $frequency
     * @return ScheduledJob
     * @throws Exception
     */
    public function addScheduledJob($command, $user, $frequency)
    {
        $data = [
            'command' => $command,
            'user' => $user,
            'frequency' => $frequency,
        ];

        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/job', $data);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        // Result is the full server object
        // So we pull out the last job
        $scheduledJobData = collect($result['jobs'])->last();

        return new ScheduledJob($scheduledJobData, $this->browser);
    }

    /**
     * Delete Scheduled Job from this server
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    public function removeScheduledJob($id)
    {
        if(!is_numeric($id)) {
            $scheduledJobs = $this->getScheduledJobs();
            $scheduledJob = $scheduledJobs->filter(function($scheduledJob) use ($id){ return $scheduledJob->command === $id; })->first();
            if($scheduledJob) {
                $id = $scheduledJob->id;
            }
        }

        $result = $this->browser->deleteContent('https://forge.laravel.com/servers/'.$this->id.'/job/'.$id);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        $response = [
            'responseCode' => $this->browser->getSession()->getStatusCode(),
            'response' => $result
        ];

        return $response;
    }

    /** ------------------------------------------------------------------------
     * Metadata
     */

    /**
     * Update the server metadata.
     *
     * @param $serverName
     * @param $ipAddress
     * @param $privateIpAddress
     * @param $size
     * @return $this
     * @throws Exception
     */
    public function updateMetadata($serverName, $ipAddress, $privateIpAddress, $size)
    {
        $data = [
            'name' => $serverName,
            'ip_address' => $ipAddress,
            'private_ip_address' => $privateIpAddress,
            'size' => $size,
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->id.'/meta', $data);

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
     * Update max file upload size
     *
     * @param $megaBytes
     * @return $this
     * @throws Exception
     */
    public function updateMaxFileUploadSize($megaBytes)
    {
        $data = [
            'megabytes' => $megaBytes,
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->id.'/settings/php/max-upload-size', $data);

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
