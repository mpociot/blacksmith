<?php

namespace Mpociot\Blacksmith\Models;

use Exception;

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

    /**
     * Load advanced server information
     */
    protected function getAdvancedData()
    {
        $this->browser->getContent('https://forge.laravel.com/api/servers/'.$this->id);
    }

    /**
     * Get all available sites on this server.
     *
     * @return $this
     */
    public function getSites()
    {
        $this->getAdvancedData();

        return collect($this->data->get('sites'))->transform(function ($data) {
            return new Site($data, $this->browser);
        });
    }

    /**
     * Get all schedules jobs on this server.
     *
     * @return $this
     */
    public function getScheduledJobs()
    {
        $this->getAdvancedData();

        return collect($this->data->get('jobs'))->transform(function ($data) {
            return new ScheduledJob($data, $this->browser);
        });
    }

    /**
     * Create a new scheduled job on this server.
     *
     * @param string $command
     * @param string $user
     * @param string $frequency
     * @return ScheduledJob
     * @throws Exception
     */
    public function addScheduledJob($command, $user = 'forge', $frequency = 'minutely')
    {
        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/job', [
            'command' => $command,
            'user' => $user,
            'frequency' => $frequency,
        ]);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new ScheduledJob($result, $this->browser);
    }

    /**
     * Create a new site on this server.
     *
     * @param string $site_name
     * @param string $project_type
     * @param string $directory
     * @param bool $wildcards
     * @return Site
     * @throws Exception
     */
    public function addSite($site_name, $project_type = 'php', $directory = '/public', $wildcards = false)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/sites', [
            'site_name' => $site_name,
            'project_type' => $project_type,
            'directory' => $directory,
            'wildcards' => $wildcards,
        ]);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        return new Site($result, $this->browser);
    }

    /**
     * Add an SSH key to server.
     *
     * @param $name
     * @param $key
     * @throws Exception
     */
    public function addSSHKey($name, $key)
    {
        $result = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->id.'/key', [
            'name' => $name,
            'key' => $key,
        ]);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }
    }

    /**
     * Delete a SSH key from the server.
     *
     * @param $id
     */
    public function deleteSSHKey($id)
    {
        $this->browser->deleteContent('https://forge.laravel.com/servers/'.$this->id.'/key/'.$id);
    }

    /**
     * Update the site metadata.
     *
     * @param string $name The server name to use
     * @param string $ip_address The public IP address
     * @param string $private_ip_address The private IP Address
     * @param integer $size The RAM size
     * @return Site
     * @throws Exception
     */
    public function updateMetadata($server_name, $ip_address, $private_ip_address, $size)
    {
        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->id.'/meta', [
            "name" => $server_name,
            "ip_address" => $ip_address,
            "private_ip_address" => $private_ip_address,
            "size" => intval($size),
        ]);

        if ($this->browser->getSession()->getStatusCode() === 500) {
            throw new Exception('Error: '.print_r($result, true));
        }

        $this->data = $this->data->merge($result);

        return $this;
    }
}
