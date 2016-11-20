# Blacksmith - The unofficial Laravel Forge PHP API

Laravel Forge is **awesome**, right? 
Yes it is - but there's one thing it's missing - a proper API.

That's why this library exists - Blacksmith is an unofficial [Laravel Forge API](http://forge.laravel.com) to automate common tasks.

The API is still improving and I will keep adding features, as I need them.

## Getting Started

```php
use Mpociot\Blacksmith\Blacksmith;

$blacksmith = new Blacksmith($email, $password);

```

## Available methods


### Get all active servers

Returns a Collection of `Server` objects.

```php
$activeServers = $blacksmith->getActiveServers();
```

### Get all sites for all servers

Returns a Collection of `Site` objects.

```php
$sites = $blacksmith->getSites();
```

### Get a server by its ID

Returns a single `Server` object.

```php
$server = $blacksmith->getServer(1);
```

### Add a server to Forge

Returns a single `Server` object with a provision url.

The following example will create a Load Balancer with a custom provider
```php
$server = $blacksmith->addServer([
    'backups' => false,
    'database' => 'forge',
    'hhvm' => false,
    'ip_address' => '94.212.124.121',
    'maria' => false,
    'name' => 'harmonious-lagoon',
    'nodeBalancer' => true,
    'old_php' => false,
    'php_version' => 'php70',
    'private_ip_address' => '10.0.0.2',
    'provider' => 'custom',
    'size' => '2',
    'timezone' => 'Europe/Berlin',
]);
```

### Get a site by its ID

Returns a single `Site` object.

```php
$site = $blacksmith->getSite(1);
```

### Get all circles

Returns a Collection of `Circle` objects of the user.

```php
$circles = $blacksmith->getCircles();
```

### Get a circle by its ID

Returns a single `Circle` object.

```php
$circle = $blacksmith->getCircle(1);
```

### Add a new circle

Returns a single `Circle` object.

```php
$circle = $blacksmith->addCircle('Name of Circle');
```

### Get all recipes

Returns a Collection of `Recipe` objects.

```php
$recipes = $blacksmith->getRecipes();
```

### Get a recipe by its ID

Returns a single `Recipe` object.

```php
$recipe = $blacksmith->getRecipe(1);
```

### Add a new recipe

Returns a single `Recipe` object.

```php
$recipe = $blacksmith->addRecipe('RecipeName', 'root', 'Recipe contents');
```

## Server methods

### Get Sites

Returns a Collection of `Site` objects for the server.

```php
$sites = $server->getSites();
```

### Add a new site

Returns a the newly created `Site` object or throws an exception if errors occur.

```php
$newSite = $server->addSite($site_name, $project_type = 'php', $directory = '/public', $wildcards = false);
```

### Add a new SSH key

Add a SSH key to a server 

```php
$server->addSSHKey('Name SSH key', 'Contents of SSH key');
```

### Remove a SSH key from a server

```php
$server->removeSSHKey(1);
```

### Update Metadata

Update the metadata of the current site, and return an updated `Server` object or throws an exception if errors occur.

```php
$server = $server->updateMetadata($server_name, $ip_address, $private_ip_address, $size);
```

### Get Schedules Jobs

Returns a Collection of `ScheduledJob` objects for the server.

```php
$jobs = $server->getScheduledJobs();
```

### Add a new scheduled job

Returns a the newly created `ScheduledJob` object or throws an exception if errors occur.

```php
$newJob = $server->addScheduledJob($command, $user = 'forge', $frequency = 'minutely');
```

### toArray

Returns an array containing all available server information.

```php
$data = $server->toArray();
```


## Site methods

### Get Environment

Returns the configured .env file

```php
$env_content = $site->getEnvironment();
```

### Install an application

Install and deploy an application to the site.

```php
$site->installApp($repository, $provider = 'github', $branch = 'master', $composer = true, $migrate = false);
```

### Deploy an application

Deploys an application on this site.

```php
$site->deploy();
```

### Get last deployment log

Returns the last deployment log for this site.

```php
$site->deployLog();
```

### toArray

Returns an array containing all available site information.

```php
$data = $site->toArray();
```

## Circle methods

### Invite a member by email

Returns a fresh `Circle` object or throws an exception if errors occur.

```php
$circle = $circle->inviteMember('email@company.com');
```

### Set all circle members

If you want to delete a member you update a circle with all member id's.
It return a fresh `Circle` object.

```php
$circle = $circle->setMembers([1,2]);
```

### Set all circle servers

If you want to add or delete a server from the circle you update a circle with all server id's.
It return a fresh `Circle` object.

```php
$circle = $circle->setServers([1,2]);
```

## Recipe methods

### Update a Recipe

```php
$recipe = $recipe->update($name, $user, $script);
```

## License

Blacksmith is free software distributed under the terms of the MIT license.
