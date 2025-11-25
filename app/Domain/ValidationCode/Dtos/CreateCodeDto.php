<?php

namespace App\Domain\ValidationCode\Dtos;

readonly class CreateCodeDto
{
    public function __construct(
        public string $identifier,
        public string $type,
        public string $code,
        public int $maxAttempts = 5,
        public ?int $expiryMinutes = 5,
    ) {}

    public  function toArray(): array
    {
        return  [
            'identifier' => $this->identifier,
            'type' => $this->type,
            'code' => $this->code,
            'expires_at' => now()->addMinutes($this->expiryMinutes),
            'max_attempts' => $this->maxAttempts
        ];
    }
}
