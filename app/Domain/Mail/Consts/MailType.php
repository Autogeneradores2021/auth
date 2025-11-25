<?php

namespace App\Domain\Mail\Consts;

enum MailType: string
{
    case PASSWORD = 'password';
    case EMAIL_VERIFICATION = 'email_verification';
}
