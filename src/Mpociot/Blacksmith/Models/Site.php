<?php

namespace Mpociot\Blacksmith\Models;

class Site extends ForgeModel
{
    protected $id;
    protected $server_id;
    protected $name;
    protected $repository;
    protected $directory;
    protected $wildcards;
    protected $displayable_project_type;
    protected $secured;
    protected $has_app_installed;

    /** ------------------------------------------------------------------------
     * Apps
     */

    /**
     * Install an application on this site.
     *
     * @param  string  $repository The repository user/name to install
     * @param  string  $provider   Application provider. Can be 'github', 'bitbucket' or 'custom'
     * @param  string  $branch     The branch to deploy
     * @param  boolean $composer   Install composer dependencies
     * @param  boolean $migrate    Migrate
     * @return mixed
     */
    public function installApp($repository, $provider = 'github', $branch = 'master', $composer = true, $migrate = false)
    {
        if ($this->has_app_installed) {
            return false;
        }

        $data = [
            'repository' => $repository,
            'provider' => $provider,
            'branch' => $branch,
            'composer' => $composer,
            'migrate' => $migrate,
        ];

        $response = $this->browser->postContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/project', $data);

        return $response;
    }

    /** ------------------------------------------------------------------------
     * Environment
     */

    /**
     * Return the configured .env data
     *
     * @return string
     */
    public function getEnvironment()
    {
        $result = $this->browser->getContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/environment', true);

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
     * Update the configured .env data
     *
     * @return string
     */
    public function updateEnvironment($env)
    {
        $data = [
            'env' => $env
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/environment', $data);

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
     * Nginx Config
     */

    /**
     * Return the sites nginx config
     *
     * @return string
     */
    public function getNginxConfig()
    {
        $result = $this->browser->getContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/nginx/config', true);

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
     * Update the sites nginx config
     *
     * @return string
     */
    public function updateNginxConfig($config)
    {
        $data = [
            'config' => $config
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/nginx/config', $data);

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
     * Meta
     */

    /**
     * Update the site metadata.
     *
     * @param $directory
     * @return array
     */
    public function updateWebDirectory($directory)
    {
        $data = [
            'directory' => $directory
        ];

        $result = $this->browser->putContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/directory', $data);

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
     * Deployments
     */

    /**
     * Deploy the current installed application on this site.
     * 
     * @return mixed
     */
    public function deploy()
    {
        if ($this->has_app_installed) {
            return $this->browser->postContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/deploy/now', []);
        }
        return false;
    }

    /**
     * Get the last deployment log on this site.
     * 
     * @return mixed
     */
    public function deployLog()
    {
        if ($this->has_app_installed) {
            return $this->browser->postContent('https://forge.laravel.com/servers/'.$this->server_id.'/sites/'.$this->id.'/deploy/log', [], true);
        }
        return '';
    }
}
