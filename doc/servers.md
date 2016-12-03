# Servers

## Get all active servers

Returns a Collection of `Server` objects.

```php
$activeServers = $blacksmith->getActiveServers();
```

## Get all servers

Returns a Collection of `Server` objects.

```php
$servers = $blacksmith->getServers();
```

## Get a server by its ID

Returns a single `Server` object.

```php
$server = $blacksmith->getServer(1);
```

## Add a server to Forge

### Globals options to set

PHP version options  
- 'php71' - Install PHP 7.1 on the server
- 'php70' - Install PHP 7.0 on the server
- 'php56' - Install PHP 5.6 on the server

Database option  
Set the 'maria' key on the configuration with one of the following
- true - Server will install and use MariaDB 10.1
- false - Server will install and use MySQL 5.7

### Add a server with DigitalOcean 2.0 provider

If you want weekly backups on DigitalOcean for this server set the 'backups' key to **true**. 

```php
$server = $blacksmith->addServer([
    'backups' => false
    'credential' => _CREDENTIAL_ID_
    'database' => 'forge'
    'hhvm' => false
    'maria' => true
    'name' => 'solitary-fern'
    'nodeBalancer' => false
    'old_php' => false
    'php_version' => 'php70'
    'provider' => 'ocean2'
    'region' => 'ams3'
    'size' => '1GB'
    'timezone' => 'Europe/Berlin'
]);
```

Replace _CREDENTIAL_ID_ with your DigitalOcean2.0 provider credential ID.

Possible size options:  
- '512MB' => 512MB RAM, 1 CPU, 20GB SSD, 1TB Transfer
- '1GB' => 1GB RAM, 1 CPU, 30GB SSD, 2TB Transfer
- '2GB' => 2GB RAM, 2 CPU, 40GB SSD, 3TB Transfer
- '4GB' => 4GB RAM, 2 CPU, 60GB SSD, 4TB Transfer
- '8GB' => 8GB RAM, 4 CPU, 80GB SSD, 5TB Transfer
- '16GB' => 16GB RAM, 8 CPU, 160GB SSD, 6TB Transfer
- 'm-16GB' => 16GB RAM, 2 CPU, 30GB SSD, 6TB Transfer
- '32GB' => 32GB RAM, 12 CPU, 320GB SSD, 7TB Transfer
- 'm-32GB' => 32GB RAM, 4 CPU, 90GB SSD, 7TB Transfer
- '64GB' => 64GB RAM, 20 CPU, 640GB SSD, 9TB Transfer
- 'm-64GB' => 64GB RAM, 8 CPU, 200GB SSD, 8TB Transfer

Possible region options:  
- 'ams2' => Amsterdam 2
- 'ams3' => Amsterdam 3
- 'blr1' => Bangalore
- 'lon1' => London
- 'fra1' => Frankfurt
- 'nyc1' => New York 1
- 'nyc2' => New York 2
- 'nyc3' => New York 3
- 'sfo1' => San Francisco 1
- 'sfo2' => San Francisco 2
- 'sgp1' => Singapore
- 'tor1' => Toronto

### Add a server with Linode provider

```php
$server = $blacksmith->addServer([
    'backups' => false
    'credential' => _CREDENTIAL_ID_
    'database' => 'forge'
    'hhvm' => false
    'maria' => true
    'name' => 'moonlight-fern'
    'nodeBalancer' => false
    'old_php' => false
    'php_version' => 'php70'
    'provider' => 'linode'
    'region' => 7
    'size' => '2GB'
    'timezone' => 'Europe/Berlin'
]);
```

Replace _CREDENTIAL_ID_ with your Linode provider credential ID.

