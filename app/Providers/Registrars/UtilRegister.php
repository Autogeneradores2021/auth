<?php

namespace App\Providers\Registrars;

use App\Utils\Abstractions\PipelineRunnerInterface;
use App\Utils\Abstractions\RateLimiterUtilInterface;
use App\Utils\Abstractions\SecureCodeUtilInterface;
use Illuminate\Contracts\Container\Container;

readonly class UtilRegister
{
    public static function register(Container $app): void
    {
        $repositories = [
            RateLimiterUtilInterface::class => \App\Utils\RateLimiterUtil::class,
            SecureCodeUtilInterface::class => \App\Utils\SecureCodeUtil::class,
            PipelineRunnerInterface::class => \App\Utils\PipelineRunner::class,
        ];

        foreach ($repositories as $interface => $implementation) {
            $app->singleton($interface, $implementation);
        }
    }
}
