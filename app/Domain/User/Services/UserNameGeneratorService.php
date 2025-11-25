<?php

namespace App\Domain\User\Services;

use Illuminate\Support\Str;

class UserNameGeneratorService
{
    public function generate(string $name, string $lastName): string
    {
        $uuid = Str::uuid();
        return "@" . Str::lower($name) . '-' . Str::lower($lastName) . '-' . $uuid;
    }
}
