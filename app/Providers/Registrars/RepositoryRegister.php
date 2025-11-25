<?php

namespace App\Providers\Registrars;

use App\Application\User\Abstractions\Repositories\UserRepositoryInterface;
use App\Application\User\Abstractions\Repositories\ValidationCodeRepositoryInterface;
use App\Application\User\Abstractions\Repositories\TokenRepositoryInterface;
use Illuminate\Contracts\Container\Container;

readonly class RepositoryRegister
{
    public static function register(Container $app): void
    {
        $repositories = [
            UserRepositoryInterface::class => \App\Infrastructure\Repositories\UserRepository::class,
            ValidationCodeRepositoryInterface::class => \App\Infrastructure\Repositories\ValidationCodeRepository::class,
            TokenRepositoryInterface::class => \App\Infrastructure\Repositories\TokenRepository::class,
        ];

        foreach ($repositories as $interface => $implementation) {
            $app->bind($interface, $implementation);
        }
    }
}
