<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use BMCLibrary\Contracts\ApiResponseInterface;
use BMCLibrary\Utils\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Verify2FACodeController extends Controller
{
    public function __construct(
        private TwoFactorAuthService $twoFactorService,
        private ApiResponseInterface $apiResponse
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify-2fa-code",
     *     summary="Verificar código 2FA",
     *     description="Verifica el código 2FA y genera token de operación temporal",
     *     operationId="verify2FACode",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "operation"},
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 description="Código 2FA de 6 dígitos",
     *                 example="123456",
     *                 pattern="^[0-9]{6}$"
     *             ),
     *             @OA\Property(
     *                 property="operation",
     *                 type="string",
     *                 description="Operación para la cual se solicita verificación",
     *                 example="change_password"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Código 2FA verificado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Código 2FA verificado exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="operation_token", type="string", example="abc123..."),
     *                 @OA\Property(property="expires_in", type="integer", example=300),
     *                 @OA\Property(property="verified_at", type="string", example="2024-01-15T10:30:00.000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'operation' => ['required', 'string'],
        ]);

        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->apiResponse->call(
                    Result::fail('Usuario no autenticado', null, 401)
                );
            }

            $result = $this->twoFactorService->verifyTwoFactorCode(
                $user,
                $request->input('code'),
                $request->input('operation')
            );

            if (!$result['success']) {
                $statusCode = match ($result['error_code'] ?? '') {
                    'NO_VALID_CODE', 'INVALID_CODE', 'MAX_ATTEMPTS_EXCEEDED' => 400,
                    default => 500
                };

                return $this->apiResponse->call(
                    Result::fail($result['message'], $result['data'] ?? null, $statusCode)
                );
            }

            return $this->apiResponse->call(
                Result::ok($result['data'], $result['message'])
            );
        } catch (\Exception $e) {
            return $this->apiResponse->call(
                Result::fail('Error de autenticación', null, 401)
            );
        }
    }
}
