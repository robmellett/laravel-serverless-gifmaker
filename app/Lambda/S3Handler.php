<?php

namespace App\Lambda;

use App\Console\Kernel;
use Bref\Context\Context;

class S3Handler implements \Bref\Event\Handler
{
    public function handle($event, Context $context)
    {
        app()->make(Kernel::class)->call('generate:gif');

        return 'Hello ' . $event['name'];
    }
}

return new S3Handler();
