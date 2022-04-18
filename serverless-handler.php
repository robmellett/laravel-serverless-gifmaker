<?php declare(strict_types=1);

use Bref\Context\Context;
use Illuminate\Contracts\Console\Kernel;

require_once __DIR__.'/vendor/autoload.php';

return function ($event, Context $context) {
    static $app;

    if (null === $app) {
        $app = require __DIR__.'/bootstrap/app.php';
    }

    return $app->make(Kernel::class)->call('generate:gif', [
        'payload' => $event
    ]);
};
