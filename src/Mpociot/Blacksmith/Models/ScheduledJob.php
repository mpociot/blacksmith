<?php

namespace Mpociot\Blacksmith\Models;

class ScheduledJob extends ForgeModel
{

    protected $id;
    protected $frequency;
    protected $cron;
    protected $user;
    protected $command;

}