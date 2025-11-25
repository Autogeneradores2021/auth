<?php

namespace App\Utils\Abstractions;

interface SecureCodeUtilInterface
{
    public function generate(): string;
}
