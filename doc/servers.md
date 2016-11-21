# Servers

## Get all active servers

Returns a Collection of `Server` objects.

```php
$activeServers = $blacksmith->getActiveServers();
```

## Get a server by its ID

Returns a single `Server` object.

```php
$server = $blacksmith->getServer(1);
```

## Add a server to Forge

### Add a server with DigitalOcean 2.0 provider

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

Replace _CREDENTIAL_ID_ with your DigitalOcean2.0 provider credential ID.

Possible size options:  
- '512MB'
- '1GB'
- '2GB'
- '4GB'
- '8GB'
- '16GB'
- 'm-16GB'
- '32GB'
- 'm-32GB'
- '64GB'
- 'm-64GB'

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
