<?php

namespace App\Providers\Registrars;

use App\Domain\Mail\Abstractions\EmailServiceInterface;
use App\Infrastructure\Services\EmailService;
use App\Infrastructure\Services\EmailValidationMailer;
use Illuminate\Contracts\Container\Container;

readonly class ServiceRegister
{
    public static function register(Container $app): void
    {
        $app->singleton(EmailServiceInterface::class, function ($app) {
            $emailService = new EmailService();

            $emailService->addMailer($app->make(EmailValidationMailer::class));
            return $emailService;
        });
    }
}
