<?php

namespace Mpociot\Blacksmith\Models;

use Exception;
use Illuminate\Support\Collection;

/**
 * Class SSHKey
 * @package Mpociot\Blacksmith
 */
class SSHKey extends ForgeModel
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;
}
