<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use BMCLibrary\Contracts\ApiResponseInterface;
use BMCLibrary\Utils\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Send2FACodeController extends Controller
{
    public function __construct(
        private TwoFactorAuthService $twoFactorService,
        private ApiResponseInterface $apiResponse
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/auth/send-2fa-code",
     *     summary="Enviar código 2FA",
     *     description="Genera y envía un código 2FA al email del usuario para operaciones críticas",
     *     operationId="send2FACode",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"operation"},
     *             @OA\Property(
     *                 property="operation",
     *                 type="string",
     *                 enum={"change_password", "delete_account", "update_email", "sensitive_data"},
     *                 description="Tipo de operación que requiere 2FA",
     *                 example="change_password"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Código 2FA enviado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Código de verificación enviado a tu email"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="expires_in", type="integer", example=600),
     *                 @OA\Property(property="code_length", type="integer", example=6),
     *                 @OA\Property(property="attempts_allowed", type="integer", example=3),
     *                 @OA\Property(property="operation", type="string", example="change_password")
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'operation' => ['required', 'string', 'in:change_password,delete_account,update_email,sensitive_data'],
        ]);

        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->apiResponse->call(
                    Result::fail('Usuario no autenticado', null, 401)
                );
            }

            $result = $this->twoFactorService->sendTwoFactorCode(
                $user,
                $request->input('operation')
            );

            if (!$result['success']) {
                $statusCode = match ($result['error_code'] ?? '') {
                    'RATE_LIMIT_EXCEEDED' => 429,
                    default => 500
                };

                return $this->apiResponse->call(
                    Result::fail($result['message'], null, $statusCode)
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