Possible size options:  
- '2GB' => 2GB RAM - 1 CPU Cores - 24GB SSD - $0.015 / Hour - $10 / Month
- '4GB' => 4GB RAM - 2 CPU Cores - 48GB SSD - $0.03 / Hour - $20 / Month
- '8GB' => 8GB RAM - 4 CPU Cores - 96GB SSD - $0.06 / Hour - $40 / Month
- '12GB' => 12GB RAM - 6 CPU Cores - 192GB SSD - $0.12 / Hour - $80 / Month
- '24GB' => 24GB RAM - 8 CPU Cores - 384GB SSD - $0.24 / Hour - $160 / Month
- '48GB' => 48GB RAM - 12 CPU Cores - 768GB SSD - $0.48 / Hour - $320 / Month
- '64GB' => 64GB RAM - 16 CPU Cores - 1152GB SSD - $0.72 / Hour - $480 / Month
- '80GB' => 80GB RAM - 20 CPU Cores - 1536GB SSD - $0.96 / Hour - $640 / Month
- '120GB' => 120GB RAM - 20 CPU Cores - 1920GB SSD - $1.44 / Hour - $960 / Month

Possible region options:  
- 4 => Atlanta
- 2 => Dallas
- 10 => Frankfurt
- 3 => Fremont
- 7 => London
- 6 => Newark
- 9 => Singapore
- 8 => Tokyo

### Add a server with Amazon provider

```php
$server = $blacksmith->addServer([
    'backups' => false
    'credential' => _CREDENTIAL_ID_
    'database' => 'forge'
    'hhvm' => false
    'maria' => true
    'name' => 'howling-moon'
    'nodeBalancer' => false
    'old_php' => false
    'php_version' => 'php70'
    'provider' => 'aws'
    'region' => 7
    'size' => '2GB'
    'timezone' => 'Europe/Berlin'
]);
```

Replace _CREDENTIAL_ID_ with your Amazon provider credential ID.

Possible size options:  
- '1GB' => 1 GiB RAM - 1 vCPU - $0.013 / Hour - $10 / Month
- '2GB' => 2 GiB RAM - 1 vCPU - $0.026 / Hour - $20 / Month
- '4GB' => 4 GiB RAM - 2 vCPUs - $0.052 / Hour - $40 / Month
- '8GB' => 8 GiB RAM - 2 vCPUs - $0.104 / Hour - $80 / Month
- '16GB' => 16 GiB RAM - 4 vCPUs - $0.239 / Hour - $175 / Month
- '32GB' => 32 GiB RAM - 8 vCPUs - $0.479 / Hour - $350 / Month
- '64GB' => 64 GiB RAM - 16 vCPUs - $0.958 / Hour - $700 / Month

Possible region options:  
- 'us-west-1' => California
- 'eu-west-1' => Ireland
- 'eu-central-1' => Frankfurt
- 'us-west-2' => Oregon
- 'sa-east-1' => Sao Paulo
- 'ap-northeast-2' => Seoul
- 'ap-southeast-1' => Singapore
- 'ap-southeast-2' => Sydney
- 'ap-northeast-1' => Tokyo
- 'us-east-1' => Virginia

### Add a custom server example

The following example will create a server with a custom provider.  
Returns a single `Server` object with a provision url.

Fields to override:  
- ip_address
- name
- size
- private_ip_address

```php
$server = $blacksmith->addServer([
    'backups' => false,
    'database' => 'forge',
    'hhvm' => false,
    'ip_address' => '123.212.124.121',
    'maria' => false,
    'name' => 'sparkling-lake',
    'nodeBalancer' => false,
    'old_php' => false,
    'php_version' => 'php70',
    'private_ip_address' => '',
    'provider' => 'custom',
    'size' => '2',
    'timezone' => 'Europe/Berlin',
]);
```

### Add a custom LoadBalancer example

The following example will create a Load Balancer with a custom provider.  
Returns a single `Server` object with a provision url.

Fields to override:  
- ip_address
- name
- size
- private_ip_address

```php
$server = $blacksmith->addServer([
    'backups' => false,
    'database' => 'forge',
    'hhvm' => false,
    'ip_address' => '123.212.124.121',
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

Notice the difference on the custom servers. The only change is nodeBalancer is set to true instead of false when a nodeBalancer is needed.

## Remove a server by its ID

Remove a server from Forge. Don't try this at home, you have been warned..

```php
$server = $blacksmith->deleteServer(1);
```
