<?php

namespace App\Http\Controllers\User;

use App\Application\User\VerificationToken\Validator\VerificationTokenRequest;
use App\Application\User\VerificationToken\VerificationTokenCommand;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class VerifyTokenController extends Controller
{
    /**
     * Valida el token enviado en la cabecera Authorization: Bearer <token>
     *
     * @OA\Post(
     *     path="/api/v1/token/verification",
     *     tags={"Verification"},
     *     summary="Valida el token de verificación",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 format="string",
     *                 description="Token de verificación",
     *                 example="7c3c09dad941d1488f891bc96a67ccdc2f38fd5564d772832594be2c9aab720c"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token válido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token válido"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="identifier", type="string", example="user@example.com"),
     *                 @OA\Property(property="type", type="string", example="email_verification"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token inválido o expirado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Token inválido o expirado")
     *         )
     *     )
     * )
     */
    public function __invoke(VerificationTokenRequest $request): JsonResponse
    {
        $command =  new VerificationTokenCommand(
            $request->input('token')
        );

        $result = $this->mediator->send($command);
        return $this->apiResponse->call($result);
    }
}
