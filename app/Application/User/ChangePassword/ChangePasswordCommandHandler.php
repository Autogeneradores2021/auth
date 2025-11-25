<?php

namespace App\Application\User\ChangePassword;

use App\Application\User\Abstractions\Repositories\UserRepositoryInterface;
use BMCLibrary\Utils\Result;
use Illuminate\Support\Facades\Hash;

class ChangePasswordCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    public function handle(ChangePasswordCommand $request): Result
    {
        $user = $this->repository->find($request->userId);

        if (!$user) {
            return Result::fail('Usuario no encontrado', null, 404);
        }

        if (!Hash::check($request->oldPassword, $user->password)) {
            return Result::fail('Contraseña actual incorrecta', null, 401);
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();

        return Result::ok(['message' => 'Contraseña actualizada exitosamente']);
    }
}
