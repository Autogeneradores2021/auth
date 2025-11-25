<?php

namespace App\Application\Auth\Login;

use App\Application\User\Abstractions\Repositories\UserRepositoryInterface;
use App\Utils\Abstractions\RateLimiterUtilInterface;
use App\Utils\Dto\RateLimiterDto;
use BMCLibrary\Utils\Result;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class LoginCommandHandler
{

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RateLimiterUtilInterface $rateLimiter
    ) {}

    public function handle(LoginCommand $request): Result
    {
        $rateLimitKey = 'login_attempts:' . $request->email;

        $rateLimitResult = $this->rateLimiter->check(new RateLimiterDto(
            rateLimitKey: $rateLimitKey,
            maxAttempts: 5,
            decayMinutes: 5,
            message: "Demasiados intentos de inicio de sesión. Inténtalo de nuevo más tarde.",
            autoIncrement: true
        ));

        if ($rateLimitResult !== null) {
            return $rateLimitResult;
        }

        $validationResult = $this->validateCredentials($request, $rateLimitKey);
        if (!$validationResult['success']) {
            $this->rateLimiter->hit($rateLimitKey, 5);
            return $validationResult['result'];
        }

        return $this->processSuccessfulLogin($validationResult['user'], $rateLimitKey);
    }

    private function validateCredentials(LoginCommand $request): array
    {
        $user = $this->userRepository->whereQuery(
            columns: ['id', 'email', 'password', 'name'],
            where: ['email' => $request->email]
        )->first();

        if (!$user) {
            return [
                "success" => false,
                "result" => Result::fail('Usuario no encontrado', null, 404)
            ];
        }

        if (!Hash::check($request->password, $user->password)) {
            return [
                "success" => false,
                "result" => Result::fail('Credenciales incorrectas', null, 401)
            ];
        }

        return [
            "success" => true,
            "user" => $user
        ];
    }

    private function processSuccessfulLogin($user, string $rateLimitKey): Result
    {
        $this->rateLimiter->clear($rateLimitKey);
        $token = JWTAuth::fromUser($user);
        return Result::ok($token);
    }
}
