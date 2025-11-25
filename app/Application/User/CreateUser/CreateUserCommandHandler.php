<?php

namespace App\Application\User\CreateUser;

use App\Application\User\Abstractions\Repositories\UserRepositoryInterface;
use App\Domain\User\Services\UserNameGeneratorService;
use Illuminate\Support\Facades\Hash;
use BMCLibrary\Utils\Result;

class CreateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private UserNameGeneratorService $nameGenerator
    ) {}

    public function handle(CreateUserCommand $request): Result
    {
        $name = $this->nameGenerator->generate($request->name, $request->lastName);

        $result = $this->repository->create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return Result::ok($result);
    }
}
